<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CartServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            // Ensure the user is logged in before fetching their cart
            $cartItems = [];
            $count = 0;

            if (Auth::check()) {
                $cartItems = Cart::where('users_id', Auth::id())
                    ->with('product') // Ensure product relationship is loaded
                    ->get();
                $count = $cartItems->count();
            }

            $view->with(compact('cartItems', 'count'));
        });
    }

    public function register()
    {
        //
    }
}
