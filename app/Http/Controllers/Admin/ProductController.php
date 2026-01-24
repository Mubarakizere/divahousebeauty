<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * List products with simple search.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $query = Product::with(['category', 'brand'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($qb) use ($search) {
                        $qb->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($qc) use ($search) {
                        $qc->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $products = $query->paginate(10)->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'search'   => $search,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::with('category')->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'express_price'  => ['required', 'numeric', 'min:0'],
            'standard_price' => ['nullable', 'numeric', 'min:0'],
            'shipping_type'  => ['required', 'in:both,express_only,standard_only'],
            'stock'          => ['required', 'integer'],
            'category_id'    => ['required', 'exists:categories,id'],
            'brand_id'       => ['nullable', 'exists:brands,id'],
            'description'    => ['nullable', 'string'],
            // 30MB per image (Laravel uses KB)
            'images.*'       => ['nullable', 'image', 'max:30720'],
        ]);

        // Validate standard_price is required when shipping_type is 'both' or 'standard_only'
        if (in_array($validated['shipping_type'], ['both', 'standard_only']) && empty($validated['standard_price'])) {
            return back()
                ->withInput()
                ->withErrors(['standard_price' => 'Standard price is required for this shipping type.']);
        }

        // ✅ Defensive check: brand must belong to same category
        if (!empty($validated['brand_id'])) {
            $brand = Brand::find($validated['brand_id']);

            if (
                $brand &&
                $brand->category_id &&
                (int) $brand->category_id !== (int) $validated['category_id']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'brand_id' => 'Selected brand belongs to a different category.',
                    ]);
            }
        }

        // ✅ Handle multiple images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                if ($img->isValid()) {
                    $images[] = $img->store('products', 'public');
                }
            }
        }

        $validated['slug']   = Str::slug($validated['name']) . '-' . uniqid();
        $validated['images'] = $images; // stored as JSON (cast in model)

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit(Product $product)
    {
        if (Gate::denies('update', $product)) {
            abort(403, 'Blocked by policy check');
        }

        $categories = Category::orderBy('name')->get();
        $brands     = Brand::with('category')->orderBy('name')->get();

        return view('admin.products.edit', [
            'product'    => $product,
            'categories' => $categories,
            'brands'     => $brands,
        ]);
    }

    /**
     * Update product (also handles single-image remove).
     */
    public function update(Request $request, Product $product)
    {
        // Current images from DB (JSON column)
        $images = (array) ($product->images ?? []);

        // ✅ STEP 1: handle image removal if requested
        if ($request->filled('remove_image')) {
            $index = (int) $request->input('remove_image');

            if (isset($images[$index])) {
                // Delete physical file
                Storage::disk('public')->delete($images[$index]);

                // Remove from array & reindex
                unset($images[$index]);
                $images = array_values($images);

                $product->images = $images;
                $product->save();

                return back()->with('success', 'Image removed successfully.');
            }

            return back()->with('error', 'Image not found.');
        }

        // ✅ STEP 2: normal update
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'express_price'  => ['required', 'numeric', 'min:0'],
            'standard_price' => ['nullable', 'numeric', 'min:0'],
            'shipping_type'  => ['required', 'in:both,express_only,standard_only'],
            'stock'          => ['required', 'integer'],
            'category_id'    => ['required', 'exists:categories,id'],
            'brand_id'       => ['nullable', 'exists:brands,id'],
            'description'    => ['nullable', 'string'],
            'images.*'       => ['nullable', 'image', 'max:30720'], // 30MB
        ]);

        // Validate standard_price is required when shipping_type is 'both' or 'standard_only'
        if (in_array($validated['shipping_type'], ['both', 'standard_only']) && empty($validated['standard_price'])) {
            return back()
                ->withInput()
                ->withErrors(['standard_price' => 'Standard price is required for this shipping type.']);
        }

        // Defensive brand/category check
        if (!empty($validated['brand_id'])) {
            $brand = Brand::find($validated['brand_id']);

            if (
                $brand &&
                $brand->category_id &&
                (int) $brand->category_id !== (int) $validated['category_id']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'brand_id' => 'Selected brand belongs to a different category.',
                    ]);
            }
        }

        // ✅ Add new images (multi-upload)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                if ($img->isValid()) {
                    $images[] = $img->store('products', 'public');
                }
            }
        }

        $validated['slug']   = Str::slug($validated['name']) . '-' . uniqid();
        $validated['images'] = $images;

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product (and all its images).
     */
    public function destroy(Product $product)
    {
        $images = (array) ($product->images ?? []);

        foreach ($images as $path) {
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return back()->with('success', 'Product deleted.');
    }
}
