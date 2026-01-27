<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class CategoryController extends Controller
{
    /** /shop or /category — all products + filters */
    public function index(Request $request)
    {
        $categories = Category::with('brands')->orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        $q           = trim((string) $request->input('q'));
        $categoryKey = $request->input('category'); // can be id or slug via query
        $brandKey    = $request->input('brand');    // single brand (legacy)
        $brandIds    = (array) $request->input('brands', []); // multi-brand
        $catIds      = (array) $request->input('categories', []);
        $minPrice    = $this->toInt($request->input('min_price'));
        $maxPrice    = $this->toInt($request->input('max_price'));
        $inStockOnly = $request->boolean('in_stock', false);
        $sortby      = $request->input('sortby', 'date');

        $productsQuery = Product::with(['brand','category','promotion']);

        if ($q !== '') $productsQuery->search($q);

        // category from ?category=... (accept id or slug)
        $category = null;
        if ($categoryKey) {
            $category = Category::where('id', $categoryKey)->orWhere('slug', $categoryKey)->first();
            if ($category) $productsQuery->where('category_id', $category->id);
        }

        // sidebar multi-select categories (ids)
        if (!empty($catIds)) {
            $productsQuery->whereIn('category_id', array_filter($catIds, 'is_numeric'));
        }

        // Single brand (legacy support)
        if ($brandKey) {
            $brandRec = Brand::where('id', $brandKey)->orWhere('slug', $brandKey)->first();
            if ($brandRec) $productsQuery->where('brand_id', $brandRec->id);
        }
        
        // Multi-brand filtering
        if (!empty($brandIds)) {
            $brandIds = array_filter($brandIds, 'is_numeric');
            if (!empty($brandIds)) {
                $productsQuery->whereIn('brand_id', $brandIds);
            }
        }
        
        // Stock availability filter
        if ($inStockOnly) {
            $productsQuery->where('stock', '>', 0);
        }

        // price range
        if ($minPrice !== null && $maxPrice !== null && $minPrice <= $maxPrice) {
            $productsQuery->whereBetween('express_price', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            $productsQuery->where('express_price', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            $productsQuery->where('express_price', '<=', $maxPrice);
        }

        // sorting
        switch ($sortby) {
            case 'price_asc':  
                $productsQuery->orderBy('express_price', 'asc'); 
                break;
            case 'price_desc': 
                $productsQuery->orderBy('express_price', 'desc'); 
                break;
            case 'name':       
                $productsQuery->orderBy('name', 'asc');   
                break;
            case 'rating':
                // Sort by average rating (requires reviews)
                $productsQuery->leftJoin('reviews', function($join) {
                    $join->on('products.id', '=', 'reviews.product_id')
                         ->where('reviews.status', '=', 'approved');
                })
                ->select('products.*', \DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating'))
                ->groupBy('products.id')
                ->orderByDesc('avg_rating');
                break;
            case 'popular':
                // Sort by number of times ordered
                $productsQuery->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                ->select('products.*', \DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'))
                ->groupBy('products.id')
                ->orderByDesc('total_sold');
                break;
            default:           
                $productsQuery->orderBy('created_at', 'desc'); // newest
        }

        $totalProducts = (clone $productsQuery)->count();
        $products      = $productsQuery->paginate(12)->withQueryString();
        $shownProducts = $products->count();

        // SEO Meta Tags
        $seo = null;
        if ($category) {
            $seo = \App\Helpers\SEOHelper::categoryMeta($category, $products);
        } else {
            // Generic category/shop page SEO
            $seo = [
                'title' => 'Shop All Products - Rwanda\'s #1 Cosmetics Store | Diva House Beauty',
                'description' => 'Shop authentic beauty products and cosmetics in Rwanda ✓ 100+ Products ✓ Fast Kigali Delivery ✓ MTN & Airtel Money ✓ Trusted Quality ✓ Best Prices',
                'keywords' => 'shop cosmetics Rwanda, beauty products Kigali, buy makeup online Rwanda, skincare Rwanda, Diva House Beauty',
                'canonical' => route('category'),
            ];
        }

        return view('category', compact(
            'category','categories','brands','products','totalProducts','shownProducts','seo'
        ))->with('brand', null);
    }

    /** /category/{category:slug} — products for a single category */
    public function show(Request $request, Category $category)
    {
        // Reuse index() logic but force category
        $request->merge(['category' => $category->id]);
        return $this->index($request);
    }

    /* ---------- helpers ---------- */
    private function toInt($v): ?int
    {
        if ($v === null || $v === '') return null;
        return (int) preg_replace('/[^\d]/', '', (string) $v);
    }
}
