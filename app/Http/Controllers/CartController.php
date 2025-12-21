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
}
