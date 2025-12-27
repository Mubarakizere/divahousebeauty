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
        $payment = Payment::where('request_token', $payload['requestToken'])->first();
        
        if (!$payment) {
            \Log::warning('Payment not found for webhook', [
                'request_token' => $payload['requestToken']
            ]);
            return;
        }
        
        // Update payment record
        $payment->update([
            'payment_ref' => $payload['paymentRef'] ?? $payment->payment_ref,
            'status' => strtolower($payload['status']),
            'payment_method' => $payload['paymentMethod'] ?? $payment->payment_method,
        ]);
        
        \Log::info('Payment status updated', [
            'payment_id' => $payment->id,
            'status' => $payment->status,
        ]);
        
        // If payment successful, complete the order
        if (strtolower($payload['status']) === 'success') {
            $this->completeOrder($payment->order);
        } elseif (strtolower($payload['status']) === 'failed') {
            $this->failOrder($payment->order);
        }
    }
    
    /**
     * Handle individual transfer status update
     */
    private function handleTransferUpdate(array $payload)
    {
        $transfer = PaymentTransfer::where('transfer_ref', $payload['transferRef'])->first();
        
        if ($transfer) {
            $transfer->update([
                'status' => strtolower($payload['status']),
            ]);
            
            \Log::info('Transfer status updated', [
                'transfer_id' => $transfer->id,
                'status' => $transfer->status,
            ]);
        }
    }
    
    /**
     * Complete order after successful payment
     */
    private function completeOrder(Order $order)
    {
        // Update order status
        $order->update([
            'order_status' => Order::STATUS_CONFIRMED ?? 'confirmed',
            'payment_status' => 'paid',
        ]);
        
        // Reduce stock for each product
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $product->track_stock) {
                $product->updateStock(
                    -$item->quantity,
                    \App\Models\StockMovement::TYPE_SALE ?? 'sale',
                    'Order #' . $order->id,
                    "Sold {$item->quantity} units"
                );
            }
        }
        
        // Send confirmation email (if mailable exists)
        try {
            if (class_exists('\App\Mail\OrderConfirmed')) {
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
        $order->update([
            'payment_status' => 'failed',
        ]);
        
        \Log::info('Order payment failed', ['order_id' => $order->id]);
    }
}
