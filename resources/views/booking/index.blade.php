@extends('layouts.dashboard')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-4">My Bookings</h1>

    @if ($bookings->isEmpty())
        <p class="text-gray-500">You haven’t made any bookings yet.</p>
    @else
        <table class="min-w-full border border-gray-200 bg-white rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Service</th>
                    <th class="px-4 py-2 text-left">Provider</th>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Time</th>
                    <th class="px-4 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $booking->service->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $booking->provider->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($booking->preferred_time)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($booking->preferred_time)->format('H:i') }}
                        </td>
                        <td class="px-4 py-2 capitalize">
                            {{ $booking->status }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
