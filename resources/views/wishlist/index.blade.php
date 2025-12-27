<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>My Wishlist | Diva House Beauty</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind (preflight OFF) + Alpine --}}
  <script>tailwind = { config: { corePlugins: { preflight: false } } }</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons --}}
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
    .badge { display: inline-flex; align-items: center; font-weight: 600; line-height: 1; padding: .35rem .65rem; font-size: .65rem; letter-spacing: 0.05em; text-transform: uppercase; }
    .badge-new { background: var(--black); color: #fff; }
    .badge-sale { background: #9F1239; color: #fff; }
    .line-through { text-decoration: line-through; }
  </style>
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen:false, authTab:'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- HEADER --}}
  @include('partials.header_home2')

  {{-- Breadcrumb --}}
  <section class="bg-white border-b border-slate-100">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-4">
      <nav class="text-xs uppercase tracking-widest text-slate-400 font-medium">
        <a href="{{ route('home') }}" class="hover:text-[var(--gold)] transition-colors">Home</a>
        <span class="mx-2">/</span>
        <span class="text-[var(--black)]">My Wishlist</span>
      </nav>
    </div>
  </section>

  {{-- CONTENT --}}
  <main class="py-12">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="text-center mb-12">
        <h1 class="text-3xl sm:text-4xl font-serif text-[var(--black)] mb-2">My Wishlist</h1>
        <div class="w-16 h-0.5 bg-[var(--gold)] mx-auto mb-4"></div>
        <p class="text-xs uppercase tracking-widest text-slate-500">{{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }} saved</p>
      </div>

      @if($wishlistItems->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          @foreach($wishlistItems as $item)
            @php
              $product = $item->product;
              // Image logic similar to product card
              $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
              $firstImage = is_array($images) && !empty($images) ? $images[0] : null;
            @endphp
            
            <div class="group relative bg-white border border-slate-100 hover:border-[var(--gold)] transition-colors duration-300">
                {{-- Remove Button (Absolute) --}}
                <form action="{{ route('wishlist.remove', $product->id) }}" method="POST" class="absolute top-2 right-2 z-20">
                  @csrf
                  @method('DELETE')
                  <button type="submit" 
                          class="w-8 h-8 rounded-full bg-white text-slate-400 hover:text-rose-500 shadow-sm border border-slate-100 flex items-center justify-center transition-colors"
                          title="Remove from wishlist">
                    <i class="la la-times"></i>
                  </button>
                </form>

                {{-- Image Link --}}
                <a href="{{ route('product', $product->slug) }}" class="block relative aspect-[4/5] bg-[#F9FAFB] overflow-hidden">
                   @if($firstImage)
                      <img src="{{ asset('storage/' . $firstImage) }}" 
                           alt="{{ $product->name }}"
                           class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                           onerror="this.onerror=null; this.src='{{ asset('assets/images/default-product.jpg') }}'; this.style.objectFit='contain'; this.style.padding='2rem';">
                   @else
                      <div class="w-full h-full flex items-center justify-center text-slate-300">
                          <i class="la la-image text-4xl"></i>
                      </div>
                   @endif

                   {{-- Quick Move to Cart (Fade in) --}}
                   @if($product->in_stock)
                     <div class="absolute bottom-3 inset-x-3 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 z-10">
                        <form action="{{ route('wishlist.moveToCart', $product->id) }}" method="POST">
                          @csrf
                          <button type="submit" class="w-full h-10 bg-[var(--black)] text-white text-xs uppercase tracking-widest font-bold hover:bg-[var(--gold)] transition-colors flex items-center justify-center gap-2">
                             Move to Cart
                          </button>
                        </form>
                     </div>
                   @endif
                </a>

                {{-- Info --}}
                <div class="p-4 text-center">
                    @if($product->category)
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">{{ $product->category->name }}</p>
                    @endif

                    <h3 class="text-sm font-medium text-slate-900 line-clamp-2 mb-2 min-h-[2.5em]">
                        <a href="{{ route('product', $product->slug) }}" class="hover:text-[var(--gold)] transition-colors">
                            {{ $product->name }}
                        </a>
                    </h3>

                    <div class="flex items-center justify-center gap-2">
                        @if($product->is_on_sale)
                            <span class="text-xs text-slate-400 line-through font-serif">Rw {{ number_format($product->price) }}</span>
                            <span class="text-sm font-medium text-rose-700 font-serif">Rw {{ number_format($product->sale_price) }}</span>
                        @else
                            <span class="text-sm font-medium text-[var(--black)] font-serif">Rw {{ number_format($product->price) }}</span>
                        @endif
                    </div>
                </div>
            </div>
          @endforeach
        </div>

        {{-- Continue Shopping --}}
        <div class="mt-12 text-center">
          <a href="{{ url('/category') }}" 
             class="inline-block border-b border-[var(--black)] pb-1 text-sm uppercase tracking-widest text-[var(--black)] hover:text-[var(--gold)] hover:border-[var(--gold)] transition-colors">
            Continue Shopping
          </a>
        </div>

      @else
        {{-- Empty State --}}
        <div class="max-w-md mx-auto text-center py-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-6">
                <i class="la la-heart-o text-4xl text-slate-300"></i>
            </div>
            <h2 class="text-2xl font-serif text-[var(--black)] mb-3">Your wishlist is empty</h2>
            <p class="text-slate-500 font-light mb-8">You haven't added any items to your wishlist yet. Browse our collections to find your favorites.</p>
            
            <a href="{{ url('/category') }}" 
               class="inline-flex items-center justify-center px-8 py-3 bg-[var(--black)] text-white text-xs uppercase tracking-widest font-bold hover:bg-[var(--gold)] transition-colors">
              Start Shopping
            </a>
        </div>
      @endif
    </div>
  </main>

  {{-- FOOTER + AUTH MODAL --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- Toast messages --}}
  @if(session()->has('message') || session()->has('error'))
    <div x-data="{ show:true }" x-show="show" x-init="setTimeout(()=>show=false,3000)"
         class="fixed top-3 right-3 z-50 px-4 py-3 rounded shadow-lg text-sm font-medium transition-all duration-300"
         :class="`{{ session()->has('message') ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}`">
      {{ session('message') ?? session('error') }}
    </div>
  @endif
</body>
</html>
