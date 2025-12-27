<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Order Success — Diva House Beauty</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>
  <style>:root{ --gold:#cc9966; }</style>
</head>

<body class="bg-[#fafafa] text-slate-700">
  @include('partials.header_home2')
  
  <main class="py-12">
    <div class="mx-auto max-w-2xl px-4 text-center">
      <div class="bg-white border border-slate-200 rounded-lg shadow-lg p-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4">
          <i class="la la-check-circle text-4xl"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Payment Successful!</h1>
        <p class="text-slate-600 mb-6">Thank you for your order. Your payment has been processed successfully.</p>
        
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-6">
          <div class="text-sm text-slate-600 mb-1">Order Number</div>
          <div class="text-2xl font-bold text-[var(--gold)]">#{{ $order->id }}</div>
          
          @if($order->payment)
            <div class="mt-3 text-xs text-slate-500">
              Payment Reference: {{ $order->payment->payment_ref ?? 'Pending' }}
            </div>
          @endif
        </div>
        
        <div class="space-y-2 text-sm text-left mb-6">
          <div class="flex justify-between">
            <span class="text-slate-600">Customer:</span>
            <span class="font-medium">{{ $order->customer_name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600">Email:</span>
            <span class="font-medium">{{ $order->customer_email }}</span>
          </div>
          <div class="flex justify-between border-t border-slate-200 pt-2">
            <span class="font-semibold">Total Paid:</span>
            <span class="font-bold text-[var(--gold)]">{{ number_format($order->total, 0) }} RWF</span>
          </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
          <a href="{{ route('orders.show', $order->id) }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-md bg-[var(--gold)] px-4 py-2.5 text-sm font-semibold text-white hover:opacity-90">
            <i class="la la-eye"></i> View Order
          </a>
          <a href="{{ route('home') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="la la-home"></i> Back to Home
          </a>
        </div>
      </div>
    </div>
  </main>
  
  @include('partials.footer')
</body>
</html>