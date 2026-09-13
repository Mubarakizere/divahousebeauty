<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentTransfer;
use App\Models\Order;
use App\Services\WeflexfyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WeflexfyWebhookController extends Controller
{
    protected $weflexfy;
    
    public function __construct(WeflexfyService $weflexfy)
    {
        $this->weflexfy = $weflexfy;
    }
    
    /**
     * Handle incoming webhooks from Weflexfy
     */
    public function handle(Request $request)
    {
        try {
            // Verify JWT token
            $payload = $this->weflexfy->verifyWebhookToken($request->input('token'));
            $requestType = $request->input('requestType');
            
            \Log::info('Weflexfy webhook received', [
                'type' => $requestType,
                'payload' => $payload,
            ]);
            
            if ($requestType === 'payment') {
                $this->handlePaymentUpdate($payload);
            } elseif ($requestType === 'transfer') {
                $this->handleTransferUpdate($payload);
            }
            
            return response()->json(['status' => 'success'], 200);
            
        } catch (\Exception $e) {
            \Log::error('Webhook verification failed', [
                'error' => $e->getMessage(),
                'token' => $request->input('token'),
            ]);
            
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    
    /**
     * Handle payment status update
     */
    private function handlePaymentUpdate(array $payload)
    {
        $payment = Payment::where('request_token', $payload['requestToken'] ?? null)->first();
        
        if (!$payment) {
            \Log::warning('Payment not found for webhook', [
                'request_token' => $payload['requestToken'] ?? null
            ]);
            return;
        }
        
        $status = strtolower($payload['status'] ?? '');

        // Update payment record
        $payment->update([
            'payment_ref' => $payload['paymentRef'] ?? $payment->payment_ref,
            'status' => $status,
            'payment_method' => $payload['paymentMethod'] ?? $payment->payment_method,
        ]);
        
        \Log::info('Payment status updated', [
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'payment_type' => $payment->payment_type,
        ]);
        
        // If payment successful, complete the order or shipping based on payment_type
        if ($status === 'success') {
            if ($payment->payment_type === 'shipping') {
                // This is a shipping payment — complete shipping
                app(\App\Http\Controllers\PaymentController::class)
                    ->completeShippingPayment($payment->order, $payload['paymentRef'] ?? null);
            } else {
                // This is a regular order payment
                $this->completeOrder($payment->order, $payload['paymentRef'] ?? null);
            }
        } elseif ($status === 'failed') {
            $this->failOrder($payment->order);
        }
    }
    
    /**
     * Handle individual transfer status update
     */
    private function handleTransferUpdate(array $payload)
    {
        $transfer = PaymentTransfer::where('transfer_ref', $payload['transferRef'] ?? null)->first();
        
        if ($transfer) {
            $status = strtolower($payload['status'] ?? '');
            $transfer->update([
                'status' => $status,
            ]);
            
            \Log::info('Transfer status updated', [
                'transfer_id' => $transfer->id,
                'status' => $transfer->status,
            ]);

            if ($status === 'success' && $transfer->payment && $transfer->payment->order) {
                $this->completeOrder($transfer->payment->order, $payload['transferRef'] ?? null);
            }
        }
    }
    
    /**
     * Complete order after successful payment
     */
    private function completeOrder(Order $order, ?string $transactionId = null)
    {
        if (!$order) return;

        // Update order status fields
        $order->update([
            'status'         => Order::STATUS_CONFIRMED ?? 'confirmed',
            'payment_status' => 'paid',
            'is_paid'        => true,
            'paid_at'        => now(),
            'transaction_id' => $transactionId ?? $order->transaction_id,
        ]);
        
        // Reduce stock for each product
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $product->track_stock) {
                if (method_exists($product, 'updateStock')) {
                    $product->updateStock(
                        -$item->quantity,
                        'sale',
                        'Order #' . $order->id,
                        "Sold {$item->quantity} units"
                    );
                } else {
                    $product->decrement('stock', $item->quantity);
                }
            }
        }
        
        // Send confirmation email (if mailable exists)
        try {
            if (class_exists('\App\Mail\OrderConfirmed') && !empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new \App\Mail\OrderConfirmed($order));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
        
        \Log::info('Order completed successfully', ['order_id' => $order->id]);
    }
    
    /**
     * Handle failed payment
     */
    private function failOrder(Order $order)
    {
        if (!$order) return;

        $order->update([
            'payment_status' => 'failed',
            'status'         => 'failed',
        ]);
        
        \Log::info('Order payment failed', ['order_id' => $order->id]);
    }
}
