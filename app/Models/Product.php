<?php

namespace App\Models;

use App\Traits\TracksRecentlyViewed;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use TracksRecentlyViewed;
    use HasFactory;

    public const DEFAULT_IMAGE = 'assets/images/default-product.jpg';
    public const NEW_DAYS      = 7;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'stock',
        'category_id', 'brand_id', 'images',
    ];

    protected $casts = [
        'images'     => 'array',
        'price'      => 'float',
        'stock'      => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'images' => '[]',
    ];

    protected $appends = [
        'formatted_price',
        'is_on_sale',
        'sale_price',
        'first_image_url',
        'image_urls',
        'in_stock',
        'is_new',
        'average_rating',
        'review_count',
    ];

    // If you later switch to {product:slug} binding, this makes it seamless
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Slugging (create + update, unique) ───────────────────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = static::uniqueSlug($product->slug ?: $product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = static::uniqueSlug($product->name, $product->id);
            } elseif ($product->isDirty('slug')) {
                $product->slug = static::uniqueSlug($product->slug, $product->id);
            }
        });
    }

    protected static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($base) ?: Str::random(8);
        $slug     = $baseSlug;
        $i        = 2;

        $q = static::query();
        if ($ignoreId) $q->where('id', '!=', $ignoreId);

        while ($q->clone()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$i}";
            $i++;
        }
        return $slug;
    }

    // ── Relationships ────────────────────────────────────────────────
    public function category() { return $this->belongsTo(Category::class); }
    public function brand()    { return $this->belongsTo(Brand::class); }

    // singular: matches usage $product->promotion
    public function promotion()
    {
        return $this->hasOne(Promotion::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Images helpers ───────────────────────────────────────────────
    /**
     * Return raw image strings as stored (no asset() here), normalized and non-empty.
     */
    public function imagesArray(): array
    {
        $imgs = $this->images;

        if (is_array($imgs)) {
            $list = $imgs;
        } elseif (is_string($imgs) && $imgs !== '') {
            $decoded = json_decode($imgs, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $list = $decoded;
            } else {
                $list = [$imgs];
            }
        } else {
            $list = [];
        }

        // trim + drop empties/whitespace
        $list = array_values(array_filter(array_map(fn($s) => trim((string)$s), $list)));

        return count($list) ? $list : [self::DEFAULT_IMAGE];
    }

    /**
     * Old helper you may still call in Blade or elsewhere.
     */
    public function getFirstImage()
    {
        return $this->imagesArray()[0] ?? self::DEFAULT_IMAGE;
    }

    /**
     * Build a full public URL from a stored image path.
     */
    protected function toPublicUrl(string $path): string
    {
        // Already absolute?
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // Default assets inside /public (no storage link needed)
        if (Str::startsWith($path, ['assets/', 'images/', 'img/'])) {
            return asset($path);
        }

        // Already a /storage URL or 'storage/...'
        if (Str::startsWith($path, ['/storage', 'storage/'])) {
            return asset(ltrim($path, '/')); // keep single /storage prefix
        }

        // Stored as 'public/...': map to 'storage/...'
        if (Str::startsWith($path, ['public/'])) {
            $rest = Str::after($path, 'public/');
            return asset('storage/' . ltrim($rest, '/'));
        }

        // Otherwise assume it lives in storage/app/public/...
        return asset('storage/' . ltrim($path, '/'));
    }

    public function getFirstImageUrlAttribute(): string
    {
        return $this->toPublicUrl($this->getFirstImage());
    }

    public function getImageUrlsAttribute(): array
    {
        return array_map(fn($img) => $this->toPublicUrl($img), $this->imagesArray());
    }

    // Optional alias if any view calls method-style
    public function firstImageUrl(): string { return $this->first_image_url; }

    // ── Pricing / Promotion ──────────────────────────────────────────
    public function getFormattedPriceAttribute(): string
    {
        // RWF generally shown without decimals
        return number_format((float) $this->price, 0) . ' RWF';
    }

    public function getIsOnSaleAttribute(): bool
    {
        $p = $this->promotion;
        if (!$p) return false;

        $now      = Carbon::now();
        $startsOk = $p->start_time ? $now->greaterThanOrEqualTo($p->start_time) : true;
        $endsOk   = $p->end_time   ? $now->lessThanOrEqualTo($p->end_time)   : true;

        return $startsOk && $endsOk && (float) ($p->discount_percentage ?? 0) > 0;
    }

    public function getSalePriceAttribute(): ?float
    {
        if (!$this->is_on_sale) return null;

        $discount = (float) ($this->promotion->discount_percentage ?? 0);
        return round(((float) $this->price) * (1 - $discount / 100), 0);
    }

    public function getInStockAttribute(): bool
    {
        return (int) $this->stock > 0;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at
            ? $this->created_at->greaterThanOrEqualTo(now()->subDays(self::NEW_DAYS))
            : false;
    }

    // ── Reviews & Ratings ───────────────────────────────────────────
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    // ── Scopes ──────────────────────────────────────────────────────
    public function scopeAvailable($q)       { return $q->where('stock', '>', 0); }
    public function scopeInCategory($q, $id) { return $q->where('category_id', $id); }

    public function scopeSearch($q, $term)
    {
        if (!$term) return $q;
        $t = '%' . trim($term) . '%';
        return $q->where(fn($qq) =>
            $qq->where('name', 'like', $t)
               ->orWhere('description', 'like', $t)
               ->orWhere('slug', 'like', $t)
        );
    }

    // ── Smart Recommendations ──────────────────────────────────────
    
    /**
     * Get related products in same category, preferring same brand
     */
    public function getRelatedProducts(int $limit = 6)
    {
        $query = static::where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->where('stock', '>', 0);
        
        // Try to get same brand first
        if ($this->brand_id) {
            $sameBrand = (clone $query)
                ->where('brand_id', $this->brand_id)
                ->limit($limit)
                ->get();
            
            if ($sameBrand->count() >= $limit) {
                return $sameBrand;
            }
        }
        
        // Otherwise get any products in category, sorted by popularity
        return $query
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get frequently bought together products
     */
    public function getFrequentlyBoughtTogether(int $limit = 3)
    {
        // Placeholder - requires order history analysis
        return collect();
    }
    
    /**
     * Get products that customers also bought
     */
    public function getCustomersAlsoBought(int $limit = 6)
    {
        $orderIds = $this->orderItems()->pluck('order_id');
        
        if ($orderIds->isEmpty()) {
            return collect();
        }
        
        return static::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->whereIn('order_items.order_id', $orderIds)
            ->where('products.id', '!=', $this->id)
            ->where('products.stock', '>', 0)
            ->distinct()
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get best sellers in a category
     */
    public static function getBestSellersInCategory(?int $categoryId, int $limit = 4)
    {
        if (!$categoryId) {
            return collect();
        }
        
        return static::where('category_id', $categoryId)
            ->where('stock', '>', 0)
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->limit($limit)
            ->get();
    }
}
