<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $cartItems = Cart::where('users_id', $userId)->with('product')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty');
        }
        
        $subtotal = $cartItems->sum(fn($i) => (float)$i->price * (int)$i->quantity);
        
        // Calculate discount
        $discount = 0;
        $appliedCoupon = null;
        if (session('applied_coupon')) {
            $appliedCoupon = Coupon::find(session('applied_coupon'));
            if ($appliedCoupon && $appliedCoupon->isValid($userId, $subtotal)) {
                $discount = $appliedCoupon->calculateDiscount($subtotal);
            }
        }
        
        $total = $subtotal - $discount;
        
        return view('checkout.index', compact('cartItems', 'subtotal', 'discount', 'total', 'appliedCoupon'));
    }
    
    public function process(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_notes' => 'nullable|string|max:1000',
        ]);
        
        $userId = auth()->id();
        $cartItems = Cart::where('users_id', $userId)->with('product')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty');
        }
        
        $subtotal = $cartItems->sum(fn($i) => (float)$i->price * (int)$i->quantity);
        
        // Calculate discount
        $discount = 0;
        $couponId = null;
        if (session('applied_coupon')) {
            $coupon = Coupon::find(session('applied_coupon'));
            if ($coupon && $coupon->isValid($userId, $subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponId = $coupon->id;
            }
        }
        
        $total = $subtotal - $discount;
        
        // Create order
        $order = Order::create([
            'user_id' => $userId,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'total' => $total,
            'payment_method' => 'weflexfy',
            'status' => 'pending',
            'order_status' => 'pending',
            'customer_notes' => $validated['customer_notes'] ?? null,
        ]);
        
        // Create order items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }
        
        // Record coupon usage if applied
        if ($couponId) {
            \App\Models\CouponUsage::create([
                'coupon_id' => $couponId,
                'user_id' => $userId,
                'order_id' => $order->id,
                'discount_amount' => $discount,
            ]);
        }
        
        // Clear cart
        Cart::where('users_id', $userId)->delete();
        session()->forget('applied_coupon');
        
        // Redirect to payment
        return redirect()->route('payment.initiate', ['order_id' => $order->id]);
    }
}
