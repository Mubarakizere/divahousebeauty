<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Checkout — Diva House Beauty</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <script>tailwind = { config: { corePlugins: { preflight: false } } }</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>
  
  <style>
    :root{ --gold:#cc9966; --black:#111827; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
  </style>

  <!-- Currency Converter Styles -->
  <link rel="stylesheet" href="{{ asset('css/currency-styles.css') }}">
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased">
  
  @include('partials.header_home2')
  
  <header class="bg-white border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Checkout</h1>
      <p class="mt-1 text-sm text-slate-500">Complete your order</p>
    </div>
  </header>
  
  <main class="py-6">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-12 gap-4 lg:gap-6">
          
          {{-- Customer Information --}}
          <section class="col-span-12 lg:col-span-8">
            <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4">
              <h2 class="text-lg font-semibold text-slate-900 mb-4">Customer Information</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                  <input type="text" name="customer_name" value="{{ auth()->user()->name ?? old('customer_name') }}" required
                         class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20">
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                  <input type="email" name="customer_email" value="{{ auth()->user()->email ?? old('customer_email') }}" required
                         class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20">
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                  <input type="tel" name="customer_phone" value="{{ auth()->user()->phone ?? old('customer_phone') }}"
                         placeholder="+250780000000"
                         class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20">
                </div>
              </div>
              
              <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Order Notes (Optional)</label>
                <textarea name="customer_notes" rows="3" placeholder="Special instructions for your order..."
                          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20">{{ old('customer_notes') }}</textarea>
              </div>
            </div>
          </section>
          
          {{-- Order Summary --}}
          <aside class="col-span-12 lg:col-span-4">
            <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4">
              <h2 class="text-lg font-semibold text-slate-900 mb-4">Order Summary</h2>
              
              {{-- Cart Items --}}
              <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                @foreach($cartItems as $item)
                  <div class="flex gap-3">
                    <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-slate-200">
                      @php
                        $img = $item->product->images;
                        $first = is_array($img) ? ($img[0] ?? 'default.jpg') : (json_decode($img, true)[0] ?? 'default.jpg');
                      @endphp
                      <img src="{{ asset('storage/'.$first) }}" class="h-full w-full object-cover" alt="{{ $item->product->name }}">
                    </div>
                    <div class="flex-1">
                      <div class="text-sm font-medium text-slate-900">{{ $item->product->name }}</div>
                      <div class="text-xs text-slate-500">Qty: {{ $item->quantity }}</div>
                      <div class="text-sm font-semibold text-slate-700 mt-1">{{ number_format($item->price * $item->quantity, 0) }} RWF</div>
                    </div>
                  </div>
                @endforeach
              </div>
              
              {{-- Totals --}}
              <dl class="space-y-2 text-sm border-t border-slate-200 pt-3">
                <div class="flex justify-between">
                  <dt class="text-slate-600">Subtotal</dt>
                  <dd class="font-semibold">{{ number_format($subtotal, 0) }} RWF</dd>
                </div>
                
                @if($discount > 0)
                  <div class="flex justify-between text-green-600">
                    <dt>Discount ({{ $appliedCoupon->code }})</dt>
                    <dd class="font-semibold">-{{ number_format($discount, 0) }} RWF</dd>
                  </div>
                @endif
                
                <div class="flex justify-between border-t border-slate-200 pt-2">
                  <dt class="font-semibold text-slate-900">Total</dt>
                  <dd class="text-lg font-bold text-[var(--gold)]">{{ number_format($total, 0) }} RWF</dd>
                </div>
              </dl>
              
              {{-- Submit --}}
              <button type="submit" class="mt-4 w-full inline-flex items-center justify-center gap-2 rounded-md bg-[var(--gold)] px-4 py-3 text-sm font-semibold text-white hover:opacity-90">
                <i class="la la-lock text-lg"></i> Proceed to Payment
              </button>
              
              <a href="{{ route('cart') }}" class="mt-2 w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="la la-arrow-left"></i> Back to Cart
              </a>
            </div>
          </aside>
          
        </div>
      </form>
    </div>
  </main>
  
  @include('partials.footer')

  <!-- Currency Converter Script -->
  <script src="{{ asset('js/currency-converter.js') }}"></script>
</body>
</html>
