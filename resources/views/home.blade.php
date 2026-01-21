<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  
  {{-- Google Search Console Verification --}}
  <meta name="google-site-verification" content="42_XUFLKGXoph33K7-fbfNzwRUFnFvInTzPQCuIJLYM" />
  
  <title>Diva House Beauty - Premium Cosmetics & Fashion</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind + Alpine --}}
  <script>
    tailwind = { config: { corePlugins: { preflight: false } } }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>
  
  {{-- Bootstrap (minimal for pagination) --}}
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

  {{-- Google Fonts: Playfair Display (Headings) + Inter (Body) --}}
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

    .shadow-ring { box-shadow: 0 0 0 1px rgba(0,0,0,.05), 0 2px 8px rgba(0,0,0,.04); }
    .no-scrollbar::-webkit-scrollbar { display: none } 
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none }
    [x-cloak] { display: none !important; }
    
    .badge { display: inline-flex; align-items: center; font-weight: 600; line-height: 1; padding: .35rem .65rem; font-size: .65rem; letter-spacing: 0.05em; text-transform: uppercase; }
    .badge-new { background: var(--black); color: #fff; }
    .badge-sale { background: #9F1239; color: #fff; } /* More elegant muted red */
    .line-through { text-decoration: line-through; }
    
    /* Elegant Fade Animation */
    @keyframes subtleFade {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .hero-slide { animation: subtleFade 1.2s ease-out; }

    /* Button Reset for Luxury Feel */
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

  <!-- Currency Converter Styles -->
  <link rel="stylesheet" href="{{ asset('css/currency-styles.css') }}">
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen:false, authTab:'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- HEADER --}}
  @include('partials.header_home2')

  {{-- MAIN CONTENT --}}
  <main class="relative overflow-hidden">

    {{-- HERO BANNER SLIDER (Editorial Style) --}}
    @if($heroBanners && $heroBanners->count() > 0)
    <section class="relative bg-[var(--black)] overflow-hidden h-[600px] md:h-[700px] lg:h-[800px]" x-data="{ 
      current: 0, 
      total: {{ $heroBanners->count() }},
      autoplay: null,
      init() {
        if(this.total > 1) {
          this.autoplay = setInterval(() => { this.next() }, 6000);
        }
      },
      next() { this.current = (this.current + 1) % this.total },
      prev() { this.current = (this.current - 1 + this.total) % this.total },
      goto(i) { this.current = i; clearInterval(this.autoplay); }
    }">
      {{-- Slides --}}
      @foreach($heroBanners as $index => $banner)
      <div x-show="current === {{ $index }}" 
           x-transition:enter="transition ease-out duration-1000"
           x-transition:enter-start="opacity-50 scale-105"
           x-transition:enter-end="opacity-100 scale-100"
           x-transition:leave="transition ease-in duration-1000"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="absolute inset-0 z-0"
           style="{{ $index === 0 ? '' : 'display: none;' }}">
        
        <img src="{{ $banner->image_url }}" 
             alt="{{ $banner->name }}"
             class="w-full h-full object-cover">
             
        {{-- Subtle darkening for text legibility, but NO heavy colored gradients --}}
        <div class="absolute inset-0 bg-black/20"></div>

        {{-- Content Overlay (Editorial Box) --}}
        @if($banner->title || $banner->subtitle)
        <div class="absolute inset-0 flex items-center justify-center text-center px-6">
          <div class="max-w-4xl hero-slide" x-show="current === {{ $index }}">
            @if($banner->subtitle)
              <p class="text-white/90 text-sm md:text-base uppercase tracking-[0.2em] mb-4 font-medium drop-shadow-md">{{ $banner->subtitle }}</p>
            @endif
            @if($banner->title)
              <h2 class="text-4xl md:text-6xl lg:text-7xl text-white mb-8 font-serif leading-tight drop-shadow-lg">{{ $banner->title }}</h2>
            @endif
            @if($banner->link_text)
              <a href="{{ $banner->link ?? '#' }}" class="inline-block border border-white text-white px-8 py-3 uppercase text-xs tracking-widest hover:bg-white hover:text-black transition-colors duration-300">
                {{ $banner->link_text }}
              </a>
            @endif
          </div>
        </div>
        @endif
      </div>
      @endforeach

      {{-- Arrow Nav (Minimalist) --}}
      @if($heroBanners->count() > 1)
        <button @click="prev" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 text-white/50 hover:text-white transition p-2">
           <i class="la la-long-arrow-left text-4xl"></i>
        </button>
        <button @click="next" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 text-white/50 hover:text-white transition p-2">
           <i class="la la-long-arrow-right text-4xl"></i>
        </button>

        {{-- Dots --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-3 z-20">
          @foreach($heroBanners as $index => $banner)
          <button @click="goto({{ $index }})"
                  class="w-2 h-2 rounded-full transition-all duration-300 border border-white"
                  :class="current === {{ $index }} ? 'bg-white scale-125' : 'bg-transparent text-white/50'"></button>
          @endforeach
        </div>
      @endif
    </section>
    @endif

    {{-- HERO SIDE BANNERS (Editorial Grid) --}}
    @if($heroSideBanners && $heroSideBanners->count() > 0)
    <section class="py-12 bg-white border-b border-gray-100">
      <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          @foreach($heroSideBanners as $banner)
          <a href="{{ $banner->link ?? '#' }}" target="{{ $banner->target }}" 
             class="group block relative overflow-hidden">
            <div class="aspect-[4/3] overflow-hidden bg-gray-100">
              <img src="{{ $banner->image_url }}" alt="{{ $banner->name }}" 
                   class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            </div>
            
            <div class="mt-4 text-center">
              @if($banner->subtitle)
                <p class="text-[10px] uppercase tracking-widest text-slate-500 mb-1">{{ $banner->subtitle }}</p>
              @endif
              @if($banner->title)
                <h3 class="text-xl font-serif text-[var(--black)] group-hover:text-[var(--gold)] transition-colors">{{ $banner->title }}</h3>
              @endif
              @if($banner->link_text)
                <span class="inline-block mt-2 text-xs border-b border-[var(--black)] pb-0.5 group-hover:border-[var(--gold)] transition-colors">
                  {{ $banner->link_text }}
                </span>
              @endif
            </div>
          </a>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- CATEGORY TOP BANNERS (Split Layout) --}}
    @if($categoryTopBanners && $categoryTopBanners->count() > 0)
    <section class="py-6">
      <div class="mx-auto max-w-7xl px-4 sm:px-6">
        @foreach($categoryTopBanners as $banner)
        <a href="{{ $banner->link ?? '#' }}" target="{{ $banner->target }}" 
           class="group grid grid-cols-1 md:grid-cols-2 bg-[#F9FAFB] items-center mb-8">
           
           {{-- Info Side --}}
           <div class="p-8 md:p-12 order-2 md:order-1 text-center md:text-left">
              @if($banner->subtitle)
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--gold)] mb-2">{{ $banner->subtitle }}</p>
              @endif
              @if($banner->title)
                <h2 class="text-3xl md:text-4xl font-serif text-[var(--black)] mb-4">{{ $banner->title }}</h2>
              @endif
              @if($banner->link_text)
                <span class="inline-block px-6 py-3 border border-[var(--black)] text-[var(--black)] text-sm uppercase tracking-wider hover:bg-[var(--black)] hover:text-white transition-all">
                  {{ $banner->link_text }}
                </span>
              @endif
           </div>

           {{-- Image Side --}}
           <div class="relative h-64 md:h-[400px] overflow-hidden order-1 md:order-2">
             <img src="{{ $banner->image_url }}" alt="{{ $banner->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
           </div>
        </a>
        @endforeach
      </div>
    </section>
    @endif

    {{-- BEST SELLERS --}}
    @if($bestSellers && $bestSellers->count() > 0)
    <section class="py-12 bg-white">
      <div class="mx-auto max-w-7xl px-3 sm:px-4">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">Best Sellers</h2>
            <p class="text-sm text-slate-500 mt-1">Top-rated products loved by customers</p>
          </div>
          <a href="{{ route('category') }}" class="text-[var(--gold)] hover:underline text-sm font-medium">
            View All <i class="la la-arrow-right"></i>
          </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          @foreach($bestSellers as $product)
          @include('partials.product_card', ['product' => $product])
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- NEW ARRIVALS --}}
    @if($newArrivals && $newArrivals->count() > 0)
    <section class="py-12 bg-gray-50">
      <div class="mx-auto max-w-7xl px-3 sm:px-4">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">New Arrivals</h2>
            <p class="text-sm text-slate-500 mt-1">Latest products just in</p>
          </div>
          <a href="{{ route('category') }}" class="text-[var(--gold)] hover:underline text-sm font-medium">
            View All <i class="la la-arrow-right"></i>
          </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          @foreach($newArrivals as $product)
          @include('partials.product_card', ['product' => $product])
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- MID PAGE BANNERS (Double Feature) --}}
    @if($midPageBanners && $midPageBanners->count() > 0)
    <section class="py-12">
      <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-gray-200 border border-gray-200">
          @foreach($midPageBanners as $banner)
          <a href="{{ $banner->link ?? '#' }}" target="{{ $banner->target }}" 
             class="group relative block aspect-[1/1] md:aspect-[4/3] overflow-hidden bg-white">
            <img src="{{ $banner->image_url }}" alt="{{ $banner->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-white/0 group-hover:bg-white/10 transition-colors">
               <div class="bg-white/90 backdrop-blur-sm px-8 py-6 max-w-xs shadow-sm">
                 @if($banner->subtitle)
                   <p class="text-xs uppercase tracking-widest text-gray-500 mb-2">{{ $banner->subtitle }}</p>
                 @endif
                 @if($banner->title)
                   <h3 class="text-2xl font-serif text-[var(--black)] mb-3">{{ $banner->title }}</h3>
                 @endif
                 @if($banner->link_text)
                   <span class="text-xs font-bold uppercase tracking-wider underline decoration-[var(--gold)] decoration-2 underline-offset-4">
                     {{ $banner->link_text }}
                   </span>
                 @endif
               </div>
            </div>
          </a>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- ON SALE --}}
    @if($onSale && $onSale->count() > 0)
    <section class="py-12 bg-white">
      <div class="mx-auto max-w-7xl px-3 sm:px-4">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">🔥 On Sale</h2>
            <p class="text-sm text-slate-500 mt-1">Limited time offers</p>
          </div>
          <a href="{{ route('deals') }}" class="text-[var(--gold)] hover:underline text-sm font-medium">
            View All <i class="la la-arrow-right"></i>
          </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          @foreach($onSale as $product)
          @include('partials.product_card', ['product' => $product])
          @endforeach
        </div>
      </div>
    </section>
    @endif

    {{-- BRAND SLIDERS (existing) --}}
    @include('partials.brand_slider', [
      'title' => 'Trending Now',
      'subtitle' => 'Fresh arrivals across all brands',
      'collection' => $brandLatestAll
    ])

    @include('partials.brand_slider', [
      'title' => ($cat5->name ?? 'Beauty') . ' Latest',
      'subtitle' => 'Newest uploads',
      'collection' => $brandLatest5
    ])

    @include('partials.brand_slider', [
      'title' => ($cat15->name ?? 'Fashion') . ' Latest',
      'subtitle' => 'Newest uploads',
      'collection' => $brandLatest15,
      'minCount' => 10,
      'fallbackCategoryId' => 15,
    ])

  </main>
  @include('partials.whyus')
    {{-- FOOTER ABOVE BANNERS (Full Width Editorial) --}}
    @if($footerAboveBanners && $footerAboveBanners->count() > 0)
    <section class="py-16">
      <div class="mx-auto max-w-7xl px-4 sm:px-6">
        @foreach($footerAboveBanners as $banner)
        <a href="{{ $banner->link ?? '#' }}" target="{{ $banner->target }}" 
           class="group relative block w-full overflow-hidden bg-gray-100">
           
           {{-- Mobile: stacked, Desktop: side-by-side or clean overlay --}}
           <div class="relative h-[400px] md:h-[500px]">
             <img src="{{ $banner->image_url }}" alt="{{ $banner->name }}" class="w-full h-full object-cover">
             
             {{-- Solid Text Box Floating Center --}}
             <div class="absolute inset-0 flex items-center justify-center p-4">
               <div class="bg-white p-8 md:p-12 text-center max-w-2xl shadow-2xl skew-y-0 hover:-skew-y-1 transition-transform duration-500">
                  @if($banner->title)
                    <h2 class="text-3xl md:text-5xl font-serif text-[var(--black)] mb-4">{{ $banner->title }}</h2>
                  @endif
                  @if($banner->subtitle)
                    <p class="text-slate-600 mb-6 font-light text-lg">{{ $banner->subtitle }}</p>
                  @endif
                  @if($banner->link_text)
                    <span class="inline-block px-8 py-3 bg-[var(--black)] text-white hover:bg-[var(--gold)] transition-colors uppercase text-sm tracking-widest font-medium">
                      {{ $banner->link_text }}
                    </span>
                  @endif
               </div>
             </div>
           </div>
        </a>
        @endforeach
      </div>
    </section>
    @endif

  {{-- FOOTER & AUTH MODAL --}}

  @include('partials.footer')
  @includeIf('partials.auth_modal')

  <!-- Currency Converter Script -->
  <script src="{{ asset('js/currency-converter.js') }}"></script>
</body>
</html>
