@extends('layouts.dashboard')

@section('title', 'Add Category')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
            Add New Category
        </h1>
        <p class="mt-1 text-sm text-gray-600">Create a category to group products (ex: "Lashes", "Skincare").</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Category Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       required
                       class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g. Lashes, Makeup, Hair, Nails...">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
