<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'usage_limit_per_user',
        'starts_at',
        'expires_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Automatically uppercase code when setting
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    /**
     * Check if coupon is valid for use
     */
    public function isValid(?int $userId = null, float $orderTotal = 0): bool
    {
        // Check active status
        if (!$this->is_active) {
            return false;
        }

        // Check start date
        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        // Check expiration
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        // Check global usage limit
        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return false;
        }

        // Check per-user usage limit
        if ($userId) {
            $userUsage = $this->usages()->where('user_id', $userId)->count();
            if ($userUsage >= $this->usage_limit_per_user) {
                return false;
            }
        }

        // Check minimum order amount
        if ($this->min_order_amount && $orderTotal < $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount for given order total
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if (!$this->isValid(auth()->id(), $orderTotal)) {
            return 0;
        }

        $discount = 0;

        switch ($this->type) {
            case 'percentage':
                $discount = ($orderTotal * $this->value) / 100;
                break;
            
            case 'fixed':
                $discount = min($this->value, $orderTotal);
                break;
            
            case 'free_shipping':
                // Can be extended to get shipping cost from config
                $discount = 0;
                break;
        }

        // Apply maximum discount cap
        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return round($discount, 2);
    }

    /**
     * Get formatted discount display
     */
    public function getDiscountDisplayAttribute(): string
    {
        return match($this->type) {
            'percentage' => $this->value . '% OFF',
            'fixed' => number_format($this->value, 0) . ' RWF OFF',
            'free_shipping' => 'FREE SHIPPING',
            default => ''
        };
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_active) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>';
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Expired</span>';
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Used Up</span>';
        }

        return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>';
    }

    /**
     * Relationship: coupon usages
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}
