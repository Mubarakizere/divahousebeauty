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
            $productsQuery->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            $productsQuery->where('price', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            $productsQuery->where('price', '<=', $maxPrice);
        }

        switch ($sortby) {
            case 'price_asc':  $productsQuery->orderBy('price', 'asc'); break;
            case 'price_desc': $productsQuery->orderBy('price', 'desc'); break;
            case 'name':       $productsQuery->orderBy('name', 'asc');   break;
            default:           $productsQuery->orderBy('created_at', 'desc'); // newest
        }

        $totalProducts = (clone $productsQuery)->count();
        $products      = $productsQuery->paginate(12)->withQueryString();
        $shownProducts = $products->count();

        // Reuse the same page; pass $brand for header context
        return view('category', compact('categories','brands','products','totalProducts','shownProducts','brand'))
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
