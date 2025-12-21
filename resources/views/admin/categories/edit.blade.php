@extends('layouts.dashboard')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-edit text-yellow-600 mr-3"></i>
            Edit Category
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Update the category name.
        </p>
    </div>

    {{-- Flash success (only shows if you redirect back here after update with ->with('success', ...)) --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Update Category Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.categories.update', $category->id) }}"
              method="POST"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Category Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Category Name <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $category->name) }}"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Category name"
                >

                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions: Cancel + Save --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200">

                <div class="flex items-center justify-end gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.categories.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Update Category
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Danger Zone: Delete --}}
    <div class="bg-white rounded-lg shadow-sm border border-red-200 mt-8 p-6">
        <div class="flex items-start justify-between flex-col sm:flex-row sm:items-center">

            <form action="{{ route('admin.categories.destroy', $category->id) }}"
                  method="POST"
                  onsubmit="return confirm('Delete this category permanently?');">
                @csrf
                @method('DELETE')

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors font-medium">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Category
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
