@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    /**
     * Props:
     * - $title (string)
     * - $subtitle (string|null)
     * - $collection (Collection<Brand> w/ ->latestProduct loaded)
     * - $minCount (int, optional)
     * - $fallbackCategoryId (int|null, optional)  // used to fill up to $minCount
     */

    // Build the list from "one newest product per brand"
    $cards = collect();
    foreach ($collection as $brand) {
        $p = $brand->latestProduct ?? null;
        if ($p) {
            $cards->push([
                'product'   => $p,
                'brandName' => $brand->name ?? null,
            ]);
        }
    }

    // If we need a minimum count (e.g., Fashion = 10), fill with latest products from that category
    $minCount = $minCount ?? 0;
    if ($minCount > 0 && !empty($fallbackCategoryId) && $cards->count() < $minCount) {
        $need = $minCount - $cards->count();
        $excludeIds = $cards->pluck('product.id')->all();

        $fallback = \App\Models\Product::with('category')
            ->where('category_id', $fallbackCategoryId)
            ->whereNotIn('id', $excludeIds)
            ->latest('created_at')
            ->take($need)
            ->get();

        foreach ($fallback as $p) {
            $cards->push([
                'product'   => $p,
                'brandName' => null, // we’re not showing a chip on the image anymore anyway
            ]);
        }
    }



    // Helper to resolve image
    $resolveImg = function($p) {
        $imgs = $p->images;
        if (!is_array($imgs)) {
            $decoded = json_decode($imgs ?? '[]', true);
            $imgs = is_array($decoded) ? $decoded : [];
        }
        $img0 = $p->first_image_url ?? ($imgs[0] ?? null);
        return $img0
            ? (Str::startsWith($img0, ['http://','https://']) ? $img0 : asset($img0))
            : asset('assets/images/default-product.jpg');
    };
@endphp

