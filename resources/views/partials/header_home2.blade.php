@php
  use Illuminate\Support\Str;

  // Ensure categories (with brands) are available
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

{{-- ===================== GLOBAL LOADER ===================== --}}
@include('partials.loader')

{{-- ===================== TOP STRIP ===================== --}}
<div class="bg-white border-b border-slate-200">
  <div class="mx-auto max-w-7xl px-3 sm:px-4 py-2 flex items-center justify-between">
    <a href="tel:+250780159059" class="inline-flex items-center text-[12px] sm:text-xs text-slate-600 hover:text-[var(--gold)]">
      <i class="la la-phone mr-1 text-[var(--gold)]"></i> +250 780 159 059
    </a>

    <div class="flex items-center gap-5 text-[12px] sm:text-xs">
      <a href="{{ route('about') }}" class="inline-flex items-center hover:text-[var(--gold)]">
        <i class="la la-info-circle mr-1"></i> About
      </a>
      <a href="{{ route('contact') }}" class="inline-flex items-center hover:text-[var(--gold)]">
        <i class="la la-envelope mr-1"></i> Contact
      </a>

      @guest
        <button type="button"
                @click="$dispatch('open-auth', {tab:'signin'})"
                class="inline-flex items-center gap-2 border border-[var(--gold)] text-[var(--gold)] px-3 py-1.5 rounded-md font-semibold hover:bg-[var(--gold)] hover:text-white">
          <i class="la la-user text-sm"></i> / Sign up
        </button>
      @else
        <div x-data="{dd:false}" class="relative">
          <button @click="dd=!dd" class="inline-flex items-center gap-1 hover:text-[var(--gold)]">
            <i class="la la-user"></i>{{ Str::of(Auth::user()->name)->words(2,'') }} <i class="la la-angle-down text-xs"></i>
          </button>
          <div x-show="dd" x-transition @click.outside="dd=false"
               class="absolute right-0 mt-2 w-44 rounded-md bg-white border border-slate-100 shadow-ring z-30">
            <a class="block px-3 py-2 text-sm hover:bg-slate-50" href="{{ route('dashboard') }}">
              <i class="la la-chart-pie mr-1"></i> Dashboard
            </a>
            <a class="block px-3 py-2 text-sm hover:bg-slate-50" href="{{ route('profile.edit') }}">
              <i class="la la-user-cog mr-1"></i> My Profile
            </a>
            <div class="h-px bg-slate-200"></div>
            <a class="block px-3 py-2 text-sm hover:bg-slate-50"
               href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();">
              <i class="la la-sign-out mr-1"></i> Logout
            </a>
            <form id="logout-form-top" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
          </div>
        </div>
        @endguest
        
        @auth
          <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline-block ml-2">
            @csrf
            <button type="submit" class="inline-flex items-center text-[var(--gold)] hover:text-slate-900 transition-colors" title="Logout">
              <i class="la la-power-off text-lg"></i>
            </button>
          </form>
        @endauth
    </div>
  </div>
</div>

