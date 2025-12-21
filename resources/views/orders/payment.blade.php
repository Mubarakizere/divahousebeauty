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
      x-data="{ authOpen: {{ $errors->any() ? 'true':'false' }}, authTab: '{{ old('_tab','signin') }}' }"
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
               x-data="{ loading:false }">
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
              <input type="hidden" name="order" value="{{ $order->id }}"/>

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

            <div class="mt-4 rounded-lg bg-slate-50 border border-slate-200 p-3">
              <div class="flex items-center justify-between text-sm">
                <span class="text-slate-600">Total Amount</span>
                <span class="text-base font-extrabold text-slate-900">RWF {{ number_format($order->total, 0) }}</span>
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
