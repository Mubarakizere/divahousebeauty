<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    // Order status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

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
        'estimated_delivery_date' => 'date',
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
        'total'     => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->masked_order_id)) {
                $order->masked_order_id = 'ORD-' . strtoupper(substr(uniqid(), -8));
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
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

    // Get order status badge
    public function getOrderStatusBadgeAttribute(): string
    {
        return match($this->order_status) {
            'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Pending</span>',
            'confirmed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Confirmed</span>',
            'processing' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Processing</span>',
            'shipped' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Shipped</span>',
            'delivered' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Delivered</span>',
            'cancelled' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>',
            'refunded' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Refunded</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    // Update order status and record history
    public function updateStatus(string $newStatus, ?string $notes = null, ?int $userId = null): void
    {
        $oldStatus = $this->status;
        
        $this->update(['status' => $newStatus]);
        
        $this->statusHistories()->create([
            'status_from' => $oldStatus,
            'status_to' => $newStatus,
            'notes' => $notes,
            'updated_by' => $userId ?? auth()->id()
        ]);
        
        // Fire event for email notifications if event exists
        if (class_exists('App\\Events\\OrderStatusChanged')) {
            event(new \App\Events\OrderStatusChanged($this, $oldStatus, $newStatus));
        }
    }

    // Check status helpers
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isShipped(): bool
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending_payment', 'processing']);
    }

    // Get formatted order number
    public function getOrderNumberAttribute(): string
    {
        return 'ORD-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