{{-- ===================== BAR 1: LOGO + SEARCH + CART ===================== --}}
<section class="bg-white">
  <div class="mx-auto max-w-7xl px-3 sm:px-4 py-3">
    <div class="grid grid-cols-12 gap-3 items-center">
      {{-- Logo --}}
      <div class="col-span-6 md:col-span-2 flex items-center">
        <a href="{{ route('home') }}" aria-label="Diva House Beauty">
          <img src="{{ asset('assets/images/demos/demo-14/logo.png') }}" alt="DHB" class="h-8 w-auto">
        </a>
      </div>

      {{-- Wishlist & Cart --}}
      <div class="col-span-6 md:col-span-2 md:order-last flex items-center justify-end gap-2">
        <!-- Currency Selector (Tailwind Design) -->
        <div x-data="{ currOpen: false }" @click.away="currOpen = false" class="relative">
          <button @click="currOpen = !currOpen" 
                  class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-1">
            <span class="text-lg leading-none">🇺🇸</span>
            <span class="font-semibold">USD</span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': currOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>

          <!-- Dropdown -->
          <div x-show="currOpen" 
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="opacity-100 scale-100"
               x-transition:leave-end="opacity-0 scale-95"
               class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
               style="display: none;">
            
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
              Select Currency
            </div>
            
            <a href="#" @click.prevent="window.currencyConverter?.changeCurrency('USD'); currOpen = false" 
               class="currency-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150" 
               data-currency="USD">
              <span class="text-2xl leading-none">🇺🇸</span>
              <div class="flex-1">
                <div class="font-semibold text-gray-900 text-sm">USD</div>
                <div class="text-xs text-gray-500">US Dollar</div>
              </div>
            </a>
            
            <a href="#" @click.prevent="window.currencyConverter?.changeCurrency('EUR'); currOpen = false" 
               class="currency-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150" 
               data-currency="EUR">
              <span class="text-2xl leading-none">🇪🇺</span>
              <div class="flex-1">
                <div class="font-semibold text-gray-900 text-sm">EUR</div>
                <div class="text-xs text-gray-500">Euro</div>
              </div>
            </a>
            
            <a href="#" @click.prevent="window.currencyConverter?.changeCurrency('GBP'); currOpen = false" 
               class="currency-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150" 
               data-currency="GBP">
              <span class="text-2xl leading-none">🇬🇧</span>
              <div class="flex-1">
                <div class="font-semibold text-gray-900 text-sm">GBP</div>
                <div class="text-xs text-gray-500">British Pound</div>
              </div>
            </a>
            
            <a href="#" @click.prevent="window.currencyConverter?.changeCurrency('RWF'); currOpen = false" 
               class="currency-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150" 
               data-currency="RWF">
              <span class="text-2xl leading-none">🇷🇼</span>
              <div class="flex-1">
                <div class="font-semibold text-gray-900 text-sm">RWF</div>
                <div class="text-xs text-gray-500">Rwandan Franc</div>
              </div>
            </a>
          </div>
        </div>
        
        {{-- Wishlist --}}
        @auth
          @php
            $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
          @endphp
          <a href="{{ route('wishlist.index') }}"
             x-data="{ count: {{ $wishlistCount }} }"
             @wishlist-updated.window="count = $event.detail.count"
             class="relative inline-flex items-center justify-center rounded-md border border-slate-200 bg-white p-2 hover:border-[var(--gold)]">
            <i class="la la-heart text-lg"></i>
            <span x-show="count > 0" 
                  x-text="count"
                  class="absolute -top-1 -right-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">
            </span>
            <span class="sr-only">Wishlist</span>
          </a>
        @endauth
        
        {{-- Cart --}}
        <a href="{{ route('cart') }}"
           class="relative inline-flex items-center justify-center rounded-md border border-slate-200 bg-white p-2 hover:border-[var(--gold)]">
          <i class="la la-shopping-cart text-lg"></i>
          <span class="absolute -top-1 -right-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-[var(--gold)] px-1 text-[10px] font-bold text-white">{{ $count }}</span>
          <span class="sr-only">Cart</span>
        </a>
      </div>

      {{-- Search (submits ?category=<slug>&q=...) --}}
      <div class="col-span-12 md:col-span-8">
        <form action="{{ route('category') }}" method="GET"
              class="flex w-full items-stretch rounded-md border border-slate-300 bg-white shadow-sm focus-within:border-[var(--gold)] focus-within:ring-2 focus-within:ring-[var(--gold)]/20">
          <label class="sr-only" for="cat">Category</label>
          <select id="cat" name="category"
                  class="hidden lg:block w-56 border-0 border-r border-slate-200 bg-transparent px-3 text-sm text-slate-600 outline-none">
            <option value="">All Departments</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->slug }}" @selected(request('category') == $cat->slug)>{{ $cat->name }}</option>
            @endforeach
          </select>

          <label class="sr-only" for="q">Search</label>
          <input id="q" name="q" type="search" required placeholder="Search products…"
                 value="{{ request('q') }}"
                 class="w-full min-w-0 border-0 px-3 py-2 text-sm text-slate-700 placeholder-slate-400 outline-none">

          <button class="inline-flex items-center gap-2 rounded-r-md bg-[var(--gold)] px-4 text-sm font-semibold text-white hover:opacity-90">
            <i class="la la-search text-base"></i><span class="hidden lg:inline">Search</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

