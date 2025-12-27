<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartRecoveryController extends Controller
{
    /**
     * Recover abandoned cart from token
     */
    public function recover($token)
    {
        $abandoned = AbandonedCart::where('recovery_token', $token)
            ->where('is_recovered', false)
            ->firstOrFail();

        // Clear existing cart if any
        if (auth()->check()) {
            Cart::where('user_id', auth()->id())->delete();
        } else {
            Cart::where('session_id', session()->getId())->delete();
        }

        // Restore cart items from abandoned cart
        foreach ($abandoned->cart_items as $item) {
            Cart::create([
                'users_id' => auth()->id() ?? null,
                'session_id' => auth()->check() ? null : session()->getId(),
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // Mark as recovered
        $abandoned->update([
            'is_recovered' => true,
            'recovered_at' => now(),
        ]);

        return redirect()->route('cart')
            ->with('success', 'Your cart has been restored! Complete your purchase now.');
    }

    /**
     * Show recovery statistics (admin only)
     */
    public function stats()
    {
        $this->authorize('viewAny', AbandonedCart::class); // Add policy if needed

        $stats = AbandonedCart::getStats();
        $recentCarts = AbandonedCart::with('user')
            ->latest('abandoned_at')
            ->limit(20)
            ->get();

        return view('admin.abandoned-carts.stats', compact('stats', 'recentCarts'));
    }
}