<section class="bg-white">
  <div class="mx-auto max-w-7xl px-3 sm:px-4 py-8 sm:py-10"
       x-data="{
          el:null, atStart:true, atEnd:false,
          update(){ if(!this.el) return; this.atStart=this.el.scrollLeft<=6; this.atEnd=Math.ceil(this.el.scrollLeft+this.el.clientWidth)>=this.el.scrollWidth-6; },
          scrollBy(dx){ this.el?.scrollBy({left:dx, behavior:'smooth'}); },
          init(){ this.el=this.$refs.track; this.update(); this.el.addEventListener('scroll',()=>this.update(),{passive:true}); window.addEventListener('resize',()=>this.update()); }
       }">

    <div class="flex items-end justify-between gap-3">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $title }}</h2>
        @if(!empty($subtitle))
          <p class="text-slate-500 text-sm">{{ $subtitle }}</p>
        @endif
      </div>

      <div class="hidden sm:flex items-center gap-2">
        <button @click="scrollBy(-360)" :disabled="atStart"
                class="h-9 w-9 grid place-items-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
          <i class="la la-angle-left text-xl"></i>
        </button>
        <button @click="scrollBy(360)" :disabled="atEnd"
                class="h-9 w-9 grid place-items-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
          <i class="la la-angle-right text-xl"></i>
        </button>
      </div>
    </div>

    <div class="mt-4 relative">
      {{-- mobile arrows --}}
      <div class="sm:hidden absolute inset-y-0 left-0 right-0 flex items-center justify-between pointer-events-none">
        <button @click="scrollBy(-280)" class="pointer-events-auto ml-1 h-8 w-8 grid place-items-center rounded-md bg-white/90 shadow-ring"><i class="la la-angle-left"></i></button>
        <button @click="scrollBy(280)"  class="pointer-events-auto mr-1 h-8 w-8 grid place-items-center rounded-md bg-white/90 shadow-ring"><i class="la la-angle-right"></i></button>
      </div>

      <div x-ref="track" class="overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory">
        <div class="flex gap-3 sm:gap-4 min-w-max pr-2">
          @forelse($cards as $card)
            @php
              /** @var \App\Models\Product $p */
              $p = $card['product'];
              $brandName = $card['brandName'];
              $img = $resolveImg($p);
              $slugOrId = $p->slug ?? $p->id;
              $price = (float)$p->express_price > 0 
                  ? (float)$p->express_price 
                  : ((float)$p->standard_price > 0 ? (float)$p->standard_price : 0);
            @endphp

            <article class="snap-start w-64 sm:w-72 shrink-0 rounded-xl bg-white border border-slate-200 shadow-ring hover:shadow-lg transition-shadow"
                     x-data="{ 
                       inWishlist: {{ auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $p->id)->exists() ? 'true' : 'false' }},
                       isProcessing: false,
                       async toggleWishlist() {
                         if (this.isProcessing) return;
                         
                         @guest
                           window.dispatchEvent(new CustomEvent('open-auth', { detail: { tab: 'signin' } }));
                           return;
                         @endguest
                         
                         this.isProcessing = true;
                         const productId = {{ $p->id }};
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
                             const countEl = document.querySelector('[data-wishlist-count]');
                             if (countEl && data.wishlistCount !== undefined) {
                               countEl.textContent = data.wishlistCount;
                               countEl.style.display = data.wishlistCount > 0 ? '' : 'none';
                             }
                           }
                         } catch (error) {
                           console.error('Wishlist error:', error);
                         } finally {
                           this.isProcessing = false;
                         }
                       }
                     }">
              <div class="relative rounded-t-xl overflow-hidden group">
                <a href="{{ route('product', $slugOrId) }}" class="block">
                  <img src="{{ $img }}" alt="{{ $p->name }}" loading="lazy" class="w-full h-44 sm:h-52 object-cover transition-transform duration-700 group-hover:scale-105">
                  @if($p->is_new)
                    <span class="absolute right-2 top-2 inline-flex items-center justify-center rounded-sm bg-[var(--gold)] px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm z-10">
                      New
                    </span>
                  @endif
                </a>
                
                {{-- Wishlist heart button --}}
                <button type="button"
                        @click.stop="toggleWishlist()"
                        :disabled="isProcessing"
                        class="absolute top-2 left-2 rounded-full bg-white/90 backdrop-blur p-2 shadow-md hover:bg-white transition-all disabled:opacity-50 z-20"
                        :class="inWishlist ? 'text-rose-500' : 'text-slate-400 hover:text-rose-500'"
                        :title="inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist'">
                  <i :class="inWishlist ? 'la-heart' : 'la-heart-o'" class="la text-lg"></i>
                </button>
              </div>

              <div class="p-3">
                <a href="{{ route('product', $slugOrId) }}" class="block text-sm font-semibold text-slate-900 line-clamp-2">
                  {{ $p->name }}
                </a>

                {{-- meta line: Brand • Category (brand optional) --}}
                <div class="mt-1 text-[12px] text-slate-500">
                  @if($brandName)
                    <span class="truncate max-w-[10rem] inline-block align-middle">{{ $brandName }}</span>
                    <span class="mx-1">•</span>
                  @endif
                  <span class="truncate max-w-[10rem] inline-block align-middle">{{ $p->category->name ?? '—' }}</span>
                </div>

                <div class="mt-2 flex items-center justify-between">
                  <div class="text-base font-bold text-slate-900 convertible-price" data-price-rwf="{{ $price }}" data-currency="RWF">RWF {{ number_format($price, 0) }}</div>
                  <a href="{{ route('product', $slugOrId) }}"
                     class="inline-flex items-center gap-1 rounded-md border border-slate-300 px-2.5 py-1.5 text-[12px] font-semibold text-slate-700 hover:bg-slate-50">
                    View <i class="la la-arrow-right text-sm"></i>
                  </a>
                </div>
              </div>
            </article>
          @empty
            <div class="py-10 text-sm text-slate-500">No products yet.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</section>
