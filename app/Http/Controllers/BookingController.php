<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\Service;
use App\Models\Provider;
use App\Models\BookingRequest;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
{
    $bookings = \App\Models\BookingRequest::with(['service', 'provider'])
        ->latest()
        ->get();

    return view('booking.index', compact('bookings'));
}

    /**
     * Show the booking form with available service types.
     */
    public function showBookingForm()
    {
        $serviceTypes = ServiceType::all();
        return view('booking.create', compact('serviceTypes'));
    }

    /**
     * Return services based on selected service type (AJAX).
     */
    public function getServices($serviceTypeId)
    {
        $services = Service::where('service_type_id', $serviceTypeId)->get();
        return response()->json($services);
    }

    /**
     * Return providers for a selected service, including price & duration.
     */
    public function getProviders($serviceId)
    {
        $providers = Provider::whereHas('services', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->with(['services' => function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        }])->get();

        $result = $providers->map(function ($provider) use ($serviceId) {
            $service = $provider->services->first();
            return [
                'id' => $provider->id,
                'name' => $provider->name,
                'price' => $service->pivot->price,
                'duration_minutes' => $service->pivot->duration_minutes,
            ];
        });

        return response()->json($result);
    }

    /**
     * Store the booking request.
     */
  public function store(Request $request)
{
    $request->validate([
        'user_name' => 'required|string|max:100',
        'user_phone' => 'required|string|max:20',
        'service_id' => 'required|exists:services,id',
        'provider_id' => 'required|exists:providers,id',
        'preferred_time_date' => 'required|date',
        'preferred_time_time' => 'required|date_format:H:i',
    ]);

    // Store booking in DB
    BookingRequest::create([
        'user_name' => $request->user_name,
        'user_phone' => $request->user_phone,
        'service_id' => $request->service_id,
        'provider_id' => $request->provider_id,
        'preferred_time' => $request->preferred_time_date . ' ' . $request->preferred_time_time,
        'status' => 'pending',
    ]);

    // Fetch additional info
    $service = Service::findOrFail($request->service_id);
    $provider = Provider::findOrFail($request->provider_id);

    // WhatsApp message
    $whatsappNumber = '250780159059';
    $website = 'https://divahousebeauty.com';  

    $message = "Hello  I’d like to make a booking from Ihuriro \n\n" .
           "Name: {$request->user_name}\n" .
           "Phone: {$request->user_phone}\n" .
           "Service: {$service->name}\n" .
           "Provider: {$provider->name}\n" .
           "Date: {$request->preferred_time_date}\n" .
           "Time: {$request->preferred_time_time}\n\n" .
           "Please confirm when possible.\n" .
           "Booked via: https://divahousebeauty.com";


    $encodedMessage = urlencode($message);
$whatsappLink = "https://wa.me/$whatsappNumber?text=$encodedMessage";
return redirect($whatsappLink);

}

}
