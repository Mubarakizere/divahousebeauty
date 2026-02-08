@extends('layouts.dashboard')

@section('title', 'Preview Import - Batch #' . $batch->id)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
        
        {{-- Category Selection --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0 sm:flex-1 sm:mr-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category for All Products</label>
                    <select name="category_id" id="category_id" required class="w-full sm:w-64 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" id="insertBtn" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-200 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-database mr-2"></i>
                    Insert All Products (<span id="readyCount">{{ $items->where('status', 'ready')->count() }}</span>)
                </button>
            </div>
        </div>

        {{-- Preview Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="previewTable">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                            <th class="px-4 py-3 w-20">Image</th>
                            <th class="px-4 py-3">OCR Text</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3 w-32">Price (RWF)</th>
                            <th class="px-4 py-3 w-24">Status</th>
                            <th class="px-4 py-3 w-20">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        @foreach($items as $item)
                        <tr class="item-row" data-item-id="{{ $item->id }}">
                            <td class="px-4 py-3">
                                @if($item->cropped_image_path)
                                <img src="{{ Storage::url($item->cropped_image_path) }}" alt="Product" class="w-16 h-16 object-cover rounded-lg">
                                @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-gray-500 max-w-xs truncate" title="{{ $item->ocr_raw_text }}">
                                    {{ Str::limit($item->ocr_raw_text, 50) ?: 'Processing...' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 name-input"
                                       value="{{ $item->parsed_name }}"
                                       data-field="parsed_name"
                                       placeholder="Product name">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 price-input"
                                       value="{{ $item->calculated_price }}"
                                       data-field="calculated_price"
                                       min="0"
                                       step="0.01"
                                       placeholder="0">
                            </td>
                            <td class="px-4 py-3">
                                @if($item->status === 'ready')
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full status-badge">Ready</span>
                                @elseif($item->status === 'inserted')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full status-badge">Inserted</span>
                                @elseif($item->status === 'failed')
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full status-badge" title="{{ $item->error_message }}">Failed</span>
                                @elseif($item->status === 'processing')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full status-badge">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>OCR
                                </span>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full status-badge">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
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
            const priceInput = row.querySelector('.price-input');
            const statusBadge = row.querySelector('.status-badge');
            
            if (!document.activeElement.isSameNode(nameInput) && item.parsed_name) {
                nameInput.value = item.parsed_name;
            }
            if (!document.activeElement.isSameNode(priceInput) && item.calculated_price) {
                priceInput.value = item.calculated_price;
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
                statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>OCR';
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
    const price = row.querySelector('.price-input').value;
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
            calculated_price: price,
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

// Auto-save on blur
document.addEventListener('change', function(e) {
    if (e.target.matches('.name-input, .price-input')) {
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
    const category = document.getElementById('category_id').value;
    if (!category) {
        e.preventDefault();
        alert('Please select a category');
        return;
    }
    
    const readyCount = parseInt(document.getElementById('readyCount').textContent);
    if (readyCount === 0) {
        e.preventDefault();
        alert('No products ready to insert. Wait for OCR processing to complete.');
        return;
    }
    
    if (!confirm(`Insert ${readyCount} products into the database?`)) {
        e.preventDefault();
    }
});
</script>
@endpush
@endsection
