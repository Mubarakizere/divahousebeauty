<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Diva House Beauty — Shop</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind (preflight OFF) + Alpine for header/nav --}}
  <script>
    tailwind = { config: { corePlugins: { preflight: false } } }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Line Awesome icons (used throughout header/content) --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>

  {{-- Minimal Bootstrap only for pagination template (bootstrap-4) --}}
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

  {{-- noUiSlider for price filter --}}
  <link rel="stylesheet" href="{{ asset('assets/css/plugins/nouislider/nouislider.css') }}">

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
    .no-scrollbar::-webkit-scrollbar { display: none } 
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none }
    .nav-pill { border-radius: 9999px }
    [x-cloak] { display: none !important; }

    /* Small helpers */
    .badge { display: inline-flex; align-items: center; font-weight: 600; line-height: 1; padding: .35rem .65rem; font-size: .65rem; letter-spacing: 0.05em; text-transform: uppercase; }
    .badge-new { background: var(--black); color: #fff; }
    .badge-sale { background: #9F1239; color: #fff; }
    .line-through { text-decoration: line-through; }
    
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

  {{-- ===================== HEADER (Tailwind) ===================== --}}
  @include('partials.header_home2')

  {{-- ===================== HERO (light, simple) ===================== --}}
  {{-- ===================== HERO (Minimalist) ===================== --}}
  <header class="bg-white border-b border-gray-100 mb-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 py-12 text-center">
      <h1 class="text-4xl md:text-5xl font-serif text-[var(--black)] mb-3">
        @if(isset($search))
          Results for “{{ request('q') }}”
        @elseif(isset($brand))
          {{ $brand->name }}
        @elseif(isset($category) && $category)
          {{ $category->name }}
        @else
          All Collections
        @endif
      </h1>
      <p class="text-slate-500 font-light text-sm uppercase tracking-widest">
        {{ $shownProducts }} Products Found
      </p>
    </div>
  </header>

  {{-- ===================== PAGE CONTENT ===================== --}}
  <main class="py-6">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="grid grid-cols-12 gap-4 lg:gap-6">
        {{-- ===================== SIDEBAR FILTERS ===================== --}}
        @php
          $currentCategoryId = isset($category) && $category ? $category->id : null;
          $qVal = request('q', '');
          $selectedCats = request('categories', []);
          $minStart = (int) request('min_price', 0);
          $maxStart = (int) request('max_price', 10000);
        @endphp

        <aside class="col-span-12 lg:col-span-3 pr-0 lg:pr-8">
          <div class="sticky top-24">
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-100">
              <h3 class="text-lg font-serif text-[var(--black)]">Filters</h3>
              <a href="{{ route('category', $currentCategoryId) }}" class="text-xs text-slate-400 hover:text-[var(--gold)] uppercase tracking-wider">Reset</a>
            </div>

            {{-- Category filter --}}
            <div class="mb-8">
              <h4 class="text-xs font-bold uppercase tracking-widest text-[var(--black)] mb-3">Categories</h4>
              <form id="category-filter-form" action="{{ route('category', $currentCategoryId) }}" method="GET" class="space-y-2">
                @if($qVal) <input type="hidden" name="q" value="{{ $qVal }}"> @endif
                @php $selectedBrands = request('brands', []); @endphp
                @foreach($selectedBrands as $bid) <input type="hidden" name="brands[]" value="{{ $bid }}"> @endforeach
                <input type="hidden" name="min_price" value="{{ $minStart }}">
                <input type="hidden" name="max_price" value="{{ $maxStart }}">
                <div class="max-h-64 overflow-y-auto pr-1 no-scrollbar space-y-2">
                  @foreach($categories as $cat)
                    <label class="flex items-center gap-3 text-sm cursor-pointer group">
                      <div class="relative flex items-center">
                        <input type="checkbox"
                               name="categories[]"
                               value="{{ $cat->id }}"
                               {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }}
                               onchange="document.getElementById('category-filter-form').submit()"
                               class="peer appearance-none w-4 h-4 border border-slate-300 rounded-sm checked:bg-[var(--gold)] checked:border-[var(--gold)] transition-all">
                        <i class="la la-check text-white text-[10px] absolute inset-0 hidden peer-checked:flex items-center justify-center pointer-events-none"></i>
                      </div>
                      <span class="text-slate-600 group-hover:text-[var(--gold)] transition-colors">{{ $cat->name }}</span>
                    </label>
                  @endforeach
                </div>
              </form>
            </div>

            {{-- Brand filter --}}
            <div class="mb-8">
              <h4 class="text-xs font-bold uppercase tracking-widest text-[var(--black)] mb-3">Brands</h4>
              <form action="{{ route('category', $currentCategoryId) }}" method="GET" class="space-y-2">
                @foreach($selectedCats as $cid) <input type="hidden" name="categories[]" value="{{ $cid }}"> @endforeach
                <input type="hidden" name="min_price" value="{{ $minStart }}">
                <input type="hidden" name="max_price" value="{{ $maxStart }}">
                @if($qVal) <input type="hidden" name="q" value="{{ $qVal }}"> @endif
                
                @php $selectedBrands = request('brands', []); @endphp
                <div class="max-h-56 overflow-y-auto pr-1 no-scrollbar space-y-2">
                  @foreach($brands as $brand)
                    <label class="flex items-center gap-3 text-sm cursor-pointer group">
                      <div class="relative flex items-center">
                        <input type="checkbox"
                               name="brands[]"
                               value="{{ $brand->id }}"
                               {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}
                               class="peer appearance-none w-4 h-4 border border-slate-300 rounded-sm checked:bg-[var(--gold)] checked:border-[var(--gold)] transition-all">
                         <i class="la la-check text-white text-[10px] absolute inset-0 hidden peer-checked:flex items-center justify-center pointer-events-none"></i>
                      </div>
                      <span class="text-slate-600 group-hover:text-[var(--gold)] transition-colors">{{ $brand->name }}</span>
                    </label>
                  @endforeach
                </div>
                <button type="submit"
                        class="mt-4 w-full py-2 border border-[var(--black)] text-[var(--black)] text-xs uppercase tracking-wider hover:bg-[var(--black)] hover:text-white transition-all">
                  Apply Filters
                </button>
              </form>
            </div>

            {{-- Price filter --}}
            <div>
              <h4 class="text-xs font-bold uppercase tracking-widest text-[var(--black)] mb-3">Price Range</h4>
              <form id="price-filter-form" action="{{ route('category', $currentCategoryId) }}" method="GET">
                @foreach($selectedCats as $cid) <input type="hidden" name="categories[]" value="{{ $cid }}"> @endforeach
                @php $selectedBrands = request('brands', []); @endphp
                @foreach($selectedBrands as $bid) <input type="hidden" name="brands[]" value="{{ $bid }}"> @endforeach
                @if($qVal) <input type="hidden" name="q" value="{{ $qVal }}"> @endif

                {{-- Slider --}}
                <div id="price-slider" class="mb-4"></div>

                {{-- Manual Input Fields --}}
                <div class="grid grid-cols-2 gap-2 mb-3">
                  <div>
                    <label for="min_price_input" class="text-xs text-slate-500 block mb-1">Min (Rw)</label>
                    <input type="number" 
                           id="min_price_input" 
                           name="min_price" 
                           value="{{ $minStart }}" 
                           min="0" 
                           max="10000" 
                           step="100"
                           class="w-full px-2 py-1 text-sm border border-slate-300 rounded-md focus:border-[var(--gold)] focus:outline-none">
                  </div>
                  <div>
                    <label for="max_price_input" class="text-xs text-slate-500 block mb-1">Max (Rw)</label>
                    <input type="number" 
                           id="max_price_input" 
                           name="max_price" 
                           value="{{ $maxStart }}" 
                           min="0" 
                           max="10000" 
                           step="100"
                           class="w-full px-2 py-1 text-sm border border-slate-300 rounded-md focus:border-[var(--gold)] focus:outline-none">
                  </div>
                </div>

                <button type="submit" 
                        class="w-full py-2 border border-[var(--black)] text-[var(--black)] text-xs uppercase tracking-wider hover:bg-[var(--black)] hover:text-white transition-all">
                  Apply Price
                </button>
              </form>
            </div>
          </div>
        </aside>

        {{-- ===================== PRODUCT LIST ===================== --}}
        <section class="col-span-12 lg:col-span-9">
          {{-- Toolbar --}}
          <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <div class="text-sm text-slate-600">
              Showing <strong>{{ $shownProducts }}</strong> of <strong>{{ $totalProducts }}</strong>
            </div>
            <div>
              <form action="{{ route('category', $currentCategoryId) }}" method="GET" class="flex items-center gap-2">
                @if($qVal) <input type="hidden" name="q" value="{{ $qVal }}"> @endif
                @foreach($selectedCats as $cid)
                  <input type="hidden" name="categories[]" value="{{ $cid }}">
                @endforeach
                @php $selectedBrands = request('brands', []); @endphp
                @foreach($selectedBrands as $bid)
                  <input type="hidden" name="brands[]" value="{{ $bid }}">
                @endforeach
                <input type="hidden" name="min_price" value="{{ $minStart }}">
                <input type="hidden" name="max_price" value="{{ $maxStart }}">

                <label for="sortby" class="text-sm text-slate-600">Sort by</label>
                <select id="sortby" name="sortby" onchange="this.form.submit()" class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                  <option value="date" {{ request('sortby')=='date' || !request('sortby') ? 'selected' : '' }}>Newest First</option>
                  <option value="price_asc" {{ request('sortby')=='price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                  <option value="price_desc" {{ request('sortby')=='price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                  <option value="name" {{ request('sortby')=='name' ? 'selected' : '' }}>Name (A-Z)</option>
                  <option value="rating" {{ request('sortby')=='rating' ? 'selected' : '' }}>Top Rated</option>
                  <option value="popular" {{ request('sortby')=='popular' ? 'selected' : '' }}>Most Popular</option>
                </select>
              </form>
            </div>
          </div>

          {{-- Grid --}}
          <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($products as $product)
              @include('partials.product_card', ['product' => $product])
            @empty
              <div class="col-span-12 py-12 text-center text-slate-400">
                <i class="la la-search text-4xl mb-3 opacity-50"></i>
                <p>No products found matching your criteria.</p>
              </div>
            @endforelse
          </div>

          {{-- Pagination --}}
          <div class="mt-6 flex justify-center">
            {{ $products->withQueryString()->links('vendor.pagination.bootstrap-4') }}
          </div>
        </section>
      </div>
    </div>
  </main>

  {{-- ===================== FOOTER (your partial) ===================== --}}
  @include('partials.footer')

  {{-- ===================== AUTH MODAL (reuse Home2 if you extracted) ===================== --}}
  @includeIf('partials.auth_modal')

  {{-- ===================== JS ===================== --}}
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/nouislider.min.js') }}"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // noUiSlider – Price with bidirectional sync
      const sliderEl = document.getElementById('price-slider');
      if (sliderEl && window.noUiSlider) {
        const minStart = {{ $minStart }};
        const maxStart = {{ $maxStart }};
        
        // Create slider
        noUiSlider.create(sliderEl, {
          start: [minStart, maxStart],
          connect: true,
          step: 100,
          range: { 'min': 0, 'max': 10000 }
        });
        
        const minInput = document.getElementById('min_price_input');
        const maxInput = document.getElementById('max_price_input');
        
        // Update inputs when slider changes
        sliderEl.noUiSlider.on('update', function (values) {
          const a = Math.round(values[0]);
          const b = Math.round(values[1]);
          minInput.value = a;
          maxInput.value = b;
        });
        
        // Update slider when manual inputs change
        minInput.addEventListener('change', function() {
          const minVal = parseInt(this.value) || 0;
          const maxVal = parseInt(maxInput.value) || 10000;
          sliderEl.noUiSlider.set([minVal, maxVal]);
        });
        
        maxInput.addEventListener('change', function() {
          const minVal = parseInt(minInput.value) || 0;
          const maxVal = parseInt(this.value) || 10000;
          sliderEl.noUiSlider.set([minVal, maxVal]);
        });
      }
    });
  </script>
  
  <!-- Currency Converter Script -->
  <script src="{{ asset('js/currency-converter.js') }}"></script>
</body>
</html>
