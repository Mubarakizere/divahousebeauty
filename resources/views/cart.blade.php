<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Diva House Beauty — Cart</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind (preflight OFF) + Alpine --}}
  <script> tailwind = { config: { corePlugins: { preflight: false } } } </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Line Awesome --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>

  <style>
    :root{ --gold:#cc9966; --black:#111827; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
    [x-cloak]{ display:none!important }
  </style>
</head>

@php
  // Header data fallbacks
  $categories = (isset($categories) && $categories instanceof \Illuminate\Support\Collection)
      ? $categories->loadMissing('brands')
      : \App\Models\Category::with('brands')->get();

  $userId    = auth()->id();
  $cartItems = isset($cartItems)
      ? ($cartItems instanceof \Illuminate\Support\Collection ? $cartItems : collect($cartItems))
      : collect();

  if ($cartItems->isEmpty() && $userId) {
      $cartItems = \App\Models\Cart::where('users_id',$userId)->with('product.brand')->get();
  }

  $count = isset($count) ? (int)$count : ($userId ? \App\Models\Cart::where('users_id',$userId)->count() : 0);
  $subtotal = $cartItems->sum(fn($i) => (float)$i->price * (int)$i->quantity);
@endphp

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen: {{ $errors->any() ? 'true':'false' }}, authTab: '{{ old('_tab','signin') }}' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- ===================== HEADER ===================== --}}
  @include('partials.header_home2')

  {{-- ===================== HERO ===================== --}}
  <header class="bg-white border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Shopping Cart</h1>
      <p class="mt-1 text-sm text-slate-500">Review your items and proceed to checkout.</p>
      <nav class="mt-2 text-[12px] text-slate-500">
        <a class="hover:text-[var(--gold)]" href="{{ route('home') }}">Home</a>
        <span class="mx-1">/</span>
        <a class="hover:text-[var(--gold)]" href="{{ route('category') }}">Shop</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700 font-medium">Cart</span>
      </nav>
    </div>
  </header>

  {{-- ===================== PAGE CONTENT ===================== --}}
  <main class="py-6">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="grid grid-cols-12 gap-4 lg:gap-6">

        {{-- CART TABLE / LIST (single form) --}}
        <section class="col-span-12 lg:col-span-9">
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring overflow-hidden"
               x-data="{ removeOpen:false, removeId:null }">
            <form x-ref="mainForm" action="{{ route('cart.update') }}" method="POST">
              @csrf
              {{-- Hidden input bound to Alpine for delete --}}
              <input type="hidden" name="remove_id" x-model="removeId">

              {{-- Desktop table --}}
              <div class="hidden md:block">
                <table class="w-full text-sm">
                  <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left text-slate-600">
                      <th class="px-4 py-3">Product</th>
                      <th class="px-4 py-3 w-24 text-right">Price</th>
                      <th class="px-4 py-3 w-32 text-center">Quantity</th>
                      <th class="px-4 py-3 w-28 text-right">Total</th>
                      <th class="px-2 py-3 w-12 text-right"> </th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($cartItems as $item)
                      @php
                        $p = $item->product;
                        $img = $p->images;
                        if (is_array($img)) { $first = $img[0] ?? 'default.jpg'; }
                        else { $arr = json_decode($img, true); $first = $arr[0] ?? 'default.jpg'; }
                        $imgUrl = asset('storage/'.$first);
                        $rowTotal = (float)$item->price * (int)$item->quantity;
                      @endphp
                      <tr class="border-b last:border-0">
                        <td class="px-4 py-3">
                          <div class="flex items-center gap-3">
                            <a href="{{ route('product', $p->slug) }}" class="block h-16 w-16 overflow-hidden rounded-md border border-slate-200">
                              <img src="{{ $imgUrl }}" alt="{{ $p->name }}" class="h-full w-full object-cover">
                            </a>
                            <div>
                              <a href="{{ route('product', $p->slug) }}" class="font-medium text-slate-900 hover:text-[var(--gold)]">
                                {{ $p->name }}
                              </a>
                              <div class="text-[12px] text-slate-500 mt-0.5">Brand: {{ $p->brand->name ?? '—' }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="px-4 py-3 text-right">RWF {{ number_format($item->price, 0) }}</td>
                        <td class="px-4 py-3 text-center">
                          {{-- IMPORTANT: desktop qty inputs are enabled only on >=md by JS --}}
                          <input type="number" min="1"
                                 name="quantities[{{ $item->id }}]"
                                 value="{{ (int)$item->quantity }}"
                                 class="qty-desktop w-20 rounded-md border border-slate-300 px-2 py-1 text-center outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20">
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-900">
                          RWF {{ number_format($rowTotal, 0) }}
                        </td>
                        <td class="px-2 py-3 text-right">
                          <button type="button"
                                  class="p-2 text-slate-400 hover:text-red-500"
                                  title="Remove"
                                  @click="removeId={{ $item->id }}; removeOpen=true">
                            <i class="la la-trash text-lg"></i>
                          </button>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                          Your cart is empty.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              {{-- Mobile stacked list --}}
              <div class="md:hidden divide-y divide-slate-200">
                @forelse($cartItems as $item)
                  @php
                    $p = $item->product;
                    $img = $p->images;
                    if (is_array($img)) { $first = $img[0] ?? 'default.jpg'; }
                    else { $arr = json_decode($img, true); $first = $arr[0] ?? 'default.jpg'; }
                    $imgUrl = asset('storage/'.$first);
                    $rowTotal = (float)$item->price * (int)$item->quantity;
                  @endphp
                  <div class="p-4">
                    <div class="flex gap-3">
                      <a href="{{ route('product', $p->slug) }}" class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-slate-200">
                        <img src="{{ $imgUrl }}" class="h-full w-full object-cover" alt="{{ $p->name }}">
                      </a>
                      <div class="flex-1">
                        <a href="{{ route('product', $p->slug) }}" class="block font-medium text-slate-900">
                          {{ $p->name }}
                        </a>
                        <div class="text-[12px] text-slate-500">Brand: {{ $p->brand->name ?? '—' }}</div>

                        <div class="mt-2 flex items-center justify-between">
                          <div class="text-sm">RWF {{ number_format($item->price, 0) }}</div>
                          <div class="text-sm font-semibold">RWF {{ number_format($rowTotal, 0) }}</div>
                        </div>

                        <div class="mt-2 flex items-center justify-between">
                          {{-- IMPORTANT: mobile qty inputs are enabled only on <md by JS --}}
                          <input type="number" min="1"
                                 name="quantities[{{ $item->id }}]"
                                 value="{{ (int)$item->quantity }}"
                                 class="qty-mobile w-24 rounded-md border border-slate-300 px-2 py-1 text-center outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20">
                          <button type="button"
                                  class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-[13px] border border-red-200 text-red-600 hover:bg-red-50"
                                  @click="removeId={{ $item->id }}; removeOpen=true">
                            <i class="la la-trash"></i> Remove
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="p-8 text-center text-slate-500">Your cart is empty.</div>
                @endforelse
              </div>

              {{-- Actions bar --}}
              <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-4 py-3">
                <a href="{{ route('category') }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                  <i class="la la-arrow-left text-base"></i> Continue Shopping
                </a>
                <button type="submit" @click="removeId=null"
                        class="inline-flex items-center gap-2 rounded-md bg-[var(--gold)] px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
                  <i class="la la-sync"></i> Update Cart
                </button>
              </div>
            </form>

            {{-- ===== Delete confirmation modal (Alpine) ===== --}}
            <div x-cloak x-show="removeOpen" x-transition.opacity
                 class="fixed inset-0 z-40 bg-black/40"></div>

            <div x-cloak x-show="removeOpen" x-transition
                 class="fixed inset-0 z-50 grid place-items-center p-4">
              <div class="w-full max-w-sm rounded-xl bg-white shadow-ring border border-slate-200">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                  <h3 class="text-sm font-semibold text-slate-900">Remove item</h3>
                  <button class="p-2 text-slate-400 hover:text-slate-600" @click="removeOpen=false; removeId=null">
                    <i class="la la-close text-lg"></i>
                  </button>
                </div>
                <div class="px-4 py-4 text-sm text-slate-700">
                  Are you sure you want to remove this item from your cart?
                </div>
                <div class="px-4 pb-4 flex items-center justify-end gap-2">
                  <button class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                          @click="removeOpen=false; removeId=null">
                    Cancel
                  </button>
                  <button class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:opacity-90"
                          @click="$refs.mainForm.submit()">
                    Remove
                  </button>
                </div>
              </div>
            </div>
            {{-- ===== End modal ===== --}}
          </div>
        </section>

        {{-- SUMMARY --}}
        <aside class="col-span-12 lg:col-span-3">
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4">
            <h3 class="text-sm font-semibold text-slate-900">Cart Total</h3>

            <dl class="mt-3 space-y-2 text-sm">
              <div class="flex items-center justify-between">
                <dt class="text-slate-600">Subtotal</dt>
                <dd class="font-semibold text-slate-900">RWF {{ number_format($subtotal, 0) }}</dd>
              </div>
              <div class="flex items-center justify-between border-t border-slate-200 pt-2">
                <dt class="text-slate-700">Total</dt>
                <dd class="text-base font-extrabold text-slate-900">RWF {{ number_format($subtotal, 0) }}</dd>
              </div>
            </dl>

            <form action="{{ route('order.place') }}" method="POST" class="mt-4">
              @csrf
              <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-[var(--gold)] px-4 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                <i class="la la-check-circle text-lg"></i> Place Order
              </button>
            </form>

            <a href="{{ route('category') }}"
               class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
              <i class="la la-store-alt text-base"></i> Continue Shopping
            </a>
          </div>
        </aside>
      </div>
    </div>
  </main>

  {{-- FOOTER & AUTH MODAL --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- JS: disable qty inputs for the hidden layout so only one set submits --}}
  <script>
    function toggleQtyInputs(){
      const isDesktop = window.matchMedia('(min-width: 768px)').matches; // md breakpoint
      document.querySelectorAll('.qty-desktop').forEach(el => el.disabled = !isDesktop);
      document.querySelectorAll('.qty-mobile').forEach(el => el.disabled =  isDesktop);
    }
    document.addEventListener('DOMContentLoaded', toggleQtyInputs);
    window.addEventListener('resize', toggleQtyInputs);

    // Auto-close any .alert after 3s (if you still use them)
    setTimeout(()=>document.querySelectorAll('.alert').forEach(el=>el.remove()), 3000);
  </script>
</body>
</html>
