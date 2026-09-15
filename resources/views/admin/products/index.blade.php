@extends('layouts.dashboard')

@section('title', 'Products')

@section('content')
<div x-data="{
    del: { show: false, name: '', action: '' },
    openDelete(action, name) { this.del.action = action; this.del.name = name; this.del.show = true; },
    closeDelete() { this.del.show = false; }
}" x-on:keydown.window.escape="closeDelete()">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Products</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ number_format($products->total()) }} total products</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.import') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-700 border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 transition-colors">
                <i class="fas fa-file-excel text-xs text-green-600"></i> Import
            </a>
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-white bg-gray-900 rounded-md px-3 py-1.5 hover:bg-gray-700 transition-colors">
                <i class="fas fa-plus text-xs"></i> Add Product
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-md mb-5">
            <i class="fas fa-check-circle text-green-500"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filters --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-5" x-data="{ showFilters: {{ request()->hasAny(['shipping_type','category_id','brand_id','stock_status','sort']) ? 'true' : 'false' }} }">
        <form method="GET" action="{{ route('admin.products.index') }}">
            {{-- Top row: Search + Toggle --}}
            <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
                <div class="flex-1">
                    <label for="search" class="block text-sm text-gray-600 mb-1">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                        <input type="text" name="search" id="search"
                               value="{{ request('search') }}"
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400"
                               placeholder="Search by name, ID, brand, category...">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="showFilters = !showFilters"
                            class="inline-flex items-center gap-2 text-sm text-gray-600 border border-gray-300 rounded-md px-3 py-2 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-filter text-xs"></i>
                        <span x-text="showFilters ? 'Hide Filters' : 'Filters'"></span>
                        @if(request()->hasAny(['shipping_type','category_id','brand_id','stock_status','sort']))
                            <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-gray-900 rounded-full">
                                {{ collect(['shipping_type','category_id','brand_id','stock_status'])->filter(fn($k) => request($k))->count() + (request('sort') && request('sort') !== 'latest' ? 1 : 0) }}
                            </span>
                        @endif
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 text-sm font-medium text-white bg-gray-900 rounded-md px-4 py-2 hover:bg-gray-700 transition-colors">
                        Search
                    </button>
                    @if(request()->hasAny(['search','shipping_type','category_id','brand_id','stock_status','sort']))
                        <a href="{{ route('admin.products.index') }}"
                           class="inline-flex items-center gap-2 text-sm text-gray-600 border border-gray-300 rounded-md px-3 py-2 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times text-xs"></i> Clear
                        </a>
                    @endif
                </div>
            </div>

            {{-- Filter row (collapsible) --}}
            <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-gray-100">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    {{-- Shipping Type --}}
                    <div>
                        <label for="shipping_type" class="block text-xs font-medium text-gray-500 mb-1">Price Type</label>
                        <select name="shipping_type" id="shipping_type"
                                class="w-full text-sm border border-gray-300 rounded-md px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <option value="">All Types</option>
                            <option value="standard_only" {{ request('shipping_type') === 'standard_only' ? 'selected' : '' }}>Standard Only</option>
                            <option value="express_only" {{ request('shipping_type') === 'express_only' ? 'selected' : '' }}>Express Only</option>
                            <option value="both" {{ request('shipping_type') === 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                        <select name="category_id" id="category_id"
                                class="w-full text-sm border border-gray-300 rounded-md px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (int)request('category_id') === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Brand --}}
                    <div>
                        <label for="brand_id" class="block text-xs font-medium text-gray-500 mb-1">Brand</label>
                        <select name="brand_id" id="brand_id"
                                class="w-full text-sm border border-gray-300 rounded-md px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ (int)request('brand_id') === $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stock Status --}}
                    <div>
                        <label for="stock_status" class="block text-xs font-medium text-gray-500 mb-1">Stock</label>
                        <select name="stock_status" id="stock_status"
                                class="w-full text-sm border border-gray-300 rounded-md px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <option value="">All</option>
                            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock (≤5)</option>
                            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label for="sort" class="block text-xs font-medium text-gray-500 mb-1">Sort By</label>
                        <select name="sort" id="sort"
                                class="w-full text-sm border border-gray-300 rounded-md px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A–Z</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z–A</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price Low → High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price High → Low</option>
                            <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Stock Low → High</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Products table --}}
    @if($products->count())
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">

            {{-- Desktop table --}}
            <div class="hidden lg:block">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/60">
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 w-10">#</th>
                            <th class="text-left py-3 px-3 text-xs font-medium text-gray-500 w-14"></th>
                            <th class="text-left py-3 px-3 text-xs font-medium text-gray-500">Product</th>
                            <th class="text-left py-3 px-3 text-xs font-medium text-gray-500">Price</th>
                            <th class="text-center py-3 px-3 text-xs font-medium text-gray-500">Stock</th>
                            <th class="text-left py-3 px-3 text-xs font-medium text-gray-500">Category</th>
                            <th class="text-left py-3 px-3 text-xs font-medium text-gray-500">Brand</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 w-24"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($products as $index => $product)
                            @php
                                $imgs = is_array($product->images) ? $product->images : (json_decode($product->images ?? '[]', true) ?: []);
                                $image = $imgs[0] ?? 'default.jpg';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 px-4 text-gray-400 text-xs">{{ $products->firstItem() + $index }}</td>
                                <td class="py-3 px-3">
                                    <img src="{{ asset('storage/' . $image) }}"
                                         alt="{{ $product->name }}"
                                         class="h-10 w-10 rounded object-cover bg-gray-100 border border-gray-200">
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-medium text-gray-900 truncate max-w-[200px]">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-400">ID: {{ $product->id }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    @if($product->shipping_type === 'both')
                                        <div class="text-xs text-gray-700">{{ number_format($product->express_price ?? 0) }} <span class="text-gray-400">exp</span></div>
                                        <div class="text-xs text-gray-500">{{ number_format($product->standard_price ?? 0) }} <span class="text-gray-400">std</span></div>
                                    @elseif($product->shipping_type === 'standard_only')
                                        <div class="text-sm text-gray-900">{{ number_format($product->standard_price ?? 0) }}</div>
                                        <div class="text-xs text-gray-400">standard</div>
                                    @else
                                        <div class="text-sm text-gray-900">{{ number_format($product->express_price ?? 0) }}</div>
                                        <div class="text-xs text-gray-400">express</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if((int)$product->stock <= 0)
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded bg-red-50 text-red-700">{{ $product->stock }}</span>
                                    @elseif((int)$product->stock <= 5)
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded bg-amber-50 text-amber-700">{{ $product->stock }}</span>
                                    @else
                                        <span class="text-sm text-gray-700">{{ $product->stock }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-600 truncate max-w-[120px]">{{ $product->category->name ?? '—' }}</td>
                                <td class="py-3 px-3 text-sm text-gray-600 truncate max-w-[120px]">{{ $product->brand->name ?? '—' }}</td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="p-1.5 text-gray-400 hover:text-gray-700 transition-colors" title="Edit">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        <button type="button"
                                                @click="openDelete('{{ route('admin.products.destroy', $product) }}','{{ addslashes($product->name) }}')"
                                                class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile list --}}
            <div class="lg:hidden divide-y divide-gray-100">
                @foreach ($products as $index => $product)
                    @php
                        $imgs = is_array($product->images) ? $product->images : (json_decode($product->images ?? '[]', true) ?: []);
                        $image = $imgs[0] ?? 'default.jpg';
                    @endphp
                    <div class="p-4 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('storage/' . $image) }}"
                                 alt="{{ $product->name }}"
                                 class="h-14 w-14 rounded object-cover bg-gray-100 border border-gray-200 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h3>
                                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                            <span>
                                                @if($product->shipping_type === 'both')
                                                    {{ number_format($product->express_price ?? 0) }} / {{ number_format($product->standard_price ?? 0) }} RWF
                                                @elseif($product->shipping_type === 'standard_only')
                                                    {{ number_format($product->standard_price ?? 0) }} RWF
                                                @else
                                                    {{ number_format($product->express_price ?? 0) }} RWF
                                                @endif
                                            </span>
                                            <span>Stock: 
                                                @if((int)$product->stock <= 0)
                                                    <span class="text-red-600 font-medium">{{ $product->stock }}</span>
                                                @elseif((int)$product->stock <= 5)
                                                    <span class="text-amber-600 font-medium">{{ $product->stock }}</span>
                                                @else
                                                    {{ $product->stock }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">
                                            {{ $product->category->name ?? '—' }}
                                            @if($product->brand) · {{ $product->brand->name }} @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="p-1.5 text-gray-400 hover:text-gray-700 transition-colors">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        <button type="button"
                                                @click="openDelete('{{ route('admin.products.destroy', $product) }}','{{ addslashes($product->name) }}')"
                                                class="p-1.5 text-gray-400 hover:text-red-600 transition-colors">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            @php
                $current = $products->currentPage();
                $last = $products->lastPage();
                // Build a smart page window: 1, ..., current-1, current, current+1, ..., last
                $pages = collect();
                $pages->push(1);
                for ($i = max(2, $current - 1); $i <= min($last - 1, $current + 1); $i++) {
                    $pages->push($i);
                }
                if ($last > 1) $pages->push($last);
                $pages = $pages->unique()->sort()->values();
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-5">
                <p class="text-sm text-gray-500">
                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}
                </p>
                <nav class="flex flex-wrap items-center gap-1">
                    @if($products->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-400 rounded border border-gray-200 cursor-default">Prev</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}"
                           class="px-3 py-1.5 text-xs text-gray-700 rounded border border-gray-300 hover:bg-gray-50 transition-colors">Prev</a>
                    @endif

                    @foreach($pages as $idx => $page)
                        {{-- Ellipsis if gap between this page and previous --}}
                        @if($idx > 0 && $page - $pages[$idx - 1] > 1)
                            <span class="px-2 py-1.5 text-xs text-gray-400">…</span>
                        @endif

                        @if($page == $current)
                            <span class="px-3 py-1.5 text-xs font-medium text-white bg-gray-900 rounded">{{ $page }}</span>
                        @else
                            <a href="{{ $products->url($page) }}"
                               class="px-3 py-1.5 text-xs text-gray-700 rounded border border-gray-300 hover:bg-gray-50 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}"
                           class="px-3 py-1.5 text-xs text-gray-700 rounded border border-gray-300 hover:bg-gray-50 transition-colors">Next</a>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-400 rounded border border-gray-200 cursor-default">Next</span>
                    @endif
                </nav>
            </div>
        @endif

    @else
        {{-- Empty state --}}
        <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
            <div class="text-gray-300 mb-4">
                <i class="fas fa-box-open text-4xl"></i>
            </div>
            <h3 class="text-base font-medium text-gray-900 mb-1">No products found</h3>
            <p class="text-sm text-gray-500 mb-6">
                @if(request()->hasAny(['search','shipping_type','category_id','brand_id','stock_status']))
                    No products match your current filters. Try adjusting your search or filters.
                @else
                    Get started by adding your first product.
                @endif
            </p>
            <div class="flex justify-center gap-3">
                @if(request()->hasAny(['search','shipping_type','category_id','brand_id','stock_status','sort']))
                    <a href="{{ route('admin.products.index') }}"
                       class="text-sm text-gray-600 border border-gray-300 rounded-md px-4 py-2 hover:bg-gray-50 transition-colors">
                        Clear Filters
                    </a>
                @endif
                <a href="{{ route('admin.products.create') }}"
                   class="text-sm font-medium text-white bg-gray-900 rounded-md px-4 py-2 hover:bg-gray-700 transition-colors">
                    Add Product
                </a>
            </div>
        </div>
    @endif

    {{-- Delete modal --}}
    <div x-cloak x-show="del.show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/30" @click="closeDelete()"></div>
        <div class="relative w-full max-w-sm rounded-lg bg-white shadow-lg border border-gray-200 p-6">
            <h3 class="text-base font-medium text-gray-900 mb-2">Delete product?</h3>
            <p class="text-sm text-gray-500 mb-5">
                <span class="font-medium text-gray-700" x-text="del.name"></span> will be permanently removed.
            </p>
            <form :action="del.action" method="POST" x-ref="delForm" class="hidden">
                @csrf @method('DELETE')
            </form>
            <div class="flex justify-end gap-3">
                <button type="button" @click="closeDelete()"
                        class="text-sm text-gray-600 border border-gray-300 rounded-md px-4 py-2 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" @click="$refs.delForm.submit()"
                        class="text-sm font-medium text-white bg-red-600 rounded-md px-4 py-2 hover:bg-red-700 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
