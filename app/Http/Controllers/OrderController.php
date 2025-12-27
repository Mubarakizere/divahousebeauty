<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $user = auth()->user();
        $cartItems = Cart::where('users_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $total = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending_payment',
            'payment_method' => null,
            'payment_token' => null,
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }

        Cart::where('users_id', $user->id)->delete();

        return redirect()->route('order.payment', $order->id);
    }

    public function paymentPage($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        
        // Fetch user's saved addresses
        $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
        
        // Get applied coupon from session
        $appliedCoupon = null;
        $discount = 0;
        $couponCode = session('coupon_code');
        
        if ($couponCode) {
            $appliedCoupon = \App\Models\Coupon::where('code', $couponCode)
                ->where('is_active', true)
                ->first();
            
            if ($appliedCoupon) {
                $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
                
                // Use the Coupon model's calculateDiscount method which validates all conditions
                $discount = $appliedCoupon->calculateDiscount($subtotal);
                
                // If discount is 0, the coupon failed validation - clear it from session
                if ($discount == 0 && $appliedCoupon->min_order_amount && $subtotal < $appliedCoupon->min_order_amount) {
                    session()->forget('coupon_code');
                    session()->flash('error', 'Coupon removed: Your order must be at least RWF ' . number_format($appliedCoupon->min_order_amount, 0) . ' to use this coupon.');
                    $appliedCoupon = null;
                } elseif ($discount == 0) {
                    // Coupon failed validation for other reasons
                    session()->forget('coupon_code');
                    session()->flash('error', 'Coupon removed: This coupon is no longer valid.');
                    $appliedCoupon = null;
                }
            }
        }
        
        return view('orders.payment', compact('order', 'addresses', 'appliedCoupon', 'discount'));
    }

    /**
     * Show customer's order history
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show order details and tracking
     */
    public function show($id)
    {
        $order = auth()->user()->orders()->with(['items.product'])->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel order if still pending/confirmed
     */
    public function cancel($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled');
        }

        $order->updateStatus(Order::STATUS_CANCELLED, 'Cancelled by customer');

        return back()->with('success', 'Order cancelled successfully');
    }
}
