@extends('layouts.dashboard')

@section('title', 'Preview Import - Batch #' . $batch->id)

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">Preview & Insert Products</h1>
            <p class="text-gray-600 mt-1">Batch #{{ $batch->id }} • Review and edit before inserting</p>
        </div>
        <a href="{{ route('admin.bulk-import.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Import
        </a>
    </div>

    {{-- Progress Indicator --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6" id="progressCard">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center">
                <span class="text-sm font-medium text-gray-700">OCR Processing Status</span>
                @if(!$batch->is_complete)
                <span class="ml-2 flex items-center text-blue-600 text-sm">
                    <i class="fas fa-spinner fa-spin mr-1"></i>
                    <span id="statusText">Processing...</span>
                </span>
                @else
                <span class="ml-2 text-green-600 text-sm">
                    <i class="fas fa-check-circle mr-1"></i>
                    Completed
                </span>
                @endif
            </div>
            <span class="text-sm text-gray-600">
                <span id="processedCount">{{ $batch->processed_images }}</span> / {{ $batch->total_images }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full transition-all" id="progressBar" style="width: {{ $batch->progress_percentage }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span><i class="fas fa-check text-green-500 mr-1"></i>Success: <span id="successCount">{{ $batch->successful_images }}</span></span>
            <span><i class="fas fa-times text-red-500 mr-1"></i>Failed: <span id="failedCount">{{ $batch->failed_images }}</span></span>
        </div>
    </div>

    {{-- Insert Form --}}
    <form action="{{ route('admin.bulk-import.insert', $batch) }}" method="POST" id="insertForm">
        @csrf
        
        {{-- Default Settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="default_category_id" class="block text-sm font-medium text-gray-700 mb-1">Default Category</label>
                    <select name="default_category_id" id="default_category_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="applyDefaultCategory()">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="default_brand_id" class="block text-sm font-medium text-gray-700 mb-1">Default Brand</label>
                    <select name="default_brand_id" id="default_brand_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="applyDefaultBrand()">
                        <option value="">No Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="default_stock" class="block text-sm font-medium text-gray-700 mb-1">Default Stock</label>
                    <input type="number" name="default_stock" id="default_stock" value="10" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="applyDefaultStock()">
                </div>
                <div>
                    <button type="submit" id="insertBtn" class="w-full inline-flex justify-center items-center px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-200 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-database mr-2"></i>
                        Insert All (<span id="readyCount">{{ $items->where('status', 'ready')->count() }}</span>)
                    </button>
                </div>
            </div>
        </div>

        {{-- Preview Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="previewTable">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                            <th class="px-3 py-3 w-16">Image</th>
                            <th class="px-3 py-3 min-w-[150px]">Name</th>
                            <th class="px-3 py-3 w-28">Standard Price<br><span class="text-[10px] font-normal text-gray-400">(Calculated)</span></th>
                            <th class="px-3 py-3 w-28">Express Price<br><span class="text-[10px] font-normal text-gray-400">(Manual)</span></th>
                            <th class="px-3 py-3 w-32">Category</th>
                            <th class="px-3 py-3 w-28">Brand</th>
                            <th class="px-3 py-3 w-16">Stock</th>
                            <th class="px-3 py-3 w-20">Status</th>
                            <th class="px-3 py-3 w-16">Save</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        @foreach($items as $item)
                        <tr class="item-row hover:bg-gray-50" data-item-id="{{ $item->id }}">
                            <td class="px-3 py-2">
                                @if($item->cropped_image_path)
                                <img src="{{ Storage::url($item->cropped_image_path) }}" alt="Product" class="w-12 h-12 object-cover rounded-lg">
                                @else
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" 
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 name-input"
                                       value="{{ $item->parsed_name }}"
                                       data-field="parsed_name"
                                       placeholder="Product name">
                                <div class="text-xs text-gray-400 mt-1 truncate max-w-[200px]" title="{{ $item->ocr_raw_text }}">
                                    OCR: {{ Str::limit($item->ocr_raw_text, 30) ?: 'Pending...' }}
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" 
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 standard-price-input bg-blue-50"
                                       value="{{ $item->standard_price }}"
                                       data-field="standard_price"
                                       min="0"
                                       step="1"
                                       placeholder="0">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" 
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 express-price-input"
                                       value="{{ $item->express_price }}"
                                       data-field="express_price"
                                       min="0"
                                       step="1"
                                       placeholder="Optional">
                            </td>
                            <td class="px-3 py-2">
                                <select class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 category-select" data-field="category_id">
                                    <option value="">Select</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <select class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 brand-select" data-field="brand_id">
                                    <option value="">None</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" 
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 stock-input"
                                       value="{{ $item->stock ?? 10 }}"
                                       data-field="stock"
                                       min="0"
                                       placeholder="10">
                            </td>
                            <td class="px-3 py-2">
                                @if($item->status === 'ready')
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full status-badge">Ready</span>
                                @elseif($item->status === 'inserted')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full status-badge">Done</span>
                                @elseif($item->status === 'failed')
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full status-badge" title="{{ $item->error_message }}">Failed</span>
                                @elseif($item->status === 'processing')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full status-badge">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full status-badge">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <button type="button" onclick="saveItem({{ $item->id }})" class="text-blue-600 hover:text-blue-800 save-btn" title="Save changes">
                                    <i class="fas fa-save"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
const batchId = {{ $batch->id }};
const isComplete = {{ $batch->is_complete ? 'true' : 'false' }};
let pollInterval;

function updateProgress(data) {
    document.getElementById('processedCount').textContent = data.batch.processed_images;
    document.getElementById('successCount').textContent = data.batch.successful_images;
    document.getElementById('failedCount').textContent = data.batch.failed_images;
    document.getElementById('progressBar').style.width = data.batch.progress_percentage + '%';
    
    // Update ready count
    const readyItems = data.items.filter(i => i.status === 'ready').length;
    document.getElementById('readyCount').textContent = readyItems;
    
    // Update status text
    if (data.batch.is_complete) {
        document.getElementById('statusText').innerHTML = '<i class="fas fa-check-circle mr-1"></i>Completed';
        document.getElementById('statusText').className = 'ml-2 text-green-600 text-sm';
        clearInterval(pollInterval);
    }
    
    // Update table rows
    data.items.forEach(item => {
        const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
        if (row) {
            const nameInput = row.querySelector('.name-input');
            const expressPriceInput = row.querySelector('.express-price-input');
            const standardPriceInput = row.querySelector('.standard-price-input');
            const statusBadge = row.querySelector('.status-badge');
            
            if (!document.activeElement.isSameNode(nameInput) && item.parsed_name) {
                nameInput.value = item.parsed_name;
            }
            if (!document.activeElement.isSameNode(expressPriceInput) && item.express_price) {
                expressPriceInput.value = item.express_price;
            }
            if (!document.activeElement.isSameNode(standardPriceInput) && item.standard_price) {
                standardPriceInput.value = item.standard_price;
            }
            
            // Update status badge
            if (item.status === 'ready') {
                statusBadge.className = 'px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full status-badge';
                statusBadge.textContent = 'Ready';
            } else if (item.status === 'failed') {
                statusBadge.className = 'px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full status-badge';
                statusBadge.innerHTML = 'Failed';
                statusBadge.title = item.error_message;
            } else if (item.status === 'processing' || item.status === 'ocr_complete') {
                statusBadge.className = 'px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full status-badge';
                statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        }
    });
}

function pollProgress() {
    fetch(`/admin/bulk-import/${batchId}/progress`)
        .then(response => response.json())
        .then(data => updateProgress(data))
        .catch(error => console.error('Error polling progress:', error));
}

function saveItem(itemId) {
    const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
    const name = row.querySelector('.name-input').value;
    const expressPrice = row.querySelector('.express-price-input').value;
    const standardPrice = row.querySelector('.standard-price-input').value;
    const categoryId = row.querySelector('.category-select').value;
    const brandId = row.querySelector('.brand-select').value;
    const stock = row.querySelector('.stock-input').value;
    const saveBtn = row.querySelector('.save-btn');
    
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch(`/admin/bulk-import/items/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            parsed_name: name,
            express_price: expressPrice,
            standard_price: standardPrice,
            category_id: categoryId || null,
            brand_id: brandId || null,
            stock: stock,
            description: name
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            saveBtn.innerHTML = '<i class="fas fa-check text-green-600"></i>';
            setTimeout(() => {
                saveBtn.innerHTML = '<i class="fas fa-save"></i>';
            }, 2000);
        } else {
            saveBtn.innerHTML = '<i class="fas fa-times text-red-600"></i>';
        }
    })
    .catch(error => {
        console.error('Error saving item:', error);
        saveBtn.innerHTML = '<i class="fas fa-times text-red-600"></i>';
    });
}

function applyDefaultCategory() {
    const categoryId = document.getElementById('default_category_id').value;
    if (!categoryId) return;
    
    document.querySelectorAll('.category-select').forEach(select => {
        if (!select.value) {
            select.value = categoryId;
        }
    });
}

function applyDefaultBrand() {
    const brandId = document.getElementById('default_brand_id').value;
    document.querySelectorAll('.brand-select').forEach(select => {
        if (!select.value) {
            select.value = brandId;
        }
    });
}

function applyDefaultStock() {
    const stock = document.getElementById('default_stock').value;
    document.querySelectorAll('.stock-input').forEach(input => {
        if (!input.value || input.value === '10') {
            input.value = stock;
        }
    });
}

// Auto-save on blur
document.addEventListener('change', function(e) {
    if (e.target.matches('.name-input, .express-price-input, .standard-price-input, .category-select, .brand-select, .stock-input')) {
        const row = e.target.closest('tr');
        const itemId = row.dataset.itemId;
        saveItem(itemId);
    }
});

// Start polling if not complete
if (!isComplete) {
    pollInterval = setInterval(pollProgress, 3000);
}

// Form validation
document.getElementById('insertForm').addEventListener('submit', function(e) {
    const readyCount = parseInt(document.getElementById('readyCount').textContent);
    if (readyCount === 0) {
        e.preventDefault();
        alert('No products ready to insert. Wait for OCR processing to complete.');
        return;
    }
    
    // Check that all ready items have a category
    let missingCategories = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge && statusBadge.textContent.trim() === 'Ready') {
            const categorySelect = row.querySelector('.category-select');
            if (!categorySelect.value) {
                missingCategories++;
                categorySelect.classList.add('border-red-500');
            }
        }
    });
    
    if (missingCategories > 0) {
        e.preventDefault();
        alert(`${missingCategories} product(s) are missing a category. Please assign categories before inserting.`);
        return;
    }
    
    if (!confirm(`Insert ${readyCount} products into the database?`)) {
        e.preventDefault();
    }
});
</script>
@endpush
@endsection
