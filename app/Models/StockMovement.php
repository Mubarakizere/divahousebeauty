<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    // Movement types
    const TYPE_PURCHASE = 'purchase';    // Restocking from supplier
    const TYPE_SALE = 'sale';            // Product sold to customer
    const TYPE_ADJUSTMENT = 'adjustment'; // Manual stock correction
    const TYPE_RETURN = 'return';         // Customer return

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product this movement belongs to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who recorded this movement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted quantity with +/- sign
     */
    public function getFormattedQuantityAttribute(): string
    {
        return ($this->quantity >= 0 ? '+' : '') . $this->quantity;
    }

    /**
     * Get human-readable type
     */
    public function getTypeLabel Attribute(): string
    {
        return match($this->type) {
            self::TYPE_PURCHASE => 'Purchase',
            self::TYPE_SALE => 'Sale',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_RETURN => 'Return',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get type badge HTML
     */
    public function getTypeBadgeAttribute(): string
    {
        $class = match($this->type) {
            self::TYPE_PURCHASE => 'bg-green-100 text-green-800',
            self::TYPE_SALE => 'bg-blue-100 text-blue-800',
            self::TYPE_ADJUSTMENT => 'bg-yellow-100 text-yellow-800',
            self::TYPE_RETURN => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };

        return "<span class=\"px-2 py-1 text-xs rounded-full {$class}\">{$this->type_label}</span>";
    }
}
