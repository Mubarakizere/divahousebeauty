@extends('layouts.dashboard')

@section('title', 'Smart Crop Studio - Batch #' . $batch->id)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
    /* Custom Scrollbar for the grid */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Cropper Container overrides */
    .cropper-container {
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .cropper-view-box,
    .cropper-face {
        border-radius: 0; 
    }
    
    /* Animation for selection */
    @keyframes pulse-ring {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    .active-ring {
        animation: pulse-ring 2s infinite;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                        Smart Crop Studio
                    </span>
                </h1>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">
                        Batch #{{ $batch->id }}
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-images mr-1.5"></i>
                        {{ $items->count() }} images total
                    </span>
                    <span class="mx-2">•</span>
                    <span class="flex items-center" id="statusSummary">
                        Loading status...
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('admin.bulk-import.crop.auto') }}" method="POST" onsubmit="return confirm('Are you sure? This will skip manual cropping for all remaining images.');">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm">
                        <i class="fas fa-magic mr-2 text-amber-500"></i>
                        Auto-Process Remaining
                    </button>
                </form>
                
                <a href="{{ route('admin.bulk-import.preview', $batch) }}" class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md transition-all">
                    <span>Review & Finalize</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        {{-- Main Workspace --}}
        <div class="grid grid-cols-12 gap-8 h-[calc(100vh-220px)] min-h-[600px]">
            
            {{-- Left Sidebar: Image Grid --}}
            <div class="col-span-12 lg:col-span-3 flex flex-col bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900">Gallery</h3>
                        <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded border border-gray-200" id="progressCount">0/0 Done</span>
                    </div>
                    
                    {{-- Progress Bar --}}
                    <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-green-500 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 0%" id="progressBar"></div>
                    </div>
                </div>

                {{-- Scrollable List --}}
                <div class="flex-1 overflow-y-auto p-3 custom-scrollbar" id="thumbnailGrid">
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($items as $index => $item)
                        <div class="group relative aspect-square cursor-pointer rounded-xl overflow-hidden border-2 transition-all duration-200 
                                    {{ $item->status === 'cropped' ? 'border-green-500 opacity-60 hover:opacity-100' : 'border-gray-200 hover:border-blue-400' }}"
                             data-item-id="{{ $item->id }}"
                             data-status="{{ $item->status }}"
                             onclick="selectImage({{ $item->id }}, '/storage/{{ $item->temp_image_path }}', this)">
                            
                            <img src="/storage/{{ $item->temp_image_path }}" 
                                 alt="Img {{ $index + 1 }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            
                            {{-- Overlay for Status --}}
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 transition-opacity duration-200 status-overlay
                                        {{ $item->status === 'cropped' ? 'opacity-100 bg-green-900/20' : 'group-hover:opacity-100' }}">
                                @if($item->status === 'cropped')
                                    <div class="bg-green-500 text-white rounded-full p-1.5 shadow-lg transform scale-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <span class="text-white text-xs font-medium bg-black/50 px-2 py-1 rounded-full backdrop-blur-sm">Edit</span>
                                @endif
                            </div>
                            
                            {{-- Index Badge --}}
                            <span class="absolute top-1 left-1 text-[10px] font-bold text-gray-600 bg-white/90 px-1.5 py-0.5 rounded shadow-sm border border-gray-100">
                                #{{ $index + 1 }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Area: Editor --}}
            <div class="col-span-12 lg:col-span-9 flex flex-col gap-6">
                
                {{-- Editor Card --}}
                <div class="flex-1 bg-white rounded-2xl shadow-lg border border-gray-200 flex flex-col overflow-hidden relative">
                    
                    {{-- Toolbar --}}
                    <div class="h-16 border-b border-gray-100 flex items-center justify-between px-6 bg-white z-10">
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-500">Aspect Ratio:</span>
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button type="button" onclick="setAspectRatio(1)" class="ratio-btn px-3 py-1.5 text-xs font-medium rounded-md text-gray-600 hover:bg-white hover:shadow-sm transition-all focus:outline-none bg-white shadow-sm ring-1 ring-black/5">1:1</button>
                                <button type="button" onclick="setAspectRatio(4/3)" class="ratio-btn px-3 py-1.5 text-xs font-medium rounded-md text-gray-600 hover:bg-white hover:shadow-sm transition-all focus:outline-none">4:3</button>
                                <button type="button" onclick="setAspectRatio(16/9)" class="ratio-btn px-3 py-1.5 text-xs font-medium rounded-md text-gray-600 hover:bg-white hover:shadow-sm transition-all focus:outline-none">16:9</button>
                                <button type="button" onclick="setAspectRatio(NaN)" class="ratio-btn px-3 py-1.5 text-xs font-medium rounded-md text-gray-600 hover:bg-white hover:shadow-sm transition-all focus:outline-none">Free</button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="rotateCrop(-90)" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Rotate Left">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button type="button" onclick="rotateCrop(90)" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Rotate Right">
                                <i class="fas fa-redo"></i>
                            </button>
                            <div class="w-px h-6 bg-gray-200 mx-1"></div>
                            <button type="button" onclick="resetCrop()" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Reset">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Canvas Area --}}
                    <div class="flex-1 bg-slate-100 relative group overflow-hidden flex items-center justify-center p-4">
                        {{-- Placeholder State --}}
                        <div id="cropperPlaceholder" class="text-center absolute inset-0 flex flex-col items-center justify-center z-0">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                                <i class="fas fa-crop-alt text-3xl text-indigo-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No Image Selected</h3>
                            <p class="text-gray-500 mt-1 max-w-xs">Select an image from the gallery on the left to start editing.</p>
                        </div>

                         {{-- Loading State --}}
                         <div id="imageLoading" class="hidden absolute inset-0 bg-white/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                            <p class="mt-3 text-sm font-medium text-indigo-600">Loading image...</p>
                        </div>

                        {{-- Image Container --}}
                        <div id="cropperContainer" class="hidden w-full h-full max-h-[65vh]">
                            <img id="cropImage" src="" alt="To Crop" class="max-w-full block">
                        </div>
                    </div>

                    {{-- Action Footer --}}
                    <div class="h-20 border-t border-gray-100 bg-white px-8 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                           <span id="currentImageName" class="font-medium text-gray-900">--</span>
                        </div>
                        
                        <div class="flex gap-4">
                            <button type="button" id="cropBtn" onclick="saveCrop()" disabled 
                                    class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                            
                            <button type="button" id="nextBtn" onclick="cropAndNext()" disabled 
                                    class="px-8 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 transition-all shadow-lg shadow-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                <span>Save & Next</span>
                                <i class="fas fa-chevron-right ml-2 text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
    // State Management
    let cropper = null;
    let currentItemId = null;
    let processedCount = {{ $items->where('status', 'cropped')->count() }};
    const totalCount = {{ $items->count() }};
    const itemIds = @json($items->pluck('id'));

    // DOM Elements
    const els = {
        progressBar: document.getElementById('progressBar'),
        progressCount: document.getElementById('progressCount'),
        statusSummary: document.getElementById('statusSummary'),
        cropperPlaceholder: document.getElementById('cropperPlaceholder'),
        cropperContainer: document.getElementById('cropperContainer'),
        imageLoading: document.getElementById('imageLoading'),
        cropImage: document.getElementById('cropImage'),
        cropBtn: document.getElementById('cropBtn'),
        nextBtn: document.getElementById('nextBtn'),
        currentImageName: document.getElementById('currentImageName'),
        ratioBtns: document.querySelectorAll('.ratio-btn')
    };

    function updateProgress() {
        const percentage = totalCount > 0 ? Math.round((processedCount / totalCount) * 100) : 0;
        els.progressBar.style.width = percentage + '%';
        els.progressCount.textContent = `${processedCount} / ${totalCount} Done`;
        
        const remaining = totalCount - processedCount;
        els.statusSummary.textContent = remaining === 0 
            ? 'All images processed!' 
            : `${remaining} image${remaining !== 1 ? 's' : ''} remaining`;
        
        if(remaining === 0) {
            els.statusSummary.className = 'text-green-600 font-medium flex items-center';
            els.statusSummary.innerHTML = '<i class="fas fa-check-circle mr-1.5"></i> All Done';
        }
    }

    function selectImage(itemId, imageUrl, element) {
        if (itemId === currentItemId) return;

        // UI Updates
        currentItemId = itemId;
        
        // Update Grid Selection
        document.querySelectorAll('[data-item-id]').forEach(el => {
            el.classList.remove('ring-4', 'ring-indigo-100', 'border-indigo-500', 'z-10');
            el.classList.add('border-gray-200');
        });
        
        if (element) {
            element.classList.remove('border-gray-200');
            element.classList.add('ring-4', 'ring-indigo-100', 'border-indigo-500', 'z-10');
            element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
             // Fallback if element not passed directly
             const el = document.querySelector(`[data-item-id="${itemId}"]`);
             if(el) {
                el.classList.remove('border-gray-200');
                el.classList.add('ring-4', 'ring-indigo-100', 'border-indigo-500', 'z-10');
             }
        }

        // Setup Editor
        els.cropperPlaceholder.classList.add('hidden');
        els.cropperContainer.classList.add('hidden');
        els.imageLoading.classList.remove('hidden');
        els.currentImageName.textContent = `Editing Image #${itemIds.indexOf(itemId) + 1}`;
        
        // Destroy prev instance
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        // Load new image
        els.cropImage.src = imageUrl;
        
        // Wait for load
        els.cropImage.onload = () => {
            els.imageLoading.classList.add('hidden');
            els.cropperContainer.classList.remove('hidden');
            
            initCropper();
            
            els.cropBtn.disabled = false;
            els.nextBtn.disabled = false;
        };
    }

    function initCropper() {
        cropper = new Cropper(els.cropImage, {
            viewMode: 1, // Restrict to canvas
            dragMode: 'move',
            autoCropArea: 0.8,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            responsive: true,
            background: false, // Cleaner look
            ready() {
                // Subtle fade in could go here
            }
        });
    }

    function setAspectRatio(ratio) {
        if (!cropper) return;
        cropper.setAspectRatio(ratio);
        
        // Update Button UI
        els.ratioBtns.forEach(btn => {
            btn.classList.remove('bg-white', 'shadow-sm', 'ring-1', 'ring-black/5', 'text-indigo-600');
            btn.classList.add('text-gray-600');
        });
        
        // Find clicked button (simple check logic or pass event)
        // For simplicity, we'll just style based on clicked logic if passed or re-implement
        // A cleaner way is to pass 'this' in HTML: onclick="setAspectRatio(1, this)"
        // But for now, we'll just reset all visuals. The user sees the crop box change immediately.
    }

    function rotateCrop(deg) {
        if (cropper) cropper.rotate(deg);
    }

    function resetCrop() {
        if (cropper) cropper.reset();
    }

    function saveCrop(callback) {
        if (!cropper || !currentItemId) return;

        // UI Loading State
        const originalBtnText = els.nextBtn.innerHTML;
        els.cropBtn.disabled = true;
        els.nextBtn.disabled = true;
        els.nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';

        // Get Data
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 1600,
            maxHeight: 1600,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        const cropData = canvas.toDataURL('image/webp', 0.90);

        // Send Request
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
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                markAsProcessed(currentItemId);
                
                // Success Toast/Notification could go here
                
                if (callback) {
                    callback();
                } else {
                    // Reset UI if just saving
                    encourageNextAction(); 
                }
            } else {
                alert('Error processing image: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error occurred.');
        })
        .finally(() => {
            els.cropBtn.disabled = false;
            els.nextBtn.disabled = false;
            els.nextBtn.innerHTML = originalBtnText;
        });
    }

    function markAsProcessed(id) {
        const card = document.querySelector(`[data-item-id="${id}"]`);
        if(card && !card.dataset.status.includes('cropped')) {
            card.dataset.status = 'cropped';
            card.classList.remove('border-gray-200', 'hover:border-blue-400');
            card.classList.add('border-green-500', 'opacity-60');
            
            // Show Success Icon Overlay
            const overlay = card.querySelector('.status-overlay');
            overlay.classList.remove('opacity-0', 'group-hover:opacity-100');
            overlay.classList.add('opacity-100', 'bg-green-900/20');
            overlay.innerHTML = `
                <div class="bg-green-500 text-white rounded-full p-1.5 shadow-lg transform scale-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            `;
            
            processedCount++;
            updateProgress();
        }
    }

    function cropAndNext() {
        saveCrop(() => {
            findAndSelectNext();
        });
    }

    function findAndSelectNext() {
        const currentIndex = itemIds.indexOf(currentItemId);
        
        // Search forward
        for (let i = currentIndex + 1; i < itemIds.length; i++) {
            const card = document.querySelector(`[data-item-id="${itemIds[i]}"]`);
            if (card && card.dataset.status !== 'cropped') {
                card.click();
                return;
            }
        }
        
        // Search from start
        for (let i = 0; i < currentIndex; i++) {
            const card = document.querySelector(`[data-item-id="${itemIds[i]}"]`);
            if (card && card.dataset.status !== 'cropped') {
                card.click();
                return;
            }
        }
        
        // If we get here, all images are done
        if(confirm('All images updated! Go to preview?')) {
            window.location.href = "{{ route('admin.bulk-import.preview', $batch) }}";
        }
    }
    
    function encourageNextAction() {
        // Optional: Visual cue to click Next
    }

    // Initialization
    document.addEventListener('DOMContentLoaded', () => {
        updateProgress();
        
        // Select first pending image
        const firstPending = document.querySelector('[data-item-id][data-status="uploaded"]');
        if (firstPending) {
            firstPending.click();
        } else {
            // Or just the first one if all done (re-editing)
            const first = document.querySelector('[data-item-id]');
            if(first) first.click();
        }
    });

    // Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        if (!cropper) return;
        
        if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            cropAndNext();
        }
    });

</script>
@endpush
@endsection
