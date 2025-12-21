@extends('layouts.dashboard')

@section('title', 'Manage Categories')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-tags text-blue-600 mr-3"></i>
                Category Management
            </h1>
            <p class="mt-1 text-sm text-gray-600">Organize products by category</p>
        </div>

        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <i class="fas fa-plus mr-2"></i>
            Add Category
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET"
              action="{{ route('admin.categories.index') }}"
              class="flex flex-col sm:flex-row gap-4">

            <div class="flex-1">
                <label for="search" class="sr-only">Search categories</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search by category name..."
                    >
                </div>
            </div>

            <button type="submit"
                    class="inline-flex items-center px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                <i class="fas fa-search mr-2"></i>
                Search
            </button>

            @if(request('search'))
                <a href="{{ route('admin.categories.index') }}"
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
    {{-- Categories exist state --}}
    {{-- ======================= --}}
    @if($categories->count())

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            {{-- Desktop table header --}}
            <div class="hidden lg:block">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <div class="grid grid-cols-5 gap-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="text-center w-10">#</div>
                        <div>Name</div>
                        <div class="text-center">Created</div>
                        <div class="text-center">Updated</div>
                        <div class="text-center">Actions</div>
                    </div>
                </div>
            </div>

            {{-- Rows --}}
            <div class="divide-y divide-gray-200">
                @foreach ($categories as $category)
                    {{-- Desktop row --}}
                    <div class="hidden lg:block px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="grid grid-cols-5 gap-4 items-center">

                            {{-- # --}}
                            <div class="text-center text-sm text-gray-900 font-medium">
                                {{ $rowStart + $loop->iteration }}
                            </div>

                            {{-- Name --}}
                            <div>
                                <div class="text-sm font-medium text-gray-900 truncate flex items-center">
                                    <i class="fas fa-tag text-gray-400 mr-2"></i>
                                    <span>{{ $category->name }}</span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    ID: {{ $category->id }}
                                </div>
                            </div>

                            {{-- Created --}}
                            <div class="text-center text-sm text-gray-700">
                                {{ $category->created_at ? $category->created_at->format('d M Y') : '—' }}
                            </div>

                            {{-- Updated --}}
                            <div class="text-center text-sm text-gray-700">
                                {{ $category->updated_at ? $category->updated_at->diffForHumans() : '—' }}
                            </div>

                            {{-- Actions --}}
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                   class="inline-flex items-center p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors"
                                   title="Edit Category">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                                            title="Delete Category">
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
                                        <i class="fas fa-tag text-gray-500"></i>
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900 truncate flex items-center">
                                            <span class="mr-2">{{ $category->name }}</span>
                                            <span class="text-[11px] text-gray-500 font-normal">#{{ $category->id }}</span>
                                        </h3>

                                        <div class="mt-1 text-xs text-gray-500 space-y-1">
                                            <div>
                                                Created:
                                                <span class="font-medium text-gray-800">
                                                    {{ $category->created_at ? $category->created_at->format('d M Y') : '—' }}
                                                </span>
                                            </div>

                                            <div>
                                                Updated:
                                                <span class="font-medium text-gray-800">
                                                    {{ $category->updated_at ? $category->updated_at->diffForHumans() : '—' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-col space-y-2 ml-4">
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                   class="p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors"
                                   title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Delete this category?');">
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

        {{-- Pagination (only if this is really a paginator) --}}
        @if(
            $categories instanceof \Illuminate\Contracts\Pagination\Paginator &&
            $categories->hasPages()
        )
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between">

                    {{-- Mobile prev/next --}}
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($categories->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                Previous
                            </span>
                        @else
                            <a href="{{ $categories->previousPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @if($categories->hasMorePages())
                            <a href="{{ $categories->nextPageUrl() }}"
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
                                <span class="font-medium">{{ $categories->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $categories->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $categories->total() }}</span>
                                categories
                            </p>
                        </div>

                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{-- Prev --}}
                                @if($categories->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-default">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $categories->previousPageUrl() }}"
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                {{-- Page numbers --}}
                                @foreach($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                                    @if($page == $categories->currentPage())
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
                                @if($categories->hasMorePages())
                                    <a href="{{ $categories->nextPageUrl() }}"
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
    {{-- No categories state     --}}
    {{-- ======================= --}}
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tags text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
            <p class="text-gray-600 mb-6">
                @if(request('search'))
                    No categories match your search. Try a different name.
                @else
                    Start by creating a category (e.g. "Lashes", "Makeup", "Hair", etc.).
                @endif
            </p>

            @if(request('search'))
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium mr-3">
                    <i class="fas fa-times mr-2"></i>
                    Clear Search
                </a>
            @endif

            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-plus mr-2"></i>
                Add First Category
            </a>
        </div>
    @endif {{-- closes @if($categories->count()) --}}

</div>
@endsection
