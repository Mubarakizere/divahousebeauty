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

        // Track this product view for "Recently Viewed" (with error handling)
        try {
            $product->trackView();
        } catch (\Exception $e) {
            \Log::warning('Failed to track product view: ' . $e->getMessage());
        }

        // Smart related products (same category, prefer same brand)
        try {
            $relatedProducts = $product->getRelatedProducts(6);
        } catch (\Exception $e) {
            $relatedProducts = collect();
            \Log::warning('Failed to get related products: ' . $e->getMessage());
        }
        
        // Frequently bought together
        try {
            $frequentlyBoughtTogether = $product->getFrequentlyBoughtTogether(3);
        } catch (\Exception $e) {
            $frequentlyBoughtTogether = collect();
            \Log::warning('Failed to get frequently bought together: ' . $e->getMessage());
        }
        
        // Customers also bought (collaborative filtering)
        try {
            $customersAlsoBought = $product->getCustomersAlsoBought(6);
        } catch (\Exception $e) {
            $customersAlsoBought = collect();
            \Log::warning('Failed to get customers also bought: ' . $e->getMessage());
        }
        
        // Recently viewed products (exclude current)
        try {
            $recentlyViewed = Product::getRecentlyViewed(6, $product->id);
        } catch (\Exception $e) {
            $recentlyViewed = collect();
            \Log::warning('Failed to get recently viewed: ' . $e->getMessage());
        }
        
        // Best sellers in this category
        try {
            $bestSellers = Product::getBestSellersInCategory($product->category_id, 4);
        } catch (\Exception $e) {
            $bestSellers = collect();
            \Log::warning('Failed to get best sellers: ' . $e->getMessage());
        }

        // Fallback: Random products if others are empty
        $alsoLike = Product::where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(6)
            ->get();

        // If your Blade needs these:
        $categories = Category::orderBy('name')->get();

        // Cart variables
        $cartItems = collect();
        $count     = $cartItems->count();

        // SEO Meta Tags
        try {
            $seo = \App\Helpers\SEOHelper::productMeta($product);
        } catch (\Exception $e) {
            $seo = [
                'title' => $product->name . ' | Diva House Beauty',
                'description' => substr($product->description ?? '', 0, 155),
            ];
            \Log::warning('Failed to generate SEO meta: ' . $e->getMessage());
        }

        return view('product', compact(
            'product',
            'relatedProducts',
            'frequentlyBoughtTogether',
            'customersAlsoBought',
            'recentlyViewed',
            'bestSellers',
            'alsoLike',
            'categories',
            'cartItems',
            'count',
            'seo'
        ));
    }

    public function category($id)
    {
        $category = Category::findOrFail($id);
        $products = Product::where('category_id', $id)->latest()->paginate(10);
        
        // SEO Meta Tags
        $seo = \App\Helpers\SEOHelper::categoryMeta($category, $products);

        return view('category', compact('products', 'category', 'seo'));
    }
}
