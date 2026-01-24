@extends('layouts.dashboard')

@section('title', 'Add Product')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                Add New Product
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Create a new product and attach it to the right category & brand/sub-brand.
            </p>
        </div>
        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Products
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @php
        // Prepare a simple structure for JS: brands grouped by category with parent_id
        $brandsByCategory = $brands->groupBy('category_id')->map(function ($group) {
            return $group->map(function ($b) {
                return [
                    'id'        => $b->id,
                    'name'      => $b->name,
                    'parent_id' => $b->parent_id,
                ];
            })->values();
        });
    @endphp

    {{-- Form Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('admin.products.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="p-6 space-y-6">
            @csrf

            {{-- Basic Information Section --}}
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Basic Information
                </h2>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ shippingType: '{{ old('shipping_type', 'express_only') }}' }">
                    {{-- Product Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="block w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            value="{{ old('name') }}"
                            required
                            placeholder="Enter product name">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Shipping Type --}}
                    <div class="md:col-span-2">
                        <label for="shipping_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Shipping Options <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="shipping_type"
                            id="shipping_type"
                            x-model="shippingType"
                            class="block w-full px-3 py-2 border {{ $errors->has('shipping_type') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="express_only" {{ old('shipping_type') === 'express_only' ? 'selected' : '' }}>Express Only (Fast Delivery)</option>
                            <option value="standard_only" {{ old('shipping_type') === 'standard_only' ? 'selected' : '' }}>Standard Only (7+ Days)</option>
                            <option value="both" {{ old('shipping_type') === 'both' ? 'selected' : '' }}>Both Options (Customer Chooses)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Choose which shipping/pricing options are available for this product.
                        </p>
                        @error('shipping_type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Express Price (Fast Delivery) --}}
                    <div x-show="shippingType === 'express_only' || shippingType === 'both'">
                        <label for="express_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Express Price (RWF) <span class="text-red-500">*</span>
                            <span class="text-xs text-blue-600 font-normal ml-1">Fast Delivery</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">RWF</span>
                            </div>
                            <input
                                type="number"
                                name="express_price"
                                id="express_price"
                                class="block w-full pl-12 pr-3 py-2 border {{ $errors->has('express_price') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('express_price') }}"
                                :required="shippingType === 'express_only' || shippingType === 'both'"
                                min="0"
                                step="1"
                                placeholder="0">
                        </div>
                        @error('express_price')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Standard Price (7+ Days) --}}
                    <div x-show="shippingType === 'standard_only' || shippingType === 'both'">
                        <label for="standard_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Standard Price (RWF) <span class="text-red-500">*</span>
                            <span class="text-xs text-green-600 font-normal ml-1">7+ Days Delivery</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">RWF</span>
                            </div>
                            <input
                                type="number"
                                name="standard_price"
                                id="standard_price"
                                class="block w-full pl-12 pr-3 py-2 border {{ $errors->has('standard_price') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('standard_price') }}"
                                :required="shippingType === 'standard_only' || shippingType === 'both'"
                                min="0"
                                step="1"
                                placeholder="0">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Lower price for customers willing to wait.</p>
                        @error('standard_price')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Stock --}}
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock"
                            id="stock"
                            class="block w-full px-3 py-2 border {{ $errors->has('stock') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            value="{{ old('stock') }}"
                            required
                            min="0"
                            placeholder="0">
                        @error('stock')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Category & Brand Section --}}
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tags text-purple-500 mr-2"></i>
                    Category & Brand / Sub-brand
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="category_id"
                            id="category_id"
                            class="block w-full px-3 py-2 border {{ $errors->has('category_id') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (int) old('category_id') === $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Start here. Once you pick a category, the Brand list will show only brands/sub-brands
                            that belong to this category.
                        </p>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Brand / Sub-brand --}}
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Brand / Sub-brand
                            <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <select
                            name="brand_id"
                            id="brand_id"
                            data-old-brand="{{ old('brand_id') }}"
                            class="block w-full px-3 py-2 border {{ $errors->has('brand_id') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            disabled>
                            <option value="">Select a category first</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Top-level brands appear normally. Sub-brands will be indented under their parent
                            (e.g. <span class="font-mono">Men → Shoes</span>).
                        </p>
                        @error('brand_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Description Section --}}
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-align-left text-orange-500 mr-2"></i>
                    Product Description
                </h2>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="5"
                        class="block w-full px-3 py-2 border {{ $errors->has('description') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Describe your product features, benefits, and specifications...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Images Section --}}
            <div class="pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-images text-pink-500 mr-2"></i>
                    Product Images
                </h2>

                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Images <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <div class="mt-1 flex flex-col gap-4">
                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="images"
                                           class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload files</span>
                                        <input id="images"
                                               name="images[]"
                                               type="file"
                                               class="sr-only"
                                               multiple
                                               accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                            </div>
                        </div>

                        {{-- Preview grid --}}
                        <div id="image-preview"
                             class="hidden grid grid-cols-2 sm:grid-cols-3 gap-4">
                            {{-- JS will inject thumbnails here --}}
                        </div>
                    </div>

                    @error('images.*')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-3 space-y-3 sm:space-y-0 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.products.index') }}"
                   class="inline-flex justify-center items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex justify-center items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Category → Brand linkage + Image preview --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ------------ Category → Brand filtering ------------
    const brandsByCategory = @json($brandsByCategory);
    const categorySelect   = document.getElementById('category_id');
    const brandSelect      = document.getElementById('brand_id');
    const oldBrandId       = brandSelect.dataset.oldBrand ? parseInt(brandSelect.dataset.oldBrand, 10) : null;

    function populateBrands(categoryId) {
        brandSelect.innerHTML = '';

        if (!categoryId || !brandsByCategory[categoryId] || brandsByCategory[categoryId].length === 0) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = categoryId ? 'No brands for this category yet' : 'Select a category first';
            brandSelect.appendChild(opt);
            brandSelect.disabled = !categoryId;
            return;
        }

        // Base "no brand" option
        const noneOpt = document.createElement('option');
        noneOpt.value = '';
        noneOpt.textContent = 'No brand / sub-brand';
        brandSelect.appendChild(noneOpt);

        const list = brandsByCategory[categoryId];

        // Build a map for parent/children to show indentation
        const byId = {};
        list.forEach(b => byId[b.id] = b);

        const topLevel = list.filter(b => !b.parent_id);
        const childrenByParent = {};
        list.forEach(b => {
            if (b.parent_id) {
                if (!childrenByParent[b.parent_id]) {
                    childrenByParent[b.parent_id] = [];
                }
                childrenByParent[b.parent_id].push(b);
            }
        });

        function addOption(brand, depth) {
            const opt = document.createElement('option');
            opt.value = brand.id;
            const prefix = depth > 0 ? '— '.repeat(depth) : '';
            opt.textContent = prefix + brand.name;
            if (oldBrandId && brand.id === oldBrandId) {
                opt.selected = true;
            }
            brandSelect.appendChild(opt);

            const children = childrenByParent[brand.id] || [];
            children.forEach(child => addOption(child, depth + 1));
        }

        topLevel.forEach(b => addOption(b, 0));

        brandSelect.disabled = false;
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            const catId = this.value || null;
            populateBrands(catId);
        });

        // Initial population if old category exists
        const initialCatId = categorySelect.value || null;
        if (initialCatId) {
            populateBrands(initialCatId);
        }
    }

    // ------------ Image preview ------------
    const imagesInput  = document.getElementById('images');
    const previewGrid  = document.getElementById('image-preview');

    if (imagesInput && previewGrid) {
        imagesInput.addEventListener('change', function (e) {
            const files = Array.from(e.target.files || []);
            previewGrid.innerHTML = '';

            if (!files.length) {
                previewGrid.classList.add('hidden');
                return;
            }

            files.forEach(file => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function (event) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-gray-50';

                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.alt = file.name;
                    img.className = 'h-28 w-full object-cover';

                    const info = document.createElement('div');
                    info.className = 'px-2 py-1 border-t border-gray-200 bg-white';
                    info.innerHTML = `
                        <p class="text-[11px] font-medium text-gray-800 truncate">${file.name}</p>
                        <p class="text-[10px] text-gray-500">
                            ${(file.size / 1024).toFixed(1)} KB
                        </p>
                    `;

                    wrapper.appendChild(img);
                    wrapper.appendChild(info);
                    previewGrid.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });

            previewGrid.classList.remove('hidden');
        });
    }
});
</script>
@endsection
