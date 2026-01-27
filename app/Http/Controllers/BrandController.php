<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class BrandController extends Controller
{
    /** Optional: /brand → send users to the main shop */
    public function index(Request $request)
    {
        return redirect()->route('category.index');
    }

    /** /brand/{brand:slug} — products for a single brand */
    public function show(Request $request, Brand $brand)
    {
        $categories = Category::with('brands')->orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        $q        = trim((string) $request->input('q'));
        $minPrice = $this->toInt($request->input('min_price'));
        $maxPrice = $this->toInt($request->input('max_price'));
        $catIds   = array_filter((array) $request->input('categories', []), 'is_numeric');
        $sortby   = $request->input('sortby', 'date'); // date | price_asc | price_desc | name

        $productsQuery = Product::with(['brand','category','promotion'])
            ->where('brand_id', $brand->id);

        if ($q !== '')                 $productsQuery->search($q);
        if (!empty($catIds))           $productsQuery->whereIn('category_id', $catIds);

        if ($minPrice !== null && $maxPrice !== null && $minPrice <= $maxPrice) {
            $productsQuery->whereBetween('express_price', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            $productsQuery->where('express_price', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            $productsQuery->where('express_price', '<=', $maxPrice);
        }

        switch ($sortby) {
            case 'price_asc':  $productsQuery->orderBy('express_price', 'asc'); break;
            case 'price_desc': $productsQuery->orderBy('express_price', 'desc'); break;
            case 'name':       $productsQuery->orderBy('name', 'asc');   break;
            default:           $productsQuery->orderBy('created_at', 'desc'); // newest
        }

        $totalProducts = (clone $productsQuery)->count();
        $products      = $productsQuery->paginate(12)->withQueryString();
        $shownProducts = $products->count();

        // SEO Meta Tags for Brand
        $seo = [
            'title' => "Buy {$brand->name} in Rwanda - Authentic Products | Diva House Beauty",
            'description' => "Shop authentic {$brand->name} products in Rwanda ✓ {$totalProducts}+ Products ✓ Fast Kigali Delivery ✓ 100% Genuine ✓ MTN & Airtel Money ✓ Rwanda's #1 Cosmetics Store",
            'keywords' => "{$brand->name} Rwanda, buy {$brand->name} Kigali, {$brand->name} online Rwanda, authentic {$brand->name}, cosmetics Rwanda, beauty products Kigali",
            'canonical' => route('brand.show', $brand->slug),
            'og' => [
                'title' => "{$brand->name} - Rwanda's Premier Beauty Store",
                'description' => "Shop authentic {$brand->name} products in Rwanda. Fast Kigali delivery, trusted quality.",
                'url' => route('brand.show', $brand->slug),
                'type' => 'website',
            ],
        ];

        // Reuse the same page; pass $brand for header context
        return view('category', compact('categories','brands','products','totalProducts','shownProducts','brand','seo'))
               ->with('category', null);
    }

    /* ---------- helpers ---------- */
    private function toInt($v): ?int
    {
        if ($v === null || $v === '') return null;
        return (int) preg_replace('/[^\d]/', '', (string) $v);
    }

    // Rest of resource not used right now
    public function create(){ abort(404); }
    public function store(Request $r){ abort(404); }
    public function edit(string $id){ abort(404); }
    public function update(Request $r, string $id){ abort(404); }
    public function destroy(string $id){ abort(404); }
}
