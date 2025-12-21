{{-- resources/views/product.blade.php --}}
<!DOCTYPE html>
<html lang="en">
@php
    use Illuminate\Support\Str;

    /** @var \App\Models\Product $product */
    $product = $product ?? null;

    // ---------- IMAGES ----------
    // Raw paths from DB (casted to array)
    $imagesRaw = (array) ($product->images ?? []);

    // First image URL (fallback to default)
    $firstImageUrl = null;
    if (count($imagesRaw)) {
        $firstPath = $imagesRaw[0];
        $firstImageUrl = Str::startsWith($firstPath, ['http://', 'https://'])
            ? $firstPath
            : asset('storage/' . ltrim($firstPath, '/'));
    } else {
        $firstImageUrl = asset('assets/images/default-product.jpg');
    }

    // Full gallery URLs
    $imageGallery = count($imagesRaw)
        ? collect($imagesRaw)->map(function ($path) {
            return Str::startsWith($path, ['http://', 'https://'])
                ? $path
                : asset('storage/' . ltrim($path, '/'));
        })->values()->all()
        : [$firstImageUrl];

    // Meta
    $shortDesc   = Str::limit(strip_tags($product->description ?? ''), 160);
    $pageTitle   = ($product->name ?? 'Product') . ' — Diva House Beauty';
    $pageUrl     = url()->current();
    $siteName    = 'Diva House Beauty';

    // Pricing
    $isOnSale  = (bool) ($product->is_on_sale ?? false);
    $inStock   = (bool) ($product->in_stock ?? true);
    $basePrice = (float) ($product->price ?? 0);
    $salePrice = $product->sale_price ?? $basePrice;

    // JSON-LD images
    $jsonLdImages = count($imageGallery) ? $imageGallery : [$firstImageUrl];

    // Lists
    $relatedProducts = collect($relatedProducts ?? []);
    $alsoLike        = collect($alsoLike ?? []);

    // Prev/Next by category
    $prevProduct = null; $nextProduct = null;
    try {
        if (!empty($product->category_id)) {
            $siblings = \App\Models\Product::where('category_id', $product->category_id)
                        ->orderBy('id')->get(['id','slug','name']);
            $ids = $siblings->pluck('id')->values()->all();
            $pos = array_search($product->id, $ids, true);
            if ($pos !== false) {
                $prevProduct = $siblings[$pos-1] ?? null;
                $nextProduct = $siblings[$pos+1] ?? null;
            }
        }
    } catch (\Throwable $e) {}
