@extends('layouts.dashboard')

@section('title', 'Smart Import Preview - Batch #' . $batch->id)

@push('styles')
<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
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

    /* Floating Input Labels */
    .floating-input-group {
        position: relative;
    }
    .floating-input-group input,
    .floating-input-group select {
        padding-top: 1.25rem;
        padding-bottom: 0.25rem;
    }
    .floating-input-group label {
        position: absolute;
        top: 0.25rem;
        left: 0.75rem;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        pointer-events: none;
        transition: all 0.2s;
    }
    .floating-input-group:focus-within label {
        color: #4f46e5;
    }

    /* Glassmorphism Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(229, 231, 235, 0.5);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-[1800px] mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                        Import Preview
                    </span>
                </h1>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">
                        Batch #{{ $batch->id }}
                    </span>
                    <span class="flex items-center" id="ocrStatusContainer">
                        @if(!$batch->is_complete)
                        <span class="flex items-center text-amber-600 animate-pulse">
                            <i class="fas fa-magic mr-1.5"></i>
                            <span id="statusText">Analyzing content...</span>
                        </span>
                        @else
                        <span class="flex items-center text-green-600">
                            <i class="fas fa-check-circle mr-1.5"></i>
                            <span>Analysis Complete</span>
                        </span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.bulk-import.crop', $batch) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Crop
                </a>
            </div>
        </div>

        <form action="{{ route('admin.bulk-import.insert', $batch) }}" method="POST" id="insertForm">
            @csrf

            <div class="grid grid-cols-12 gap-8">
                {{-- Left Sidebar: Controls & Stats --}}
                <div class="col-span-12 xl:col-span-3 space-y-6">
                    
                    {{-- Progress Card --}}
                    <div class="glass-card rounded-2xl shadow-sm p-5">
                        <h3 class="text-gray-900 font-semibold mb-4 flex items-center">
                            <i class="fas fa-chart-pie text-indigo-500 mr-2"></i> Progress
                        </h3>
                        
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-200">
                                        Processed
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold inline-block text-indigo-600" id="progressText">
                                        {{ $batch->progress_percentage }}%
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-100">
                                <div style="width:{{ $batch->progress_percentage }}%" id="progressBar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500 transition-all duration-500"></div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div class="bg-green-50 rounded-lg p-2 border border-green-100">
                                    <span class="block text-2xl font-bold text-green-600" id="successCount">{{ $batch->successful_images }}</span>
                                    <span class="text-xs text-green-600 font-medium">Successful</span>
                                </div>
                                <div class="bg-red-50 rounded-lg p-2 border border-red-100">
                                    <span class="block text-2xl font-bold text-red-600" id="failedCount">{{ $batch->failed_images }}</span>
                                    <span class="text-xs text-red-600 font-medium">Failed</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Global Settings --}}
                    <div class="glass-card rounded-2xl shadow-sm p-5 sticky top-6">
                        <h3 class="text-gray-900 font-semibold mb-4 flex items-center">
                            <i class="fas fa-sliders-h text-indigo-500 mr-2"></i> Default Settings
                        </h3>
                        <p class="text-xs text-gray-500 mb-4">Apply these settings to any product that doesn't have a specific value set.</p>

                        <div class="space-y-4">
                            <div class="floating-input-group">
                                <label for="default_category_id">Category</label>
                                <select name="default_category_id" id="default_category_id" onchange="applyDefaultCategory()" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50">
                                    <option value="">Select Category...</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="floating-input-group">
                                <label for="default_brand_id">Brand</label>
                                <select name="default_brand_id" id="default_brand_id" onchange="applyDefaultBrand()" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50">
                                    <option value="">No Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="floating-input-group">
                                <label for="default_stock">Start Stock</label>
                                <input type="number" name="default_stock" id="default_stock" value="10" min="0" onchange="applyDefaultStock()" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50">
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-100">
                             <button type="submit" id="insertBtn" class="w-full flex justify-center items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 shadow-lg shadow-green-200 focus:ring-4 focus:ring-green-100 transition-all font-medium disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>
                                Import <span id="readyCount" class="ml-1">{{ $items->where('status', 'ready')->count() }}</span> Products
                            </button>
                        </div>
                    </div>

                </div>

                {{-- Right: Product List --}}
                <div class="col-span-12 xl:col-span-9">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        
                        {{-- Toolbar --}}
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">Detected Items</h3>
                            <div class="flex space-x-2">
                                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border border-gray-200">
                                    <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                                    Red borders indicate missing info
                                </span>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                                    <tr>
                                        <th class="px-6 py-4 w-20">Image</th>
                                        <th class="px-6 py-4 min-w-[250px]">Product Details</th>
                                        <th class="px-6 py-4 w-40">Pricing (RWF)</th>
                                        <th class="px-6 py-4 w-48">Classification</th>
                                        <th class="px-6 py-4 w-24 text-center">Status</th>
                                        <th class="px-6 py-4 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white" id="tableBody">
                                    @foreach($items as $item)
                                    <tr class="item-row group hover:bg-blue-50/30 transition-colors" data-item-id="{{ $item->id }}">
                                        {{-- Image --}}
                                        <td class="px-6 py-4 align-top">
                                            <div class="relative w-16 h-16 rounded-lg overflow-hidden border border-gray-200 shadow-sm group-hover:shadow-md transition-all">
                                                @if($item->cropped_image_path)
                                                <img src="{{ Storage::url($item->cropped_image_path) }}" 
                                                     alt="Product" 
                                                     class="w-full h-full object-cover cursor-zoom-in"
                                                     onclick="window.open(this.src, '_blank')">
                                                @else
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-300 text-xl"></i>
                                                </div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Details (Name & Stock) --}}
                                        <td class="px-6 py-4 align-top space-y-3">
                                            <div class="floating-input-group">
                                                <label>Product Name</label>
                                                <input type="text" 
                                                       class="block w-full border-0 border-b-2 border-gray-200 bg-transparent focus:border-indigo-600 focus:ring-0 px-0 text-sm font-medium text-gray-900 name-input placeholder-gray-300"
                                                       value="{{ $item->parsed_name }}"
                                                       placeholder="Enter product name..."
                                                       data-field="parsed_name">
                                            </div>
                                            
                                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                                <i class="fas fa-align-left"></i>
                                                <span class="truncate max-w-[200px]" title="{{ $item->ocr_raw_text }}">
                                                    OCR: {{ Str::limit($item->ocr_raw_text, 40) ?: 'Waiting...' }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Pricing --}}
                                        <td class="px-6 py-4 align-top space-y-3">
                                            <div class="floating-input-group">
                                                <label>Standard (Auto)</label>
                                                <input type="number" 
                                                       class="block w-full border-0 border-b border-gray-200 bg-gray-50/50 focus:border-indigo-600 focus:ring-0 px-2 py-2 text-sm text-gray-700 standard-price-input"
                                                       value="{{ $item->standard_price }}"
                                                       placeholder="0"
                                                       min="0" step="1"
                                                       data-field="standard_price">
                                            </div>
                                            <div class="floating-input-group">
                                                <label>Express (Manual)</label>
                                                <input type="number" 
                                                       class="block w-full border-0 border-b border-gray-200 bg-transparent focus:border-indigo-600 focus:ring-0 px-0 text-sm font-medium text-gray-900 express-price-input placeholder-gray-300"
                                                       value="{{ $item->express_price }}"
                                                       placeholder="Optional"
                                                       min="0" step="1"
                                                       data-field="express_price">
                                            </div>
                                        </td>

                                        {{-- Classification --}}
                                        <td class="px-6 py-4 align-top space-y-3">
                                            <div class="floating-input-group">
                                                <label>Category</label>
                                                <select class="block w-full border-0 border-b border-gray-200 bg-transparent focus:border-indigo-600 focus:ring-0 px-0 text-sm text-gray-700 category-select cursor-pointer" 
                                                        data-field="category_id">
                                                    <option value="" class="text-gray-400">Select...</option>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="floating-input-group">
                                                    <label>Brand</label>
                                                    <select class="block w-full border-0 border-b border-gray-200 bg-transparent focus:border-indigo-600 focus:ring-0 px-0 text-xs text-gray-600 brand-select cursor-pointer" 
                                                            data-field="brand_id">
                                                        <option value="">None</option>
                                                        @foreach($brands as $brand)
                                                        <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="floating-input-group">
                                                    <label>Stock</label>
                                                    <input type="number" 
                                                           class="block w-full border-0 border-b border-gray-200 bg-transparent focus:border-indigo-600 focus:ring-0 px-0 text-xs text-gray-600 stock-input"
                                                           value="{{ $item->stock ?? 10 }}"
                                                           placeholder="10"
                                                           data-field="stock">
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-6 py-4 align-middle text-center">
                                            <div class="status-badge-container">
                                                @if($item->status === 'ready')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Ready
                                                    </span>
                                                @elseif($item->status === 'inserted')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Added
                                                    </span>
                                                @elseif($item->status === 'failed')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 cursor-help" title="{{ $item->error_message }}">
                                                        Failed
                                                    </span>
                                                @elseif($item->status === 'processing')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="animate-spin -ml-1 mr-1.5 h-3 w-3 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        OCR
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4 align-middle text-right">
                                            <button type="button" onclick="saveItem({{ $item->id }})" 
                                                    class="p-2 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-indigo-50 save-btn relative group" 
                                                    title="Save Changes">
                                                <i class="fas fa-save"></i>
                                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                                    Save Item
                                                </span>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Empty State --}}
                        @if($items->isEmpty())
                        <div class="p-12 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
                            <p>No items found in this batch.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const batchId = {{ $batch->id }};
    const isComplete = {{ $batch->is_complete ? 'true' : 'false' }};
    let pollInterval;

    // --- Stats & Polling ---
    function updateStats(data) {
        // Update Progress Bar
        const percent = data.batch.progress_percentage;
        document.getElementById('progressBar').style.width = percent + '%';
        document.getElementById('progressText').textContent = percent + '%';
        
        // Update Counts
        document.getElementById('successCount').textContent = data.batch.successful_images;
        document.getElementById('failedCount').textContent = data.batch.failed_images;
        
        // Update Ready Count (for button)
        const readyItems = data.items.filter(i => i.status === 'ready').length;
        document.getElementById('readyCount').textContent = readyItems;

        // Completion Status
        if (data.batch.is_complete) {
            const statusContainer = document.getElementById('ocrStatusContainer');
            statusContainer.innerHTML = `
                <span class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-1.5"></i>
                    <span>Analysis Complete</span>
                </span>
            `;
            if(pollInterval) clearInterval(pollInterval);
        }

        // Update Table Rows
        data.items.forEach(item => updateRow(item));
    }

    function updateRow(item) {
        const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
        if (!row) return;

        // Only update fields if NOT focused (to prevent overwriting user typing)
        const updateField = (selector, value) => {
            const el = row.querySelector(selector);
            if (el && document.activeElement !== el && value !== null && value !== undefined) {
                // For price inputs, don't overwrite with 0 if user cleared it? 
                // Currently just standard overwrite if backend changes
                el.value = value;
            }
        };

        if(item.parsed_name) updateField('.name-input', item.parsed_name);
        if(item.express_price) updateField('.express-price-input', item.express_price);
        if(item.standard_price) updateField('.standard-price-input', item.standard_price);
        
        // Status Badge Logic
        const badgeContainer = row.querySelector('.status-badge-container');
        let badgeHtml = '';
        
        if (item.status === 'ready') {
            badgeHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ready</span>';
        } else if (item.status === 'failed') {
            badgeHtml = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 cursor-help" title="${item.error_message}">Failed</span>`;
        } else if (item.status === 'processing') {
            badgeHtml = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><svg class="animate-spin -ml-1 mr-1.5 h-3 w-3 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>OCR</span>`;
        } else {
             badgeHtml = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span>`;
        }
        badgeContainer.innerHTML = badgeHtml;
    }

    function pollProgress() {
        fetch(`/admin/bulk-import/${batchId}/progress`)
            .then(res => res.json())
            .then(data => updateStats(data))
            .catch(console.error);
    }

    if (!isComplete) {
        pollInterval = setInterval(pollProgress, 3000);
    }

    // --- Editing & Saving ---
    
    function saveItem(itemId) {
        const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
        const btn = row.querySelector('.save-btn');
        const icon = btn.querySelector('i');
        
        // Loading State
        icon.className = 'fas fa-spinner fa-spin text-indigo-500';

        const payload = {
            parsed_name: row.querySelector('.name-input').value,
            express_price: row.querySelector('.express-price-input').value,
            standard_price: row.querySelector('.standard-price-input').value,
            category_id: row.querySelector('.category-select').value,
            brand_id: row.querySelector('.brand-select').value,
            stock: row.querySelector('.stock-input').value,
            description: row.querySelector('.name-input').value
        };

        fetch(`/admin/bulk-import/items/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Success State
                icon.className = 'fas fa-check text-green-500';
                
                // Remove error highlighting if present
                row.querySelector('.category-select').classList.remove('border-red-500', 'text-red-500');

                // Revert icon after 2s
                setTimeout(() => icon.className = 'fas fa-save', 2000);
                
                // Manually trigger a UI row status update to "Ready" if validation passed
                // But typically we wait for the next poll or rely on visual cues
            } else {
                icon.className = 'fas fa-times text-red-500';
            }
        })
        .catch(() => {
            icon.className = 'fas fa-exclamation-triangle text-red-500';
        });
    }

    // Auto-save on blur/change
    document.addEventListener('change', (e) => {
        if (e.target.matches('.name-input, .express-price-input, .standard-price-input, .category-select, .brand-select, .stock-input')) {
            const row = e.target.closest('tr');
            if(row) saveItem(row.dataset.itemId);
        }
    });

    // --- Default Settings Helpers ---
    function applyDefaultCategory() {
        const val = document.getElementById('default_category_id').value;
        if (!val) return;
        document.querySelectorAll('.category-select').forEach(el => {
            if(!el.value) {
                el.value = val;
                saveItem(el.closest('tr').dataset.itemId);
            }
        });
    }

    function applyDefaultBrand() {
        const val = document.getElementById('default_brand_id').value;
        document.querySelectorAll('.brand-select').forEach(el => {
            if(!el.value) {
                el.value = val;
                saveItem(el.closest('tr').dataset.itemId);
            }
        });
    }

    function applyDefaultStock() {
        const val = document.getElementById('default_stock').value;
        document.querySelectorAll('.stock-input').forEach(el => {
            if(!el.value || el.value == 10) {
                el.value = val;
                saveItem(el.closest('tr').dataset.itemId);
            }
        });
    }

    // --- Validation on Submit ---
    document.getElementById('insertForm').addEventListener('submit', (e) => {
        const readyCount = parseInt(document.getElementById('readyCount').textContent) || 0;
        if (readyCount === 0) {
            e.preventDefault();
            alert('No products are ready to insert! Wait for OCR or manually edit items.');
            return;
        }

        let invalid = 0;
        document.querySelectorAll('tr[data-item-id]').forEach(row => {
            // Check if user wants to import this line (simplification: if name exists)
            // Ideally we check if it's "Ready"
            // Let's enforce Category requirement
            const cat = row.querySelector('.category-select');
            const name = row.querySelector('.name-input');
            
            // Only validate if it has a name (implies intent to import)
            if(name.value.trim() !== '') {
                if(!cat.value) {
                    invalid++;
                    cat.classList.add('border-red-500', 'text-red-500');
                }
            }
        });

        if(invalid > 0) {
            e.preventDefault();
            alert(`${invalid} products are missing a Category. Please select a category for them.`);
            return;
        }

        if(!confirm(`Are you sure you want to import ${readyCount} products?`)) {
            e.preventDefault();
        }
    });

</script>
@endpush
@endsection
