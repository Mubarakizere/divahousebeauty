<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Show the cart page (Tailwind cart.blade.php).
     */
    public function cart()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to view your cart.');
        }

        $user    = Auth::user();
        $cartItems = Cart::where('users_id', $user->id)
            ->with(['product.brand'])
            ->get();
        $count = $cartItems->count();

        return view('cart', compact('count', 'cartItems'));
    }

    /**
     * Lightweight JSON for mini-carts / APIs.
     */
    public function getCartItems()
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'User not logged in',
                'cart'    => [],
                'total'   => 0,
            ]);
        }

        $userId    = Auth::id();
        $cartItems = Cart::where('users_id', $userId)
            ->with('product')
            ->get();

        $totalPrice = $cartItems->sum(fn($i) => (int)$i->quantity * (float)$i->price);

        return response()->json([
            'cart'  => $cartItems,
            'total' => $totalPrice,
        ]);
    }

    /**
     * Remove a single cart row (dedicated DELETE route).
     */
    public function remove($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $cartItem = Cart::where('id', $id)
            ->where('users_id', Auth::id())
            ->first();

        if (!$cartItem) {
            return back()->with('error', 'Product could not be removed.');
        }

        $cartItem->delete();
        return back()->with('success', 'Product removed from cart.');
    }

    /**
     * Update quantities OR remove an item if remove_id is present.
     * This lets the delete button live inside the same POST form safely.
     */
    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $userId = Auth::id();

        // If a delete was requested from inside the update form:
        if ($request->filled('remove_id')) {
            $toRemove = (int) $request->input('remove_id');
            $cartItem = Cart::where('id', $toRemove)
                ->where('users_id', $userId)
                ->first();

            if ($cartItem) {
                $cartItem->delete();
                return back()->with('success', 'Product removed from cart.');
            }
            return back()->with('error', 'Product could not be removed.');
        }

        // Otherwise, it's a standard quantity update.
        $validated = $request->validate([
            'quantities'   => ['nullable', 'array'],
            'quantities.*' => ['integer', 'min:1', 'max:999'],
        ]);

        $quantities = $validated['quantities'] ?? [];

        if (empty($quantities)) {
            return back()->with('info', 'Nothing to update.');
        }

        // Update only the current user's rows.
        foreach ($quantities as $cartId => $qty) {
            $cartItem = Cart::where('id', (int)$cartId)
                ->where('users_id', $userId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity = max(1, (int)$qty);
                $cartItem->save();
            }
        }

        return back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Apply a coupon code to the session
     */
    public function applyCoupon(Request $request)
    {
        if (!Auth::check()) {
            return back()->with('error', 'Please login to use coupons.');
        }

        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->coupon_code));
        
        // Find the coupon
        $coupon = \App\Models\Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        // Check if coupon has expired
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return back()->with('error', 'This coupon has expired.');
        }

        // Check if coupon has started
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return back()->with('error', 'This coupon is not yet valid.');
        }

        // Check total usage limit
        if ($coupon->usage_limit && $coupon->usages()->count() >= $coupon->usage_limit) {
            return back()->with('error', 'This coupon has reached its usage limit.');
        }

        // Check per-user usage limit
        $userUsageCount = $coupon->usages()->where('user_id', Auth::id())->count();
        if ($userUsageCount >= $coupon->usage_limit_per_user) {
            return back()->with('error', 'You have already used this coupon the maximum number of times.');
        }

        // Calculate cart total
        $cartItems = \App\Models\Cart::where('users_id', Auth::id())->with('product')->get();
        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->price);

        // Check minimum order amount
        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return back()->with('error', 'Your order must be at least RWF ' . number_format($coupon->min_order_amount, 0) . ' to use this coupon.');
        }

        // Store coupon in session
        session(['coupon_code' => $code]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    /**
     * Remove the applied coupon from session
     */
    public function removeCoupon()
    {
        session()->forget('coupon_code');
        return back()->with('success', 'Coupon removed.');
    }
}
