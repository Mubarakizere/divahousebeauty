<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Better naming: return a listing view (separate from product detail)
        $productsCategory8 = Product::where('category_id', 8)->latest()->paginate(4);
        $productsCategory9 = Product::where('category_id', 9)->latest()->paginate(4);
        $categories        = Category::orderBy('name')->get();
        $products          = Product::latest()->paginate(10);

        return view('products.index', compact('productsCategory8', 'productsCategory9', 'products', 'categories'));
    }

    public function search(Request $request)
    {
        $query      = (string) $request->input('q');
        $categoryId = $request->input('cat');

        $products = Product::when($categoryId, fn($qb)=>$qb->where('category_id', $categoryId))
            ->search($query)
            ->latest()
            ->paginate(12);

        return view('products.search_results', compact('products', 'query', 'categoryId'));
    }

    public function getPromotionalProducts()
    {
        $now = Carbon::now();

        $products = Product::whereHas('promotion', function ($q) use ($now) {
                $q->where(function ($qq) use ($now) {
                    $qq->whereNull('start_time')->orWhere('start_time', '<=', $now);
                })->where(function ($qq) use ($now) {
                    $qq->whereNull('end_time')->orWhere('end_time', '>=', $now);
                })->where('discount_percentage', '>', 0);
            })
            ->with('promotion')
            ->latest()
            ->get()
            ->map(function (Product $p) {
                $p->new_price = $p->sale_price ?? $p->price;
                return $p;
            });

        return view('home', compact('products'));
    }

    /**
     * Show product detail by slug.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        // Sidebar & carousel datasets
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()->limit(4)->get();

        $alsoLike = Product::inRandomOrder()->take(6)->get();

        // If your Blade needs these:
        $categories = Category::orderBy('name')->get();

        // Cart variables (adapt to your implementation)
        $cartItems = collect(); // replace with your real cart items
        $count     = $cartItems->count();

        // Use a dedicated detail view
        return view('product', compact('product', 'relatedProducts', 'alsoLike', 'categories', 'cartItems', 'count'));
    }

    public function category($id)
    {
        $category = Category::findOrFail($id);
        $products = Product::where('category_id', $id)->latest()->paginate(10);

        return view('category', compact('products', 'category'));
    }
}
