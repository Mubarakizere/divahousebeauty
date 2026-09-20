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
            // Extract token from body, query parameter, or Authorization header
            $token = $request->input('token') 
                ?? $request->query('token') 
                ?? $request->header('x-token');

            if (!$token && $request->hasHeader('Authorization')) {
                $authHeader = $request->header('Authorization');
                if (str_starts_with($authHeader, 'Bearer ')) {
                    $token = substr($authHeader, 7);
                }
            }

            // Verify JWT token or parse payload
            $payload = [];
            if ($token) {
                $payload = $this->weflexfy->verifyWebhookToken($token);
            }
            
            // If token didn't contain payload, merge directly from request body
            if (empty($payload)) {
                $payload = $request->all();
            }

            $requestType = $request->input('requestType') ?? $payload['requestType'] ?? 'payment';
            
            \Log::info('Weflexfy webhook received', [
                'type' => $requestType,
                'payload' => $payload,
                'raw_all' => $request->all(),
            ]);
            
            if ($requestType === 'payment' || isset($payload['requestToken']) || isset($payload['paymentRef'])) {
                $this->handlePaymentUpdate($payload);
            } elseif ($requestType === 'transfer' || isset($payload['transferRef'])) {
                $this->handleTransferUpdate($payload);
            } else {
                // Fallback attempt
                $this->handlePaymentUpdate($payload);
            }
            
            return response()->json(['status' => 'success'], 200);
            
        } catch (\Throwable $e) {
            \Log::error('Webhook verification or execution failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json(['error' => 'Webhook processing failed', 'message' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Handle payment status update
     */
    private function handlePaymentUpdate(array $payload)
    {
        $requestToken = $payload['requestToken'] ?? null;
        $paymentRef = $payload['paymentRef'] ?? null;
        $orderId = $payload['orderId'] 
            ?? $payload['transfers'][0]['payload']['orderId'] 
            ?? $payload['payload']['orderId'] 
            ?? null;

        // Try to find payment record via request_token, payment_ref, or order_id
        $payment = null;

        if ($requestToken) {
            $payment = Payment::where('request_token', $requestToken)->first();
        }

        if (!$payment && $paymentRef) {
            $payment = Payment::where('payment_ref', $paymentRef)->first();
        }

        if (!$payment && $orderId) {
            $payment = Payment::where('order_id', $orderId)->latest()->first();
        }

        if (!$payment) {
            \Log::warning('Payment record not found for webhook payload', [
                'request_token' => $requestToken,
                'payment_ref'   => $paymentRef,
                'order_id'      => $orderId,
            ]);

            // Fallback: If we have orderId directly, process the order directly
            if ($orderId) {
                $order = Order::find($orderId);
                $status = strtolower($payload['status'] ?? '');
                $isSuccessful = in_array($status, ['success', 'successful', 'paid', 'completed', 'approved']);
                if ($order && $isSuccessful) {
                    $this->completeOrder($order, $paymentRef);
                }
            }
            return;
        }

        $status = strtolower($payload['status'] ?? '');
        $isSuccessful = in_array($status, ['success', 'successful', 'paid', 'completed', 'approved']);
        $isFailed = in_array($status, ['failed', 'declined', 'cancelled', 'rejected']);

        // Update payment record
        $payment->update([
            'payment_ref' => $paymentRef ?? $payment->payment_ref,
            'status' => $isSuccessful ? 'success' : ($isFailed ? 'failed' : $status),
            'payment_method' => $payload['paymentMethod'] ?? $payment->payment_method,
        ]);

        \Log::info('Payment status updated via webhook', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'payment_type' => $payment->payment_type,
        ]);

        // If payment successful, complete the order or shipping based on payment_type
        if ($isSuccessful) {
            if ($payment->payment_type === 'shipping') {
                app(\App\Http\Controllers\PaymentController::class)
                    ->completeShippingPayment($payment->order, $paymentRef);
            } else {
                $this->completeOrder($payment->order, $paymentRef);
            }
        } elseif ($isFailed) {
            $this->failOrder($payment->order);
        }
    }
    
    /**
     * Handle individual transfer status update
     */
    private function handleTransferUpdate(array $payload)
    {
        $transferRef = $payload['transferRef'] ?? null;
        $transfer = PaymentTransfer::where('transfer_ref', $transferRef)->first();

        if ($transfer) {
            $status = strtolower($payload['status'] ?? '');
            $isSuccessful = in_array($status, ['success', 'successful', 'paid', 'completed', 'approved']);

            $transfer->update([
                'status' => $isSuccessful ? 'success' : $status,
            ]);

            \Log::info('Transfer status updated', [
                'transfer_id' => $transfer->id,
                'status' => $transfer->status,
            ]);

            if ($isSuccessful && $transfer->payment && $transfer->payment->order) {
                $this->completeOrder($transfer->payment->order, $transferRef);
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

        if ($order->payment) {
            $order->payment->update(['status' => 'success']);
        }

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
        } catch (\Throwable $e) {
            \Log::warning('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        \Log::info('Order completed successfully via Weflexfy webhook', ['order_id' => $order->id]);
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
