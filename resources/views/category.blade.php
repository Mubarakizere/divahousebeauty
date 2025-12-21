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

  <style>
    :root{ --gold:#cc9966; --black:#111827; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
    .no-scrollbar::-webkit-scrollbar{display:none} .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
    .nav-pill{ border-radius:9999px }
    [x-cloak]{ display:none !important; }

    /* small helpers */
    .badge{ display:inline-flex; align-items:center; font-weight:700; line-height:1; border-radius:9999px; padding:.25rem .5rem; font-size:.675rem }
    .badge-new{ background:#10b981; color:#fff }
    .badge-sale{ background:#ef4444; color:#fff }
    .line-through { text-decoration: line-through; }
  </style>
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen:false, authTab:'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- ===================== HEADER (Tailwind) ===================== --}}
  @include('partials.header_home2')

  {{-- ===================== HERO (light, simple) ===================== --}}
  <header class="bg-white border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
        @if(isset($search))
  <h2>Search Results</h2>
@elseif(isset($brand))
  <h2>{{ $brand->name }} — Products</h2>
@elseif(isset($category) && $category)
  <h2>{{ $category->name }} Products</h2>
@else
  <h2>All Products</h2>
@endif

      </h1>
      <p class="mt-1 text-sm text-slate-500">
        Showing <strong>{{ $shownProducts }}</strong> of <strong>{{ $totalProducts }}</strong> products
        @if(request('q')) for: <em>“{{ request('q') }}”</em> @endif
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

        <aside class="col-span-12 lg:col-span-3">
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-slate-900">Filters</h3>
              <a href="{{ route('category', $currentCategoryId) }}" class="text-[12px] text-[var(--gold)] hover:underline">Clean all</a>
            </div>

            {{-- Category filter --}}
            <div class="mt-4">
              <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Categories</h4>
              <form action="{{ route('category', $currentCategoryId) }}" method="GET" class="space-y-2">
                @if($qVal) <input type="hidden" name="q" value="{{ $qVal }}"> @endif
                <div class="max-h-64 overflow-y-auto pr-1">
                  @foreach($categories as $cat)
                    <label class="flex items-center gap-2 text-sm">
                      <input type="checkbox"
                             name="categories[]"
                             value="{{ $cat->id }}"
                             {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }}
                             class="rounded border-slate-300 text-[var(--gold)] focus:ring-[var(--gold)]/40">
                      <span>{{ $cat->name }}</span>
                    </label>
                  @endforeach
                </div>
                <button type="submit"
                        class="mt-2 inline-flex items-center justify-center rounded-md bg-[var(--gold)] px-3 py-2 text-xs font-semibold text-white hover:opacity-90">
                  Apply Filter
                </button>
              </form>
            </div>

            {{-- Price filter --}}
            <div class="mt-6">
              <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Price</h4>
              <form action="{{ route('category', $currentCategoryId) }}" method="GET">
                @if($qVal) <input type="hidden" name="q" value="{{ $qVal }}"> @endif
                @foreach($selectedCats as $cid)
                  <input type="hidden" name="categories[]" value="{{ $cid }}">
                @endforeach

                <div class="text-[12px] text-slate-600 mb-2">
                  Price Range:
                  <span id="price-range-text">{{ $minStart }} - {{ $maxStart }}</span>
                </div>

                <input type="hidden" id="min_price" name="min_price" value="{{ $minStart }}">
                <input type="hidden" id="max_price" name="max_price" value="{{ $maxStart }}">

                <div id="price-slider" class="mt-3"></div>

                <button type="submit"
                        class="mt-3 inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
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
                <input type="hidden" name="min_price" value="{{ $minStart }}">
                <input type="hidden" name="max_price" value="{{ $maxStart }}">

                <label for="sortby" class="text-sm text-slate-600">Sort by</label>
                <select id="sortby" name="sortby" class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                  <option value="date" {{ request('sortby')=='date' ? 'selected' : '' }}>Date</option>
                  {{-- Add more when controller supports it (price_asc/price_desc, etc.) --}}
                </select>
                <button class="inline-flex items-center rounded-md bg-[var(--gold)] px-3 py-1.5 text-xs font-semibold text-white hover:opacity-90">
                  Apply
                </button>
              </form>
            </div>
          </div>

          {{-- Grid --}}
          <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-4">
            @forelse($products as $product)
              @php
                $url  = route('product', $product->slug);
                $img  = $product->first_image_url;
                $isNew = (bool) $product->is_new;
                $onSale = (bool) $product->is_on_sale;
                $salePrice = $product->sale_price;
                $brandName = $product->brand->name ?? 'Unknown';
              @endphp

              <article class="bg-white border border-slate-200 rounded-lg shadow-ring overflow-hidden flex flex-col">
                <a href="{{ $url }}" class="relative block">
                  <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy"
                       class="w-full h-48 object-cover">
                  <div class="absolute top-2 left-2 space-x-1">
                    @if($isNew)
                      <span class="badge badge-new">New</span>
                    @endif
                    @if($onSale)
                      <span class="badge badge-sale">Sale</span>
                    @endif
                  </div>
                </a>

                <div class="p-3 flex-1 flex flex-col">
                  <a href="{{ $url }}" class="text-sm font-semibold text-slate-900 line-clamp-2 min-h-[2.5rem]">
                    {{ $product->name }}
                  </a>
                  <div class="mt-1 text-[12px] text-slate-500">Brand: {{ $brandName }}</div>

                  <div class="mt-2">
                    @if($onSale && $salePrice)
                      <div class="flex items-baseline gap-2">
                        <div class="text-base font-bold text-slate-900">RWF {{ number_format($salePrice, 0) }}</div>
                        <div class="text-[12px] text-slate-400 line-through">RWF {{ number_format($product->price, 0) }}</div>
                      </div>
                    @else
                      <div class="text-base font-bold text-slate-900">RWF {{ number_format($product->price, 0) }}</div>
                    @endif
                  </div>

                  <form action="{{ url('addcart', $product->id) }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-[13px] font-semibold text-slate-700 hover:bg-slate-50">
                      <i class="la la-shopping-bag"></i> Add to Cart
                    </button>
                  </form>
                </div>
              </article>
            @empty
              <div class="col-span-12">
                <div class="rounded-md border border-slate-200 bg-white p-6 text-center text-slate-500">
                  No products matched your filters.
                </div>
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
      // noUiSlider – Price
      const sliderEl = document.getElementById('price-slider');
      if (sliderEl && window.noUiSlider) {
        const minStart = {{ $minStart }};
        const maxStart = {{ $maxStart }};
        noUiSlider.create(sliderEl, {
          start: [minStart, maxStart],
          connect: true,
          step: 100,
          range: { 'min': 0, 'max': 10000 }
        });
        const minInput = document.getElementById('min_price');
        const maxInput = document.getElementById('max_price');
        const rangeText = document.getElementById('price-range-text');
        sliderEl.noUiSlider.on('update', function (values) {
          const a = Math.round(values[0]);
          const b = Math.round(values[1]);
          minInput.value = a; maxInput.value = b;
          rangeText.textContent = a + ' - ' + b;
        });
      }
    });
  </script>
</body>
</html>
