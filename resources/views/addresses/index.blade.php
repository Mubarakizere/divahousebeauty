@extends('layouts.store')

@section('title', 'My Addresses')
@section('subtitle', 'Manage your delivery locations.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-[var(--gold)] transition-colors">
            <i class="la la-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>

    {{-- Header & Add Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-[var(--black)]">Saved Addresses</h2>
        </div>
        <a href="{{ route('address.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-[var(--black)] text-white rounded text-sm font-medium hover:bg-slate-800 transition-colors shadow-sm">
            <i class="la la-plus mr-2 text-lg"></i>
            Add New Address
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6 flex items-center text-sm">
            <i class="la la-check-circle mr-2 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Addresses List --}}
    @if($addresses->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ 
            deleteModalOpen: false, 
            deleteAction: '', 
            addressLabel: '',
            openDeleteModal(action, label) {
                this.deleteAction = action;
                this.addressLabel = label;
                this.deleteModalOpen = true;
            }
        }">
            @foreach($addresses as $address)
                <div class="bg-white border {{ $address->is_default ? 'border-[var(--gold)] ring-1 ring-[var(--gold)]/20' : 'border-slate-200' }} rounded-lg p-6 shadow-ring hover:shadow-md transition-all relative group">
                    
                    @if($address->is_default)
                        <div class="absolute top-4 right-4">
                            <span class="badge bg-[var(--gold)] text-white">Default</span>
                        </div>
                    @endif

                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                             @if($address->type == 'home')
                                <i class="la la-home text-lg text-slate-400"></i>
                            @elseif($address->type == 'work')
                                <i class="la la-briefcase text-lg text-slate-400"></i>
                            @else
                                <i class="la la-map-marker text-lg text-slate-400"></i>
                            @endif
                            <h3 class="font-bold text-[var(--black)] text-lg">{{ $address->label ?? 'Address' }}</h3>
                        </div>
                        
                        <div class="text-slate-600 text-sm leading-relaxed space-y-1">
                            @if($address->recipient_name)
                                <p class="font-medium text-slate-800">{{ $address->recipient_name }}</p>
                            @endif
                            <p>{{ $address->address_line_1 }}</p>
                            @if($address->address_line_2)
                                <p>{{ $address->address_line_2 }}</p>
                            @endif
                            <p>
                                {{ $address->city }}
                                @if($address->state), {{ $address->state }}@endif
                                @if($address->postal_code) - {{ $address->postal_code }}@endif
                            </p>
                            <p>{{ $address->country }}</p>
                            @if($address->phone)
                                <p class="pt-2 text-slate-500"><i class="la la-phone mr-1"></i> {{ $address->phone }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 text-sm">
                        <div class="flex gap-3">
                            <a href="{{ route('address.edit', $address) }}" class="text-slate-500 hover:text-[var(--gold)] font-medium">Edit</a>
                            
                            <button type="button" 
                                    @click="openDeleteModal('{{ route('address.destroy', $address) }}', '{{ addslashes($address->label ?? 'this address') }}')"
                                    class="text-red-500 hover:text-red-700 font-medium bg-transparent border-0 p-0 cursor-pointer">
                                Delete
                            </button>
                        </div>

                        @if(!$address->is_default)
                            <form action="{{ route('address.update', $address) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="set_default" value="1">
                                <button type="submit" class="text-[var(--gold)] hover:text-[#B08D4C] font-medium bg-transparent border-0 p-0 cursor-pointer">Set Default</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Custom Delete Modal --}}
            <div x-show="deleteModalOpen" 
                 class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0" 
                 style="display: none;">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 transform transition-all" 
                     @click="deleteModalOpen = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                {{-- Modal Content --}}
                <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                <i class="la la-exclamation-triangle text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-[var(--black)]">Delete Address?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">
                                        Are you sure you want to delete "<span x-text="addressLabel" class="font-medium text-slate-700"></span>"? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <form :action="deleteAction" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Delete
                            </button>
                        </form>
                        <button type="button" 
                                @click="deleteModalOpen = false"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-base font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[var(--gold)] focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    @else
        {{-- Empty State --}}
        <div class="text-center py-16 bg-white rounded-lg border border-slate-100">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="la la-map-marked text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-[var(--black)] mb-2">No addresses saved</h3>
            <p class="text-slate-500 mb-6">Add your delivery addresses to speed up checkout.</p>
            <a href="{{ route('address.create') }}" 
               class="btn-gold inline-flex items-center px-6 py-2 rounded text-sm font-medium">
                Add Address
            </a>
        </div>
    @endif
</div>
@endsection