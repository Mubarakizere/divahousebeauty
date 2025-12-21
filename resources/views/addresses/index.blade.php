@extends('layouts.dashboard')

@section('title', 'My Addresses')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-map-marker-alt text-red-500 mr-3"></i>
                My Addresses
            </h1>
            <p class="mt-1 text-sm text-gray-600">Manage your delivery addresses</p>
        </div>
        
        <a href="{{ route('address.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-sm">
            <i class="fas fa-plus mr-2"></i>
            Add New Address
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Addresses List --}}
    @if($addresses->count())
        <div class="space-y-4">
            @foreach($addresses as $index => $address)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                            {{-- Address Details --}}
                            <div class="flex-1 mb-4 lg:mb-0 lg:mr-6">
                                <div class="flex items-start space-x-4">
                                    {{-- Address Icon --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-home text-red-600 text-lg"></i>
                                        </div>
                                    </div>
                                    
                                    {{-- Address Info --}}
                                    <div class="flex-1 min-w-0">
                                        {{-- Address Title --}}
                                        <div class="flex items-center mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 truncate">
                                                {{ $address->label ?? 'Address ' . ($index + 1) }}
                                            </h3>
                                            @if($address->is_default ?? false)
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-star mr-1"></i>
                                                    Default
                                                </span>
                                            @endif
                                        </div>
                                        
                                        {{-- Street Address --}}
                                        <div class="flex items-start mb-2">
                                            <i class="fas fa-map-marker-alt text-gray-400 mt-1 mr-2 flex-shrink-0"></i>
                                            <div>
                                                <p class="text-gray-900 font-medium">{{ $address->street }}</p>
                                                <p class="text-gray-600">
                                                    {{ $address->city }}{{ $address->district ? ', ' . $address->district : '' }}
                                                    @if($address->postal_code), {{ $address->postal_code }}@endif
                                                </p>
                                                @if($address->country)
                                                    <p class="text-gray-500 text-sm">{{ $address->country }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Contact Info --}}
                                        @if($address->phone)
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-phone text-gray-400 mr-2 flex-shrink-0"></i>
                                                <span class="text-gray-600">{{ $address->phone }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($address->recipient_name)
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-user text-gray-400 mr-2 flex-shrink-0"></i>
                                                <span class="text-gray-600">{{ $address->recipient_name }}</span>
                                            </div>
                                        @endif
                                        
                                        {{-- Additional Notes --}}
                                        @if($address->notes)
                                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="flex items-start">
                                                    <i class="fas fa-sticky-note text-gray-400 mt-0.5 mr-2 flex-shrink-0"></i>
                                                    <p class="text-sm text-gray-600 italic">{{ $address->notes }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Address Type Badge --}}
                                        @if($address->type)
                                            <div class="mt-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($address->type == 'home') bg-green-100 text-green-800
                                                    @elseif($address->type == 'work') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    @if($address->type == 'home')
                                                        <i class="fas fa-home mr-1"></i>
                                                    @elseif($address->type == 'work')
                                                        <i class="fas fa-briefcase mr-1"></i>
                                                    @else
                                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                                    @endif
                                                    {{ ucfirst($address->type) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Action Buttons --}}
                            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 lg:flex-col lg:space-x-0 lg:space-y-2">
                                {{-- Set as Default (if not already default) --}}
                                @if(!($address->is_default ?? false))
                                    <form action="{{ route('address.update', $address) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="set_default" value="1">
                                        <button type="submit" 
                                                class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors font-medium text-sm">
                                            <i class="fas fa-star mr-2"></i>
                                            Set Default
                                        </button>
                                    </form>
                                @endif
                                
                                {{-- Edit Button --}}
                                <a href="{{ route('address.edit', $address) }}" 
                                   class="inline-flex items-center justify-center px-3 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors font-medium text-sm">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit
                                </a>
                                
                                {{-- Delete Button --}}
                                <form action="{{ route('address.destroy', $address) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Are you sure you want to delete this address?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors font-medium text-sm">
                                        <i class="fas fa-trash mr-2"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Pagination (if you have it) --}}
        @if(method_exists($addresses, 'hasPages') && $addresses->hasPages())
            <div class="mt-8">
                {{ $addresses->links() }}
            </div>
        @endif
        
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-map-marker-alt text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No addresses saved</h3>
            <p class="text-gray-600 mb-6">
                You haven't added any delivery addresses yet. Add your first address to get started with orders.
            </p>
            <a href="{{ route('address.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-plus mr-2"></i>
                Add Your First Address
            </a>
        </div>
    @endif

    {{-- Quick Add Address Tips --}}
    @if($addresses->count() > 0 && $addresses->count() < 3)
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-lightbulb text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800">Pro Tip</h4>
                    <p class="mt-1 text-sm text-blue-700">
                        Add multiple addresses (home, work, etc.) for faster checkout. You can set one as your default delivery address.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Confirmation Modal Script --}}
<script>
// Enhanced confirmation for address deletion
function confirmDelete(addressLabel) {
    return confirm(`Are you sure you want to delete "${addressLabel}"? This action cannot be undone.`);
}

// Auto-hide success messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s ease-out';
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.remove();
            }, 500);
        }, 5000);
    }
});
</script>
@endsection