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
        $brandKey    = $request->input('brand');    // can be id or slug via query
        $catIds      = (array) $request->input('categories', []);
        $minPrice    = $this->toInt($request->input('min_price'));
        $maxPrice    = $this->toInt($request->input('max_price'));
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

        // brand from ?brand=... (accept id or slug)
        if ($brandKey) {
            $brandRec = Brand::where('id', $brandKey)->orWhere('slug', $brandKey)->first();
            if ($brandRec) $productsQuery->where('brand_id', $brandRec->id);
        }

        // price range
        if ($minPrice !== null && $maxPrice !== null && $minPrice <= $maxPrice) {
            $productsQuery->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            $productsQuery->where('price', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        // sorting
        switch ($sortby) {
            case 'price_asc':  $productsQuery->orderBy('price', 'asc'); break;
            case 'price_desc': $productsQuery->orderBy('price', 'desc'); break;
            case 'name':       $productsQuery->orderBy('name', 'asc');   break;
            default:           $productsQuery->orderBy('created_at', 'desc'); // newest
        }

        $totalProducts = (clone $productsQuery)->count();
        $products      = $productsQuery->paginate(12)->withQueryString();
        $shownProducts = $products->count();

        return view('category', compact(
            'category','categories','brands','products','totalProducts','shownProducts'
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
