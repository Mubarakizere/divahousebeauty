<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
     protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total',
        'payment_method',
        'status',
        'is_paid',
        'paid_at',
        'transaction_id',
        'masked_order_id',
        'payment_token',
    ];

    protected $casts = [
        'is_paid'   => 'boolean',
        'paid_at'   => 'datetime',
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'total'     => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Accessor for formatted total
    public function getFormattedTotalAttribute(): string
    {
        return 'RWF ' . number_format($this->total, 0);
    }

    // Check if order is fully paid
    public function isPaid(): bool
    {
        return $this->is_paid === true;
    }

    // Get payment status badge
    public function getPaymentStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'confirmed' => '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Paid</span>',
            'pending' => '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">Pending</span>',
            'failed' => '<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Failed</span>',
            default => '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">Unknown</span>',
        };
    }
}
