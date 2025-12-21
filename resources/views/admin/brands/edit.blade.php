@extends('layouts.dashboard')

@section('title', 'Edit Brand')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-industry text-blue-600 mr-3"></i>
                Edit Brand: {{ $brand->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Update brand details, category and parent brand (sub-brand structure).
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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        {{-- UPDATE FORM --}}
        <form id="brand-update-form"
              action="{{ route('admin.brands.update', $brand) }}"
              method="POST"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Brand Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $brand->name) }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                <p class="mt-1 text-xs text-gray-500">
                    Slug: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $brand->slug }}</span>
                    (auto-generated; may update if you change the name).
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
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ (int) old('category_id', $brand->category_id) === $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    If you choose a parent brand, this brand will automatically use the parent’s category when saving.
                </p>
            </div>

            {{-- Parent brand --}}
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
                        $grouped = $possibleParents->groupBy(fn($b) => optional($b->category)->name ?? 'Uncategorized');
                    @endphp

                    @foreach($grouped as $categoryName => $groupBrands)
                        <optgroup label="{{ $categoryName }}">
                            @foreach($groupBrands as $parent)
                                <option value="{{ $parent->id }}"
                                    {{ (int) old('parent_id', $brand->parent_id) === $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Use this if this brand is a sub-brand of an existing top-level brand.
                    The child will inherit the parent’s category on save.
                </p>
            </div>

            {{-- Info about children --}}
            @if($brand->children && $brand->children->count())
                <div class="border border-yellow-100 bg-yellow-50 text-yellow-800 text-xs rounded-lg px-3 py-3">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                        <div>
                            <p class="font-semibold mb-1">Sub-brands under this brand</p>
                            <p class="mb-1">
                                This brand currently has
                                <span class="font-semibold">{{ $brand->children->count() }}</span>
                                sub-brand(s):
                            </p>
                            <ul class="list-disc list-inside">
                                @foreach($brand->children as $child)
                                    <li>{{ $child->name }}</li>
                                @endforeach
                            </ul>
                            <p class="mt-2">
                                If you delete this brand, its sub-brands will be detached (their parent will be set to "None").
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </form>

        {{-- ACTION BUTTONS (separate from the form to avoid nesting issues) --}}
        <div class="pt-4 flex items-center justify-between gap-3">
            <a href="{{ route('admin.brands.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>

            <div class="flex items-center gap-3">
                {{-- Submit update form --}}
                <button type="submit"
                        form="brand-update-form"
                        class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Update Brand
                </button>

                {{-- DELETE FORM (separate form, NOT nested) --}}
                <form action="{{ route('admin.brands.destroy', $brand) }}"
                      method="POST"
                      onsubmit="return confirm('Delete this brand? This cannot be undone. Sub-brands will be detached.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-red-200 text-sm font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
