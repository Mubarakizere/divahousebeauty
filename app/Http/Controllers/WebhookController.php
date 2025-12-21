<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $token = $request->input('token');
        $type = $request->input('requestType');

        if (!$token || !$type) {
            return response()->json(['error' => 'Missing token or type'], 400);
        }

        try {
            $decoded = JWT::decode($token, new Key(config('services.weflexfy.secret_key'), 'HS256'));
            $data = (array) $decoded;

            Log::info("✅ Webhook verified:", $data);

            if ($type === 'payment' && $data['status'] === 'SUCCESS') {
                $order = Order::where('payment_token', $data['requestToken'])->first();

                if ($order) {
                    $order->update([
                        'status' => 'paid',
                        'payment_method' => 'WeFlexfy',
                        'updated_at' => now(),
                        'transaction_id' => $data['paymentRef'] ?? null,
                        'masked_order_id' => $data['masked_order_id'] ?? null,
                    ]);

                    Log::info("🎉 Order #{$order->id} marked as paid.");
                } else {
                    Log::warning("❌ No matching order for token: {$data['requestToken']}");
                }
            }

            return response()->json(['message' => 'Webhook received'], 200);

        } catch (\Exception $e) {
            Log::error("❌ Webhook verification failed: " . $e->getMessage());
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
}
