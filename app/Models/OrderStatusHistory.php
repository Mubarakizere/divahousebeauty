<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'status_from',
        'status_to',
        'notes',
        'updated_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order this status change belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who updated the status
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get formatted status names
     */
    public function getStatusFromLabelAttribute(): string
    {
        return $this->formatStatus($this->status_from);
    }

    public function getStatusToLabelAttribute(): string
    {
        return $this->formatStatus($this->status_to);
    }

    private function formatStatus(?string $status): string
    {
        if (!$status) return 'N/A';
        
        return ucfirst(strtolower($status));
    }
}
