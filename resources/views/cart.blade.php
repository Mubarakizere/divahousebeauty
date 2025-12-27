<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Shopping Cart | Diva House Beauty</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind (preflight OFF) + Alpine --}}
  <script> tailwind = { config: { corePlugins: { preflight: false } } } </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons: Line Awesome + Brand Icons --}}
  <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css"/>

  {{-- Google Fonts: Playfair Display + Inter --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

  <style>
    /* Classic Luxury Palette */
    :root { 
      --gold: #C5A059; /* Muted, metallic gold */
      --black: #0F172A; /* Deep navy-black */
      --gray-light: #F9FAFB;
    }
    
    body { font-family: 'Inter', sans-serif; color: #334155; }
    h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; }

    .shadow-ring { box-shadow: 0 0 0 1px rgba(0,0,0,.05); }
    [x-cloak] { display: none !important; }
    
    /* Small helpers */
    .btn-gold {
        background-color: var(--gold);
        color: white;
        transition: all 0.3s ease;
    }
    .btn-gold:hover {
        background-color: #B08D4C;
        transform: translateY(-1px);
    }
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

  {{-- ===================== BREADCRUMB ===================== --}}
  <section class="bg-white border-b border-slate-100">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-4">
      <nav class="text-xs uppercase tracking-widest text-slate-400 font-medium">
        <a href="{{ route('home') }}" class="hover:text-[var(--gold)] transition-colors">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ url('/category') }}" class="hover:text-[var(--gold)] transition-colors">Shop</a>
        <span class="mx-2">/</span>
        <span class="text-[var(--black)]">Cart</span>
      </nav>
    </div>
  </section>

  {{-- ===================== PAGE CONTENT ===================== --}}
  <main class="py-12">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="mb-10 text-center md:text-left border-b border-slate-200 pb-6">
        <h1 class="text-3xl sm:text-4xl font-serif text-[var(--black)] mb-2">Shopping Cart</h1>
        <p class="text-sm text-slate-500 font-light">Review your items and proceed to secure checkout.</p>
      </div>

      <div class="grid grid-cols-12 gap-8">

        {{-- CART TABLE --}}
        <section class="col-span-12 lg:col-span-8">
          <div class="bg-white border border-slate-100 rounded-lg overflow-hidden mb-6"
               x-data="{ removeOpen:false, removeId:null }">
            
            <form x-ref="mainForm" action="{{ route('cart.update') }}" method="POST">
              @csrf
              <input type="hidden" name="remove_id" x-model="removeId">

              {{-- Desktop Table --}}
              <div class="hidden md:block">
                <table class="w-full text-sm">
                  <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-xs uppercase tracking-wider font-semibold text-slate-500 text-left">
                      <th class="px-6 py-4 font-serif text-[var(--black)]">Product</th>
                      <th class="px-6 py-4 font-serif text-[var(--black)] text-right">Price</th>
                      <th class="px-6 py-4 font-serif text-[var(--black)] text-center">Quantity</th>
                      <th class="px-6 py-4 font-serif text-[var(--black)] text-right">Total</th>
                      <th class="px-4 py-4 w-10"></th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    @forelse($cartItems as $item)
                      @php
                        $p = $item->product;
                        
                        // Robust image logic
                        $img = is_string($p->images) ? json_decode($p->images, true) : $p->images;
                        $first = is_array($img) && !empty($img) ? $img[0] : null;
                        
                        $imgUrl = $first 
                            ? (Str::startsWith($first, 'http') ? $first : asset('storage/' . $first))
                            : asset('assets/images/default-product.jpg');

                        $rowTotal = (float)$item->price * (int)$item->quantity;
                      @endphp
                      <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                          <div class="flex items-center gap-4">
                            <a href="{{ route('product', $p->slug) }}" class="block h-20 w-16 flex-shrink-0 bg-slate-50 overflow-hidden border border-slate-100">
                              <img src="{{ $imgUrl }}" alt="{{ $p->name }}" class="h-full w-full object-cover mix-blend-multiply"
                                   onerror="this.onerror=null; this.src='{{ asset('assets/images/default-product.jpg') }}';">
                            </a>
                            <div>
                              <div class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">{{ $p->brand->name ?? 'Diva House' }}</div>
                              <a href="{{ route('product', $p->slug) }}" class="font-serif text-lg text-[var(--black)] hover:text-[var(--gold)] transition-colors leading-tight block mb-1">
                                {{ $p->name }}
                              </a>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-slate-600">Rw {{ number_format($item->price, 0) }}</td>
                        <td class="px-6 py-4 text-center">
                          <input type="number" min="1"
                                 name="quantities[{{ $item->id }}]"
                                 value="{{ (int)$item->quantity }}"
                                 class="qty-desktop w-16 text-center border-b border-slate-300 bg-transparent py-1 focus:outline-none focus:border-[var(--black)] transition-colors text-slate-700">
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-[var(--black)]">
                          Rw {{ number_format($rowTotal, 0) }}
                        </td>
                        <td class="px-4 py-4 text-right">
                          <button type="button"
                                  class="text-slate-300 hover:text-rose-500 transition-colors"
                                  title="Remove"
                                  @click="removeId={{ $item->id }}; removeOpen=true">
                            <i class="la la-times text-xl"></i>
                          </button>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                          <p class="text-slate-500 mb-4">Your cart is currently empty.</p>
                          <a href="{{ url('/category') }}" class="text-[var(--gold)] hover:text-[var(--black)] underline decoration-1 underline-offset-4">Start Shopping</a>
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              {{-- Mobile Stacked --}}
              <div class="md:hidden divide-y divide-slate-100">
                @forelse($cartItems as $item)
                  @php
                        $p = $item->product;
                        $img = is_string($p->images) ? json_decode($p->images, true) : $p->images;
                        $first = is_array($img) && !empty($img) ? $img[0] : null;
                        $imgUrl = $first 
                            ? (Str::startsWith($first, 'http') ? $first : asset('storage/' . $first))
                            : asset('assets/images/default-product.jpg');
                        $rowTotal = (float)$item->price * (int)$item->quantity;
                  @endphp
                  <div class="p-4 bg-white">
                    <div class="flex gap-4">
                      <a href="{{ route('product', $p->slug) }}" class="h-24 w-20 flex-shrink-0 bg-slate-50 overflow-hidden border border-slate-100">
                        <img src="{{ $imgUrl }}" class="h-full w-full object-cover" alt="{{ $p->name }}" onError="this.src='{{ asset('assets/images/default-product.jpg') }}'">
                      </a>
                      <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <div class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">{{ $p->brand->name ?? 'Diva House' }}</div>
                                <a href="{{ route('product', $p->slug) }}" class="font-serif text-base text-[var(--black)] leading-tight block">
                                  {{ $p->name }}
                                </a>
                            </div>
                            <button type="button" class="text-slate-300 hover:text-rose-500" @click="removeId={{ $item->id }}; removeOpen=true">
                                <i class="la la-times text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-end justify-between mt-3">
                           <div>
                               <div class="text-xs text-slate-500 mb-1">Price: Rw {{ number_format($item->price, 0) }}</div>
                               <input type="number" min="1"
                                      name="quantities[{{ $item->id }}]"
                                      value="{{ (int)$item->quantity }}"
                                      class="qty-mobile w-16 text-center border border-slate-200 rounded py-1 text-sm bg-slate-50 focus:outline-none focus:border-[var(--black)]">
                           </div>
                           <div class="text-sm font-bold text-[var(--black)]">
                               Rw {{ number_format($rowTotal, 0) }}
                           </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                   <div class="p-12 text-center">
                      <p class="text-slate-500 mb-4">Your cart is currently empty.</p>
                      <a href="{{ url('/category') }}" class="btn-gold px-6 py-3 text-xs uppercase tracking-widest font-bold inline-block">Start Shopping</a>
                   </div>
                @endforelse
              </div>

              {{-- Cart Actions --}}
              @if($cartItems->isNotEmpty())
                <div class="bg-slate-50 px-6 py-4 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100">
                   <a href="{{ url('/category') }}" class="text-xs uppercase tracking-widest font-bold text-slate-500 hover:text-[var(--gold)] transition-colors flex items-center gap-2">
                     <i class="la la-long-arrow-left text-lg"></i> Continue Shopping
                   </a>
                   
                   <button type="submit" @click="removeId=null" class="text-xs uppercase tracking-widest font-bold text-[var(--black)] hover:text-[var(--gold)] transition-colors flex items-center gap-2">
                     <i class="la la-sync text-lg"></i> Update Cart
                   </button>
                </div>
              @endif

            </form>

            {{-- Delete Modal --}}
            <div x-cloak x-show="removeOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
               <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="removeOpen=false; removeId=null" x-transition.opacity></div>
               <div class="relative w-full max-w-sm bg-white rounded-lg shadow-2xl p-6 text-center" x-transition.scale>
                   <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-4">
                       <i class="la la-trash text-2xl"></i>
                   </div>
                   <h3 class="text-lg font-serif text-[var(--black)] mb-2">Remove Item?</h3>
                   <p class="text-sm text-slate-500 mb-6">Are you sure you want to remove this item from your cart?</p>
                   <div class="flex gap-3 justify-center">
                       <button @click="removeOpen=false; removeId=null" class="px-5 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800">
                           Cancel
                       </button>
                       <button @click="$refs.mainForm.submit()" class="px-5 py-2 text-sm font-semibold bg-rose-600 text-white rounded hover:bg-rose-700 shadow-sm">
                           Remove
                       </button>
                   </div>
               </div>
            </div>

          </div>
        </section>

        {{-- SUMMARY --}}
        <aside class="col-span-12 lg:col-span-4">
           <div class="bg-[#F9FAFB] p-6 md:p-8 rounded-lg border border-slate-100 sticky top-24">
             <h3 class="text-lg font-serif text-[var(--black)] border-b border-slate-200 pb-4 mb-6">Order Summary</h3>
             
             @php
               $appliedCoupon = null;
               $discount = 0;
               $couponCode = session('coupon_code');
               if ($couponCode) {
                   $appliedCoupon = \App\Models\Coupon::where('code', $couponCode)->where('is_active', true)->first();
                   if ($appliedCoupon) {
                       if ($appliedCoupon->type === 'percentage') {
                           $discount = ($subtotal * $appliedCoupon->value) / 100;
                           if ($appliedCoupon->max_discount && $discount > $appliedCoupon->max_discount) {
                               $discount = $appliedCoupon->max_discount;
                           }
                       } elseif ($appliedCoupon->type === 'fixed') {
                           $discount = min($appliedCoupon->value, $subtotal);
                       }
                   }
               }
               $total = max(0, $subtotal - $discount);
             @endphp

             {{-- Subtotals --}}
             <div class="space-y-4 mb-6">
               <div class="flex justify-between items-center text-sm text-slate-600">
                 <span>Subtotal</span>
                 <span class="font-medium text-slate-900">Rw {{ number_format($subtotal, 0) }}</span>
               </div>
               
               @if($discount > 0)
                 <div class="flex justify-between items-center text-sm text-emerald-600">
                   <span>Discount</span>
                   <span class="font-medium">- Rw {{ number_format($discount, 0) }}</span>
                 </div>
               @endif

               <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                 <span class="text-base font-serif text-[var(--black)]">Total</span>
                 <span class="text-xl font-serif text-[var(--black)]">Rw {{ number_format($total, 0) }}</span>
               </div>
               <p class="text-xs text-slate-400 text-right italic">Shipping calculated at checkout</p>
             </div>

             {{-- Coupon Form --}}
             <div class="mb-8">
                 @if($appliedCoupon)
                    <div class="flex items-center justify-between bg-white border border-emerald-200 rounded p-3 text-sm">
                        <div class="flex items-center gap-2 text-emerald-700">
                            <i class="la la-tag"></i>
                            <span class="font-semibold">{{ $appliedCoupon->code }}</span>
                        </div>
                        <form action="{{ route('cart.removeCoupon') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-rose-500"><i class="la la-times"></i></button>
                        </form>
                    </div>
                 @else
                     <form action="{{ route('cart.applyCoupon') }}" method="POST" class="relative">
                         @csrf
                         <input type="text" name="coupon_code" placeholder="Gift card or discount code" 
                                class="w-full bg-white border border-slate-300 rounded-md py-2.5 pl-3 pr-20 text-sm outline-none focus:border-[var(--gold)] focus:ring-1 focus:ring-[var(--gold)]" required>
                         <button type="submit" class="absolute right-1 top-1 bottom-1 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold uppercase rounded transition-colors">Apply</button>
                     </form>
                 @endif

                 {{-- Alerts --}}
                 @if(session('success'))
                    <p class="text-xs text-emerald-600 mt-2 flex items-center gap-1"><i class="la la-check-circle"></i> {{ session('success') }}</p>
                 @endif
                 @if(session('error'))
                    <p class="text-xs text-rose-600 mt-2 flex items-center gap-1"><i class="la la-exclamation-circle"></i> {{ session('error') }}</p>
                 @endif
             </div>

             {{-- Checkout Button --}}
             <form action="{{ route('order.place') }}" method="POST">
                @csrf
                <button type="submit" class="w-full btn-gold py-4 text-xs font-bold uppercase tracking-widest shadow-lg flex items-center justify-center gap-2">
                    <i class="la la-lock"></i> Proceed to Checkout
                </button>
             </form>

             {{-- Trust Badges --}}
             <div class="mt-8 text-center">
                 <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-3">Guaranteed Safe Checkout</p>
                 <div class="flex justify-center gap-3 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                     <img src="https://upload.wikimedia.org/wikipedia/commons/d/d6/Visa_2021.svg" class="h-6 w-auto" alt="Visa">
                     <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Mastercard_2019_logo.svg" class="h-6 w-auto" alt="Mastercard">
                     <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" class="h-6 w-auto" alt="PayPal">
                 </div>
             </div>
           </div>
        </aside>

      </div>
    </div>
  </main>

  {{-- FOOTER & AUTH MODAL --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  <script>
    function toggleQtyInputs(){
      const isDesktop = window.matchMedia('(min-width: 768px)').matches; 
      document.querySelectorAll('.qty-desktop').forEach(el => el.disabled = !isDesktop);
      document.querySelectorAll('.qty-mobile').forEach(el => el.disabled =  isDesktop);
    }
    document.addEventListener('DOMContentLoaded', toggleQtyInputs);
    window.addEventListener('resize', toggleQtyInputs);
  </script>
</body>
</html>
