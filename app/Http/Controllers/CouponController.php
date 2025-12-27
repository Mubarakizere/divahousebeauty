<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Apply coupon code to cart
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $code = strtoupper(trim($request->input('code')));
        
        // Find coupon
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        // Get cart total from session (adjust based on your cart implementation)
        $cartTotal = session('cart_total', 0);

        // Validate coupon
        if (!$coupon->isValid(auth()->id(), $cartTotal)) {
            $message = $this->getValidationErrorMessage($coupon, $cartTotal);
            return back()->with('error', $message);
        }

        // Store coupon in session
        session(['applied_coupon' => $coupon->id]);

        $discount = $coupon->calculateDiscount($cartTotal);

        return back()->with('success', "Coupon '{$coupon->code}' applied! You saved " . number_format($discount, 0) . ' RWF');
    }

    /**
     * Remove applied coupon
     */
    public function remove()
    {
        session()->forget('applied_coupon');

        return back()->with('success', 'Coupon removed.');
    }

    /**
     * Get validation error message
     */
    private function getValidationErrorMessage(Coupon $coupon, float $cartTotal): string
    {
        if (!$coupon->is_active) {
            return 'This coupon is not active.';
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return 'This coupon is not yet valid.';
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return 'This coupon has expired.';
        }

        if ($coupon->usage_limit && $coupon->times_used >= $coupon->usage_limit) {
            return 'This coupon has reached its usage limit.';
        }

        if (auth()->check()) {
            $userUsage = $coupon->usages()->where('user_id', auth()->id())->count();
            if ($userUsage >= $coupon->usage_limit_per_user) {
                return 'You have already used this coupon the maximum number of times.';
            }
        }

        if ($coupon->min_order_amount && $cartTotal < $coupon->min_order_amount) {
            return 'Your order total must be at least ' . number_format($coupon->min_order_amount, 0) . ' RWF to use this coupon.';
        }

        return 'This coupon is not valid.';
    }
}
