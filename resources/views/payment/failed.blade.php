{{-- resources/views/payment/failed.blade.php --}}
@extends('layouts.payment')

@section('title', 'Payment Failed')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Error Icon and Message -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Failed</h1>
            <p class="text-lg text-gray-600">We were unable to process your payment. Please try again.</p>
        </div>

        @if(isset($order) && $order)
        <!-- Order Information Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-medium text-gray-700">Order Details</h3>
                    <p class="text-gray-600">Order #{{ $order->id }}</p>
                    <p class="text-gray-600">Date: {{ $order->created_at->format('M d, Y') }}</p>
                    <p class="text-gray-600">Status: <span class="text-red-600 font-medium">Payment Failed</span></p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700">Amount</h3>
                    <p class="text-2xl font-bold text-gray-800">RWF {{ number_format($order->total, 0) }}</p>
                    <p class="text-sm text-gray-500">Total amount</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Common Reasons Card -->
        <div class="bg-yellow-50 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-yellow-800 mb-3">Common Reasons for Payment Failure</h2>
            <ul class="space-y-2 text-yellow-700">
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Insufficient balance in your mobile money account
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Incorrect PIN entered multiple times
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Network connectivity issues
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Card expired or blocked for online transactions
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Payment was cancelled or timed out
                </li>
            </ul>
        </div>

        <!-- Troubleshooting Steps -->
        <div class="bg-blue-50 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-blue-800 mb-3">Before You Retry</h2>
            <ol class="space-y-2 text-blue-700 list-decimal list-inside">
                <li>Check your mobile money or bank account balance</li>
                <li>Ensure you have a stable internet connection</li>
                <li>For card payments, verify your card is enabled for online transactions</li>
                <li>Make sure you enter the correct PIN/password</li>
                <li>Contact your mobile money provider or bank if issues persist</li>
            </ol>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
            @if(isset($order) && $order)
            <a href="{{ route('payment.retry', $order->id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Retry Payment
            </a>
            @endif
            
            <a href="{{ route('orders.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                View My Orders
            </a>
            
            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Continue Shopping
            </a>
        </div>

        <!-- Contact Support -->
        <div class="bg-gray-50 rounded-lg p-6">
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Still Having Issues?</h3>
                <p class="text-gray-600 mb-4">Our support team is here to help you complete your order.</p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="mailto:support@yourstore.com" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Support
                    </a>
                    
                    <a href="tel:+250788000000" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-600 bg-green-100 hover:bg-green-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Call Support
                    </a>
                </div>
                
                @if(isset($order) && $order)
                <p class="text-sm text-gray-500 mt-3">
                    Reference Order #{{ $order->id }} when contacting support
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Log failed payment for analytics
    console.log('Payment failed', {
        orderId: {{ $order->id ?? 'null' }},
        amount: {{ $order->total ?? 0 }},
        timestamp: new Date().toISOString()
    });

    // Track payment failure event
    if (typeof gtag !== 'undefined') {
        gtag('event', 'payment_failed', {
            transaction_id: '{{ $order->id ?? "" }}',
            value: {{ $order->total ?? 0 }},
            currency: 'RWF'
        });
    }

    // Auto-focus retry button after 3 seconds to encourage retry
    setTimeout(() => {
        const retryButton = document.querySelector('a[href*="retry"]');
        if (retryButton) {
            retryButton.focus();
        }
    }, 3000);
});
</script>
@endsection