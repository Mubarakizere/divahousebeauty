@extends('layouts.dashboard')

@section('title', 'Create Banner')

@section('content')
<div class="max-w-4xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.banners.index') }}" 
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create New Banner</h1>
        </div>
        <p class="text-sm text-gray-500 ml-11">Add a promotional banner to your homepage</p>
    </div>

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Main Details Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Banner Details</h2>
            
            <div class="space-y-4">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Banner Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror" 
                           value="{{ old('name') }}"
                           placeholder="e.g., Summer Sale 2024">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Internal reference name (not shown to customers)</p>
                </div>

                {{-- Title & Subtitle --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Banner Title
                        </label>
                        <input type="text" name="title" id="title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror" 
                               value="{{ old('title') }}"
                               placeholder="Headline text on banner">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subtitle" class="block text-sm font-medium text-gray-700 mb-1">
                            Banner Subtitle
                        </label>
                        <input type="text" name="subtitle" id="subtitle"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('subtitle') border-red-500 @enderror" 
                               value="{{ old('subtitle') }}"
                               placeholder="Description text">
                        @error('subtitle')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Position & Order --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                            Position <span class="text-red-500">*</span>
                        </label>
                        <select name="position" id="position" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('position') border-red-500 @enderror">
                            @foreach($positions as $value => $label)
                                <option value="{{ $value }}" {{ old('position') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-1">
                            Display Order
                        </label>
                        <input type="number" name="order" id="order" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('order') border-red-500 @enderror" 
                               value="{{ old('order', 0) }}">
                        @error('order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                    </div>
                </div>

                {{-- Active Status --}}
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" 
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                        Active (show immediately)
                    </label>
                </div>
            </div>
        </div>

        {{-- Image Upload Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Banner Image</h2>
            
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Image <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="image" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, WebP (MAX. 30MB)</p>
                            <p class="text-xs text-gray-400 mt-1">Recommended: 1920x600px for hero banners</p>
                        </div>
                        <input id="image" name="image" type="file" class="hidden" accept="image/*" required />
                    </label>
                </div>
                @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Link Settings Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Link Settings</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="link" class="block text-sm font-medium text-gray-700 mb-1">
                        Link URL
                    </label>
                    <input type="url" name="link" id="link"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('link') border-red-500 @enderror" 
                           value="{{ old('link') }}"
                           placeholder="https://example.com/sale">
                    @error('link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Where to redirect when banner is clicked</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="link_text" class="block text-sm font-medium text-gray-700 mb-1">
                            Button Text
                        </label>
                        <input type="text" name="link_text" id="link_text" maxlength="50"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('link_text') border-red-500 @enderror" 
                               value="{{ old('link_text') }}"
                               placeholder="Shop Now">
                        @error('link_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="target" class="block text-sm font-medium text-gray-700 mb-1">
                            Link Target
                        </label>
                        <select name="target" id="target"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="_self" {{ old('target') == '_self' ? 'selected' : '' }}>Same Tab</option>
                            <option value="_blank" {{ old('target') == '_blank' ? 'selected' : '' }}>New Tab</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Schedule Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="far fa-calendar text-indigo-600 mr-2"></i>Schedule (Optional)
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Start Date & Time
                    </label>
                    <input type="datetime-local" name="start_date" id="start_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('start_date') border-red-500 @enderror" 
                           value="{{ old('start_date') }}">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">When to start showing</p>
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                        End Date & Time
                    </label>
                    <input type="datetime-local" name="end_date" id="end_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('end_date') border-red-500 @enderror" 
                           value="{{ old('end_date') }}">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">When to stop showing</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3">
            <button type="submit" 
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i>Create Banner
            </button>
            <a href="{{ route('admin.banners.index') }}" 
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Create preview container if it doesn't exist
            let previewContainer = document.getElementById('image-preview-container');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.id = 'image-preview-container';
                previewContainer.className = 'mt-4';
                document.querySelector('label[for="image"]').parentElement.appendChild(previewContainer);
            }
            
            previewContainer.innerHTML = `
                <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                <div class="relative inline-block rounded-lg overflow-hidden border border-gray-200">
                    <img src="${e.target.result}" alt="Preview" class="max-w-full h-auto max-h-64 object-cover">
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection
