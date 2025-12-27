@extends('layouts.store')

@section('title', isset($address) ? 'Edit Address' : 'Add Address')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('address.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-[var(--gold)] transition-colors">
            <i class="la la-arrow-left mr-2"></i>
            Back to Addresses
        </a>
    </div>

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[var(--black)]">
                @if(isset($address))
                    Edit Address
                @else
                    Add New Address
                @endif
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Use Google Maps for precise location.
            </p>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded p-4 text-sm">
            <div class="flex items-start">
                <i class="la la-exclamation-triangle text-red-400 mr-2 text-lg"></i>
                <div>
                    <h3 class="font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-1 text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-ring p-6 md:p-8">
        <form action="{{ isset($address) ? route('address.update', $address) : route('address.store') }}" 
              method="POST" 
              class="space-y-6">
            @csrf
            @if(isset($address)) @method('PUT') @endif

            <!-- Google Maps Address Form Component -->
            @include('components.google-maps-address-form', [
                'address' => $address ?? null,
                'latitude' => old('latitude', $address->latitude ?? null),
                'longitude' => old('longitude', $address->longitude ?? null)
            ])

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('address.index') }}" 
                   class="inline-flex items-center px-6 py-2 border border-slate-300 text-slate-700 rounded text-sm font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="btn-gold inline-flex items-center px-8 py-2 rounded text-sm font-medium">
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
@endsection