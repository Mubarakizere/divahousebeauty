@extends('layouts.dashboard')

@section('title', isset($coupon) ? 'Edit Coupon' : 'Create Coupon')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if(isset($coupon))
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Edit Coupon
                @else
                    <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                    Create New Coupon
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                @if(isset($coupon))
                    Update coupon details and settings
                @else
                    Create a discount coupon for your customers
                @endif
            </p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Coupons
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-400 mr-3 mt-0.5"></i>
                <div>
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

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow">
        <form action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" 
              method="POST" 
              class="p-6 space-y-6"
              x-data="{
                  type: '{{ old('type', $coupon->type ?? 'percentage') }}',
                  isActive: {{ old('is_active', $coupon->is_active ?? true) ? 'true' : 'false' }}
              }">
            @csrf
            @if(isset($coupon)) @method('PUT') @endif

            <!-- Coupon Code & Type -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Coupon Code *
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code', $coupon->code ?? '') }}"
                           required
                           placeholder="e.g., SUMMER2024"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
                    <p class="mt-1 text-xs text-gray-500">Code will be converted to uppercase</p>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Discount Type *
                    </label>
                    <select id="type" 
                            name="type" 
                            x-model="type"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (RWF)</option>
                        <option value="free_shipping">Free Shipping</option>
                    </select>
                </div>
            </div>

            <!-- Value & Min Order -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div x-show="type !== 'free_shipping'">
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1">
                        Discount Value *
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="value" 
                               name="value" 
                               value="{{ old('value', $coupon->value ?? '') }}"
                               step="0.01"
                               min="0"
                               :required="type !== 'free_shipping'"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span x-text="type === 'percentage' ? '%' : 'RWF'" class="text-gray-500 text-sm"></span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500" x-show="type === 'percentage'">Maximum 100</p>
                </div>

                <div>
                    <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Minimum Order Amount
                    </label>
                    <input type="number" 
                           id="min_order_amount" 
                           name="min_order_amount" 
                           value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}"
                           step="0.01"
                           min="0"
                           placeholder="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Order must be at least this amount to use coupon</p>
                </div>
            </div>

            <!-- Max Discount & Usage Limits -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div x-show="type === 'percentage'">
                    <label for="max_discount" class="block text-sm font-medium text-gray-700 mb-1">
                        Max Discount (RWF)
                    </label>
                    <input type="number" 
                           id="max_discount" 
                           name="max_discount" 
                           value="{{ old('max_discount', $coupon->max_discount ?? '') }}"
                           step="0.01"
                           min="0"
                           placeholder="No limit"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Cap the maximum discount amount</p>
                </div>

                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1">
                        Total Usage Limit
                    </label>
                    <input type="number" 
                           id="usage_limit" 
                           name="usage_limit" 
                           value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                           min="1"
                           placeholder="Unlimited"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Max times coupon can be used</p>
                </div>

                <div>
                    <label for="usage_limit_per_user" class="block text-sm font-medium text-gray-700 mb-1">
                        Usage Per User *
                    </label>
                    <input type="number" 
                           id="usage_limit_per_user" 
                           name="usage_limit_per_user" 
                           value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user ?? 1) }}"
                           min="1"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Max times per customer</p>
                </div>
            </div>

            <!-- Valid Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">
                        Start Date
                    </label>
                    <input type="datetime-local" 
                           id="starts_at" 
                           name="starts_at" 
                           value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">When coupon becomes valid</p>
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">
                        Expiry Date
                    </label>
                    <input type="datetime-local" 
                           id="expires_at" 
                           name="expires_at" 
                           value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">When coupon expires</p>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          maxlength="500"
                          placeholder="Optional description for internal use..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $coupon->description ?? '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Internal note about this coupon (max 500 characters)</p>
            </div>

            <!-- Active Status -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           x-model="isActive"
                           {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-3 block text-sm">
                        <span class="font-medium text-gray-900">Active</span>
                        <span class="text-gray-500 ml-2">- Customers can use this coupon</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.coupons.index') }}" 
                   class="inline-flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    @if(isset($coupon))
                        Update Coupon
                    @else
                        Create Coupon
                    @endif
                </button>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-2">Coupon Tips:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Percentage:</strong> e.g., 20% off the order total</li>
                    <li><strong>Fixed Amount:</strong> e.g., RWF 5000 off the order</li>
                    <li><strong>Free Shipping:</strong> Waives shipping fees</li>
                    <li>Set usage limits to control budget</li>
                    <li>Use start/end dates for time-limited promotions</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
