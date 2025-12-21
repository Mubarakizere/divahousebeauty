<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Promotion;
use App\Models\Brand;

class HomeController extends Controller
{
    // Redirect users based on role
    public function redirect()
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $usertype = Auth::user()->role;
    
    if ($usertype == 'admin') {
        return redirect()->route('filament.pages');
    } else {
        $user = auth()->user();

        // Fetch the cart count and cart items for the user
        $count = Cart::where('users_id', $user->id)->count();
        $cartItems = Cart::where('users_id', $user->id)
                          ->with('product')
                          ->get();

        return view('home', compact('count', 'cartItems'));
    }
}


    // Load home page with categories, products, promotions, and cart data
    public function index()
{
    $categories = Category::with('brands')->get();
    $products   = Product::with('category')->get();
    $promotions = Promotion::with('product')->get();

    // --- 1) newest product per brand (ALL brands) ---
    $brandLatestAll = Brand::with([
            'category',
            'latestProduct' => fn ($q) => $q->with('category') // allow any category
        ])
        ->has('latestProduct')
        ->get()
        ->sortByDesc(fn ($b) => optional($b->latestProduct)->created_at)
        ->values()
        ->take(12);

    // --- 2) newest product per brand (ONLY category 5) ---
    $brandLatest5 = Brand::with([
            'category',
            'latestProduct' => fn ($q) => $q->with('category')->where('category_id', 5)
        ])
        ->where('category_id', 5)
        ->has('latestProduct')
        ->get()
        ->sortByDesc(fn ($b) => optional($b->latestProduct)->created_at)
        ->values()
        ->take(12);

    // --- 3) newest product per brand (ONLY category 15) ---
    $brandLatest15 = Brand::with([
            'category',
            'latestProduct' => fn ($q) => $q->with('category')->where('category_id', 15)
        ])
        ->where('category_id', 15)
        ->has('latestProduct')
        ->get()
        ->sortByDesc(fn ($b) => optional($b->latestProduct)->created_at)
        ->values()
        ->take(12);

    $cat5  = Category::find(5);
    $cat15 = Category::find(15);

    // cart data
    $count = 0; $cartItems = collect();
    if (Auth::check()) {
        $user = auth()->user();
        $count = Cart::where('users_id', $user->id)->count();
        $cartItems = Cart::where('users_id', $user->id)->with('product')->get();
    }

    return view('home', compact(
        'categories', 'products', 'promotions',
        'count', 'cartItems',
        'brandLatestAll', 'brandLatest5', 'brandLatest15',
        'cat5', 'cat15'
    ));
}
    // Show login page
    public function login()
    {
        $categories = Category::all(); // Fetch all categories
        return view('login', compact('categories'));
    }

    // Add product to cart
    public function addcart(Request $request, $id)
{
    if (Auth::check()) {
        $user = auth()->user();
        $product = Product::find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        // Check for active promotions
        $promotion = $product->promotion()
    ->where('end_time', '>=', now())
    ->first();

        $discountedPrice = $promotion
            ? $product->price * (1 - $promotion->discount_percentage / 100)
            : $product->price;

        // Log applied price for debugging
        \Log::info('Applied Price:', ['price' => $discountedPrice]);

        // Create new cart entry
        $cart = new Cart;
        $cart->users_id = $user->id;
        $cart->product_id = $product->id;
        $cart->product_title = $product->name;
        $cart->price = $discountedPrice;
        $cart->quantity = $request->quantity ?? 1;
        $cart->image = json_encode($product->images);
        $cart->save();

        return redirect()->back()->with('message', 'Product Added Successfully');
    } else {
        // Instead of redirecting to the login route, set the error message in the session
        return redirect()->back()->with('error', 'You must log in first to add items to the cart');
    }
}


    // Show product details
    public function showProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $categories = Category::with('brands')->get();
        $promotions = Promotion::where('product_id', $id)->get();
        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->limit(4)
                                  ->get();

        $count = 0;
        $cartItems = collect();

        if (Auth::check()) {
            $user = auth()->user();
            $count = Cart::where('users_id', $user->id)->count();
            $cartItems = Cart::where('users_id', $user->id)
                             ->with('product')
                             ->get();
        }

        return view('product', compact('product', 'categories', 'promotions', 'relatedProducts', 'count', 'cartItems'));
    }
}
