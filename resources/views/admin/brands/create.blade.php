@extends('layouts.dashboard')

@section('title', 'Add Brand')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-industry text-blue-600 mr-3"></i>
                Add New Brand
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Create a brand and optionally attach it as a sub-brand to another brand in the same category.
            </p>
        </div>

        <a href="{{ route('admin.brands.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Brands
        </a>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="ml-3 text-sm">
                    <p class="font-semibold mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.brands.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Brand Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g. Nivea, MAC, Fenty Beauty"
                    required
                >
                <p class="mt-1 text-xs text-gray-500">
                    The slug (URL key) will be generated automatically from this name.
                </p>
            </div>

            {{-- Category --}}
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">
                    Category <span class="text-red-500">*</span>
                </label>
                <select
                    name="category_id"
                    id="category_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">Select category…</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ (int) old('category_id') === $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    The brand will belong to this product category.
                </p>
            </div>

            {{-- Parent brand (for sub-brand) --}}
            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700">
                    Parent Brand (optional)
                </label>
                <select
                    name="parent_id"
                    id="parent_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">None — top-level brand</option>

                    @php
                        $grouped = $brands->groupBy(fn($b) => optional($b->category)->name ?? 'Uncategorized');
                    @endphp

                    @foreach($grouped as $categoryName => $groupBrands)
                        <optgroup label="{{ $categoryName }}">
                            @foreach($groupBrands as $parent)
                                <option value="{{ $parent->id }}"
                                    {{ (int) old('parent_id') === $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Use this if this brand is a sub-brand of an existing top-level brand in the same category.
                </p>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin.brands.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Save Brand
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
