@extends('layouts.public')

@section('title', 'Retry Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Retry Payment</h1>
            
            <div class="mb-6">
                <p class="text-gray-600">Order #{{ $order->id }}</p>
                <p class="text-2xl font-bold text-gray-800">RWF {{ number_format($order->total, 0) }}</p>
            </div>

            <form action="{{ route('payment.initiate') }}" method="POST">
                @csrf
                <input type="hidden" name="order" value="{{ $order->id }}">
                <input type="hidden" name="name" value="{{ $order->customer_name ?? auth()->user()->name }}">
                <input type="hidden" name="email" value="{{ $order->customer_email ?? auth()->user()->email }}">
                
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ $order->customer_phone ?? '' }}"
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="momo" class="mr-2" checked>
                            <span>Mobile Money</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="card" class="mr-2">
                            <span>Debit/Credit Card</span>
                        </label>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700">
                    Retry Payment
                </button>
            </form>
        </div>
    </div>
</div>
@endsection