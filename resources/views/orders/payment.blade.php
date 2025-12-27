<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Diva House Beauty — Checkout</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind (preflight OFF) + Alpine --}}
  <script> tailwind = { config: { corePlugins: { preflight: false } } } </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Line Awesome Icons --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>

  {{-- Google Maps API --}}
  <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=Function.prototype" async defer></script>

  <style>
    :root { --gold:#cc9966; --black:#111827; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
    [x-cloak]{ display:none!important }
  </style>
</head>

@php
  // ---------- SAFETY FALLBACKS FOR HEADER ----------
  $categories = (isset($categories) && $categories instanceof \Illuminate\Support\Collection)
      ? $categories->loadMissing('brands')
      : \App\Models\Category::with('brands')->get();

  $userId    = auth()->id();
  $cartItems = isset($cartItems)
      ? ($cartItems instanceof \Illuminate\Support\Collection ? $cartItems : collect($cartItems))
      : collect();

  if ($cartItems->isEmpty() && $userId) {
      $cartItems = \App\Models\Cart::where('users_id',$userId)->with('product')->get();
  }

  $count = isset($count) ? (int)$count : ($userId ? \App\Models\Cart::where('users_id',$userId)->count() : 0);
@endphp

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen: {{ $errors->has('email') || $errors->has('password') || session('auth_error') ? 'true':'false' }}, authTab: '{{ old('_tab','signin') }}' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- ===================== HEADER (Tailwind partial) ===================== --}}
  @include('partials.header_home2')

  {{-- ===================== HERO ===================== --}}
  <header class="bg-white border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Checkout</h1>
      <p class="mt-1 text-sm text-slate-500">Enter your details and pick a payment method to complete your order.</p>
      <nav class="mt-2 text-[12px] text-slate-500">
        <a class="hover:text-[var(--gold)]" href="{{ route('home') }}">Home</a>
        <span class="mx-1">/</span>
        <a class="hover:text-[var(--gold)]" href="{{ route('category') }}">Shop</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700 font-medium">Checkout</span>
      </nav>
    </div>
  </header>

  {{-- ===================== CONTENT ===================== --}}
  <main class="py-6">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="grid grid-cols-12 gap-4 lg:gap-6">

        {{-- ========== LEFT: FORM ========== --}}
        <section class="col-span-12 lg:col-span-8">
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4 sm:p-6"
               x-data="{ loading:false, showAddressModal: false }">
            <h2 class="text-lg font-semibold text-slate-900">Complete Your Order</h2>

            {{-- Alerts from session --}}
            @if(session('success'))
              <div class="mt-3 rounded-md border border-green-200 bg-green-50 text-green-700 px-3 py-2 text-sm">
                {{ session('success') }}
              </div>
            @endif
            @if(session('error'))
              <div class="mt-3 rounded-md border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm">
                {{ session('error') }}
              </div>
            @endif

            <form action="{{ route('payment.initiate') }}" method="POST" class="mt-4 space-y-6"
                  @submit="loading=true">
              @csrf
              <input type="hidden" name="order_id" value="{{ $order->id }}"/>

              {{-- Customer Information --}}
              <section>
                <h3 class="text-sm font-semibold text-slate-800">Customer Information</h3>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label for="name" class="block text-[13px] font-medium text-slate-700">Full Name *</label>
                    <input id="name" name="name" type="text" required
                           value="{{ old('name', auth()->user()->name ?? '') }}"
                           class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20"/>
                    @error('name')
                      <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                  </div>
                  <div>
                    <label for="email" class="block text-[13px] font-medium text-slate-700">Email Address *</label>
                    <input id="email" name="email" type="email" required
                           value="{{ old('email', auth()->user()->email ?? '') }}"
                           class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20"/>
                    @error('email')
                      <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                  </div>
                </div>

                <div class="mt-3">
                  <label for="phone" class="block text-[13px] font-medium text-slate-700">Phone Number *</label>
                  <input id="phone" name="phone" type="tel" required
                         placeholder="+2507XXXXXXXX or 07XXXXXXXX"
                         value="{{ old('phone', auth()->user()->phone ?? '') }}"
                         pattern="^(\+?250)?\s?7[2389]\d{7}$|^0?7[2389]\d{7}$"
                         class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20"/>
                  <p class="mt-1 text-[12px] text-slate-500">Use your MoMo/Airtel Money number.</p>
                  @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                  @enderror
                </div>
              </section>

              {{-- Shipping Address --}}
              <section>
                <h3 class="text-sm font-semibold text-slate-800">Shipping Address *</h3>
                
                @if($addresses->count() > 0)
                  <div class="mt-3 space-y-2">
                    @foreach($addresses as $address)
                      <label class="relative flex gap-3 rounded-lg border border-slate-300 bg-white p-3 cursor-pointer hover:border-[var(--gold)] hover:bg-slate-50">
                        <input type="radio" name="address_id" value="{{ $address->id }}"
                               class="sr-only peer"
                               {{ $loop->first && $address->is_default ? 'checked' : '' }}>
                        <span class="grid place-items-center rounded-full h-5 w-5 border border-slate-300 text-white peer-checked:bg-[var(--gold)] peer-checked:border-[var(--gold)] flex-shrink-0 mt-0.5">
                          <i class="la la-check text-xs"></i>
                        </span>
                        <div class="flex-1 text-sm">
                          <div class="font-semibold text-slate-900 flex items-center gap-2">
                            {{ $address->name }}
                            @if($address->is_default)
                              <span class="px-1.5 py-0.5 text-[10px] bg-[var(--gold)] text-white rounded">DEFAULT</span>
                            @endif
                          </div>
                          <div class="mt-1 text-slate-600">
                            {{ $address->address_line_1 }}
                            @if($address->address_line_2), {{ $address->address_line_2 }}@endif
                          </div>
                          <div class="text-slate-600">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</div>
                          <div class="text-slate-500">{{ $address->phone }}</div>
                        </div>
                      </label>
                    @endforeach
                  </div>
                @else
                  <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                    <i class="la la-exclamation-triangle mr-1"></i>
                    No saved addresses. Please add a shipping address.
                  </div>
                @endif

                <button type="button" @click="showAddressModal = true"
                        class="mt-3 inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50">
                  <i class="la la-plus"></i>
                  Add New Address
                </button>
                
                @error('address_id')
                  <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

              </section>

              {{-- Payment Method --}}
              <section>
                <h3 class="text-sm font-semibold text-slate-800">Select Payment Method *</h3>
                <div class="mt-3 grid gap-3">
                  {{-- MoMo --}}
                  <label class="relative flex items-center gap-3 rounded-lg border border-slate-300 bg-white p-3 cursor-pointer hover:border-[var(--gold)] hover:bg-slate-50">
                    <input type="radio" name="payment_method" value="momo"
                           class="sr-only peer"
                           {{ old('payment_method', 'momo') === 'momo' ? 'checked' : '' }}>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-[var(--gold)]">
                      <i class="la la-mobile-alt text-xl"></i>
                    </span>
                    <span class="flex-1">
                      <span class="block text-sm font-semibold text-slate-900">Mobile Money</span>
                      <span class="block text-[12px] text-slate-500">Pay with MTN MoMo or Airtel Money</span>
                    </span>
                    <span class="grid place-items-center rounded-full h-6 w-6 border border-slate-300 text-white peer-checked:bg-[var(--gold)] peer-checked:border-[var(--gold)]">
                      <i class="la la-check text-xs"></i>
                    </span>
                  </label>

                  {{-- Card --}}
                </div>
                @error('payment_method')
                  <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </section>

              {{-- Submit --}}
              <div class="pt-2">
                <button type="submit"
                        :disabled="loading"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-[var(--gold)] px-4 py-3 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-60">
                  <template x-if="!loading">
                    <span class="inline-flex items-center gap-2">
                      <i class="la la-lock"></i> PROCEED TO PAYMENT
                    </span>
                  </template>
                  <template x-if="loading">
                    <span class="inline-flex items-center gap-2">
                      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                      </svg>
                      Processing...
                    </span>
                  </template>
                </button>
              </div>
            </form>

            {{-- Address Modal (Moved outside form) --}}
            <div x-show="showAddressModal" x-cloak
                 x-data="addressModalForm()"
                 x-init="initGoogleMaps()"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 @keydown.escape.window="showAddressModal = false">
              <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50" @click="showAddressModal = false"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                  <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                    <h3 class="text-lg font-semibold text-gray-900">
                      <i class="la la-plus-circle text-green-600 mr-2"></i>
                      Add New Address
                    </h3>
                    <button type="button" @click="showAddressModal = false" 
                            class="text-gray-400 hover:text-gray-600">
                      <i class="la la-times text-2xl"></i>
                    </button>
                  </div>
                  
                  <div class="p-6">
                    <form @submit.prevent="submitAddress()" class="space-y-6">
                      @csrf
                      
                      {{-- Error Display --}}
                      <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <i class="la la-exclamation-circle mr-1"></i>
                        <span x-text="error"></span>
                      </div>
                      
                      {{-- Success Display --}}
                      <div x-show="success" x-cloak class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        <i class="la la-check-circle mr-1"></i>
                        <span x-text="success"></span>
                      </div>

                      {{-- Google Maps Search --}}
                      <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                          <i class="la la-search text-[var(--gold)] mr-1"></i>
                          Search Your Address
                        </label>
                        <div class="relative">
                          <input type="text" 
                                 id="modal-autocomplete-input"
                                 x-ref="autocompleteInput"
                                 placeholder="Start typing your address..."
                                 class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-sm">
                          <button type="button" 
                                  @click="getCurrentLocation()"
                                  class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-[var(--gold)] transition-colors"
                                  title="Use my current location">
                            <i class="la la-crosshairs text-lg"></i>
                          </button>
                        </div>
                        <p class="text-xs text-gray-500">
                          <i class="la la-info-circle mr-1"></i>
                          Type to search or click the location icon to use your current location
                        </p>
                      </div>

                      {{-- Map Container --}}
                      <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                          <i class="la la-map text-[var(--gold)] mr-1"></i>
                          Pin Your Location
                        </label>
                        <div class="relative">
                          <div x-show="!mapsLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-100 rounded-lg z-10">
                            <div class="text-center">
                              <i class="la la-spinner la-spin text-3xl text-[var(--gold)] mb-2"></i>
                              <p class="text-sm text-gray-600">Loading map...</p>
                            </div>
                          </div>
                          <div id="modal-map" class="w-full h-64 rounded-lg border-2 border-gray-200 bg-gray-100"></div>
                        </div>
                        <p class="text-xs text-gray-500">Click on the map to set your exact location</p>
                      </div>

                      {{-- Hidden Fields for Coordinates --}}
                      <input type="hidden" name="latitude" x-model="latitude">
                      <input type="hidden" name="longitude" x-model="longitude">
                      <input type="hidden" name="google_place_id" x-model="googlePlaceId">
                      <input type="hidden" name="formatted_address" x-model="formattedAddress">

                      {{-- Address Form Fields --}}
                      <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                          <i class="la la-map-marker text-red-500 mr-2"></i>
                          Address Details
                        </h4>
                        
                        <div class="space-y-4">
                          {{-- Street Address (address_line_1) --}}
                          <div>
                            <label for="modal_address_line_1" class="block text-sm font-medium text-gray-700 mb-1">
                              Street Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="address_line_1" id="modal_address_line_1"
                                   x-model="addressLine1"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)]"
                                   placeholder="e.g., KN 5 Ave, House 123">
                          </div>

                          {{-- Address Line 2 (Optional) --}}
                          <div>
                            <label for="modal_address_line_2" class="block text-sm font-medium text-gray-700 mb-1">
                              Apartment, Suite, etc. (Optional)
                            </label>
                            <input type="text" name="address_line_2" id="modal_address_line_2"
                                   x-model="addressLine2"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)]"
                                   placeholder="Apt 4B, Floor 2, etc.">
                          </div>

                          {{-- City and State --}}
                          <div class="grid grid-cols-2 gap-4">
                            <div>
                              <label for="modal_city" class="block text-sm font-medium text-gray-700 mb-1">
                                City <span class="text-red-500">*</span>
                              </label>
                              <input type="text" name="city" id="modal_city"
                                     x-model="city"
                                     class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)]"
                                     placeholder="e.g., Kigali">
                            </div>

                            <div>
                              <label for="modal_state" class="block text-sm font-medium text-gray-700 mb-1">
                                Province/State
                              </label>
                              <input type="text" name="state" id="modal_state"
                                     x-model="state"
                                     class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)]"
                                     placeholder="e.g., Kigali City">
                            </div>
                          </div>

                          {{-- Postal Code and Country --}}
                          <div class="grid grid-cols-2 gap-4">
                            <div>
                              <label for="modal_postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                                Postal Code
                              </label>
                              <input type="text" name="postal_code" id="modal_postal_code"
                                     x-model="postalCode"
                                     class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)]"
                                     placeholder="Optional">
                            </div>

                            <div>
                              <label for="modal_country" class="block text-sm font-medium text-gray-700 mb-1">
                                Country <span class="text-red-500">*</span>
                              </label>
                              <input type="text" name="country" id="modal_country"
                                     x-model="country"
                                     class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)]">
                            </div>
                          </div>
                        </div>
                      </div>

                      {{-- Contact Information --}}
                      <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                          <i class="la la-user text-blue-500 mr-2"></i>
                          Contact Information
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                          <div>
                            <label for="modal_name" class="block text-sm font-medium text-gray-700 mb-1">
                              Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="modal_name"
                                   x-model="name"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)]"
                                   placeholder="Full name for delivery">
                          </div>

                          <div>
                            <label for="modal_phone" class="block text-sm font-medium text-gray-700 mb-1">
                              Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" id="modal_phone"
                                   x-model="phone"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--gold)] focus:border-[var(--gold)]"
                                   placeholder="+250 7XX XXX XXX">
                          </div>
                        </div>
                      </div>

                      {{-- Default Address Checkbox --}}
                      <div class="flex items-center">
                        <input type="checkbox" name="is_default" id="modal_is_default" value="1"
                               x-model="isDefault"
                               class="w-4 h-4 text-[var(--gold)] bg-gray-100 border-gray-300 rounded focus:ring-[var(--gold)]">
                        <label for="modal_is_default" class="ml-2 text-sm text-gray-700">
                          Set as default shipping address
                        </label>
                      </div>
                      
                      {{-- Action Buttons --}}
                      <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" @click="showAddressModal = false"
                                :disabled="submitting"
                                class="inline-flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50">
                          <i class="la la-times mr-2"></i>
                          Cancel
                        </button>
                        <button type="submit"
                                :disabled="submitting"
                                class="inline-flex items-center px-6 py-2 bg-[var(--gold)] text-white rounded-lg hover:opacity-90 focus:ring-4 focus:ring-[var(--gold)]/30 transition-colors disabled:opacity-50">
                          <template x-if="!submitting">
                            <i class="la la-save mr-2"></i>
                          </template>
                          <template x-if="submitting">
                            <svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                          </template>
                          <span x-text="submitting ? 'Saving...' : 'Save Address'"></span>
                        </button>
                      </div>
                    </form>
                 </div>
                </div>
              </div>
            </div>
            
            {{-- Address Modal Alpine.js Component Script --}}
            <script>
              function addressModalForm() {
                return {
                  // Form state
                  submitting: false,
                  error: null,
                  success: null,
                  
                  // Google Maps
                  map: null,
                  marker: null,
                  autocomplete: null,
                  mapsLoaded: false,
                  
                  // Form fields
                  latitude: null,
                  longitude: null,
                  googlePlaceId: '',
                  formattedAddress: '',
                  addressLine1: '',
                  addressLine2: '',
                  city: '',
                  state: '',
                  postalCode: '',
                  country: 'Rwanda',
                  name: '{{ auth()->user()->name ?? "" }}',
                  phone: '{{ auth()->user()->phone ?? "" }}',
                  isDefault: false,
                  
                  initGoogleMaps() {
                    // Check if Google Maps is already loaded
                    this.checkMapsLoaded();
                  },
                  
                  checkMapsLoaded() {
                    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                      this.mapsLoaded = true;
                      this.$nextTick(() => this.initMap());
                    } else {
                      setTimeout(() => this.checkMapsLoaded(), 100);
                    }
                  },
                  
                  initMap() {
                    const mapElement = document.getElementById('modal-map');
                    if (!mapElement) return;
                    
                    // Default center: Kigali, Rwanda
                    const center = { lat: -1.9403, lng: 29.8739 };
                    
                    this.map = new google.maps.Map(mapElement, {
                      center: center,
                      zoom: 12,
                      mapTypeControl: false,
                      streetViewControl: false,
                      fullscreenControl: false,
                    });
                    
                    // Initialize autocomplete
                    const input = document.getElementById('modal-autocomplete-input');
                    if (input) {
                      this.autocomplete = new google.maps.places.Autocomplete(input, {
                        componentRestrictions: { country: 'rw' },
                        fields: ['address_components', 'geometry', 'name', 'formatted_address', 'place_id']
                      });
                      
                      this.autocomplete.addListener('place_changed', () => {
                        const place = this.autocomplete.getPlace();
                        if (place.geometry && place.geometry.location) {
                          this.updateLocation(place);
                        }
                      });
                    }
                    
                    // Click on map to set location
                    this.map.addListener('click', (event) => {
                      this.reverseGeocode(event.latLng);
                    });
                  },
                  
                  addMarker(location) {
                    if (this.marker) {
                      this.marker.setMap(null);
                    }
                    
                    this.marker = new google.maps.Marker({
                      position: location,
                      map: this.map,
                      animation: google.maps.Animation.DROP,
                    });
                  },
                  
                  updateLocation(place) {
                    const location = place.geometry.location;
                    
                    this.latitude = location.lat();
                    this.longitude = location.lng();
                    this.googlePlaceId = place.place_id || '';
                    this.formattedAddress = place.formatted_address || '';
                    
                    // Parse address components
                    this.fillFormFromPlace(place);
                    
                    // Update map
                    this.map.setCenter(location);
                    this.map.setZoom(15);
                    this.addMarker(location);
                  },
                  
                  fillFormFromPlace(place) {
                    const components = place.address_components || [];
                    
                    let streetNumber = '';
                    let route = '';
                    let city = '';
                    let state = '';
                    let postalCode = '';
                    let country = '';
                    
                    components.forEach(component => {
                      const types = component.types;
                      
                      if (types.includes('street_number')) {
                        streetNumber = component.long_name;
                      }
                      if (types.includes('route')) {
                        route = component.long_name;
                      }
                      if (types.includes('locality') || types.includes('administrative_area_level_2')) {
                        city = component.long_name;
                      }
                      if (types.includes('administrative_area_level_1')) {
                        state = component.long_name;
                      }
                      if (types.includes('postal_code')) {
                        postalCode = component.long_name;
                      }
                      if (types.includes('country')) {
                        country = component.long_name;
                      }
                    });
                    
                    // Build address line 1
                    let addressLine = '';
                    if (streetNumber && route) {
                      addressLine = streetNumber + ' ' + route;
                    } else if (route) {
                      addressLine = route;
                    } else if (place.name) {
                      addressLine = place.name;
                    } else if (place.formatted_address) {
                      addressLine = place.formatted_address.split(',')[0];
                    }
                    
                    this.addressLine1 = addressLine;
                    this.city = city;
                    this.state = state;
                    this.postalCode = postalCode;
                    this.country = country || 'Rwanda';
                  },
                  
                  reverseGeocode(latLng) {
                    const geocoder = new google.maps.Geocoder();
                    
                    geocoder.geocode({ location: latLng }, (results, status) => {
                      if (status === 'OK' && results[0]) {
                        this.updateLocation(results[0]);
                        const input = document.getElementById('modal-autocomplete-input');
                        if (input) {
                          input.value = results[0].formatted_address;
                        }
                      }
                    });
                  },
                  
                  getCurrentLocation() {
                    if (navigator.geolocation) {
                      navigator.geolocation.getCurrentPosition(
                        (position) => {
                          const latLng = new google.maps.LatLng(
                            position.coords.latitude,
                            position.coords.longitude
                          );
                          this.reverseGeocode(latLng);
                        },
                        (error) => {
                          this.error = 'Could not get your location: ' + error.message;
                        }
                      );
                    } else {
                      this.error = 'Geolocation is not supported by your browser';
                    }
                  },
                  
                  async submitAddress() {
                    this.submitting = true;
                    this.error = null;
                    this.success = null;
                    
                    // Validate required fields
                    if (!this.addressLine1 || !this.city || !this.country || !this.name || !this.phone) {
                      this.error = 'Please fill in all required fields (Street Address, City, Country, Name, and Phone)';
                      this.submitting = false;
                      return;
                    }
                    
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('address_line_1', this.addressLine1);
                    formData.append('address_line_2', this.addressLine2);
                    formData.append('city', this.city);
                    formData.append('state', this.state);
                    formData.append('postal_code', this.postalCode);
                    formData.append('country', this.country);
                    formData.append('name', this.name);
                    formData.append('phone', this.phone);
                    formData.append('latitude', this.latitude || '');
                    formData.append('longitude', this.longitude || '');
                    formData.append('google_place_id', this.googlePlaceId);
                    formData.append('formatted_address', this.formattedAddress);
                    if (this.isDefault) {
                      formData.append('is_default', '1');
                    }
                    
                    try {
                      const response = await fetch('{{ route("address.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                          'X-Requested-With': 'XMLHttpRequest',
                          'Accept': 'application/json'
                        }
                      });
                      
                      if (response.ok) {
                        this.success = 'Address saved successfully! Refreshing...';
                        setTimeout(() => {
                          window.location.reload();
                        }, 1000);
                      } else {
                        const data = await response.json();
                        if (data.errors) {
                          const errorMessages = Object.values(data.errors).flat().join(', ');
                          this.error = errorMessages || 'Please check the form for errors';
                        } else {
                          this.error = data.message || 'Failed to save address. Please try again.';
                        }
                        this.submitting = false;
                      }
                    } catch (err) {
                      console.error('Address save error:', err);
                      this.error = 'An error occurred. Please try again.';
                      this.submitting = false;
                    }
                  }
                };
              }
            </script>
          </div>
        </section>

        {{-- ========== RIGHT: ORDER SUMMARY ========== --}}
        <aside class="col-span-12 lg:col-span-4">
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4 sm:p-6 lg:sticky lg:top-6">
            <h3 class="text-lg font-semibold text-slate-900">Order Summary</h3>

            <div class="mt-3 text-sm text-slate-600 flex items-center justify-between">
              <div>
                <div class="font-medium text-slate-800">Order #{{ $order->id }}</div>
                <div class="text-[12px] text-slate-500">{{ $order->created_at->format('M d, Y • H:i') }}</div>
              </div>
            </div>

            @if($order->items && $order->items->count())
              <div class="mt-4">
                <h4 class="text-sm font-semibold text-slate-800">Items</h4>
                <div class="mt-2 divide-y divide-slate-200">
                  @foreach($order->items->take(3) as $item)
                    @php
                      $p = $item->product;
                      $img = $p->images ?? [];
                      if (is_array($img)) { $first = $img[0] ?? 'default.jpg'; }
                      else { $arr = json_decode($img, true); $first = $arr[0] ?? 'default.jpg'; }
                      $imgUrl = asset('storage/'.$first);
                    @endphp
                    <div class="py-2 flex items-center gap-3">
                      <img src="{{ $imgUrl }}" class="h-12 w-12 rounded-md border border-slate-200 object-cover" alt="">
                      <div class="flex-1">
                        <div class="text-[13px] font-medium text-slate-900 line-clamp-2">{{ $p->name ?? 'Product' }}</div>
                        <div class="text-[12px] text-slate-500">Qty: {{ $item->quantity ?? 1 }}</div>
                      </div>
                      <div class="text-[13px] font-semibold text-slate-900">
                        RWF {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 0) }}
                      </div>
                    </div>
                  @endforeach
                </div>

                @if($order->items->count() > 3)
                  <details class="mt-2">
                    <summary class="text-[12px] text-[var(--gold)] hover:underline cursor-pointer">
                      + {{ $order->items->count() - 3 }} more items
                    </summary>
                    <div class="mt-2 divide-y divide-slate-200">
                      @foreach($order->items->slice(3) as $item)
                        @php
                          $p = $item->product;
                          $img = $p->images ?? [];
                          if (is_array($img)) { $first = $img[0] ?? 'default.jpg'; }
                          else { $arr = json_decode($img, true); $first = $arr[0] ?? 'default.jpg'; }
                          $imgUrl = asset('storage/'.$first);
                        @endphp
                        <div class="py-2 flex items-center gap-3">
                          <img src="{{ $imgUrl }}" class="h-10 w-10 rounded-md border border-slate-200 object-cover" alt="">
                          <div class="flex-1">
                            <div class="text-[13px] text-slate-800 line-clamp-2">{{ $p->name ?? 'Product' }}</div>
                            <div class="text-[12px] text-slate-500">Qty: {{ $item->quantity ?? 1 }}</div>
                          </div>
                          <div class="text-[12px] font-semibold text-slate-900">
                            RWF {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 0) }}
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </details>
                @endif
              </div>
            @endif

            {{-- Order Totals --}}
            <div class="mt-4 space-y-2 text-sm">
              @php
                $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
                $total = $subtotal - ($discount ?? 0);
              @endphp
              
              <div class="flex items-center justify-between">
                <span class="text-slate-600">Subtotal</span>
                <span class="font-semibold text-slate-900">RWF {{ number_format($subtotal, 0) }}</span>
              </div>

              @if(isset($appliedCoupon) && $appliedCoupon && $discount > 0)
                <div class="flex items-center justify-between text-green-600">
                  <div class="flex items-center gap-2">
                    <i class="la la-tag"></i>
                    <span>Discount ({{ $appliedCoupon->code }})</span>
                  </div>
                  <span class="font-semibold">-RWF {{ number_format($discount, 0) }}</span>
                </div>
              @endif

              <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                <span class="text-slate-900 font-semibold">Total Amount</span>
                <span class="text-lg font-extrabold text-[var(--gold)]">RWF {{ number_format($total, 0) }}</span>
              </div>
            </div>

            <div class="mt-4 flex items-start gap-3 rounded-lg border border-blue-100 bg-blue-50 p-3">
              <div class="h-8 w-8 grid place-items-center rounded-full bg-blue-600 text-white">
                <i class="la la-shield-alt text-lg"></i>
              </div>
              <div>
                <div class="text-sm font-semibold text-slate-900">Secure Payment</div>
                <p class="text-[12px] text-slate-600">Your payment information is encrypted and protected.</p>
              </div>
            </div>
          </div>
        </aside>

      </div>
    </div>
  </main>

  {{-- ===================== FOOTER & AUTH MODAL PARTIALS ===================== --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- Tiny helper to auto-close any .alert after 3s if they appear elsewhere --}}
  <script>
    setTimeout(()=>document.querySelectorAll('.alert').forEach(el=>el.remove()), 3000);
  </script>
</body>
</html>
