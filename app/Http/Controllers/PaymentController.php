<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Mail\OrderConfirmed;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PaymentController extends Controller
{
    /**
     * Initiate payment and show iframe
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'order' => 'required|integer|exists:orders,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|min:10',
            'payment_method' => 'required|in:momo,card',
        ]);

        $order = Order::findOrFail($request->order);
        $amount = (float) $order->total;
        $recipientNumber = config('services.weflexfy.business_number');

        if (!$recipientNumber) {
            Log::error('❌ WeFlexfy Error: Missing business number');
            return back()->with('error', 'Payment setup error. Contact support.');
        }

        // Normalize phone number
        $phone = $this->normalizePhoneNumber($request->phone);

        $payload = [
            'amount' => $amount,
            'currency' => 'RWF',
            'billName' => $request->name,
            'billEmail' => $request->email,
            'billPhone' => $phone,
            'billCountry' => 'RW',
            'transfers' => [
                [
                    'percentage' => 100,
                    'recipientNumber' => $recipientNumber,
                    'payload' => [
                        'orderId' => $order->id,
                        'method' => $request->payment_method,
                        'type' => 'order_payment',
                        'customerName' => $request->name,
                        'customerEmail' => $request->email,
                    ],
                ],
            ],
        ];

        $response = Http::withHeaders([
            'access_key' => config('services.weflexfy.access_key'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.weflexfy.com/api/v1/payment/initiate', $payload);

        if ($response->status() === 201) {
            $data = $response->json('data');

            // Update order with payment information
            $order->update([
                'payment_token' => $data['requestToken'],
                'payment_method' => strtoupper($request->payment_method),
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $phone,
            ]);

            Log::info('✅ Payment initiated successfully', [
                'order_id' => $order->id,
                'request_token' => $data['requestToken'],
                'payment_method' => $request->payment_method,
                'iframe_url' => $data['iframeUrl']
            ]);

            // Redirect to iframe page
            return redirect()->to('/payment/iframe?url=' . urlencode($data['iframeUrl']) . '&order=' . $order->id);
        }

        Log::error('❌ WeFlexfy Payment Init Failed', [
            'status' => $response->status(),
            'body' => $response->body(),
            'sent' => $payload,
        ]);

        return back()->with('error', 'Payment failed: ' . ($response->json('message') ?? 'Unknown error'));
    }

    /**
     * Show payment iframe page
     */
    public function showIframe(Request $request)
    {
        $iframeUrl = $request->query('url');
        $orderId = $request->query('order');

        if (!$iframeUrl || !$orderId) {
            return redirect()->route('orders.index')
                ->with('error', 'Invalid payment session.');
        }

        $order = Order::findOrFail($orderId);
        return view('payment.iframe', compact('iframeUrl', 'order'));
    }

    /**
     * Normalize phone number to international format
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Remove any spaces, dashes, or parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        // If it doesn't start with +, assume it's a Rwandan number
        if (!str_starts_with($phone, '+')) {
            return '+250' . ltrim($phone, '0');
        }
        
        return $phone;
    }

    /**
     * Payment success callback page
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order');
        
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order && $order->is_paid) {
                return view('payment.success', compact('order'));
            }
        }

        return view('payment.success');
    }

    /**
     * Payment failed callback page
     */
    public function failed(Request $request)
    {
        $orderId = $request->query('order');
        $order = null;
        
        if ($orderId) {
            $order = Order::find($orderId);
        }

        return view('payment.failed', compact('order'));
    }

    /**
     * Webhook: WeFlexfy - Updated to match their documentation
     */
    public function handleWebhook(Request $request)
    {
        Log::info('✅ WeFlexfy Webhook received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'raw_body' => $request->getContent()
        ]);

        // Get token and requestType from request body
        $token = $request->input('token');
        $requestType = $request->input('requestType');

        if (!$token || !$requestType) {
            Log::warning('⚠️ Webhook missing required fields', [
                'token' => $token ? 'present' : 'missing',
                'requestType' => $requestType ?? 'missing'
            ]);
            return response()->json(['message' => 'Missing token or requestType'], 400);
        }

        try {
            // Verify and decode JWT using secret key
            $decoded = JWT::decode($token, new Key(config('services.weflexfy.secret_key'), 'HS256'));
            $payload = (array) $decoded;

            Log::info("🔓 WeFlexfy JWT decoded successfully", [
                'requestType' => $requestType,
                'payload' => $payload
            ]);

            // Handle different request types
            if ($requestType === 'payment') {
                return $this->handlePaymentWebhook($payload);
            } elseif ($requestType === 'transfer') {
                return $this->handleTransferWebhook($payload);
            }

            Log::warning('⚠️ Unknown requestType received', ['type' => $requestType]);
            return response()->json(['message' => 'Unknown requestType'], 400);

        } catch (\Exception $e) {
            Log::error('❌ WeFlexfy webhook JWT verification failed', [
                'error' => $e->getMessage(),
                'token_preview' => substr($token, 0, 50) . '...'
            ]);
            return response()->json(['message' => 'Invalid token'], 401);
        }
    }

    /**
     * Handle payment status webhook (requestType = "payment")
     */
    private function handlePaymentWebhook(array $payload)
    {
        // Extract payment data according to WeFlexfy documentation
        $paymentRef = $payload['paymentRef'] ?? null;
        $requestToken = $payload['requestToken'] ?? null;
        $status = $payload['status'] ?? null;
        $amount = $payload['amount'] ?? null;
        $billName = $payload['billName'] ?? null;
        $billEmail = $payload['billEmail'] ?? null;
        $billPhone = $payload['billPhone'] ?? null;

        if (!$requestToken) {
            Log::warning('⚠️ Payment webhook missing requestToken');
            return response()->json(['message' => 'Missing requestToken'], 400);
        }

        // Find order by requestToken (stored as payment_token)
        $order = Order::where('payment_token', $requestToken)->first();

        if (!$order) {
            Log::warning('⚠️ Order not found for payment webhook', [
                'requestToken' => $requestToken,
                'paymentRef' => $paymentRef
            ]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        Log::info('📦 Processing payment webhook for order', [
            'order_id' => $order->id,
            'status' => $status,
            'paymentRef' => $paymentRef,
            'amount' => $amount
        ]);

        // Handle different payment statuses
        if ($status === 'SUCCESS') {
            $order->update([
                'status' => 'confirmed',
                'is_paid' => 1,
                'transaction_id' => $paymentRef,
                'paid_at' => now(),
            ]);

            Log::info('✅ Payment confirmed successfully', [
                'order_id' => $order->id,
                'paymentRef' => $paymentRef,
                'amount' => $amount
            ]);

            // Send confirmation email
            $this->sendOrderConfirmationEmail($order);

            return response()->json([
                'message' => 'Payment confirmed',
                'order_id' => $order->id,
                'status' => 'success'
            ], 200);

        } elseif ($status === 'FAILED') {
            $order->update([
                'status' => 'failed',
                'is_paid' => 0,
            ]);

            Log::warning('❌ Payment failed', [
                'order_id' => $order->id,
                'paymentRef' => $paymentRef,
                'reason' => $payload['reason'] ?? 'Unknown'
            ]);

            return response()->json([
                'message' => 'Payment failed',
                'order_id' => $order->id,
                'status' => 'failed'
            ], 200);

        } elseif ($status === 'PENDING') {
            // Payment is still being processed
            Log::info('⏳ Payment still pending', [
                'order_id' => $order->id,
                'paymentRef' => $paymentRef
            ]);

            return response()->json([
                'message' => 'Payment pending',
                'order_id' => $order->id,
                'status' => 'pending'
            ], 200);
        }

        Log::info('ℹ️ Payment status update processed', [
            'order_id' => $order->id,
            'status' => $status,
            'paymentRef' => $paymentRef
        ]);

        return response()->json(['message' => 'Status updated'], 200);
    }

    /**
     * Handle transfer status webhook (requestType = "transfer")
     */
    private function handleTransferWebhook(array $payload)
    {
        // Extract transfer data according to WeFlexfy documentation
        $transferRef = $payload['transferRef'] ?? null;
        $paymentRef = $payload['paymentRef'] ?? null;
        $requestToken = $payload['requestToken'] ?? null;
        $status = $payload['status'] ?? null;
        $amount = $payload['amount'] ?? null;
        $recipientNumber = $payload['recipientNumber'] ?? null;
        $transferPayload = $payload['payload'] ?? null;

        Log::info('🔄 Transfer webhook received', [
            'transferRef' => $transferRef,
            'paymentRef' => $paymentRef,
            'requestToken' => $requestToken,
            'status' => $status,
            'amount' => $amount,
            'recipientNumber' => $recipientNumber,
            'payload' => $transferPayload
        ]);

        // For now, we'll just log transfer updates
        // You can add specific transfer handling logic here if needed
        
        return response()->json([
            'message' => 'Transfer status received',
            'transferRef' => $transferRef,
            'status' => $status
        ], 200);
    }

    /**
     * Send order confirmation email
     */
    private function sendOrderConfirmationEmail(Order $order)
    {
        if ($order->customer_email) {
            try {
                Mail::to($order->customer_email)->send(new OrderConfirmed($order));
                Log::info('✅ Confirmation email sent', [
                    'order_id' => $order->id,
                    'email' => $order->customer_email
                ]);
            } catch (\Exception $e) {
                Log::error('📧 Failed to send confirmation email', [
                    'order_id' => $order->id,
                    'email' => $order->customer_email,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::warning('⚠️ No email address for order confirmation', [
                'order_id' => $order->id
            ]);
        }
    }

    /**
     * AJAX Polling from iframe to check payment status
     */
    public function checkOrderStatus($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            Log::info('🔍 Payment status check', [
                'order_id' => $order->id,
                'is_paid' => $order->is_paid,
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'transaction_id' => $order->transaction_id
            ]);

            return response()->json([
                'paid' => $order->is_paid == 1,
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'transaction_id' => $order->transaction_id,
                'updated_at' => $order->updated_at->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error checking order status', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'paid' => false,
                'status' => 'error',
                'message' => 'Unable to check payment status'
            ], 500);
        }
    }

    /**
     * Retry payment for failed orders
     */
    public function retryPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->is_paid) {
            return redirect()->route('payment.success', ['order' => $orderId])
                ->with('info', 'This order is already paid.');
        }

        return view('payment.retry', compact('order'));
    }
}