@extends('layouts.dashboard')

@section('title', isset($address) ? 'Edit Address' : 'Add Address')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                @if(isset($address))
                    <i class="fas fa-edit text-blue-600 mr-3"></i>
                    Edit Address
                @else
                    <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                    Add New Address
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                @if(isset($address))
                    Update your delivery address information
                @else
                    Add a new delivery address to your account
                @endif
            </p>
        </div>
        <a href="{{ route('address.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Addresses
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
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

    {{-- Form Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ isset($address) ? route('address.update', $address) : route('address.store') }}" 
              method="POST" 
              class="p-6 space-y-6">
            @csrf
            @if(isset($address)) @method('PUT') @endif

            {{-- Address Label & Type Section --}}
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tag text-purple-500 mr-2"></i>
                    Address Label & Type
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Address Label --}}
                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700 mb-2">
                            Address Label <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <input type="text" 
                               name="label" 
                               id="label"
                               class="block w-full px-3 py-2 border {{ $errors->has('label') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('label', $address->label ?? '') }}"
                               placeholder="e.g., Home, Work, Mom's House">
                        <p class="mt-1 text-xs text-gray-500">Give this address a memorable name</p>
                        @error('label')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Address Type --}}
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Address Type <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <select name="type" 
                                id="type"
                                class="block w-full px-3 py-2 border {{ $errors->has('type') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select type</option>
                            <option value="home" {{ old('type', $address->type ?? '') == 'home' ? 'selected' : '' }}>
                                🏠 Home
                            </option>
                            <option value="work" {{ old('type', $address->type ?? '') == 'work' ? 'selected' : '' }}>
                                🏢 Work
                            </option>
                            <option value="other" {{ old('type', $address->type ?? '') == 'other' ? 'selected' : '' }}>
                                📍 Other
                            </option>
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Location Details Section --}}
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                    Location Details
                </h2>
                
                <div class="space-y-6">
                    {{-- Street Address --}}
                    <div>
                        <label for="street" class="block text-sm font-medium text-gray-700 mb-2">
                            Street Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="street" 
                               id="street"
                               class="block w-full px-3 py-2 border {{ $errors->has('street') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('street', $address->street ?? '') }}"
                               required
                               placeholder="Enter street address, house number">
                        @error('street')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- City and District --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="city" 
                                   id="city"
                                   class="block w-full px-3 py-2 border {{ $errors->has('city') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('city', $address->city ?? '') }}"
                                   required
                                   placeholder="Enter city name">
                            @error('city')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700 mb-2">
                                District <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input type="text" 
                                   name="district" 
                                   id="district"
                                   class="block w-full px-3 py-2 border {{ $errors->has('district') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('district', $address->district ?? '') }}"
                                   placeholder="Enter district">
                            @error('district')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Postal Code and Country --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input type="text" 
                                   name="postal_code" 
                                   id="postal_code"
                                   class="block w-full px-3 py-2 border {{ $errors->has('postal_code') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('postal_code', $address->postal_code ?? '') }}"
                                   placeholder="Enter postal code">
                            @error('postal_code')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                Country
                            </label>
                            <input type="text" 
                                   name="country" 
                                   id="country"
                                   class="block w-full px-3 py-2 border {{ $errors->has('country') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('country', $address->country ?? 'Rwanda') }}"
                                   placeholder="Enter country">
                            @error('country')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information Section --}}
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-address-book text-blue-500 mr-2"></i>
                    Contact Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Recipient Name --}}
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Recipient Name <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <input type="text" 
                               name="recipient_name" 
                               id="recipient_name"
                               class="block w-full px-3 py-2 border {{ $errors->has('recipient_name') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('recipient_name', $address->recipient_name ?? '') }}"
                               placeholder="Who will receive deliveries?">
                        <p class="mt-1 text-xs text-gray-500">Leave blank to use your name</p>
                        @error('recipient_name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone"
                               class="block w-full px-3 py-2 border {{ $errors->has('phone') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('phone', $address->phone ?? '') }}"
                               required
                               placeholder="+250 xxx xxx xxx">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Additional Information Section --}}
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>
                    Additional Information
                </h2>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Delivery Notes <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <textarea name="notes" 
                              id="notes"
                              rows="3"
                              class="block w-full px-3 py-2 border {{ $errors->has('notes') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Special delivery instructions, landmarks, gate codes, etc.">{{ old('notes', $address->notes ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Help delivery personnel find your location easily</p>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Address Preferences Section --}}
            <div class="pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cog text-gray-500 mr-2"></i>
                    Address Preferences
                </h2>
                
                <div class="space-y-4">
                    {{-- Default Address Checkbox --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   name="is_default" 
                                   id="is_default"
                                   value="1"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                   {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3">
                            <label for="is_default" class="text-sm font-medium text-gray-700">
                                Set as default address
                            </label>
                            <p class="text-xs text-gray-500">
                                This address will be pre-selected during checkout
                            </p>
                        </div>
                    </div>

                    {{-- Show current default if editing --}}
                    @if(isset($address) && !($address->is_default ?? false))
                        @php
                            $defaultAddress = auth()->user()->addresses()->where('is_default', true)->first();
                        @endphp
                        @if($defaultAddress)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                                    <span class="text-sm text-blue-800">
                                        Current default: <strong>{{ $defaultAddress->label ?: $defaultAddress->street }}</strong>
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-3 space-y-3 sm:space-y-0 pt-6 border-t border-gray-200">
                <a href="{{ route('address.index') }}" 
                   class="inline-flex justify-center items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex justify-center items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i>
                    @if(isset($address))
                        Update Address
                    @else
                        Save Address
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Form Enhancement Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate label from type selection
    const typeSelect = document.getElementById('type');
    const labelInput = document.getElementById('label');
    
    if (typeSelect && labelInput) {
        typeSelect.addEventListener('change', function() {
            if (!labelInput.value && this.value) {
                const typeLabels = {
                    'home': 'Home',
                    'work': 'Work',
                    'other': 'Other'
                };
                labelInput.value = typeLabels[this.value] || '';
            }
        });
    }
    
    // Phone number formatting (simple Rwanda format)
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.startsWith('250')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+25' + value;
            } else if (!value.startsWith('+') && value.length > 0) {
                value = '+250' + value;
            }
            this.value = value;
        });
    }
});
</script>
@endsection