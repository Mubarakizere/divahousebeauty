{{-- Google Maps Address Form Component --}}

<!-- Google Maps Script - Load at top -->
<script>
window.initGoogleMaps = function() {
    console.log('Google Maps loaded successfully');
};
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initGoogleMaps" async defer></script>

<div x-data="{
    map: null,
    marker: null,
    autocomplete: null,
    latitude: {{ $latitude ?? 'null' }},
    longitude: {{ $longitude ?? 'null' }},
    mapsLoaded: false,
    
    init() {
        // Wait for Google Maps to load
        this.checkMapsLoaded();
    },
    
    checkMapsLoaded() {
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            this.mapsLoaded = true;
            this.initMap();
        } else {
            setTimeout(() => this.checkMapsLoaded(), 100);
        }
    },
    
    initMap() {
        // Initialize map
        const center = this.latitude && this.longitude 
            ? { lat: parseFloat(this.latitude), lng: parseFloat(this.longitude) }
            : { lat: -1.9403, lng: 29.8739 }; // Kigali, Rwanda
            
        this.map = new google.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: this.latitude ? 15 : 12,
            mapTypeControl: false,
        });
        
        // Add marker if we have coordinates
        if (this.latitude && this.longitude) {
            this.addMarker(center);
        }
        
        // Initialize autocomplete
        const input = document.getElementById('autocomplete-input');
        this.autocomplete = new google.maps.places.Autocomplete(input, {
            componentRestrictions: { country: 'rw' }, // Restrict to Rwanda
            fields: ['address_components', 'geometry', 'name', 'formatted_address', 'place_id']
        });
        
        this.autocomplete.addListener('place_changed', () => {
            const place = this.autocomplete.getPlace();
            
            if (!place.geometry || !place.geometry.location) {
                return;
            }
            
            this.updateLocation(place);
        });
        
        // Allow clicking on map to set location
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
        
        document.getElementById('latitude').value = this.latitude;
        document.getElementById('longitude').value = this.longitude;
        document.getElementById('google_place_id').value = place.place_id || '';
        document.getElementById('formatted_address').value = place.formatted_address || '';
        
        // Update form fields
        this.fillFormFromPlace(place);
        
        // Update map
        this.map.setCenter(location);
        this.map.setZoom(15);
        this.addMarker(location);
    },
    
    fillFormFromPlace(place) {
        const components = place.address_components || [];
        
        let street = '';
        let city = '';
        let state = '';
        let postalCode = '';
        let country = '';
        
        components.forEach(component => {
            const types = component.types;
            
            if (types.includes('street_number')) {
                street = component.long_name + ' ';
            }
            if (types.includes('route')) {
                street += component.long_name;
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
        
        // Set address_line_1 - use street if available, otherwise use place name or formatted address
        const addressLine1 = street || place.name || place.formatted_address || '';
        document.getElementById('address_line_1').value = addressLine1;
        document.getElementById('city').value = city;
        document.getElementById('state').value = state;
        document.getElementById('postal_code').value = postalCode;
        document.getElementById('country').value = country || 'Rwanda';
    },
    
    reverseGeocode(latLng) {
        const geocoder = new google.maps.Geocoder();
        
        geocoder.geocode({ location: latLng }, (results, status) => {
            if (status === 'OK' && results[0]) {
                this.updateLocation(results[0]);
                document.getElementById('autocomplete-input').value = results[0].formatted_address;
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
                    alert('Error getting your location: ' + error.message);
                }
            );
        } else {
            alert('Geolocation is not supported by your browser');
        }
    }
}" 
class="space-y-6">

    <!-- Search with Autocomplete -->
    <div class="space-y-2">
        <label class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider">
            Search Address
        </label>
        <div class="relative">
            <input type="text" 
                   id="autocomplete-input"
                   placeholder="Search for your address..."
                   class="w-full px-4 py-3 pr-12 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
            <button type="button" 
                    @click="getCurrentLocation()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-slate-400 hover:text-[var(--gold)] transition-colors"
                    title="Use current location">
                <i class="la la-crosshairs text-xl"></i>
            </button>
        </div>
        <p class="text-xs text-slate-500">
            <i class="la la-info-circle mr-1"></i>
            Type to search or click the icon to use your current location
        </p>
    </div>

    <!-- Map -->
    <div class="space-y-2">
        <label class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider">
            Or Click on Map to Set Location
        </label>
        <div class="relative bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
            <!-- Loading Indicator -->
            <div x-show="!mapsLoaded" class="absolute inset-0 flex items-center justify-center bg-slate-50/80 backdrop-blur-sm z-10 transition-opacity">
                <div class="text-center">
                    <i class="la la-circle-notch la-spin text-3xl text-[var(--gold)] mb-2"></i>
                    <p class="text-sm font-medium text-slate-600">Loading Map...</p>
                </div>
            </div>
            <!-- Map Container -->
            <div id="map" class="w-full h-80"></div>
        </div>
    </div>

    <!-- Hidden Fields for Coordinates -->
    <input type="hidden" id="latitude" name="latitude" x-model="latitude" value="">
    <input type="hidden" id="longitude" name="longitude" x-model="longitude" value="">
    <input type="hidden" id="google_place_id" name="google_place_id" value="">
    <input type="hidden" id="formatted_address" name="formatted_address" value="">

    <!-- Address Form Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label for="address_line_1" class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider mb-2">
                Street Address <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="address_line_1" 
                   name="address_line_1" 
                   value="{{ old('address_line_1', $address->address_line_1 ?? '') }}"
                   placeholder="e.g., KN 5 Ave"
                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
        </div>

        <div class="md:col-span-2">
            <label for="address_line_2" class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider mb-2">
                Apartment, Suite, etc. <span class="text-slate-400 font-normal normal-case">(Optional)</span>
            </label>
            <input type="text" 
                   id="address_line_2" 
                   name="address_line_2" 
                   value="{{ old('address_line_2', $address->address_line_2 ?? '') }}"
                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
        </div>

        <div>
            <label for="city" class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider mb-2">
                City <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="city" 
                   name="city" 
                   value="{{ old('city', $address->city ?? '') }}"
                   placeholder="e.g., Kigali"
                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
        </div>

        <div>
            <label for="state" class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider mb-2">
                State/Province
            </label>
            <input type="text" 
                   id="state" 
                   name="state" 
                   value="{{ old('state', $address->state ?? '') }}"
                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
        </div>

        <div>
            <label for="postal_code" class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider mb-2">
                Postal Code
            </label>
            <input type="text" 
                   id="postal_code" 
                   name="postal_code" 
                   value="{{ old('postal_code', $address->postal_code ?? '') }}"
                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
        </div>

        <div>
            <label for="country" class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider mb-2">
                Country <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="country" 
                   name="country" 
                   value="{{ old('country', $address->country ?? 'Rwanda') }}"
                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
        </div>
    </div>

    <!-- Name and Phone -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
        <div>
            <label for="name" class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider mb-2">
                Full Name <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $address->name ?? auth()->user()->name) }}"
                   placeholder="Full name for delivery"
                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
        </div>

        <div>
            <label for="phone" class="block text-sm font-bold text-[var(--black)] uppercase tracking-wider mb-2">
                Phone Number <span class="text-red-500">*</span>
            </label>
            <input type="tel" 
                   id="phone" 
                   name="phone" 
                   value="{{ old('phone', $address->phone ?? '') }}"
                   placeholder="+250 XXX XXX XXX"
                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] placeholder-slate-400 text-slate-700 transition-all">
        </div>
    </div>

    <!-- Set as Default -->
    <div class="flex items-center pt-2">
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" 
                   id="is_default" 
                   name="is_default" 
                   value="1"
                   {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}
                   class="form-checkbox h-5 w-5 text-[var(--gold)] border-slate-300 rounded focus:ring-[var(--gold)] transition-colors">
            <span class="ml-2 text-sm text-slate-700">Set as default address</span>
        </label>
    </div>
</div>
