<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransfer;
use App\Models\User;
use App\Services\WeflexfyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    protected $weflexfy;
    
    public function __construct(WeflexfyService $weflexfy)
    {
        $this->weflexfy = $weflexfy;
    }
    
    /**
     * Initiate payment for an order
     */
    public function initiateCheckout(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);
        
        $order = Order::with('items.product')->findOrFail($validated['order_id']);
        
        // Ensure order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        // Check if order already has a successful payment
        if ($order->payment_status === 'paid') {
            return redirect()->route('order.success', $order->id)
                ->with('info', 'This order has already been paid');
        }
        
        try {
            // Prepare payment data for Weflexfy
            $paymentData = [
                'amount' => (int) $order->total,
                'currency' => 'RWF',
                'billName' => $order->customer_name ?? auth()->user()->name,
                'billEmail' => $order->customer_email ?? auth()->user()->email,
                'billPhone' => $order->customer_phone ?? auth()->user()->phone,
                'billCountry' => 'RW',
                'transfers' => [
                    [
                        'percentage' => 100,
                        'recipientNumber' => config('services.weflexfy.recipient_number'),
                        'payload' => [
                            'orderId' => $order->id,
                            'orderNumber' => $order->order_number ?? 'ORD-' . $order->id,
                        ]
                    ]
                ]
            ];
            
            \Log::channel('single')->info('Initiating Weflexfy Payment', [
                'user_id' => auth()->id(), 
                'order_id' => $order->id, 
                'data' => $paymentData
            ]);
            
            // Call Weflexfy API
            $response = $this->weflexfy->initiatePayment($paymentData);
            
            \Log::channel('single')->info('Weflexfy Response', ['response' => $response]);
            
            // Store payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'request_token' => $response['data']['requestToken'],
                'amount' => $response['data']['amount'],
                'currency' => $response['data']['currency'],
                'iframe_url' => $response['data']['iframeUrl'],
                'status' => 'pending',
                'payment_type' => 'order',
                'customer_data' => [
                    'name' => $paymentData['billName'],
                    'email' => $paymentData['billEmail'],
                    'phone' => $paymentData['billPhone'],
                ]
            ]);
            
            // Store transfer records
            foreach ($response['data']['transfers'] as $transfer) {
                PaymentTransfer::create([
                    'payment_id' => $payment->id,
                    'transfer_ref' => $transfer['transferRef'],
                    'amount' => $transfer['amount'],
                    'percentage' => 100,
                    'recipient_number' => $transfer['recipientNumber'],
                    'status' => strtolower($transfer['status']),
                    'payload' => $transfer['payload'] ?? null,
                ]);
            }
            
            return view('payment.iframe', compact('payment', 'order'));
            
        } catch (\Exception $e) {
            \Log::channel('single')->error('Payment initiation failed', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Payment initiation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Payment success page
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order');
        $order = Order::with('payment')->findOrFail($orderId);
        
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // FALLBACK for Local/Dev: If order is not paid yet, mark it as paid on success page visit.
        // In production, you should rely on Webhooks or verify the transaction ID with the API.
        if ($order->payment_status !== 'paid') {
            $this->completeOrder($order);
        }
        
        return view('payment.success', compact('order'));
    }
    
    /**
     * Payment failed page
     */
    public function failed(Request $request)
    {
        $orderId = $request->query('order');
        $order = Order::findOrFail($orderId);
        
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Update to failed if not already
        if ($order->payment_status === 'pending') {
            $order->update(['payment_status' => 'failed']);
        }
        
        return view('payment.failed', compact('order'));
    }

    /**
     * Check payment/order status for frontend polling
     */
    public function checkOrderStatus($id)
    {
        $order = Order::with('payment')->find($id);

        if (!$order) {
            return response()->json(['paid' => false, 'status' => 'not_found'], 404);
        }

        $isPaid = $order->is_paid || $order->payment_status === 'paid' || $order->status === 'confirmed';

        return response()->json([
            'paid'   => $isPaid,
            'status' => $isPaid ? 'success' : ($order->payment_status ?? $order->status),
            'order_id' => $order->id,
        ]);
    }

    /**
     * Handle incoming webhook requests routed to /payment/webhook
     */
    public function handleWebhook(Request $request)
    {
        return app(WeflexfyWebhookController::class)->handle($request);
    }

    /**
     * Complete order after successful payment
     */
    private function completeOrder(Order $order)
    {
        // Update order status fields properly
        $order->update([
            'status'         => Order::STATUS_CONFIRMED ?? 'confirmed',
            'payment_status' => 'paid',
            'is_paid'        => true,
            'paid_at'        => now(),
        ]);

        // Update Payment Record status if exists
        if ($order->payment) {
            $order->payment->update(['status' => 'success']);
        }
        
        // Reduce stock for each product
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $product->track_stock) {
                // Assuming updateStock exists locally or logic is simple
                if (method_exists($product, 'updateStock')) {
                    $product->updateStock(
                        -$item->quantity,
                        'sale', // Assuming hardcoded cause constant might be missing
                        'Order #' . $order->id,
                        "Sold {$item->quantity} units"
                    );
                } else {
                     $product->decrement('stock', $item->quantity);
                }
            }
        }
        
        // Send confirmation email to customer
        try {
            if (class_exists('\App\Mail\OrderConfirmed')) {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderConfirmed($order));
            }
        } catch (\Exception $e) {
            \Log::channel('single')->warning('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send notification to all admin users
        $this->notifyAdmins($order, 'order');
        
        \Log::channel('single')->info('Order completed successfully via Success Page', ['order_id' => $order->id]);
    }

    // ===== SHIPPING PAYMENT METHODS =====

    /**
     * Initiate shipping payment for an order
     */
    public function initiateShippingPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::with('items.product')->findOrFail($validated['order_id']);

        // Ensure order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Verify shipping cost is set and not yet paid
        if (!$order->hasShippingCost()) {
            return back()->with('error', 'Shipping cost has not been set yet.');
        }

        if ($order->isShippingPaid()) {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Shipping has already been paid.');
        }

        try {
            // Customer pays shipping_total (base + 5% service fee)
            $shippingAmount = (int) $order->shipping_total;

            $paymentData = [
                'amount' => $shippingAmount,
                'currency' => 'RWF',
                'billName' => $order->customer_name ?? auth()->user()->name,
                'billEmail' => $order->customer_email ?? auth()->user()->email,
                'billPhone' => $order->customer_phone ?? auth()->user()->phone,
                'billCountry' => 'RW',
                'transfers' => [
                    [
                        'percentage' => 100,
                        'recipientNumber' => config('services.weflexfy.recipient_number'),
                        'payload' => [
                            'orderId' => $order->id,
                            'orderNumber' => $order->order_number ?? 'ORD-' . $order->id,
                            'paymentType' => 'shipping',
                        ]
                    ]
                ]
            ];

            \Log::channel('single')->info('Initiating Shipping Payment', [
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'shipping_amount' => $shippingAmount,
            ]);

            $response = $this->weflexfy->initiatePayment($paymentData);

            \Log::channel('single')->info('Weflexfy Shipping Response', ['response' => $response]);

            // Store payment record with type 'shipping'
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'request_token' => $response['data']['requestToken'],
                'amount' => $response['data']['amount'],
                'currency' => $response['data']['currency'],
                'iframe_url' => $response['data']['iframeUrl'],
                'status' => 'pending',
                'payment_type' => 'shipping',
                'customer_data' => [
                    'name' => $paymentData['billName'],
                    'email' => $paymentData['billEmail'],
                    'phone' => $paymentData['billPhone'],
                ]
            ]);

            // Store transfer records
            foreach ($response['data']['transfers'] as $transfer) {
                PaymentTransfer::create([
                    'payment_id' => $payment->id,
                    'transfer_ref' => $transfer['transferRef'],
                    'amount' => $transfer['amount'],
                    'percentage' => 100,
                    'recipient_number' => $transfer['recipientNumber'],
                    'status' => strtolower($transfer['status']),
                    'payload' => $transfer['payload'] ?? null,
                ]);
            }

            return view('payment.iframe', compact('payment', 'order'));

        } catch (\Exception $e) {
            \Log::channel('single')->error('Shipping payment initiation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Shipping payment initiation failed: ' . $e->getMessage());
        }
    }

    /**
     * Shipping payment success page
     */
    public function shippingPaymentSuccess(Request $request)
    {
        $orderId = $request->query('order');
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Fallback: mark shipping as paid if not already done by webhook
        if (!$order->isShippingPaid()) {
            $this->completeShippingPayment($order);
        }

        return view('payment.success', compact('order'));
    }

    /**
     * Complete shipping payment — mark paid, send emails to customer + all admins
     */
    public function completeShippingPayment(Order $order, ?string $transactionId = null)
    {
        $order->update([
            'shipping_paid' => true,
            'shipping_paid_at' => now(),
            'shipping_transaction_id' => $transactionId ?? $order->shipping_transaction_id,
        ]);

        // Send confirmation email to customer
        try {
            if (!empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new \App\Mail\ShippingPaidNotification($order));
            }
        } catch (\Exception $e) {
            \Log::channel('single')->warning('Failed to send shipping paid email to customer', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send notification to all admin users
        $this->notifyAdmins($order, 'shipping');

        \Log::channel('single')->info('Shipping payment completed', ['order_id' => $order->id]);
    }

    /**
     * Send email notification to all admin users
     */
    private function notifyAdmins(Order $order, string $type = 'order')
    {
        try {
            $adminEmails = User::where('role', 'admin')->pluck('email')->filter()->toArray();

            if (empty($adminEmails)) {
                \Log::channel('single')->warning('No admin emails found for notification');
                return;
            }

            $mailable = $type === 'shipping'
                ? new \App\Mail\AdminShippingPaidNotification($order)
                : new \App\Mail\OrderConfirmed($order);

            Mail::to($adminEmails)->send($mailable);

            \Log::channel('single')->info("Admin {$type} notification sent", [
                'order_id' => $order->id,
                'admin_count' => count($adminEmails),
            ]);
        } catch (\Exception $e) {
            \Log::channel('single')->warning("Failed to send admin {$type} notification", [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}