<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please login to view your wishlist');
        }

        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with('product.category', 'product.brand')
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist (AJAX)
     */
    public function add(Request $request, $productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to wishlist'
            ], 401);
        }

        $user = Auth::user();
        
        // Check if product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist'
            ]);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        $wishlistCount = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist',
            'wishlistCount' => $wishlistCount
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function remove($productId)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please login first');
        }

        $user = Auth::user();
        
        $deleted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        if ($deleted) {
            // For AJAX requests
            if (request()->ajax()) {
                $wishlistCount = Wishlist::where('user_id', $user->id)->count();
                return response()->json([
                    'success' => true,
                    'message' => 'Removed from wishlist',
                    'wishlistCount' => $wishlistCount
                ]);
            }
            
            return redirect()->back()->with('message', 'Removed from wishlist');
        }

        return redirect()->back()->with('error', 'Item not found in wishlist');
    }

    /**
     * Move item from wishlist to cart
     */
    public function moveToCart($productId)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please login first');
        }

        $user = Auth::user();
        $product = Product::find($productId);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        // Add to cart (reusing existing cart logic)
        $cart = new \App\Models\Cart;
        $cart->users_id = $user->id;
        $cart->product_id = $product->id;
        $cart->product_title = $product->name;
        $cart->price = $product->is_on_sale ? $product->sale_price : $product->price;
        $cart->quantity = 1;
        $cart->image = json_encode($product->images);
        $cart->save();

        // Remove from wishlist
        Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        return redirect()->route('cart')->with('message', 'Product moved to cart');
    }

    /**
     * Get wishlist count for current user
     */
    public function getCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = Wishlist::where('user_id', Auth::id())->count();
        
        return response()->json(['count' => $count]);
    }
}