{{-- ===================== BAR 2: MAIN NAV (desktop dropdown + mobile sheet) ===================== --}}
<nav class="bg-[var(--black)] text-white sticky top-0 z-40"
     x-data="{ mobileSheet:false, sheetCat:null }">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
        <ul class="flex flex-wrap sm:flex-nowrap items-center gap-1 sm:gap-2 overflow-x-visible sm:overflow-visible py-2">
            <li>
                <a href="{{ route('home') }}"
                   class="nav-pill px-2 py-1 text-[11px] sm:px-3 sm:py-2 sm:text-sm whitespace-nowrap hover:bg-white/10 focus:bg-white/10">
                    Home
                </a>
            </li>
            <li>
                <a href="{{ route('category') }}"
                   class="nav-pill px-2 py-1 text-[11px] sm:px-3 sm:py-2 sm:text-sm whitespace-nowrap hover:bg-white/10 focus:bg-white/10">
                    Shop
                </a>
            </li>

            @foreach($categories as $cat)
                <li class="relative"
                    x-data="{ open:false }"
                    @mouseenter="if (window.innerWidth>=640) open=true"
                    @mouseleave="if (window.innerWidth>=640) open=false"
                    @click.outside="open=false">

                    <div class="flex items-center">
                        {{-- Category link --}}
                        <a href="{{ route('category.show', $cat->slug) }}"
                           class="nav-pill px-2 py-1 text-[11px] sm:px-3 sm:py-2 sm:text-sm whitespace-nowrap hover:bg-white/10 focus:bg-white/10">
                            {{ $cat->name }}
                        </a>

                        {{-- Toggle brands --}}
                        <button type="button"
                                class="ml-1 nav-pill px-2 py-1 text-[11px] sm:px-2 sm:py-2 hover:bg-white/10 focus:bg-white/10"
                                :aria-expanded="open"
                                @click.stop="
                                    if (window.innerWidth < 640) {
                                        sheetCat = {{ $cat->id }};
                                        mobileSheet = true;
                                    } else {
                                        open = !open;
                                    }
                                ">
                            <i class="la" :class="open ? 'la-angle-up' : 'la-angle-down'"></i>
                            <span class="sr-only">Toggle brands for {{ $cat->name }}</span>
                        </button>
                    </div>

                    {{-- Desktop brands dropdown (sub-brand appears on hover of parent) --}}
                    <div x-show="open" x-transition
                         class="hidden sm:block absolute left-0 mt-2 w-[26rem] max-h-[70vh] overflow-y-auto
                                bg-white text-slate-700 rounded-md border border-slate-100 shadow-ring z-50 p-3">

                        @php
                            $topBrands    = $cat->brands->whereNull('parent_id');
                            $orphanBrands = $cat->brands->whereNotNull('parent_id')
                                                       ->filter(fn($b) => !$topBrands->contains('id', $b->parent_id));
                        @endphp

                        <div class="space-y-1">
                            {{-- Parent brands --}}
                            @forelse($topBrands as $brand)
                                <div class="group rounded-md px-2 py-1.5 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('brand.show', $brand->slug) }}"
                                           class="text-sm font-medium text-slate-800">
                                            {{ $brand->name }}
                                        </a>

                                        @if($brand->children && $brand->children->count())
                                            <span class="text-[10px] text-slate-400 ml-2">
                                                {{ $brand->children->count() }} sub-brand(s)
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Sub-brands: only visible when hovering parent --}}
                                    @if($brand->children && $brand->children->count())
                                        <div class="mt-1 pl-3 space-y-0.5 hidden group-hover:block">
                                            @foreach($brand->children as $sub)
                                                <a href="{{ route('brand.show', $sub->slug) }}"
                                                   class="flex items-center text-xs text-slate-600 hover:text-slate-900">
                                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-300 mr-1.5"></span>
                                                    {{ $sub->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <span class="block rounded px-2 py-1.5 text-sm text-slate-400">
                                    No brands
                                </span>
                            @endforelse

                            {{-- Orphan brands (no parent) --}}
                            @foreach($orphanBrands as $brand)
                                <a href="{{ route('brand.show', $brand->slug) }}"
                                   class="block rounded-md px-2 py-1.5 text-sm hover:bg-slate-50">
                                    {{ $brand->name }}
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('category.show', $cat->slug) }}"
                               class="inline-flex items-center text-[12px] text-[var(--gold)] hover:underline">
                                View all {{ $cat->name }}
                                <i class="la la-arrow-right ml-1 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Mobile brands sheet --}}
    <div x-show="mobileSheet" x-transition.opacity
         class="fixed inset-0 z-40 bg-black/40 sm:hidden"></div>

    <div x-show="mobileSheet" x-transition
         class="fixed inset-x-0 bottom-0 z-50 sm:hidden rounded-t-2xl bg-white shadow-ring max-h-[70vh] overflow-y-auto">
        @foreach($categories as $cat)
            <section x-show="sheetCat === {{ $cat->id }}">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
                    <h4 class="text-sm font-semibold">Brands — {{ $cat->name }}</h4>
                    <button class="p-2 text-slate-500 hover:text-slate-700"
                            @click="mobileSheet = false">
                        <i class="la la-close text-xl"></i>
                        <span class="sr-only">Close</span>
                    </button>
                </div>

                @php
                    $topBrands    = $cat->brands->whereNull('parent_id');
                    $orphanBrands = $cat->brands->whereNotNull('parent_id')
                                               ->filter(fn($b) => !$topBrands->contains('id', $b->parent_id));
                @endphp

                <div class="p-3 space-y-3">
                    @forelse($topBrands as $brand)
                        <div class="border border-slate-100 rounded-lg px-3 py-2">
                            <a href="{{ route('brand.show', $brand->slug) }}"
                               class="block text-sm font-semibold text-slate-800">
                                {{ $brand->name }}
                            </a>

                            @if($brand->children && $brand->children->count())
                                <div class="mt-1 grid grid-cols-2 gap-1">
                                    @foreach($brand->children as $sub)
                                        <a href="{{ route('brand.show', $sub->slug) }}"
                                           class="flex items-center text-xs text-slate-600 hover:text-slate-900">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-300 mr-1.5"></span>
                                            {{ $sub->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-slate-400">No brands.</div>
                    @endforelse

                    @if($orphanBrands->count())
                        <div class="border border-slate-100 rounded-lg px-3 py-2">
                            <p class="text-xs font-semibold text-slate-500 mb-1">Other brands</p>
                            <div class="grid grid-cols-2 gap-1">
                                @foreach($orphanBrands as $brand)
                                    <a href="{{ route('brand.show', $brand->slug) }}"
                                       class="text-xs text-slate-700 hover:text-slate-900">
                                        {{ $brand->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('category.show', $cat->slug) }}"
                       class="block mt-1 inline-flex items-center text-[12px] text-[var(--gold)] hover:underline px-1.5 py-1">
                        View all {{ $cat->name }}
                        <i class="la la-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </section>
        @endforeach
    </div>
</nav>

