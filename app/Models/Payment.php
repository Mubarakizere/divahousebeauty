<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'request_token',
        'payment_ref',
        'amount',
        'currency',
        'payment_method',
        'status',
        'iframe_url',
        'customer_data',
    ];

    protected $casts = [
        'customer_data' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the order this payment belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who made this payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transfer legs for this payment
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(PaymentTransfer::class);
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0) . ' ' . $this->currency;
    }
}
