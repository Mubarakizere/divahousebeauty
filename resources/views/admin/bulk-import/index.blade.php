@extends('layouts.dashboard')

@section('title', 'Bulk Image Import')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">Bulk Image Import</h1>
            <p class="text-gray-600 mt-1">Upload product images and extract data using OCR</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Products
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-start">
            <i class="fas fa-check-circle mr-2 mt-0.5"></i>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-start">
            <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Upload Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-images text-blue-500 mr-2"></i>
                Upload Product Images
            </h2>

            <form action="{{ route('admin.bulk-import.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf

                <div class="mb-6">
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Images <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-500 transition-colors cursor-pointer bg-gray-50" id="dropZone">
                        <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600 mb-1">Drag & drop images here or click to browse</p>
                        <p class="text-xs text-gray-500">JPEG, PNG, WebP • Max 200 images • 10MB each</p>
                    </div>
                    
                    <div id="fileList" class="mt-4 hidden">
                        <p class="text-sm font-medium text-gray-700 mb-2">Selected: <span id="fileCount">0</span> images</p>
                        <div id="thumbnails" class="grid grid-cols-6 gap-2 max-h-48 overflow-y-auto"></div>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-amber-500 mt-0.5 mr-2"></i>
                        <div class="text-sm text-amber-800">
                            <p class="font-medium mb-1">Image Text Format Required:</p>
                            <code class="bg-amber-100 px-2 py-1 rounded">Product Name @ Price</code>
                            <p class="mt-1 text-xs">Example: "Multipurpose makeup brush @ 150"</p>
                        </div>
                    </div>
                </div>

                <button type="submit" id="uploadBtn" disabled class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-upload mr-2"></i>
                    Upload & Continue to Crop
                </button>
            </form>
        </div>

        {{-- Instructions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-list-ol text-purple-500 mr-2"></i>
                How It Works
            </h2>

            <ol class="space-y-4">
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold mr-3">1</span>
                    <div>
                        <p class="font-medium text-gray-900">Upload Images</p>
                        <p class="text-sm text-gray-600">Select up to 200 product images at once</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold mr-3">2</span>
                    <div>
                        <p class="font-medium text-gray-900">Crop Product Area</p>
                        <p class="text-sm text-gray-600">Manually select the product area in each image</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold mr-3">3</span>
                    <div>
                        <p class="font-medium text-gray-900">OCR Processing</p>
                        <p class="text-sm text-gray-600">Text is extracted automatically from images</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold mr-3">4</span>
                    <div>
                        <p class="font-medium text-gray-900">Review & Edit</p>
                        <p class="text-sm text-gray-600">Review parsed data and make corrections</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold mr-3">5</span>
                    <div>
                        <p class="font-medium text-gray-900">Insert Products</p>
                        <p class="text-sm text-gray-600">Confirm and add all products to database</p>
                    </div>
                </li>
            </ol>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-2">Price Conversion Formula</h4>
                <p class="text-sm text-gray-600">
                    <code class="bg-gray-200 px-2 py-1 rounded">Detected Price × 2 × 10 = RWF Price</code>
                </p>
                <p class="text-xs text-gray-500 mt-1">Example: 150 → 3,000 RWF</p>
            </div>
        </div>
    </div>

    {{-- Recent Batches --}}
    @if($batches->count() > 0)
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-history text-gray-500 mr-2"></i>
            Recent Import Batches
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase border-b border-gray-200">
                        <th class="pb-3 pr-4">ID</th>
                        <th class="pb-3 pr-4">Status</th>
                        <th class="pb-3 pr-4">Progress</th>
                        <th class="pb-3 pr-4">Images</th>
                        <th class="pb-3 pr-4">Created</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($batches as $batch)
                    <tr>
                        <td class="py-3 pr-4 font-medium">#{{ $batch->id }}</td>
                        <td class="py-3 pr-4">
                            @if($batch->status === 'completed')
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Completed</span>
                            @elseif($batch->status === 'processing')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Processing</span>
                            @elseif($batch->status === 'failed')
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Failed</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Pending</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $batch->progress_percentage }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $batch->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td class="py-3 pr-4 text-sm">{{ $batch->processed_images }}/{{ $batch->total_images }}</td>
                        <td class="py-3 pr-4 text-sm text-gray-500">{{ $batch->created_at->diffForHumans() }}</td>
                        <td class="py-3">
                            <div class="flex space-x-2">
                                @if($batch->status === 'pending')
                                    <a href="{{ route('admin.bulk-import.crop', $batch) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-crop-alt"></i>
                                    </a>
                                @endif
                                @if(in_array($batch->status, ['processing', 'completed']))
                                    <a href="{{ route('admin.bulk-import.preview', $batch) }}" class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.bulk-import.destroy', $batch) }}" method="POST" class="inline" onsubmit="return confirm('Delete this batch?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('images');
    const fileList = document.getElementById('fileList');
    const fileCount = document.getElementById('fileCount');
    const thumbnails = document.getElementById('thumbnails');
    const uploadBtn = document.getElementById('uploadBtn');

    // Click to select files
    dropZone.addEventListener('click', () => fileInput.click());

    // Drag and drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        
        const files = e.dataTransfer.files;
        handleFiles(files);
    });

    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        if (files.length > 200) {
            alert('Maximum 200 images allowed');
            return;
        }

        fileList.classList.remove('hidden');
        fileCount.textContent = files.length;
        thumbnails.innerHTML = '';
        uploadBtn.disabled = files.length === 0;

        Array.from(files).slice(0, 20).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-12 object-cover rounded">`;
                    thumbnails.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });

        if (files.length > 20) {
            const more = document.createElement('div');
            more.className = 'flex items-center justify-center h-12 bg-gray-100 rounded text-xs text-gray-500';
            more.textContent = `+${files.length - 20} more`;
            thumbnails.appendChild(more);
        }
    }
});
</script>
@endpush
@endsection