@endphp
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>

  {{-- Primary SEO --}}
  <title>{{ $pageTitle }}</title>
  <link rel="canonical" href="{{ $pageUrl }}"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="{{ $shortDesc }}">

  {{-- Optional keywords (basic, you can adjust later in DB if you add a field) --}}
  <meta name="keywords"
        content="{{ implode(', ', array_filter([
            $product->name ?? null,
            $product->category->name ?? null,
            'Diva House Beauty',
            'beauty products',
            'skincare',
            'makeup',
        ])) }}">

  {{-- Indexing --}}
  <meta name="robots" content="index,follow"/>

  {{-- Open Graph (Facebook, WhatsApp, etc.) --}}
  <meta property="og:type" content="product">
  <meta property="og:title" content="{{ $pageTitle }}">
  <meta property="og:description" content="{{ $shortDesc }}">
  <meta property="og:image" content="{{ $firstImageUrl }}">
  <meta property="og:image:alt" content="{{ $product->name }}">
  <meta property="og:url" content="{{ $pageUrl }}">
  <meta property="og:site_name" content="{{ $siteName }}">

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $pageTitle }}">
  <meta name="twitter:description" content="{{ $shortDesc }}">
  <meta name="twitter:image" content="{{ $firstImageUrl }}">

  {{-- Structured data helpers (for Google Image / rich results) --}}
  <meta itemprop="name" content="{{ $pageTitle }}">
  <meta itemprop="description" content="{{ $shortDesc }}">
  <meta itemprop="image" content="{{ $firstImageUrl }}">

  {{-- Tailwind (preflight OFF) + Alpine --}}
  <script>tailwind = {}; tailwind.config = { corePlugins: { preflight: false } };</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>

  <style>
    :root{ --gold:#cc9966; --black:#111827; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
    [x-cloak]{ display:none !important; }
    .line-through{ text-decoration: line-through; }
  </style>
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen:false, authTab:'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- HEADER --}}
  @include('partials.header_home2')

  {{-- Breadcrumb --}}
  <section class="bg-white border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-3">
      <nav class="text-[12px] text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-[var(--gold)]">Home</a>
        <span class="mx-1">/</span>
        <a href="{{ url('/category') }}" class="hover:text-[var(--gold)]">Products</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">{{ $product->name }}</span>
      </nav>
    </div>
  </section>

  {{-- CONTENT --}}
  <main class="py-6">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="grid grid-cols-12 gap-4 lg:gap-6">

        {{-- LEFT --}}
        <section class="col-span-12 lg:col-span-9">
          <div class="grid grid-cols-12 gap-4">

            {{-- GALLERY --}}
            <div class="col-span-12 md:col-span-6">
              <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-3 md:p-4"
                   x-data="{
                     images: @json($imageGallery),
                     index: 0,
                     name: @js($product->name),
                     set(i){
                       if(!this.images.length) return;
                       this.index = i;
                       this.$refs.main.src = this.images[i] || this.$refs.main.dataset.fallback;
                     },
                     prev(){
                       if(!this.images.length) return;
                       this.index = (this.index - 1 + this.images.length) % this.images.length;
                       this.$refs.main.src = this.images[this.index] || this.$refs.main.dataset.fallback;
                     },
                     next(){
                       if(!this.images.length) return;
                       this.index = (this.index + 1) % this.images.length;
                       this.$refs.main.src = this.images[this.index] || this.$refs.main.dataset.fallback;
                     }
                   }"
                   x-init="
                     // initial image
                     $refs.main.src = $refs.main.dataset.src || $refs.main.dataset.fallback;
                     window.addEventListener('keydown', e => {
                       if (e.key==='ArrowLeft')  prev();
                       if (e.key==='ArrowRight') next();
                     });
                   ">

                <div class="relative aspect-square overflow-hidden rounded-md border border-slate-200 bg-slate-50">
                  {{-- main image (SSR src via data-src) --}}
                  <img
                    x-ref="main"
                    data-src="{{ $firstImageUrl }}"
                    data-fallback="{{ asset('assets/images/default-product.jpg') }}"
                    alt="{{ $product->name }} image"
                    class="h-full w-full object-cover transition-opacity duration-200"
                    onerror="this.onerror=null; this.src=this.dataset.fallback">

                  @if(count($imageGallery) > 1)
                    <span class="absolute bottom-2 right-2 rounded-full bg-black/60 text-[11px] text-white px-2 py-0.5">
                      <span x-text="(index+1) + ' / ' + images.length"></span>
                    </span>
                  @endif
                </div>

                {{-- Thumbnails --}}
                @if(count($imageGallery) > 1)
                  <div class="mt-3 grid grid-cols-5 gap-2" x-cloak>
                    <template x-for="(img, idx) in images" :key="idx">
                      <button type="button"
                              @click="set(idx)"
                              class="relative aspect-square rounded-md border overflow-hidden"
                              :class="idx===index
                                      ? 'border-[var(--gold)] ring-2 ring-[var(--gold)]/30'
                                      : 'border-slate-200 hover:border-[var(--gold)]'">
                        <img :src="img"
                             class="h-full w-full object-cover rounded"
                             :alt="`thumb ${idx+1}`"
                             loading="lazy">
                      </button>
                    </template>
                  </div>
                @endif

                @if(count($imageGallery) > 1)
                  <div class="mt-2 flex items-center justify-between text-[12px] text-slate-500">
                    <button type="button" class="hover:text-[var(--gold)]" @click="prev()">
                      <i class="la la-angle-left"></i> Prev
                    </button>
                    <button type="button" class="hover:text-[var(--gold)]" @click="next()">
                      Next <i class="la la-angle-right"></i>
                    </button>
                  </div>
                @endif
              </div>
            </div>

            {{-- DETAILS --}}
            <div class="col-span-12 md:col-span-6">
              <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $product->name }}</h1>

                <div class="mt-2 flex items-center gap-2 text-[12px]">
                  <div class="relative h-3 w-24 bg-slate-200 rounded">
                    <div class="absolute inset-y-0 left-0 bg-yellow-400 rounded" style="width:80%"></div>
                  </div>
                  <a href="#reviews" class="text-slate-500 hover:text-[var(--gold)]">( {{ $product->review_count ?? 4 }} Reviews )</a>
                </div>

                <div class="mt-3">
                  @if($isOnSale)
                    <div class="flex items-baseline gap-2">
                      <span class="text-2xl font-extrabold text-rose-600">{{ number_format($salePrice, 0) }} RWF</span>
                      <span class="text-sm text-slate-400 line-through">Was {{ number_format($basePrice, 0) }} RWF</span>
                    </div>
                  @else
                    <div class="text-2xl font-extrabold text-slate-900">{{ number_format($basePrice, 0) }} RWF</div>
                  @endif
                  <div class="mt-1">
                    @if($inStock)
                      <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700 border border-emerald-200">
                        <i class="la la-check mr-1"></i> In Stock
                      </span>
                    @else
                      <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-medium text-rose-700 border border-rose-200">
                        <i class="la la-times mr-1"></i> Out of Stock
                      </span>
                    @endif
                  </div>
                </div>

                <div class="mt-4 prose prose-sm max-w-none text-slate-700">
                  {!! $product->description !!}
                </div>

                <div class="mt-5">
                  <form action="{{ url('addcart', $product->id) }}" method="POST" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <label class="text-sm text-slate-600">Qty</label>
                    <input type="number" name="quantity" min="1" max="10" value="1"
                           class="w-24 rounded-md border border-slate-300 px-2 py-1 text-center outline-none focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20"/>

                    <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--gold)] px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
                      <i class="la la-shopping-cart text-lg"></i> Add to Cart
                    </button>

                    @php
                        $rawNumber = '250780159059';
                        $productMessage = "Hello, I'm interested in ordering:\n".
                            "Product: {$product->name}\n".
                            "Price: " . number_format($basePrice, 0) . " RWF\n".
                            "Link: " . url()->current();
                        $whatsappLink = "https://wa.me/{$rawNumber}?text=" . urlencode($productMessage);
                    @endphp
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-md border border-emerald-300 bg-white px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                      <i class="la la-whatsapp text-lg"></i> Quick Order on WhatsApp
                    </a>
                  </form>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4">
                  <div class="text-sm text-slate-600">
                    <span class="font-medium text-slate-700">Category:</span>
                    @php $catSlug = $product->category->slug ?? null; @endphp
                    @if($catSlug)
                      <a href="{{ url('/category/'.$catSlug) }}" class="hover:text-[var(--gold)]">
                        {{ $product->category->name ?? 'Uncategorized' }}
                      </a>
                    @else
                      <span>{{ $product->category->name ?? 'Uncategorized' }}</span>
                    @endif
                  </div>

                  <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-600">Share:</span>
                    <a class="text-slate-500 hover:text-[var(--gold)]"
                       href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                       target="_blank" rel="noopener" title="Facebook">
                      <i class="lab la-facebook-f text-lg"></i>
                    </a>
                    <a class="text-slate-500 hover:text-[var(--gold)]"
                       href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}"
                       target="_blank" rel="noopener" title="Twitter / X">
                      <i class="lab la-twitter text-lg"></i>
                    </a>
                    <a class="text-slate-500 hover:text-[var(--gold)]"
                       href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' ' . url()->current()) }}"
                       target="_blank" rel="noopener" title="WhatsApp">
                      <i class="lab la-whatsapp text-lg"></i>
                    </a>
                    <a class="text-slate-500 hover:text-[var(--gold)]"
                       href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ urlencode($product->name) }}"
                       target="_blank" rel="noopener" title="Pinterest">
                      <i class="lab la-pinterest text-lg"></i>
                    </a>
                  </div>
                </div>

                @if($prevProduct || $nextProduct)
                  <div class="mt-4 flex items-center justify-between text-sm text-slate-600">
                    <div>
                      @if($prevProduct)
                        <a class="hover:text-[var(--gold)]"
                           href="{{ route('product', $prevProduct->slug ?? $prevProduct->id) }}">
                          <i class="la la-angle-left"></i> {{ Str::limit($prevProduct->name, 28) }}
                        </a>
                      @endif
                    </div>
                    <div>
                      @if($nextProduct)
                        <a class="hover:text-[var(--gold)]"
                           href="{{ route('product', $nextProduct->slug ?? $nextProduct->id) }}">
                          {{ Str::limit($nextProduct->name, 28) }} <i class="la la-angle-right"></i>
                        </a>
                      @endif
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          {{-- YOU MAY ALSO LIKE --}}
          @if($alsoLike->count())
            <div class="mt-6">
              <h2 class="text-center text-lg sm:text-xl font-semibold text-slate-900">You May Also Like</h2>
              <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($alsoLike as $p)
                  <div class="group bg-white border border-slate-200 rounded-lg shadow-ring overflow-hidden">
                    <a href="{{ route('product', $p->slug ?? $p->id) }}" class="block">
                      <div class="aspect-square overflow-hidden bg-slate-50">
                        <img src="{{ $p->first_image_url }}"
                             alt="{{ $p->name }}"
                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"/>
                      </div>
                      <div class="p-3">
                        <div class="text-[12px] text-slate-500">{{ $p->category->name ?? 'Category' }}</div>
                        <div class="mt-0.5 line-clamp-2 text-sm font-medium text-slate-900">{{ $p->name }}</div>
                        <div class="mt-1 text-[13px]">
                          @if($p->is_on_sale ?? false)
                            <span class="font-semibold text-rose-600">
                              {{ number_format($p->sale_price ?? $p->price ?? 0, 0) }} RWF
                            </span>
                            <span class="ml-1 text-slate-400 line-through">
                              {{ number_format($p->price ?? 0, 0) }} RWF
                            </span>
                          @else
                            <span class="font-semibold text-slate-900">
                              {{ number_format($p->price ?? 0, 0) }} RWF
                            </span>
                          @endif
                        </div>
                      </div>
                    </a>
                    <form action="{{ url('addcart', $p->id) }}" method="POST" class="p-3 pt-0">
                      @csrf
                      <input type="hidden" name="quantity" value="1">
                      <button class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-[13px] font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="la la-shopping-bag"></i> Add to Cart
                      </button>
                    </form>
                  </div>
                @endforeach
              </div>
            </div>
          @endif
        </section>

        {{-- RIGHT --}}
        <aside class="col-span-12 lg:col-span-3">
          <div class="bg-white border border-slate-200 rounded-lg shadow-ring p-4">
            <h4 class="text-sm font-semibold text-slate-900">Related Products</h4>
            <div class="mt-3 space-y-3">
              @foreach ($relatedProducts as $related)
                @php $relSlug = $related->slug ?? $related->id; @endphp
                <a href="{{ route('product', $relSlug) }}"
                   class="flex items-center gap-3 rounded-md border border-slate-200 p-2 hover:border-[var(--gold)]">
                  <img src="{{ $related->first_image_url }}"
                       class="h-16 w-16 flex-shrink-0 rounded object-cover"
                       alt="{{ $related->name }}">
                  <div class="min-w-0">
                    <div class="truncate text-sm font-medium text-slate-900">
                      {{ $related->name }}
                    </div>
                    <div class="text-[12px] text-slate-600">
                      {{ number_format($related->price ?? 0, 0) }} RWF
                    </div>
                  </div>
                </a>
              @endforeach
            </div>

            @if(!empty($product->category?->slug))
              <a href="{{ url('/category/'.$product->category->slug) }}"
                 class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-[13px] font-semibold text-slate-700 hover:bg-slate-50">
                View more in {{ $product->category->name ?? 'Category' }}
                <i class="la la-arrow-right text-sm"></i>
              </a>
            @endif
          </div>
        </aside>

      </div>
    </div>
  </main>

  {{-- FOOTER + AUTH MODAL --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- Toast --}}
  @if(session()->has('message') || session()->has('error'))
    <div x-data="{ show:true }" x-show="show" x-init="setTimeout(()=>show=false,2500)"
         class="fixed top-3 right-3 z-50 rounded-md px-3 py-2 text-sm shadow-ring"
         :class="`{{ session()->has('message') ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}`">
      <span class="font-medium">{{ session('message') ?? session('error') }}</span>
    </div>
  @endif

  {{-- JSON-LD --}}
  <script type="application/ld+json">
  {
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": @json($product->name),
    "image": @json($jsonLdImages),
    "description": @json(strip_tags($product->description)),
    "sku": @json((string) $product->id),
    "brand": {"@type": "Brand", "name": "Diva House Beauty"},
    "offers": {
      "@type": "Offer",
      "priceCurrency": "RWF",
      "price": "{{ $isOnSale ? number_format($salePrice, 0, '.', '') : number_format($basePrice, 0, '.', '') }}",
      "availability": "https://schema.org/{{ $inStock ? 'InStock' : 'OutOfStock' }}",
      "url": "{{ url()->current() }}"
    }
  }
  </script>
</body>
</html>
