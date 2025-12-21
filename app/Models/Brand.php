<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'parent_id',
    ];

    /** Use slugs in URLs  */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Auto-generate unique slugs on create/update */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Brand $m) {
            // Base for slug = provided slug OR name OR random
            $base = $m->slug ?: ($m->name ?: Str::random(8));

            $m->slug = static::makeUniqueSlug($base, $m->id);
        });
    }

    protected static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($base) ?: Str::random(8);
        $slug     = $baseSlug;
        $i        = 2;

        $q = static::query();
        if ($ignoreId) {
            $q->where('id', '!=', $ignoreId);
        }

        while ($q->clone()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$i}";
            $i++;
        }

        return $slug;
    }

    /* ---------------- Relationships ---------------- */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** Parent brand (for sub-brands) */
    public function parent()
    {
        return $this->belongsTo(Brand::class, 'parent_id');
    }

    /** Children (sub-brands) */
    public function children()
    {
        return $this->hasMany(Brand::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function latestProduct()
    {
        return $this->hasOne(Product::class)->latestOfMany('created_at');
    }

    /** Scope for only top-level brands (no parent) */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
