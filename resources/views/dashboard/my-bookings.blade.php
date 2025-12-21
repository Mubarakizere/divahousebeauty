@extends('layouts.dashboard')

@section('title', 'My Bookings')

@section('content')
<h4 class="fw-bold mb-4">📅 My Bookings</h4>

@if($bookings->count())
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Services</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Paid</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $index => $booking)
                    <tr>
                        <td>{{ $bookings->firstItem() + $index }}</td>
                        <td>{{ $booking->reference }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}</td>
                        <td>
                            <ul class="mb-0">
                                @foreach($booking->services as $service)
                                    <li>{{ $service->name }} ({{ number_format($service->price) }} RWF)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $booking->provider->user->name ?? '—' }}</td>
                        <td><span class="badge bg-{{ $booking->status == 'accepted' ? 'success' : ($booking->status == 'declined' ? 'danger' : 'secondary') }}">{{ ucfirst($booking->status) }}</span></td>
                        <td>
                            {{ $booking->is_fully_paid ? '✅ Full' : '💰 Partial' }}
                        </td>
                        <td>
                            <a href="{{ route('booking.receipt', $booking->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-receipt"></i>
                            </a>
                            @if(!$booking->is_fully_paid)
                                <a href="{{ route('booking.pay.remaining', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-cash-coin"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $bookings->links() }}</div>
    </div>
@else
    <div class="alert alert-info">No bookings found yet.</div>
@endif
@endsection
