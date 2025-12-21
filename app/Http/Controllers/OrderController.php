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
        $order = Order::findOrFail($orderId);

        return view('orders.payment', compact('order'));
    }
}
