<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class WeFlexfyWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        // ✅ Log the full response for debugging (optional)
        Log::info('WeFlexfy Webhook Received', $payload);

        $reference = $payload['external_reference'] ?? null;
        $status = $payload['status'] ?? 'failed';

        if (!$reference) {
            return response()->json(['error' => 'Missing reference'], 400);
        }

        $booking = Booking::where('reference', $reference)->first();

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        if ($status === 'success') {
            $booking->status = 'confirmed';
            if ($booking->deposit_amount && $payload['amount'] >= $booking->service->price) {
                $booking->is_fully_paid = true;
            }
            $booking->save();

            // Redirect user to success page (for browser-triggered requests)
            return redirect()->route('booking.success')->with('success', 'Payment completed and booking confirmed!');
        }

        // For failed payment
        $booking->status = 'failed';
        $booking->save();

        return redirect()->route('booking.step4')->with('error', 'Payment failed. Please try again.');
    }
}
