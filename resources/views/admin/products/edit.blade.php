@extends('layouts.dashboard')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Edit Product</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $product->name }}</p>
        </div>
        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i> Back
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-md mb-5">
            <i class="fas fa-check-circle text-green-500"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-md mb-5">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-md mb-5">
            <p class="font-medium mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ───────────────────────────────────────────────────────────── --}}
    {{-- CURRENT IMAGES (separate from main form to avoid nesting)   --}}
    {{-- ───────────────────────────────────────────────────────────── --}}
    @if(is_array($product->images) && count($product->images))
        <div class="bg-white rounded-lg border border-gray-200 p-5 mb-4">
            <p class="text-sm font-medium text-gray-800 mb-3">Current Images</p>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                @foreach($product->images as $index => $img)
                    <div class="rounded-md border border-gray-200 overflow-hidden bg-gray-50">
                        <div class="aspect-square">
                            <img src="{{ asset('storage/' . $img) }}"
                                 alt="Image {{ $index + 1 }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="px-2 py-1.5 flex items-center justify-between bg-white border-t border-gray-100">
                            <span class="text-[11px] text-gray-400">
                                #{{ $index + 1 }}
                                @if($index === 0)<span class="text-amber-600 font-medium">Main</span>@endif
                            </span>
                            {{-- Each remove button is its own form — NOT nested --}}
                            <form action="{{ route('admin.products.update', $product) }}" method="POST"
                                  onsubmit="return confirm('Remove this image?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="remove_image" value="{{ $index }}">
                                <button type="submit"
                                        class="text-[11px] text-red-500 hover:text-red-700 font-medium transition-colors">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ───────────────────────────────────────────────────────────── --}}
    {{-- MAIN EDIT FORM                                              --}}
    {{-- ───────────────────────────────────────────────────────────── --}}
    <form action="{{ route('admin.products.update', $product) }}"
          method="POST"
          enctype="multipart/form-data"
          id="edit-product-form">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5 mb-4">
            <p class="text-sm font-medium text-gray-800 mb-4">Basic Information</p>

            {{-- Name --}}
            <div class="mb-4">
                <label for="name" class="block text-sm text-gray-600 mb-1">Product Name <span class="text-red-400">*</span></label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $product->name) }}"
                       required
                       class="w-full px-3 py-2 text-sm border @error('name') border-red-400 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Shipping type + prices --}}
            <div x-data="{ type: '{{ old('shipping_type', $product->shipping_type ?? 'express_only') }}' }" class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Shipping Options <span class="text-red-400">*</span></label>
                <select name="shipping_type" x-model="type" required
                        class="w-full px-3 py-2 text-sm border @error('shipping_type') border-red-400 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400 mb-3">
                    <option value="express_only" {{ old('shipping_type', $product->shipping_type) === 'express_only' ? 'selected' : '' }}>Express Only</option>
                    <option value="standard_only" {{ old('shipping_type', $product->shipping_type) === 'standard_only' ? 'selected' : '' }}>Standard Only (7+ days)</option>
                    <option value="both" {{ old('shipping_type', $product->shipping_type) === 'both' ? 'selected' : '' }}>Both (customer chooses)</option>
                </select>

                <div class="grid grid-cols-2 gap-3">
                    <div x-show="type === 'express_only' || type === 'both'">
                        <label class="block text-xs text-gray-500 mb-1">Express Price (RWF) <span class="text-red-400">*</span></label>
                        <input type="number" name="express_price" id="express_price"
                               value="{{ old('express_price', $product->express_price) }}"
                               min="0" step="1"
                               :required="type === 'express_only' || type === 'both'"
                               class="w-full px-3 py-2 text-sm border @error('express_price') border-red-400 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                        @error('express_price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div x-show="type === 'standard_only' || type === 'both'">
                        <label class="block text-xs text-gray-500 mb-1">Standard Price (RWF) <span class="text-red-400">*</span></label>
                        <input type="number" name="standard_price" id="standard_price"
                               value="{{ old('standard_price', $product->standard_price) }}"
                               min="0" step="1"
                               :required="type === 'standard_only' || type === 'both'"
                               class="w-full px-3 py-2 text-sm border @error('standard_price') border-red-400 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                        @error('standard_price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Stock --}}
            <div>
                <label for="stock" class="block text-sm text-gray-600 mb-1">Stock Quantity <span class="text-red-400">*</span></label>
                <input type="number" name="stock" id="stock"
                       value="{{ old('stock', $product->stock) }}"
                       min="0" required
                       class="w-full px-3 py-2 text-sm border @error('stock') border-red-400 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                @error('stock')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Category & Brand --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5 mb-4">
            <p class="text-sm font-medium text-gray-800 mb-4">Category & Brand</p>

            @php $selectedBrandId = (int) old('brand_id', $product->brand_id); @endphp

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-sm text-gray-600 mb-1">Category <span class="text-red-400">*</span></label>
                    <select name="category_id" id="category_id" required
                            class="w-full px-3 py-2 text-sm border @error('category_id') border-red-400 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                        <option value="">Select category…</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ (int) old('category_id', $product->category_id) === (int) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="brand_id" class="block text-sm text-gray-600 mb-1">Brand <span class="text-xs text-gray-400">(optional)</span></label>
                    <select name="brand_id" id="brand_id"
                            data-selected="{{ $selectedBrandId }}"
                            class="w-full px-3 py-2 text-sm border @error('brand_id') border-red-400 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                        <option value="">No brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                                    data-category-id="{{ $brand->category_id ?? '' }}"
                                    {{ $selectedBrandId === (int) $brand->id ? 'selected' : '' }}>
                                {{ $brand->parent_id ? '— ' : '' }}{{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5 mb-4">
            <p class="text-sm font-medium text-gray-800 mb-3">Description <span class="text-xs text-gray-400 font-normal">(optional)</span></p>
            <textarea name="description" id="description" rows="4"
                      placeholder="Describe the product…"
                      class="w-full px-3 py-2 text-sm border @error('description') border-red-400 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400 resize-none"
            >{{ old('description', $product->description) }}</textarea>
            @error('description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Add New Images --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5 mb-6">
            <p class="text-sm font-medium text-gray-800 mb-3">
                @if(is_array($product->images) && count($product->images))
                    Add More Images <span class="text-xs text-gray-400 font-normal">(optional)</span>
                @else
                    Upload Images <span class="text-xs text-gray-400 font-normal">(optional)</span>
                @endif
            </p>

            <label for="images"
                   class="flex flex-col items-center gap-1 w-full px-4 py-5 border border-dashed border-gray-300 rounded-md cursor-pointer hover:border-gray-400 hover:bg-gray-50/50 transition-colors text-center">
                <i class="fas fa-image text-lg text-gray-300"></i>
                <span class="text-sm text-gray-600">
                    <span id="upload-label" class="font-medium text-gray-700">Choose images</span>
                    <span class="text-gray-400">or drag & drop</span>
                </span>
                <span class="text-xs text-gray-400">PNG, JPG up to 30 MB each</span>
                <input type="file" id="images" name="images[]" multiple accept="image/*" class="sr-only">
            </label>

            {{-- New image previews --}}
            <div id="new-previews" class="hidden mt-3">
                <p class="text-xs text-gray-500 mb-2" id="new-count"></p>
                <div id="preview-grid" class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-2"></div>
                <button type="button" onclick="clearImages()"
                        class="mt-2 text-xs text-gray-500 hover:text-red-500 underline transition-colors">
                    Clear selection
                </button>
            </div>

            @error('images.*')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit row --}}
        <div class="flex items-center justify-end gap-3 mb-8">
            <a href="{{ route('admin.products.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md px-4 py-2 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="text-sm font-medium text-white bg-gray-900 hover:bg-gray-700 rounded-md px-5 py-2 transition-colors">
                Save Changes
            </button>
        </div>

    </form>
</div>

<script>
    // ── Image preview ───────────────────────────────────────────────────
    const imgInput    = document.getElementById('images');
    const previewBox  = document.getElementById('new-previews');
    const previewGrid = document.getElementById('preview-grid');
    const countLabel  = document.getElementById('new-count');
    const uploadLabel = document.getElementById('upload-label');

    function showPreviews(files) {
        previewGrid.innerHTML = '';
        if (!files || !files.length) {
            previewBox.classList.add('hidden');
            uploadLabel.textContent = 'Choose images';
            return;
        }
        countLabel.textContent = `${files.length} image${files.length > 1 ? 's' : ''} selected`;
        uploadLabel.textContent = `${files.length} image${files.length > 1 ? 's' : ''} chosen`;
        previewBox.classList.remove('hidden');

        Array.from(files).forEach(f => {
            const r = new FileReader();
            r.onload = e => {
                const div = document.createElement('div');
                div.className = 'aspect-square rounded overflow-hidden border border-gray-200 bg-gray-50';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-full object-cover';
                div.appendChild(img);
                previewGrid.appendChild(div);
            };
            r.readAsDataURL(f);
        });
    }

    imgInput?.addEventListener('change', function() { showPreviews(this.files); });

    function clearImages() {
        imgInput.value = '';
        showPreviews(null);
    }

    // Drag & drop on the label
    const dropLabel = imgInput?.closest('label');
    if (dropLabel) {
        dropLabel.addEventListener('dragover', e => { e.preventDefault(); dropLabel.classList.add('border-gray-500'); });
        dropLabel.addEventListener('dragleave', () => dropLabel.classList.remove('border-gray-500'));
        dropLabel.addEventListener('drop', e => {
            e.preventDefault();
            dropLabel.classList.remove('border-gray-500');
            if (e.dataTransfer.files.length) {
                imgInput.files = e.dataTransfer.files;
                showPreviews(e.dataTransfer.files);
            }
        });
    }

    // ── Brand filter by category ────────────────────────────────────────
    const catSel   = document.getElementById('category_id');
    const brandSel = document.getElementById('brand_id');

    if (catSel && brandSel) {
        const allOpts    = Array.from(brandSel.options);
        const savedBrand = brandSel.dataset.selected || '';

        function rebuildBrands() {
            const catId = catSel.value;
            brandSel.innerHTML = '';

            const placeholder = allOpts.find(o => !o.value);
            if (placeholder) brandSel.appendChild(placeholder.cloneNode(true));

            allOpts.forEach(o => {
                if (!o.value) return;
                const oCat = o.dataset.categoryId || '';
                if (!catId || oCat === catId) brandSel.appendChild(o.cloneNode(true));
            });

            if (savedBrand) {
                Array.from(brandSel.options).forEach(o => { if (o.value === savedBrand) o.selected = true; });
            }
        }

        rebuildBrands();
        catSel.addEventListener('change', () => { brandSel.dataset.selected = ''; rebuildBrands(); });
    }

    // ── Form submission fix ─────────────────────────────────────────────
    // Ensure hidden Alpine.js fields (like standard_price when type is express_only)
    // don't block form submission by removing their required attribute.
    document.getElementById('edit-product-form')?.addEventListener('submit', function() {
        this.querySelectorAll('[x-show]').forEach(container => {
            // Alpine uses display:none for hidden x-show elements
            const isHidden = window.getComputedStyle(container).display === 'none';
            if (isHidden) {
                container.querySelectorAll('[required]').forEach(inp => {
                    inp.removeAttribute('required');
                });
            }
        });
    });
</script>
@endsection
