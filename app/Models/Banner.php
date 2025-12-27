<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'name',
        'position',
        'image',
        'link',
        'link_text',
        'title',
        'subtitle',
        'start_date',
        'end_date',
        'is_active',
        'order',
        'target',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope to get only active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope to get banners by position
     */
    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope to order by the order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the full URL for the banner image
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // Use asset helper which works for both local and production
        return asset('storage/' . $this->image);
    }

    /**
     * Check if banner is currently valid (within date range)
     */
    public function isCurrentlyValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Get human-readable position name
     */
    public function getPositionNameAttribute(): string
    {
        return match($this->position) {
            'hero_main' => 'Hero Main Slider',
            'hero_side' => 'Hero Side Banner',
            'category_top' => 'Category Top',
            'mid_page' => 'Mid Page',
            'footer_above' => 'Above Footer',
            default => ucfirst(str_replace('_', ' ', $this->position))
        };
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_active) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>';
        }

        if (!$this->isCurrentlyValid()) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Scheduled</span>';
        }

        if ($this->end_date && now()->addDays(7)->gte($this->end_date)) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Expiring Soon</span>';
        }

        return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>';
    }
}
