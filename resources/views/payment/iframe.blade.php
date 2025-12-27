{{-- resources/views/payment.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Secure Payment — {{ config('app.name', 'Your Store') }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind (preflight OFF) + Alpine --}}
  <script> tailwind = { config: { corePlugins: { preflight: false } } } </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Line Awesome Icons --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>

  <style>
    :root{ --gold:#cc9966; --gold2:#b8845a; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
    [x-cloak]{ display:none!important }
  </style>
</head>

@php
  // ---------- SAFETY FALLBACKS (so header/cart badge render even if not passed) ----------
  $categories = (isset($categories) && $categories instanceof \Illuminate\Support\Collection)
      ? $categories->loadMissing('brands')
      : \App\Models\Category::with('brands')->get();

  $userId    = auth()->id();
  $count     = isset($count) ? (int)$count : ($userId ? \App\Models\Cart::where('users_id',$userId)->count() : 0);
@endphp

<body class="bg-[#f8f9fa] text-slate-700 antialiased"
      x-data="paymentPage({
        orderId: {{ $order->id }},
        statusUrl: '/payment/status/{{ $order->id }}',
        successUrl: '/payment/success?order={{ $order->id }}',
        failedUrl:  '/payment/failed?order={{ $order->id }}',
        iframeOrigins: ['https://weflexfy.com'] // add sandbox/dev origins if needed
      })"
      x-init="init()">

  {{-- ===================== HEADER (Tailwind partial) ===================== --}}
 

  {{-- ===================== PAYMENT HEADER STRIP ===================== --}}
  <section class="bg-white border-b-4" style="border-color: var(--gold)">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-4">
      <div class="flex items-center justify-between gap-3">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
          <img src="{{ asset('assets/images/demos/demo-14/logo.png') }}" alt="Logo" class="h-6">
          <div>
            <h4 class="text-slate-900 font-semibold leading-tight">Secure Payment</h4>
            <small class="text-slate-500">Order #{{ $order->id }}</small>
          </div>
        </a>
        <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 text-[12px] font-medium">
          <i class="la la-shield-alt mr-1"></i> SSL Secured
        </span>
      </div>
    </div>
  </section>

  {{-- ===================== PROGRESS STEPS ===================== --}}
  <section class="bg-gradient-to-r from-[var(--gold)] to-[var(--gold2)]">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-4">
      <div class="flex flex-col md:flex-row items-center justify-center gap-4 text-white">
        <div class="flex items-center gap-2">
          <div class="w-10 h-10 grid place-items-center rounded-full bg-emerald-500 font-bold">✓</div>
          <span class="text-sm">Order Created</span>
        </div>

        <div class="hidden md:block h-0.5 w-16 opacity-70 bg-white"></div>

        <div class="flex items-center gap-2">
          <div id="step-paying"
               class="w-10 h-10 grid place-items-center rounded-full bg-amber-400 animate-pulse font-bold">⏳</div>
          <span class="text-sm">Payment Processing</span>
        </div>

        <div id="step-line" class="hidden md:block h-0.5 w-16 opacity-50 bg-white"></div>

        <div class="flex items-center gap-2">
          <div id="step-done" class="w-10 h-10 grid place-items-center rounded-full bg-slate-500 font-bold">⏳</div>
          <span class="text-sm">Completed</span>
        </div>
      </div>
    </div>
  </section>

  {{-- ===================== MAIN ===================== --}}
  <main class="py-6">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="grid grid-cols-12 gap-4 lg:gap-6">

        {{-- ===== RIGHT: ORDER SUMMARY ===== --}}
        <aside class="col-span-12 lg:col-span-4 order-2 lg:order-2">
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring overflow-hidden">
            <div class="px-4 py-3 text-white" style="background: linear-gradient(135deg, var(--gold), var(--gold2))">
              <h5 class="font-semibold"><i class="la la-shopping-cart mr-2"></i>Order Summary</h5>
            </div>
            <div class="p-4">
              <div class="rounded-md bg-slate-50 border border-slate-200 p-3">
                <div class="flex items-start justify-between">
                  <div>
                    <div class="text-sm font-semibold text-slate-900">Order #{{ $order->id }}</div>
                    <div class="text-[12px] text-slate-500">{{ $order->created_at->format('M d, Y • H:i') }}</div>
                  </div>
                  <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-[12px]">
                    Processing
                  </span>
                </div>
              </div>

              @if($order->items && $order->items->count() > 0)
                <div class="mt-3 divide-y divide-slate-200">
                  @foreach($order->items->take(3) as $item)
                    <div class="py-3 flex items-center justify-between">
                      <div class="min-w-0">
                        <div class="text-sm font-medium text-slate-900 truncate">{{ $item->product->name ?? 'Product' }}</div>
                        <div class="text-[12px] text-slate-500">Qty: {{ $item->quantity ?? 1 }}</div>
                      </div>
                      <div class="text-sm font-semibold text-slate-900">
                        RWF {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 0) }}
                      </div>
                    </div>
                  @endforeach
                </div>

                @if($order->items->count() > 3)
                  <div class="mt-2 text-center">
                    <button type="button" class="text-[12px] text-slate-500 hover:text-[var(--gold)] hover:underline">
                      + {{ $order->items->count() - 3 }} more items
                    </button>
                  </div>
                @endif
              @endif

              <div class="mt-4 rounded-lg bg-white border border-slate-200 p-3">
                <div class="flex items-center justify-between">
                  <span class="text-slate-700 font-medium">Total Amount</span>
                  <span class="text-lg font-extrabold text-slate-900">RWF {{ number_format($order->total, 0) }}</span>
                </div>
              </div>

              @if($order->customer_name || $order->customer_email || $order->customer_phone)
                <div class="mt-4 border-t border-slate-200 pt-3">
                  <h6 class="text-sm font-semibold text-slate-900 mb-2">Customer Details</h6>
                  @if($order->customer_name)
                    <p class="text-[12px] text-slate-600">{{ $order->customer_name }}</p>
                  @endif
                  @if($order->customer_email)
                    <p class="text-[12px] text-slate-600">{{ $order->customer_email }}</p>
                  @endif
                  @if($order->customer_phone)
                    <p class="text-[12px] text-slate-600">{{ $order->customer_phone }}</p>
                  @endif
                </div>
              @endif

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
          </div>
        </aside>

        {{-- ===== LEFT: PAYMENT FORM / IFRAME ===== --}}
        <section class="col-span-12 lg:col-span-8 order-1 lg:order-1 space-y-3">

          {{-- Alerts --}}
          <div id="payment-alerts" class="space-y-2">
            <div x-show="alert==='success'" x-cloak
                 class="rounded-md border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3">
              <div class="flex items-start gap-3">
                <i class="la la-check-circle text-xl"></i>
                <div>
                  <h6 class="font-semibold">Payment Successful!</h6>
                  <p class="text-sm">Your order has been confirmed. Redirecting to success page…</p>
                </div>
              </div>
            </div>

            <div x-show="alert==='failed'" x-cloak
                 class="rounded-md border border-red-200 bg-red-50 text-red-800 px-4 py-3">
              <div class="flex items-start gap-3">
                <i class="la la-times-circle text-xl"></i>
                <div>
                  <h6 class="font-semibold">Payment Failed</h6>
                  <p class="text-sm">There was an issue processing your payment. Redirecting to help you retry…</p>
                </div>
              </div>
            </div>

            <template x-if="timeoutHtml">
              <div x-html="timeoutHtml"></div>
            </template>
          </div>

          {{-- Payment Card --}}
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring overflow-hidden">
            <div class="px-4 py-3 text-white" style="background: linear-gradient(135deg, var(--gold), var(--gold2))">
              <h5 class="font-semibold"><i class="la la-credit-card mr-2"></i>Complete Payment</h5>
              <small class="opacity-90">Choose your preferred payment method in the secure window</small>
            </div>

            <div class="relative min-h-[600px] bg-white">
              {{-- Loading overlay --}}
              <div id="loading-overlay"
                   class="absolute inset-0 grid place-items-center bg-white z-10"
                   x-show="loading" x-cloak>
                <div class="text-center">
                  <div class="h-12 w-12 rounded-full border-4 border-slate-200 border-t-[var(--gold)] animate-spin mx-auto"></div>
                  <h5 class="mt-3 font-semibold text-slate-900">Loading Payment Form</h5>
                  <p class="text-sm text-slate-500">Please wait while we prepare your secure payment…</p>
                </div>
              </div>

              {{-- iframe --}}
              <iframe
                id="payment-iframe"
                src="{{ $payment->iframe_url }}"
                class="w-full h-[600px] border-0 transition-opacity duration-500"
                :class="{'opacity-0': loading, 'opacity-100': !loading}"
                @load="handleIframeLoad"
              ></iframe>
            </div>
          </div>

          {{-- Help Section --}}
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <h6 class="text-sm font-semibold text-slate-900">Payment Help</h6>
                <ul class="mt-2 space-y-2 text-sm">
                  <li class="flex items-center gap-2"><i class="la la-check text-emerald-600"></i> Ensure enough Mobile Money balance</li>
                  <li class="flex items-center gap-2"><i class="la la-check text-emerald-600"></i> Enable online transactions for cards</li>
                  <li class="flex items-center gap-2"><i class="la la-check text-emerald-600"></i> Keep your phone nearby for OTP</li>
                </ul>
              </div>
              <div>
                <h6 class="text-sm font-semibold text-slate-900">Need Support?</h6>
                <ul class="mt-2 space-y-2 text-sm">
                  <li class="flex items-center gap-2"><i class="la la-envelope text-emerald-600"></i>
                    <a href="mailto:support@yourstore.com" class="hover:text-[var(--gold)]">support@yourstore.com</a></li>
                  <li class="flex items-center gap-2"><i class="la la-phone text-emerald-600"></i>
                    <a href="tel:+250788000000" class="hover:text-[var(--gold)]">+250 788 000 000</a></li>
                </ul>
              </div>
            </div>
          </div>

        </section>
      </div>
    </div>
  </main>

  {{-- ===================== FOOTER & AUTH MODAL PARTIALS ===================== --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- ===================== PAGE LOGIC (Alpine component) ===================== --}}
  <script>
    function paymentPage(cfg){
      return {
        // state
        loading: true,
        alert: null,         // 'success' | 'failed' | null
        done: false,
        attempts: 0,
        pollTimer: null,
        timeoutHtml: null,

        init(){
          this.setupPostMessage();
          // start polling as a fallback after a short delay
          setTimeout(()=> this.startPolling(), 2500);

          // pause/resume on visibility change
          document.addEventListener('visibilitychange', () => {
            if (this.done) return;
            if (document.hidden) this.stopPolling(); else if (!this.pollTimer) this.startPolling();
          });

          // stop when navigating away
          window.addEventListener('beforeunload', () => this.stopPolling());
          // prevent accidental navigation mid-payment
          window.addEventListener('beforeunload', (e) => {
            if (!this.done && this.attempts > 0) {
              e.preventDefault();
              e.returnValue = 'Your payment is being processed. Are you sure you want to leave?';
              return 'Your payment is being processed. Are you sure you want to leave?';
            }
          }, {capture:true});
        },

        setupPostMessage(){
          window.addEventListener('message', (event) => {
            // allow only trusted origins (adjust list if you have staging)
            if (!cfg.iframeOrigins.some(o => event.origin.startsWith(o))) return;
            const msg = event.data || {};
            if (msg.type !== 'PAYMENT_STATUS') return;

            switch (msg.status) {
              case 'init':
                this.handleIframeLoad();
                break;
              case 'success':
                this.handleSuccess({ paid:true, status:'success' });
                break;
              case 'failed':
                this.handleFailure({ paid:false, status:'failed' });
                break;
              case 'close':
                // optional: show info
                break;
            }
          });
        },

        startPolling(){
          if (this.pollTimer) return;
          this.pollTimer = setInterval(() => this.checkStatus(), 5000);
        },
        stopPolling(){
          if (this.pollTimer){
            clearInterval(this.pollTimer);
            this.pollTimer = null;
          }
        },

        async checkStatus(){
          if (this.done) return;
          this.attempts++;

          if (this.attempts >= 120){
            this.stopPolling();
            this.showTimeout();
            return;
          }

          try{
            const res = await fetch(cfg.statusUrl, {
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              },
              credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data?.paid === true) this.handleSuccess(data);
            else if (data?.status === 'failed') this.handleFailure(data);
          }catch(e){
            // swallow transient errors
          }
        },

        handleSuccess(data){
          if (this.done) return;
          this.done = true;
          this.stopPolling();
          this.alert = 'success';
          // update steps
          this.$nextTick(() => {
            const paying = document.getElementById('step-paying');
            const done   = document.getElementById('step-done');
            const line   = document.getElementById('step-line');
            if (paying){ paying.classList.remove('bg-amber-400','animate-pulse'); paying.textContent='✓'; paying.classList.add('bg-emerald-500'); }
            if (done){ done.classList.remove('bg-slate-500'); done.textContent='✓'; done.classList.add('bg-emerald-500'); }
            if (line){ line.classList.add('bg-emerald-400'); line.classList.remove('bg-white'); }
          });
          setTimeout(()=> window.location.href = cfg.successUrl, 3000);
        },

        handleFailure(data){
          if (this.done) return;
          this.done = true;
          this.stopPolling();
          this.alert = 'failed';
          // update step visual
          const paying = document.getElementById('step-paying');
          if (paying){ paying.classList.remove('bg-amber-400','animate-pulse'); paying.textContent='✗'; paying.classList.add('bg-red-600'); }
          setTimeout(()=> window.location.href = cfg.failedUrl, 8000);
        },

        showTimeout(){
          this.timeoutHtml = `
            <div class="rounded-md border border-sky-200 bg-sky-50 text-sky-900 px-4 py-3">
              <div class="flex items-start gap-3">
                <i class="la la-info-circle text-xl"></i>
                <div>
                  <h6 class="font-semibold">Payment Check Timeout</h6>
                  <p class="text-sm">We're still processing your payment. Please refresh or check your order status.</p>
                  <div class="mt-2 flex gap-2">
                    <button onclick="location.reload()" class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">
                      <i class="la la-sync"></i> Refresh Page
                    </button>
                    <a href="/orders/${cfg.orderId}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">
                      <i class="la la-receipt"></i> Check Order Status
                    </a>
                  </div>
                </div>
              </div>
            </div>`;
        },

        handleIframeLoad(){
          this.loading = false;
        }
      }
    }
  </script>
</body>
</html>
