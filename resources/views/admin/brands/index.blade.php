@extends('layouts.dashboard')

@section('title', 'Manage Brands')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-industry text-blue-600 mr-3"></i>
                Brand Management
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Organize products by brand and sub-brand within each category.
            </p>
        </div>

        <a href="{{ route('admin.brands.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <i class="fas fa-plus mr-2"></i>
            Add Brand
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET"
              action="{{ route('admin.brands.index') }}"
              class="flex flex-col sm:flex-row gap-4">

            <div class="flex-1">
                <label for="search" class="sr-only">Search brands</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search', $search ?? '') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search by brand name..."
                    >
                </div>
            </div>

            <button type="submit"
                    class="inline-flex items-center px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                <i class="fas fa-search mr-2"></i>
                Search
            </button>

            @if(request('search'))
                <a href="{{ route('admin.brands.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ======================= --}}
    {{-- Brands exist state     --}}
    {{-- ======================= --}}
    @if($brands->count())

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            {{-- Desktop table header --}}
            <div class="hidden lg:block">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <div class="grid grid-cols-6 gap-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="text-center w-10">#</div>
                        <div>Brand</div>
                        <div>Category</div>
                        <div>Parent Brand</div>
                        <div class="text-center">Created</div>
                        <div class="text-center">Updated</div>
                    </div>
                </div>
            </div>

            {{-- Rows --}}
            <div class="divide-y divide-gray-200">
                @foreach ($brands as $brand)
                    {{-- Desktop row --}}
                    <div class="hidden lg:block px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="grid grid-cols-6 gap-4 items-center">

                            {{-- # --}}
                            <div class="text-center text-sm text-gray-900 font-medium">
                                {{ $rowStart + $loop->iteration }}
                            </div>

                            {{-- Brand --}}
                            <div>
                                <div class="text-sm font-medium text-gray-900 truncate flex items-center">
                                    <i class="fas fa-industry text-gray-400 mr-2"></i>
                                    <span>{{ $brand->name }}</span>
                                </div>
                                <div class="text-xs text-gray-500 space-x-2">
                                    <span>ID: {{ $brand->id }}</span>
                                    <span>• Slug: {{ $brand->slug }}</span>
                                </div>
                            </div>

                            {{-- Category --}}
                            <div class="text-sm text-gray-700">
                                @if($brand->category)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        <i class="fas fa-tags mr-1"></i>
                                        {{ $brand->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">No category</span>
                                @endif
                            </div>

                            {{-- Parent Brand --}}
                            <div class="text-sm text-gray-700">
                                @if($brand->parent)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700">
                                        <i class="fas fa-sitemap mr-1"></i>
                                        {{ $brand->parent->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Top-level brand</span>
                                @endif
                            </div>

                            {{-- Created --}}
                            <div class="text-center text-sm text-gray-700">
                                {{ $brand->created_at ? $brand->created_at->format('d M Y') : '—' }}
                            </div>

                            {{-- Updated + actions --}}
                            <div class="flex items-center justify-center space-x-3">
                                <span class="text-xs text-gray-500 mr-3">
                                    {{ $brand->updated_at ? $brand->updated_at->diffForHumans() : '—' }}
                                </span>

                                <a href="{{ route('admin.brands.edit', $brand) }}"
                                   class="inline-flex items-center p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors"
                                   title="Edit Brand">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>

                                <form action="{{ route('admin.brands.destroy', $brand) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Delete this brand? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                                            title="Delete Brand">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                    {{-- Mobile card --}}
                    <div class="lg:hidden p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-md bg-gray-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-industry text-gray-500"></i>
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900 truncate flex items-center">
                                            <span class="mr-2">{{ $brand->name }}</span>
                                            <span class="text-[11px] text-gray-500 font-normal">#{{ $brand->id }}</span>
                                        </h3>

                                        <div class="mt-1 text-xs text-gray-500 space-y-1">
                                            <div>
                                                Category:
                                                <span class="font-medium text-gray-800">
                                                    {{ $brand->category->name ?? '—' }}
                                                </span>
                                            </div>

                                            <div>
                                                Parent:
                                                <span class="font-medium text-gray-800">
                                                    {{ $brand->parent->name ?? 'Top-level' }}
                                                </span>
                                            </div>

                                            <div>
                                                Created:
                                                <span class="font-medium text-gray-800">
                                                    {{ $brand->created_at ? $brand->created_at->format('d M Y') : '—' }}
                                                </span>
                                            </div>

                                            <div>
                                                Updated:
                                                <span class="font-medium text-gray-800">
                                                    {{ $brand->updated_at ? $brand->updated_at->diffForHumans() : '—' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col space-y-2 ml-4">
                                <a href="{{ route('admin.brands.edit', $brand) }}"
                                   class="p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors"
                                   title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>

                                <form action="{{ route('admin.brands.destroy', $brand) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Delete this brand? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                                            title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div> {{-- /divide-y --}}
        </div> {{-- /table wrapper --}}

        {{-- Pagination --}}
        @if(
            $brands instanceof \Illuminate\Contracts\Pagination\Paginator &&
            $brands->hasPages()
        )
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between">

                    {{-- Mobile prev/next --}}
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($brands->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                Previous
                            </span>
                        @else
                            <a href="{{ $brands->previousPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @if($brands->hasMorePages())
                            <a href="{{ $brands->nextPageUrl() }}"
                               class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                Next
                            </span>
                        @endif
                    </div>

                    {{-- Desktop pagination --}}
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ $brands->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $brands->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $brands->total() }}</span>
                                brands
                            </p>
                        </div>

                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{-- Prev --}}
                                @if($brands->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-default">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $brands->previousPageUrl() }}"
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                {{-- Page numbers --}}
                                @foreach($brands->getUrlRange(1, $brands->lastPage()) as $page => $url)
                                    @if($page == $brands->currentPage())
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}"
                                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                {{-- Next --}}
                                @if($brands->hasMorePages())
                                    <a href="{{ $brands->nextPageUrl() }}"
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-default">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    {{-- ======================= --}}
    {{-- No brands state        --}}
    {{-- ======================= --}}
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-industry text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No brands found</h3>
            <p class="text-gray-600 mb-6">
                @if(request('search'))
                    No brands match your search. Try a different name.
                @else
                    Start by creating a brand (e.g. "Nivea", "L'Oréal", "Local brand", etc.).
                @endif
            </p>

            @if(request('search'))
                <a href="{{ route('admin.brands.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium mr-3">
                    <i class="fas fa-times mr-2"></i>
                    Clear Search
                </a>
            @endif

            <a href="{{ route('admin.brands.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-plus mr-2"></i>
                Add First Brand
            </a>
        </div>
    @endif {{-- closes @if($brands->count()) --}}

</div>
@endsection
