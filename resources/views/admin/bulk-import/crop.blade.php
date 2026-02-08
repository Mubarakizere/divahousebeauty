@extends('layouts.dashboard')

@section('title', 'Crop Images - Batch #' . $batch->id)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
    .cropper-container {
        max-height: 500px;
    }
    .image-card {
        transition: all 0.2s;
    }
    .image-card.processed {
        opacity: 0.5;
    }
    .image-card.active {
        ring: 2px;
        ring-color: #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">Crop Product Images</h1>
            <p class="text-gray-600 mt-1">Batch #{{ $batch->id }} • {{ $items->count() }} images to process</p>
        </div>
        <div class="flex space-x-3">
            <form action="{{ route('admin.bulk-import.crop.auto') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                    <i class="fas fa-magic mr-2"></i>
                    Skip Cropping (Use Original)
                </button>
            </form>
            <a href="{{ route('admin.bulk-import.preview', $batch) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-arrow-right mr-2"></i>
                Go to Preview
            </a>
        </div>
    </div>

    {{-- Progress --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Cropping Progress</span>
            <span class="text-sm text-gray-600" id="progressText">0 / {{ $items->count() }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full transition-all" style="width: 0%" id="progressBar"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Image Thumbnails --}}
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-900 mb-4">Images to Crop</h3>
            <div class="grid grid-cols-3 gap-2 max-h-[600px] overflow-y-auto" id="thumbnailGrid">
                @foreach($items as $index => $item)
                <div class="image-card relative cursor-pointer rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-300 {{ $item->status === 'cropped' ? 'processed' : '' }}" 
                     data-item-id="{{ $item->id }}"
                     data-image-url="{{ Storage::url($item->temp_image_path) }}"
                     onclick="selectImage({{ $item->id }}, '{{ Storage::url($item->temp_image_path) }}')">
                    <img src="{{ Storage::url($item->temp_image_path) }}" alt="Image {{ $index + 1 }}" class="w-full h-20 object-cover">
                    @if($item->status === 'cropped')
                    <div class="absolute inset-0 bg-green-500 bg-opacity-50 flex items-center justify-center">
                        <i class="fas fa-check text-white text-xl"></i>
                    </div>
                    @endif
                    <span class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs text-center py-0.5">{{ $index + 1 }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Cropping Area --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Crop Area</h3>
                <span class="text-sm text-gray-500" id="currentImageName">Select an image</span>
            </div>

            <div class="bg-gray-100 rounded-lg overflow-hidden mb-4" style="min-height: 400px;">
                <div id="cropperPlaceholder" class="flex items-center justify-center h-96 text-gray-400">
                    <div class="text-center">
                        <i class="fas fa-crop-alt text-5xl mb-3"></i>
                        <p>Click an image thumbnail to start cropping</p>
                    </div>
                </div>
                <div id="cropperContainer" class="hidden">
                    <img id="cropImage" src="" alt="Crop preview" style="max-width: 100%;">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex space-x-2">
                    <button type="button" onclick="rotateCrop(-90)" class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300" title="Rotate Left">
                        <i class="fas fa-undo"></i>
                    </button>
                    <button type="button" onclick="rotateCrop(90)" class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300" title="Rotate Right">
                        <i class="fas fa-redo"></i>
                    </button>
                    <button type="button" onclick="resetCrop()" class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300" title="Reset">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="flex space-x-3">
                    <button type="button" id="cropBtn" onclick="saveCrop()" disabled class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-crop-alt mr-2"></i>
                        Crop & Process
                    </button>
                    <button type="button" id="nextBtn" onclick="cropAndNext()" disabled class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-forward mr-2"></i>
                        Crop & Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
let cropper = null;
let currentItemId = null;
let processedCount = {{ $items->where('status', 'cropped')->count() }};
const totalCount = {{ $items->count() }};
const itemIds = @json($items->pluck('id'));

function updateProgress() {
    const percentage = totalCount > 0 ? Math.round((processedCount / totalCount) * 100) : 0;
    document.getElementById('progressBar').style.width = percentage + '%';
    document.getElementById('progressText').textContent = processedCount + ' / ' + totalCount;
}

function selectImage(itemId, imageUrl) {
    currentItemId = itemId;
    
    // Update active state
    document.querySelectorAll('.image-card').forEach(card => {
        card.classList.remove('border-blue-500');
    });
    document.querySelector(`[data-item-id="${itemId}"]`).classList.add('border-blue-500');
    
    // Show cropper
    document.getElementById('cropperPlaceholder').classList.add('hidden');
    document.getElementById('cropperContainer').classList.remove('hidden');
    document.getElementById('currentImageName').textContent = 'Image #' + (itemIds.indexOf(itemId) + 1);
    
    // Initialize cropper
    const cropImage = document.getElementById('cropImage');
    cropImage.src = imageUrl;
    
    if (cropper) {
        cropper.destroy();
    }
    
    cropImage.onload = function() {
        cropper = new Cropper(cropImage, {
            aspectRatio: NaN, // Free ratio
            viewMode: 1,
            responsive: true,
            restore: false,
            autoCropArea: 0.8,
            guides: true,
            center: true,
            highlight: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: true,
        });
        
        document.getElementById('cropBtn').disabled = false;
        document.getElementById('nextBtn').disabled = false;
    };
}

function rotateCrop(degrees) {
    if (cropper) {
        cropper.rotate(degrees);
    }
}

function resetCrop() {
    if (cropper) {
        cropper.reset();
    }
}

function saveCrop(callback) {
    if (!cropper || !currentItemId) return;
    
    const canvas = cropper.getCroppedCanvas({
        maxWidth: 1200,
        maxHeight: 1200,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });
    
    const cropData = canvas.toDataURL('image/webp', 0.85);
    
    // Show loading
    document.getElementById('cropBtn').disabled = true;
    document.getElementById('nextBtn').disabled = true;
    document.getElementById('cropBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    fetch('{{ route("admin.bulk-import.crop.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            item_id: currentItemId,
            crop_data: cropData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mark as processed
            const card = document.querySelector(`[data-item-id="${currentItemId}"]`);
            card.classList.add('processed');
            card.innerHTML += '<div class="absolute inset-0 bg-green-500 bg-opacity-50 flex items-center justify-center"><i class="fas fa-check text-white text-xl"></i></div>';
            
            processedCount++;
            updateProgress();
            
            if (callback) callback();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save crop');
    })
    .finally(() => {
        document.getElementById('cropBtn').disabled = false;
        document.getElementById('nextBtn').disabled = false;
        document.getElementById('cropBtn').innerHTML = '<i class="fas fa-crop-alt mr-2"></i>Crop & Process';
    });
}

function cropAndNext() {
    saveCrop(() => {
        // Find next unprocessed image
        const currentIndex = itemIds.indexOf(currentItemId);
        for (let i = currentIndex + 1; i < itemIds.length; i++) {
            const card = document.querySelector(`[data-item-id="${itemIds[i]}"]`);
            if (!card.classList.contains('processed')) {
                card.click();
                return;
            }
        }
        
        // Check from beginning
        for (let i = 0; i < currentIndex; i++) {
            const card = document.querySelector(`[data-item-id="${itemIds[i]}"]`);
            if (!card.classList.contains('processed')) {
                card.click();
                return;
            }
        }
        
        // All done
        alert('All images have been cropped! Click "Go to Preview" to continue.');
    });
}

// Initialize progress
updateProgress();

// Auto-select first unprocessed image
document.addEventListener('DOMContentLoaded', function() {
    const firstUnprocessed = document.querySelector('.image-card:not(.processed)');
    if (firstUnprocessed) {
        firstUnprocessed.click();
    }
});
</script>
@endpush
@endsection
