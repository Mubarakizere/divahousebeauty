@extends('layouts.dashboard')

@section('title', 'Import Products')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-file-excel text-green-600 mr-3"></i>
                Import Products
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Upload an Excel or CSV file to bulk import products.
            </p>
        </div>
        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Products
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-start">
            <i class="fas fa-check-circle mr-2 mt-0.5"></i>
            <div class="flex-1">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-start">
            <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
            <div class="flex-1">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Import Errors List --}}
    @if(session('errors_list') && count(session('errors_list')) > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">Some rows had errors:</h3>
                    <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1 max-h-40 overflow-y-auto">
                        @foreach(session('errors_list') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-400 mt-0.5"></i>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Upload Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-upload text-blue-500 mr-2"></i>
                Upload File
            </h2>

            <form action="{{ route('admin.products.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Excel/CSV File <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-file-excel text-4xl text-green-500"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                XLSX, XLS, or CSV up to 10MB
                            </p>
                        </div>
                    </div>
                    <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
                </div>

                <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-200 transition-colors font-medium">
                    <i class="fas fa-upload mr-2"></i> Import Products
                </button>
            </form>
        </div>

        {{-- Template Download --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-download text-purple-500 mr-2"></i>
                Download Template
            </h2>

            <p class="text-sm text-gray-600 mb-4">
                Download the template file with sample data. The template includes all required columns:
            </p>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">Template Columns:</h4>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-center">
                        <span class="w-32 font-medium text-gray-700">name</span>
                        <span class="text-red-500 text-xs mr-2">*</span>
                        <span>Product name</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-32 font-medium text-gray-700">description</span>
                        <span class="text-gray-400 text-xs mr-2">opt</span>
                        <span>Product description</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-32 font-medium text-gray-700">express_price</span>
                        <span class="text-red-500 text-xs mr-2">*</span>
                        <span>Fast delivery price (RWF)</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-32 font-medium text-gray-700">standard_price</span>
                        <span class="text-gray-400 text-xs mr-2">opt</span>
                        <span>7+ days delivery price (RWF)</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-32 font-medium text-gray-700">shipping_type</span>
                        <span class="text-gray-400 text-xs mr-2">opt</span>
                        <span>both, express_only, standard_only</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-32 font-medium text-gray-700">stock</span>
                        <span class="text-gray-400 text-xs mr-2">opt</span>
                        <span>Quantity in stock</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-32 font-medium text-gray-700">category</span>
                        <span class="text-gray-400 text-xs mr-2">opt</span>
                        <span>Category name (created if not exists)</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-32 font-medium text-gray-700">brand</span>
                        <span class="text-gray-400 text-xs mr-2">opt</span>
                        <span>Brand name (created if not exists)</span>
                    </li>
                </ul>
            </div>

            <a href="{{ route('admin.products.import.template') }}" 
               class="w-full inline-flex justify-center items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-200 transition-colors font-medium">
                <i class="fas fa-download mr-2"></i> Download Template (CSV)
            </a>

            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Note:</strong> Images can be uploaded later by editing each product individually.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        document.getElementById('file-name').textContent = fileName ? `Selected: ${fileName}` : '';
    });
</script>
@endsection
