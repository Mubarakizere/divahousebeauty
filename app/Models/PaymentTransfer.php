<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransfer extends Model
{
    protected $fillable = [
        'payment_id',
        'transfer_ref',
        'amount',
        'percentage',
        'recipient_number',
        'status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the parent payment
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Check if transfer is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }
}
