<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $query = Brand::with(['category', 'parent'])
            ->orderBy('name');

        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $brands */
        $brands = $query->paginate(15)->withQueryString();

        // Row start for numbering (same as categories page)
        $rowStart = ($brands->currentPage() - 1) * $brands->perPage();

        return view('admin.brands.index', compact('brands', 'search', 'rowStart'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        // For parent select: only top-level brands
        $brands = Brand::with('category')
            ->topLevel()
            ->orderBy('name')
            ->get();

        return view('admin.brands.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'parent_id'   => ['nullable', 'exists:brands,id'],
        ]);

        // If a parent is selected, force the child to use the parent's category
        if (!empty($data['parent_id'])) {
            $parent = Brand::findOrFail($data['parent_id']);
            $data['category_id'] = $parent->category_id;
        }

        Brand::create($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        $categories = Category::orderBy('name')->get();

        // Possible parents: top-level brands (any category), except itself
        $possibleParents = Brand::topLevel()
            ->where('id', '!=', $brand->id)
            ->orderBy('name')
            ->get();

        return view('admin.brands.edit', [
            'brand'           => $brand,
            'categories'      => $categories,
            'possibleParents' => $possibleParents,
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'parent_id'   => ['nullable', 'exists:brands,id'],
        ]);

        // Prevent setting itself as parent
        if (!empty($data['parent_id']) && (int) $data['parent_id'] === $brand->id) {
            return back()
                ->withInput()
                ->withErrors([
                    'parent_id' => 'A brand cannot be its own parent.',
                ]);
        }

        // If a parent is selected, force the child to use the parent's category
        if (!empty($data['parent_id'])) {
            $parent = Brand::findOrFail($data['parent_id']);
            $data['category_id'] = $parent->category_id;
        }

        $brand->update($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        // Detach children from this parent before delete
        $brand->children()->update(['parent_id' => null]);

        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }
}
