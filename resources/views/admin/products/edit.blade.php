@extends('layouts.dashboard')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-edit text-blue-600 mr-3"></i>
                Edit Product
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Update product information, category/brand and manage images.
            </p>
        </div>
        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Products
        </a>
    </div>

    {{-- Success --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Errors --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-400 mt-0.5"></i>
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

    {{-- Form --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('admin.products.update', $product) }}"
              method="POST"
              enctype="multipart/form-data"
              class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic --}}
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Basic Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ shippingType: '{{ old('shipping_type', $product->shipping_type ?? 'express_only') }}' }">
                    {{-- Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $product->name) }}"
                            required
                            class="block w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
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
                            <option value="express_only" {{ old('shipping_type', $product->shipping_type ?? 'express_only') === 'express_only' ? 'selected' : '' }}>Express Only (Fast Delivery)</option>
                            <option value="standard_only" {{ old('shipping_type', $product->shipping_type ?? '') === 'standard_only' ? 'selected' : '' }}>Standard Only (7+ Days)</option>
                            <option value="both" {{ old('shipping_type', $product->shipping_type ?? '') === 'both' ? 'selected' : '' }}>Both Options (Customer Chooses)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Choose which shipping/pricing options are available for this product.
                        </p>
                        @error('shipping_type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Express Price --}}
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
                                value="{{ old('express_price', $product->express_price) }}"
                                min="0"
                                step="1"
                                :required="shippingType === 'express_only' || shippingType === 'both'"
                                class="block w-full pl-12 pr-3 py-2 border {{ $errors->has('express_price') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        @error('express_price')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Standard Price --}}
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
                                value="{{ old('standard_price', $product->standard_price) }}"
                                min="0"
                                step="1"
                                :required="shippingType === 'standard_only' || shippingType === 'both'"
                                class="block w-full pl-12 pr-3 py-2 border {{ $errors->has('standard_price') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Lower price for customers willing to wait.</p>
                        @error('standard_price')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
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
                            value="{{ old('stock', $product->stock) }}"
                            min="0"
                            required
                            class="block w-full px-3 py-2 border {{ $errors->has('stock') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('stock')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Category & Brand / Sub-brand --}}
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
                            required
                        >
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ (int) old('category_id', $product->category_id) === (int) $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Start here. Once you pick a category, the Brand list will only show brands/sub-brands that belong to this category.
                        </p>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Brand --}}
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Brand / Sub-brand
                            <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>

                        @php
                            // Pre-build options with data attributes for JS filtering
                            $selectedBrandId = (int) old('brand_id', $product->brand_id);
                        @endphp

                        <select
                            name="brand_id"
                            id="brand_id"
                            data-selected="{{ $selectedBrandId }}"
                            class="block w-full px-3 py-2 border {{ $errors->has('brand_id') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">No brand selected</option>
                            @foreach($brands as $brand)
                                <option
                                    value="{{ $brand->id }}"
                                    data-category-id="{{ $brand->category_id ?? '' }}"
                                    data-parent-id="{{ $brand->parent_id ?? '' }}"
                                    {{ $selectedBrandId === (int) $brand->id ? 'selected' : '' }}
                                >
                                    {{ $brand->parent_id ? '— ' : '' }}{{ $brand->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-1 text-xs text-gray-500">
                            Top-level brands appear normally. Sub-brands will be indented under their parent
                            (e.g. Men → Shoes). Brand must belong to the selected category.
                        </p>

                        @error('brand_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Description --}}
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
                        placeholder="Describe your product features, benefits, and specifications..."
                    >{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Current Images --}}
            @if(is_array($product->images) && count($product->images))
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-images text-green-500 mr-2"></i>
                        Current Images
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ count($product->images) }} images
                        </span>
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($product->images as $index => $img)
                            <div class="relative group bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <div class="aspect-square">
                                    <img
                                        src="{{ asset('storage/' . $img) }}"
                                        alt="Product image {{ $index + 1 }}"
                                        class="w-full h-full object-cover"
                                    >
                                </div>

                                {{-- Overlay: remove button --}}
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-200 flex items-center justify-center">
                                    <form
                                        action="{{ route('admin.products.update', $product) }}"
                                        method="POST"
                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                        onsubmit="return confirm('Remove this image?')"
                                    >
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="remove_image" value="{{ $index }}">
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors font-medium shadow-lg">
                                            <i class="fas fa-trash mr-2"></i> Remove
                                        </button>
                                    </form>
                                </div>

                                {{-- Index badge --}}
                                <div class="absolute top-2 left-2 bg-white/90 text-gray-700 text-xs px-2 py-1 rounded-full font-medium flex items-center space-x-1">
                                    <span>#{{ $index + 1 }}</span>
                                    @if($index === 0)
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-800">
                                            Feature
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        The <span class="font-semibold">first image</span> is treated as the feature image in listings.
                    </p>
                </div>
            @endif

            {{-- Add Images --}}
            <div class="pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-pink-500 mr-2"></i>
                    Add More Images
                </h2>

                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Additional Images <span class="text-gray-400 text-xs">(Optional)</span>
                </label>

                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                        <div class="flex text-sm text-gray-600">
                            <label for="images"
                                   class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Upload files</span>
                                <input
                                    id="images"
                                    name="images[]"
                                    type="file"
                                    class="sr-only"
                                    multiple
                                    accept="image/*"
                                >
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">
                            PNG, JPG, GIF up to <span class="font-semibold">30MB</span> each. You can upload multiple images at once.
                        </p>
                    </div>
                </div>
                @error('images.*')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-3 space-y-3 sm:space-y-0 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.products.index') }}"
                   class="inline-flex justify-center items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <button type="submit"
                        class="inline-flex justify-center items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i> Update Product
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<script>
    // Show number of selected files
    document.getElementById('images')?.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files.length > 0) {
            const span = document.querySelector('label[for="images"] span');
            if (span) {
                span.textContent = `${files.length} file(s) selected`;
            }
        }
    });

    // Filter brands by selected category
    const categorySelect = document.getElementById('category_id');
    const brandSelect    = document.getElementById('brand_id');

    if (categorySelect && brandSelect) {
        const allBrandOptions = Array.from(brandSelect.options);
        const selectedBrandId = brandSelect.dataset.selected || '';

        const rebuildBrandOptions = () => {
            const currentCategoryId = categorySelect.value;

            brandSelect.innerHTML = '';

            // default placeholder
            const placeholder = allBrandOptions.find(opt => opt.value === '');
            if (placeholder) {
                brandSelect.appendChild(placeholder.cloneNode(true));
            }

            allBrandOptions.forEach(opt => {
                if (!opt.value) return; // skip placeholder here
                const catId = opt.getAttribute('data-category-id') || '';

                if (!currentCategoryId || catId === currentCategoryId) {
                    const clone = opt.cloneNode(true);
                    brandSelect.appendChild(clone);
                }
            });

            // restore selected value if still valid
            if (selectedBrandId) {
                Array.from(brandSelect.options).forEach(o => {
                    if (o.value === selectedBrandId) {
                        o.selected = true;
                    }
                });
            }
        };

        // initial build
        rebuildBrandOptions();

        // on change
        categorySelect.addEventListener('change', () => {
            // clear selected brand when category changes
            brandSelect.dataset.selected = '';
            rebuildBrandOptions();
        });
    }
</script>
@endsection
