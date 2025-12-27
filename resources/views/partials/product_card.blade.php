{{-- Product Card Component --}}
@php
    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
    $firstImage = is_array($images) && !empty($images) ? $images[0] : null;
    
    // Check for active promotion
    $promotion = $product->promotion()->where('end_time', '>=', now())->first();
    $discountedPrice = $promotion 
        ? $product->price * (1 - $promotion->discount_percentage / 100) 
        : $product->price;
    $hasDiscount = $promotion && $promotion->discount_percentage > 0;
    
    // Check if in wishlist
    $inWishlist = auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists();
@endphp

<div class="group relative bg-white border border-slate-100 hover:border-[var(--gold)] transition-colors duration-300">
    {{-- Image --}}
    {{-- Image Container --}}
    <div class="relative aspect-[4/5] bg-[#F9FAFB] overflow-hidden">
        <a href="{{ route('product', $product->slug) }}" class="block w-full h-full">
            @if($firstImage)
                <img src="{{ asset('storage/' . $firstImage) }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-300">
                    <i class="la la-image text-4xl"></i>
                </div>
            @endif

            {{-- Badges - Inside link is fine, just visual --}}
            <div class="absolute top-3 left-3 flex flex-col gap-1 z-10">
                @if($hasDiscount)
                    <span class="badge badge-sale">-{{ $promotion->discount_percentage }}%</span>
                @endif
                @if($product->is_new)
                    <span class="product-label label-new">New</span>
                @endif
            </div>
        </a>

        {{-- Quick Actions (Buttons) - OUTSIDE the link, z-index higher --}}
        <div class="absolute bottom-3 inset-x-3 flex items-center justify-center gap-2 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 z-20">
            {{-- Wishlist --}}
             <div x-data="{ 
                inWishlist: {{ $inWishlist ? 'true' : 'false' }}, 
                loading: false,
                toggleWishlist() {
                    if (this.loading) return;
                    
                    @guest
                        window.dispatchEvent(new CustomEvent('open-auth', { detail: { tab: 'signin' } }));
                        return;
                    @endguest

                    this.loading = true;
                    // Optimistic update
                    const originalState = this.inWishlist;
                    this.inWishlist = !this.inWishlist;

                    const url = originalState 
                        ? '{{ route('wishlist.remove', $product->id) }}'
                        : '{{ route('wishlist.add', $product->id) }}';
                    
                    const method = originalState ? 'DELETE' : 'POST';

                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.loading = false;
                        if (data.success) {
                            // Update global count
                            if (data.wishlistCount !== undefined) {
                                window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                            }
                        } else {
                            // Revert on failure
                            this.inWishlist = originalState;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.loading = false;
                        this.inWishlist = originalState; // Revert
                    });
                }
             }">
                <button type="button" 
                        @click.stop="toggleWishlist()" 
                        :disabled="loading"
                        class="w-10 h-10 bg-white text-slate-800 hover:scale-105 flex items-center justify-center transition shadow-sm border border-slate-100"
                        :class="inWishlist ? 'text-red-500' : 'hover:text-red-500'"
                        title="Wishlist">
                    <i class="text-lg" :class="inWishlist ? 'la la-heart' : 'la la-heart-o'"></i>
                </button>
             </div>

            {{-- Add to Cart --}}
            <form action="{{ url('/addcart/' . $product->id) }}" method="POST">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-10 h-10 bg-[var(--black)] text-white hover:bg-[var(--gold)] hover:scale-105 flex items-center justify-center transition shadow-sm">
                    <i class="la la-shopping-cart text-lg"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Product Info --}}
    <div class="p-4 text-center">
        @if($product->category)
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">{{ $product->category->name }}</p>
        @endif

        <h3 class="text-sm font-medium text-slate-900 line-clamp-2 mb-2 min-h-[2.5em]">
            <a href="{{ route('product', $product->slug) }}" class="hover:text-[var(--gold)] transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        <div class="flex items-center justify-center gap-3">
            @if($hasDiscount)
                <span class="text-xs text-slate-400 line-through">Rw {{ number_format($product->price) }}</span>
                <span class="text-sm font-semibold text-[var(--black)]">Rw {{ number_format($discountedPrice) }}</span>
            @else
                <span class="text-sm font-semibold text-[var(--black)]">Rw {{ number_format($product->price) }}</span>
            @endif
        </div>
    </div>
</div>
