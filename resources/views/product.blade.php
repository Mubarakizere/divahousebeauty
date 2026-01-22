{{-- resources/views/product.blade.php --}}
<!DOCTYPE html>
<html lang="en">
@php
    use Illuminate\Support\Str;

    /** @var \App\Models\Product $product */
    $product = $product ?? null;

    // ---------- IMAGES ----------
    // ---------- IMAGES ----------
    // Align with product_card.blade.php: manually decode and prepend 'storage/'
    $rawImages = is_string($product->images) ? json_decode($product->images, true) : $product->images;
    $rawImages = is_array($rawImages) ? $rawImages : [];

    $firstImagePath = !empty($rawImages) ? $rawImages[0] : null;
    $firstImageUrl  = $firstImagePath ? asset('storage/' . $firstImagePath) : asset('assets/images/default-product.jpg');

    if (!empty($rawImages)) {
        // Just use the first image, no gallery needed
        // $imageGallery = array_map(fn($img) => asset('storage/' . $img), $rawImages);
    } else {
        // $imageGallery = [$firstImageUrl];
    }

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
    $jsonLdImages = [$firstImageUrl];

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

  {{-- HEADER --}}
  @include('partials.header_home2')

  {{-- Breadcrumb (Minimalist) --}}
  <section class="bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 py-4">
      <nav class="text-xs uppercase tracking-widest text-slate-400 font-medium">
        <a href="{{ route('home') }}" class="hover:text-[var(--gold)] transition-colors">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ url('/category') }}" class="hover:text-[var(--gold)] transition-colors">Shop</a>
        <span class="mx-2">/</span>
        <span class="text-[var(--black)]">{{ $product->name }}</span>
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

            {{-- GALLERY (Clean Grid) --}}
            <div class="col-span-12 md:col-span-6">
              <div class="sticky top-24">
                
                {{-- Main Image --}}
                <div class="relative aspect-[4/5] overflow-hidden bg-gray-50 border border-slate-100">
                   <img src="{{ $firstImageUrl }}"
                        alt="{{ $product->name }}"
                        class="absolute inset-0 w-full h-full object-cover"
                        onerror="this.onerror=null; this.src='{{ asset('assets/images/default-product.jpg') }}'; this.style.objectFit='contain'; this.style.padding='2rem';">
                </div>

              </div>
            </div>


            {{-- DETAILS --}}
            <div class="col-span-12 md:col-span-6 pl-0 md:pl-8">
              <div>
                <h1 class="text-3xl sm:text-4xl font-serif text-[var(--black)] leading-tight mb-4">{{ $product->name }}</h1>

                <div class="flex items-center gap-4 mb-6">
                  <div class="flex items-center gap-1">
                     @include('partials.star_rating', ['rating' => $product->average_rating, 'size' => 'sm'])
                  </div>
                  <a href="#reviews" class="text-xs uppercase tracking-wider text-slate-500 border-b border-transparent hover:border-slate-500 transition-colors">
                    {{ $product->review_count }} Reviews
                  </a>
                </div>

                <div class="mb-6">
                  @if($isOnSale)
                    <div class="flex items-baseline gap-3 bg-red-50 px-3 py-1 rounded-lg">
                      <span class="text-3xl font-serif text-rose-700 convertible-price" data-price-rwf="{{ $salePrice }}" data-currency="RWF">Rw {{ number_format($salePrice) }}</span>
                      <span class="text-lg text-slate-400 line-through font-serif convertible-price" data-price-rwf="{{ $basePrice }}" data-currency="RWF">Rw {{ number_format($basePrice) }}</span>
                    </div>
                  @else
                    <div class="text-3xl font-serif text-[var(--black)] convertible-price" data-price-rwf="{{ $basePrice }}" data-currency="RWF">Rw {{ number_format($basePrice) }}</div>
                  @endif
                  
                  <div class="mt-3">
                    @if($inStock)
                      <span class="inline-flex items-center text-xs uppercase tracking-widest text-emerald-700 font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span> In Stock
                      </span>
                    @else
                       <span class="inline-flex items-center text-xs uppercase tracking-widest text-rose-700 font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></span> Out of Stock
                      </span>
                    @endif
                  </div>
                </div>

                <div class="prose prose-sm prose-slate max-w-none mb-8 text-slate-600 leading-relaxed font-light">
                  {!! $product->description !!}
                </div>

                <div class="mt-5" 
                     x-data="{ 
                       inWishlist: {{ auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists() ? 'true' : 'false' }},
                       isProcessing: false,
                       async toggleWishlist() {
                         if (this.isProcessing) return;
                         
                         @guest
                           window.dispatchEvent(new CustomEvent('open-auth', { detail: { tab: 'signin' } }));
                           return;
                         @endguest
                         
                         this.isProcessing = true;
                         const productId = {{ $product->id }};
                         const url = this.inWishlist 
                           ? `/wishlist/remove/${productId}`
                           : `/wishlist/add/${productId}`;
                         const method = this.inWishlist ? 'DELETE' : 'POST';
                         
                         try {
                           const response = await fetch(url, {
                             method: method,
                             headers: {
                               'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                               'Accept': 'application/json',
                               'Content-Type': 'application/json'
                             }
                           });
                           
                           const data = await response.json();
                           
                           if (data.success) {
                             this.inWishlist = !this.inWishlist;
                             // Update global count via event
                             if (data.wishlistCount !== undefined) {
                               window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                             }
                           }
                         } catch (error) {
                           console.error('Wishlist error:', error);
                         } finally {
                           this.isProcessing = false;
                         }
                       }
                     }">
                  <form action="{{ url('addcart', $product->id) }}" method="POST" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <label class="text-xs uppercase tracking-wider font-bold text-[var(--black)] mr-3">Quantity</label>
                    <div class="flex items-center border border-slate-300 px-2 h-10 w-24">
                        <input type="number" name="quantity" min="1" max="10" value="1"
                            class="w-full text-center outline-none bg-transparent border-none text-slate-800"/>
                    </div>

                    <button type="submit" class="flex-1 h-10 inline-flex items-center justify-center gap-2 bg-[var(--black)] text-white text-xs uppercase tracking-widest font-bold hover:bg-[var(--gold)] transition-colors">
                      Add to Cart
                    </button>

                    <button type="button" 
                            @click="toggleWishlist()"
                            :disabled="isProcessing"
                             class="h-10 w-10 border border-slate-300 flex items-center justify-center text-slate-500 hover:border-[var(--black)] hover:text-[var(--black)] transition-colors"
                            :class="inWishlist ? 'text-rose-500 border-rose-500' : ''"
                            title="Add to Wishlist">
                      <i :class="inWishlist ? 'la-heart' : 'la-heart-o'" class="la text-xl"></i>
                    </button>
                  </form>
                  
                  <div class="mt-4">
                   @php
                        $rawNumber = '250780159059';
                        $productMessage = "Hello, I'm interested in ordering:\n".
                            "Product: {$product->name}\n".
                            "Price: Rw " . number_format($basePrice) . "\n".
                            "Link: " . url()->current();
                        $whatsappLink = "https://wa.me/{$rawNumber}?text=" . urlencode($productMessage);
                    @endphp
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-md border border-emerald-300 bg-white px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                      <i class="la la-whatsapp text-lg"></i> Quick Order on WhatsApp
                    </a>
                </div>
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
                     <span class="text-xs uppercase tracking-wider font-bold text-[var(--black)] mr-2">Share:</span>
                     
                     <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                        target="_blank" rel="noopener" 
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-[#3b5998] text-white hover:opacity-90 transition-opacity" title="Facebook">
                       <i class="lab la-facebook-f text-lg"></i>
                     </a>
                     
                     <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}" 
                        target="_blank" rel="noopener" 
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-[#1DA1F2] text-white hover:opacity-90 transition-opacity" title="Twitter">
                       <i class="lab la-twitter text-lg"></i>
                     </a>
                     
                     <a href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' ' . url()->current()) }}" 
                        target="_blank" rel="noopener" 
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-[#25D366] text-white hover:opacity-90 transition-opacity" title="WhatsApp">
                       <i class="lab la-whatsapp text-lg"></i>
                     </a>
                     
                     <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ urlencode($product->name) }}" 
                        target="_blank" rel="noopener" 
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-[#bd081c] text-white hover:opacity-90 transition-opacity" title="Pinterest">
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
            <div class="mt-24 pt-16 border-t border-slate-200">
              <h2 class="text-center text-3xl font-serif text-[var(--black)] mb-12">You May Also Like</h2>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($alsoLike as $p)
                   @include('partials.product_card', ['product' => $p])
                @endforeach
              </div>
            </div>
          @endif

          {{-- REVIEWS SECTION --}}
          <div class="mt-16 w-full" id="reviews">
            <div class="text-center mb-12">
              <h2 class="text-3xl font-serif text-[var(--black)] mb-2">Customer Reviews</h2>
              <div class="w-20 h-0.5 bg-[var(--gold)] mx-auto"></div>
            </div>
            
            {{-- Rating Summary --}}
            <div class="bg-white shadow-lg border border-slate-100 rounded-xl p-6 md:p-8 mb-8">
              <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: center;">
                {{-- Average Rating --}}
                <div style="text-align: center; min-width: 140px;">
                  <div class="text-5xl font-serif text-[var(--black)]">{{ number_format($product->average_rating, 1) }}</div>
                  <div style="margin-top: 0.5rem; display: flex; justify-content: center;">
                    @include('partials.star_rating', [
                      'rating' => $product->average_rating,
                      'size' => 'lg'
                    ])
                  </div>
                  <div class="mt-2 text-sm text-slate-500">
                    {{ $product->review_count }} Reviews
                  </div>
                </div>
                
                {{-- Rating Distribution --}}
                <div style="flex: 1; min-width: 200px;">
                  @php
                    $ratingCounts = $product->approvedReviews()->selectRaw('rating, COUNT(*) as count')->groupBy('rating')->pluck('count', 'rating')->toArray();
                    $totalReviews = $product->review_count ?: 1;
                  @endphp
                  @for ($i = 5; $i >= 1; $i--)
                    @php
                      $count = $ratingCounts[$i] ?? 0;
                      $percentage = ($count / $totalReviews) * 100;
                    @endphp
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                      <span class="text-slate-700" style="width: 1.5rem; font-weight: 500;">{{ $i }}</span>
                      <div style="flex: 1; height: 0.75rem; background: #f1f5f9; border-radius: 9999px; overflow: hidden;">
                        <div style="height: 100%; background: var(--gold); border-radius: 9999px; width: {{ $percentage }}%;"></div>
                      </div>
                      <span class="text-slate-400" style="width: 2.5rem; text-align: right; font-size: 0.75rem;">{{ $count }}</span>
                    </div>
                  @endfor
                </div>
              </div>
            </div>

            {{-- Review Form --}}
            @auth
              @php
                $existingReview = $product->reviews()->where('user_id', auth()->id())->first();
                $hasPurchased = auth()->user()->hasPurchased($product->id);
              @endphp
              
              @if(!$hasPurchased)
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 rounded-lg p-5 mb-8 shadow-sm">
                  <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="la la-info-circle text-2xl text-amber-600"></i>
                    <span class="text-sm font-medium text-amber-900">Purchase this product to leave a verified review</span>
                  </div>
                </div>
              @endif
              
              @include('reviews._review_form', ['product' => $product, 'existingReview' => $existingReview])
            @else
              @include('reviews._review_form', ['product' => $product])
            @endauth

            {{-- Existing Reviews --}}
            @php
              $reviews = $product->approvedReviews()->with('user')->latest()->get();
            @endphp
            
            @if($reviews->count() > 0)
              <div style="margin-top: 2.5rem;">
                <h3 class="text-xl font-serif text-[var(--black)]" style="padding-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; margin-bottom: 1.5rem;">All Reviews ({{ $reviews->count() }})</h3>
                
                @foreach($reviews as $review)
                  <div class="bg-white shadow-md border border-slate-100 rounded-xl" style="padding: 1.5rem; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                      <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                          <div style="display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: var(--gold); color: white; font-weight: 600;">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                          </div>
                          <div>
                            <div class="font-semibold text-slate-900">{{ $review->user->name ?? 'Anonymous' }}</div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem;" class="text-slate-500">
                              <span>{{ $review->created_at->format('M d, Y') }}</span>
                              @if($review->verified_purchase)
                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                  <i class="la la-check-circle" style="font-size: 0.75rem;"></i>
                                  Verified Purchase
                                </span>
                              @endif
                            </div>
                          </div>
                        </div>
                        
                        <div style="margin-bottom: 0.5rem;">
                          @include('partials.star_rating', [
                            'rating' => $review->rating,
                            'size' => 'sm'
                          ])
                        </div>
                        
                        @if($review->title)
                          <h4 class="font-semibold text-slate-900" style="margin-bottom: 0.5rem;">{{ $review->title }}</h4>
                        @endif
                        
                        @if($review->review)
                          <p class="text-slate-700">{{ $review->review }}</p>
                        @endif
                      </div>
                      
                      @auth
                        @if($review->user_id === auth()->id())
                          <button type="button" 
                                  class="text-sm text-slate-500 hover:text-[var(--gold)]">
                            <i class="la la-edit"></i> Edit
                          </button>
                        @endif
                      @endauth
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div style="margin-top: 2.5rem; text-align: center; padding: 4rem 1rem; background: linear-gradient(to bottom right, #f8fafc, #f9fafb); border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                <i class="la la-comments" style="font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
                <p class="text-lg font-medium text-slate-700" style="margin-bottom: 0.5rem;">No reviews yet</p>
                <p class="text-sm text-slate-500">Be the first to review this product!</p>
              </div>
            @endif
          </div>

        </section>

        {{-- RIGHT --}}
        <aside class="col-span-12 lg:col-span-3">
          <div class="sticky top-24">
            <h4 class="text-xs font-bold uppercase tracking-widest text-[var(--black)] mb-6 pb-2 border-b border-gray-100">Related Products</h4>
            <div class="space-y-6">
              @foreach ($relatedProducts as $related)
                @php $relSlug = $related->slug ?? $related->id; @endphp
                <a href="{{ route('product', $relSlug) }}" class="flex gap-4 group">
                  <div class="w-16 h-20 bg-gray-100 overflow-hidden">
                     <img src="{{ $related->first_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $related->name }}">
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="text-sm font-serif text-[var(--black)] group-hover:text-[var(--gold)] transition-colors line-clamp-2 leading-snug">
                      {{ $related->name }}
                    </div>
                    <div class="text-xs text-slate-500 mt-1 convertible-price" data-price-rwf="{{ $related->price ?? 0 }}" data-currency="RWF">
                      Rw {{ number_format($related->price ?? 0) }}
                    </div>
                  </div>
                </a>
              @endforeach
            </div>

            @if(!empty($product->category?->slug))
              <a href="{{ url('/category/'.$product->category->slug) }}"
                 class="mt-8 block w-full py-3 border border-[var(--black)] text-center text-xs uppercase tracking-widest hover:bg-[var(--black)] hover:text-white transition-all">
                View All in {{ $product->category->name }}
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

  <!-- Currency Converter Script -->
  <script src="{{ asset('js/currency-converter.js') }}"></script>
</body>
</html>
