<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Order Confirmed — Diva House Beauty</title>

  {{-- Tailwind (preflight OFF) + Alpine --}}
  <script>tailwind = { config: { corePlugins: { preflight: false } } }</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons --}}
  <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css"/>

  {{-- Google Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

  <style>
    :root { --gold: #C5A059; --black: #0F172A; }
    body { font-family: 'Inter', sans-serif; color: #334155; }
    h1, h2, h3 { font-family: 'Playfair Display', serif; }

    @keyframes checkPop {
      0%   { transform: scale(0) rotate(-10deg); opacity: 0; }
      70%  { transform: scale(1.1) rotate(3deg); opacity: 1; }
      100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes shimmer {
      0%   { background-position: -200% center; }
      100% { background-position: 200% center; }
    }

    .check-icon { animation: checkPop 0.6s cubic-bezier(.36,.07,.19,.97) both; }
    .fade-up    { animation: fadeUp 0.5s ease forwards; }
    .fade-up-1  { animation-delay: 0.15s; opacity: 0; }
    .fade-up-2  { animation-delay: 0.3s;  opacity: 0; }
    .fade-up-3  { animation-delay: 0.45s; opacity: 0; }
    .fade-up-4  { animation-delay: 0.6s;  opacity: 0; }

    .gold-shimmer {
      background: linear-gradient(90deg, var(--gold) 0%, #e8c97a 40%, var(--gold) 60%, #b8882e 100%);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: shimmer 3s linear infinite;
    }

    .confetti-dot {
      position: absolute;
      width: 8px; height: 8px;
      border-radius: 50%;
      animation: confettiFall linear infinite;
    }
    @keyframes confettiFall {
      0%   { transform: translateY(-20px) rotate(0deg); opacity: 1; }
      100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
  </style>
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen: false, authTab: 'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  @include('partials.header_home2')

  <main class="py-10 sm:py-16 relative overflow-hidden">

    {{-- Subtle confetti background dots --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
      @foreach([
        ['top-10','left-[10%]','bg-[#C5A059]','1.2s','3.5s'],
        ['top-20','left-[25%]','bg-emerald-400','2.1s','4.2s'],
        ['top-5', 'left-[50%]','bg-[#C5A059]','0.8s','5s'],
        ['top-16','left-[70%]','bg-rose-400','1.8s','3.8s'],
        ['top-8', 'left-[88%]','bg-sky-400','1.4s','4.5s'],
      ] as [$top,$left,$bg,$dur,$delay])
        <div class="confetti-dot {{ $top }} {{ $left }} {{ $bg }}"
             style="animation-duration:{{ $dur }};animation-delay:{{ $delay }};"></div>
      @endforeach
    </div>

    <div class="mx-auto max-w-2xl px-4 relative z-10">

      {{-- Main Card --}}
      <div class="bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden">

        {{-- Gold top bar --}}
        <div class="h-1.5 bg-gradient-to-r from-[#C5A059] via-[#e8c97a] to-[#C5A059]"></div>

        <div class="px-6 sm:px-10 py-10 text-center">

          {{-- Animated check circle --}}
          <div class="check-icon inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-200 mb-6">
            <i class="la la-check text-4xl font-bold"></i>
          </div>

          {{-- Headline --}}
          <h1 class="fade-up fade-up-1 text-3xl sm:text-4xl font-serif text-[var(--black)] mb-2">
            Thank you, <span class="gold-shimmer">{{ $order->customer_name ?? auth()->user()->name }}</span>!
          </h1>
          <p class="fade-up fade-up-2 text-slate-500 text-sm mb-8">
            Your payment was successful and your order is confirmed. We'll begin processing it right away.
          </p>

          {{-- Order Summary Box --}}
          <div class="fade-up fade-up-2 bg-slate-50 border border-slate-200 rounded-xl p-5 mb-6 text-left">
            <div class="flex items-center justify-between mb-4">
              <div>
                <div class="text-[10px] uppercase tracking-widest text-slate-400 font-medium mb-1">Order Number</div>
                <div class="text-2xl font-serif font-bold text-[var(--black)]">
                  {{ $order->masked_order_id ?? $order->order_number }}
                </div>
              </div>
              <div class="text-right">
                <div class="text-[10px] uppercase tracking-widest text-slate-400 font-medium mb-1">Status</div>
                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Confirmed
                </span>
              </div>
            </div>

            <div class="space-y-2 text-sm border-t border-slate-200 pt-4">
              <div class="flex justify-between">
                <span class="text-slate-500">Customer</span>
                <span class="font-semibold text-slate-800">{{ $order->customer_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500">Email</span>
                <span class="font-medium text-slate-700">{{ $order->customer_email }}</span>
              </div>
              @if($order->customer_phone)
              <div class="flex justify-between">
                <span class="text-slate-500">Phone</span>
                <span class="font-medium text-slate-700">{{ $order->customer_phone }}</span>
              </div>
              @endif
              @if($order->payment)
              <div class="flex justify-between">
                <span class="text-slate-500">Payment Ref</span>
                <span class="font-medium text-slate-700 text-xs">{{ $order->payment->payment_ref ?? $order->payment->request_token ?? 'Confirmed' }}</span>
              </div>
              @endif
            </div>
          </div>

          {{-- Items Summary --}}
          @if($order->items && $order->items->count())
          <div class="fade-up fade-up-3 mb-6 text-left">
            <h3 class="text-xs uppercase tracking-widest font-bold text-slate-400 mb-3">Items Ordered</h3>
            <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
              @foreach($order->items as $item)
              <div class="flex items-center gap-3 px-4 py-3 bg-white">
                <div class="w-10 h-10 bg-slate-100 rounded flex-shrink-0 overflow-hidden">
                  @php
                    $product = $item->product;
                    $imgs = $product ? (is_array($product->images) ? $product->images : json_decode($product->images, true)) : [];
                    $thumb = !empty($imgs) ? asset('storage/' . $imgs[0]) : asset('assets/images/default-product.jpg');
                  @endphp
                  <img src="{{ $thumb }}" class="w-full h-full object-cover"
                       onerror="this.src='{{ asset('assets/images/default-product.jpg') }}'">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium text-slate-800 truncate">{{ $item->product_name ?? ($product->name ?? 'Product') }}</div>
                  <div class="text-xs text-slate-400">Qty: {{ $item->quantity }}</div>
                </div>
                <div class="text-sm font-bold text-[var(--black)] shrink-0">
                  Rw {{ number_format($item->price * $item->quantity, 0) }}
                </div>
              </div>
              @endforeach
            </div>
          </div>
          @endif

          {{-- Total --}}
          <div class="fade-up fade-up-3 flex items-center justify-between bg-[var(--black)] text-white rounded-xl px-5 py-4 mb-8">
            <span class="font-serif text-lg">Total Paid</span>
            <span class="font-serif text-2xl font-bold">Rw {{ number_format($order->total, 0) }}</span>
          </div>

          {{-- CTA Buttons --}}
          <div class="fade-up fade-up-4 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('orders.show', $order->id) }}"
               class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-bold text-white uppercase tracking-widest transition-all"
               style="background: var(--gold);">
              <i class="la la-box text-lg"></i> View My Order
            </a>
            <a href="{{ route('home') }}"
               class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition-all">
              <i class="la la-home text-lg"></i> Continue Shopping
            </a>
          </div>

          {{-- Reassurance strip --}}
          <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-3 gap-4 text-center text-[11px] text-slate-400 uppercase tracking-wider">
            <div><i class="la la-shield-alt text-[var(--gold)] text-lg block mb-1"></i>Secure Payment</div>
            <div><i class="la la-truck text-[var(--gold)] text-lg block mb-1"></i>Fast Delivery</div>
            <div><i class="la la-headset text-[var(--gold)] text-lg block mb-1"></i>24/7 Support</div>
          </div>
        </div>
      </div>

      {{-- Confirmation email note --}}
      <p class="text-center text-xs text-slate-400 mt-4">
        A confirmation email has been sent to <strong class="text-slate-600">{{ $order->customer_email }}</strong>
      </p>
    </div>
  </main>

  @include('partials.footer')
  @includeIf('partials.auth_modal')

</body>
</html>