<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /** Use slugs in URLs */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Auto-generate unique slugs on create/update */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Category $m) {
            $m->slug = static::makeUniqueSlug($m->slug ?: ($m->name ?: Str::random(8)), $m->id);
        });
    }

    protected static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($base) ?: Str::random(8);
        $slug = $baseSlug; $i = 2;

        $q = static::query();
        if ($ignoreId) $q->where('id', '!=', $ignoreId);

        while ($q->clone()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$i}";
            $i++;
        }
        return $slug;
    }

    /* ---------------- Relationships ---------------- */

    public function brands()
    {
        return $this->hasMany(Brand::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
