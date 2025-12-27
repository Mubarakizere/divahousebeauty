<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    /**
     * Display a listing of user's addresses
     */
    public function index()
    {
        $addresses = auth()->user()->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new address
     */
    public function create()
    {
        return view('addresses.create');
    }

    /**
     * Store a newly created address
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_place_id' => 'nullable|string|max:255',
            'formatted_address' => 'nullable|string',
            'is_default' => 'sometimes|boolean',
        ]);

        // Set default country if not provided
        $validated['country'] = $validated['country'] ?? 'Rwanda';

        // If this is set as default OR user has no addresses, make it default
        $userAddressCount = auth()->user()->addresses()->count();
        if ($request->has('is_default') || $userAddressCount === 0) {
            // Remove default from all existing addresses
            auth()->user()->addresses()->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        // Ensure user_id is set
        $validated['user_id'] = auth()->id();

        // Create the address
        $address = auth()->user()->addresses()->create($validated);

        Log::info('Address created', [
            'user_id' => auth()->id(),
            'address_id' => $address->id,
            'is_default' => $address->is_default
        ]);

        // Return JSON response for AJAX requests (modal form)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Address saved successfully!',
                'address' => $address
            ]);
        }

        return redirect()
            ->route('address.index')
            ->with('success', 'Address saved successfully!');
    }

    /**
     * Show the form for editing an address
     */
    public function edit(Address $address)
    {
        // Debug information
        Log::info('Edit Address Debug', [
            'address_id' => $address->id,
            'address_user_id' => $address->user_id,
            'current_user_id' => auth()->id(),
            'address_exists' => $address->exists,
        ]);

        // Ensure the logged-in user owns this address
        $this->authorizeAddress($address);

        return view('addresses.edit', compact('address'));
    }

    /**
     * Update the specified address
     */
    public function update(Request $request, Address $address)
    {
        Log::info('Update Address Debug', [
            'address_id' => $address->id,
            'address_user_id' => $address->user_id,
            'current_user_id' => auth()->id(),
            'has_set_default' => $request->has('set_default')
        ]);

        $this->authorizeAddress($address);

        // Handle setting address as default (PATCH request)
        if ($request->has('set_default')) {
            return $this->setAsDefault($address);
        }

        // Regular update validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_place_id' => 'nullable|string|max:255',
            'formatted_address' => 'nullable|string',
            'is_default' => 'sometimes|boolean',
        ]);

        // Set default country if not provided
        $validated['country'] = $validated['country'] ?? 'Rwanda';

        // Handle default address logic
        if ($request->has('is_default') && ($validated['is_default'] ?? false)) {
            auth()->user()->addresses()->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            // Ensure we don't accidentally unset default if checkbox wasn't checked
            $validated['is_default'] = $validated['is_default'] ?? false;
        }

        $address->update($validated);

        Log::info('Address updated', [
            'user_id' => auth()->id(),
            'address_id' => $address->id,
            'is_default' => $address->is_default
        ]);

        return redirect()
            ->route('address.index')
            ->with('success', 'Address updated successfully!');
    }

    /**
     * Set an address as the default address
     */
    public function setAsDefault(Address $address)
    {
        $this->authorizeAddress($address);

        // Remove default from all user addresses
        auth()->user()->addresses()->update(['is_default' => false]);
        
        // Set this address as default
        $address->update(['is_default' => true]);

        Log::info('Default address changed', [
            'user_id' => auth()->id(),
            'new_default_address_id' => $address->id
        ]);

        return redirect()
            ->route('address.index')
            ->with('success', 'Default address updated successfully!');
    }

    /**
     * Remove the specified address
     */
    public function destroy(Address $address)
    {
        $this->authorizeAddress($address);

        $wasDefault = $address->is_default;
        $addressLabel = $address->label ?: 'Address';
        $addressId = $address->id;
        
        $address->delete();

        // If deleted address was default, set another address as default
        if ($wasDefault) {
            $nextAddress = auth()->user()->addresses()->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
                Log::info('New default address set after deletion', [
                    'user_id' => auth()->id(),
                    'deleted_address_id' => $addressId,
                    'new_default_address_id' => $nextAddress->id
                ]);
            }
        }

        Log::info('Address deleted', [
            'user_id' => auth()->id(),
            'deleted_address_id' => $addressId,
            'was_default' => $wasDefault
        ]);

        return redirect()
            ->route('address.index')
            ->with('success', $addressLabel . ' deleted successfully!');
    }

    /**
     * Get user's default address (API endpoint)
     */
    public function getDefault()
    {
        $defaultAddress = auth()->user()->addresses()
            ->where('is_default', true)
            ->first();

        if (!$defaultAddress) {
            $defaultAddress = auth()->user()->addresses()->first();
        }

        return response()->json($defaultAddress);
    }

    /**
     * Get all user addresses for checkout/order (API endpoint)
     */
    public function getAddressesForCheckout()
    {
        $addresses = auth()->user()->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($addresses);
    }

    /**
     * Validate address ownership with proper error handling
     */
    private function authorizeAddress(Address $address)
    {
        // Check if address exists
        if (!$address->exists) {
            Log::error('Address does not exist', [
                'address_id' => $address->id ?? 'unknown',
                'user_id' => auth()->id()
            ]);
            abort(404, 'Address not found.');
        }

        // Check if address has user_id
        if (!$address->user_id) {
            Log::error('Address missing user_id', [
                'address_id' => $address->id,
                'address_data' => $address->toArray(),
                'user_id' => auth()->id()
            ]);
            
            // Try to fix it if possible (for existing addresses without user_id)
            if (auth()->check()) {
                $address->update(['user_id' => auth()->id()]);
                Log::info('Fixed address user_id', [
                    'address_id' => $address->id,
                    'user_id' => auth()->id()
                ]);
                return; // Continue with the request
            }
            
            abort(500, 'Address is not properly configured.');
        }

        // Check ownership
        if ($address->user_id !== auth()->id()) {
            Log::warning('Unauthorized address access attempt', [
                'address_id' => $address->id,
                'address_user_id' => $address->user_id,
                'current_user_id' => auth()->id(),
                'user_email' => auth()->user()->email ?? 'unknown'
            ]);
            
            abort(403, 'Unauthorized access to this address.');
        }
    }

    /**
     * Get address statistics for dashboard
     */
    public function getAddressStats()
    {
        $user = auth()->user();
        
        $stats = [
            'total_addresses' => $user->addresses()->count(),
            'has_default' => $user->addresses()->where('is_default', true)->exists(),
            'address_types' => $user->addresses()
                ->selectRaw('type, COUNT(*) as count')
                ->whereNotNull('type')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk operations on addresses
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete',
            'address_ids' => 'required|array|min:1',
            'address_ids.*' => 'exists:addresses,id'
        ]);

        $addresses = Address::whereIn('id', $validated['address_ids'])
            ->where('user_id', auth()->id())
            ->get();

        if ($addresses->isEmpty()) {
            return redirect()
                ->route('address.index')
                ->with('error', 'No valid addresses selected.');
        }

        $count = $addresses->count();
        $hasDefault = $addresses->where('is_default', true)->isNotEmpty();

        // Perform bulk delete
        if ($validated['action'] === 'delete') {
            $addresses->each->delete();

            // If we deleted the default address, set a new one
            if ($hasDefault) {
                $nextAddress = auth()->user()->addresses()->first();
                if ($nextAddress) {
                    $nextAddress->update(['is_default' => true]);
                }
            }

            Log::info('Bulk address deletion', [
                'user_id' => auth()->id(),
                'deleted_count' => $count,
                'had_default' => $hasDefault
            ]);

            return redirect()
                ->route('address.index')
                ->with('success', $count . ' address(es) deleted successfully!');
        }

        return redirect()
            ->route('address.index')
            ->with('error', 'Invalid bulk action.');
    }

    /**
     * Validate address format (for checkout validation)
     */
    public function validateAddress(Request $request)
    {
        $rules = [
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json(['valid' => true]);
    }

    /**
     * Fix addresses without user_id (maintenance function)
     */
    public function fixAddressesWithoutUserId()
    {
        // This should only be accessible by admins or via artisan command
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $addressesWithoutUserId = Address::whereNull('user_id')->orWhere('user_id', 0)->get();
        
        $fixed = 0;
        foreach ($addressesWithoutUserId as $address) {
            // Try to find the user based on other criteria or manual assignment
            // For now, we'll just log them
            Log::warning('Address without user_id found', [
                'address_id' => $address->id,
                'address_data' => $address->toArray()
            ]);
        }

        return response()->json([
            'found_addresses_without_user_id' => $addressesWithoutUserId->count(),
            'fixed' => $fixed,
            'addresses' => $addressesWithoutUserId->toArray()
        ]);
    }
}