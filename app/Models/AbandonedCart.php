<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AbandonedCart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'email',
        'recovery_token',
        'cart_items',
        'cart_total',
        'abandoned_at',
        'reminder_sent_at',
        'recovered_at',
        'is_recovered',
    ];

    protected $casts = [
        'cart_items' => 'array',
        'cart_total' => 'decimal:2',
        'abandoned_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'recovered_at' => 'datetime',
        'is_recovered' => 'boolean',
    ];

    /**
     * Generate unique recovery token
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Check if reminder should be sent
     */
    public function shouldSendReminder(): bool
    {
        if ($this->reminder_sent_at || $this->is_recovered) {
            return false;
        }

        $hoursSince = $this->abandoned_at->diffInHours(now());
        
        return $hoursSince >= 1 && $hoursSince <= 24;
    }

    /**
     * Get formatted cart total
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->cart_total, 0) . ' RWF';
    }

    /**
     * Get the user who abandoned the cart
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate recovery rate
     */
    public static function getRecoveryRate(): float
    {
        $total = static::whereNotNull('reminder_sent_at')->count();
        $recovered = static::where('is_recovered', true)->count();
        
        return $total > 0 ? round(($recovered / $total) * 100, 2) : 0;
    }

    /**
     * Get recovery statistics
     */
    public static function getStats(): array
    {
        return [
            'total_abandoned' => static::count(),
            'reminders_sent' => static::whereNotNull('reminder_sent_at')->count(),
            'recovered' => static::where('is_recovered', true)->count(),
            'recovery_rate' => static::getRecoveryRate(),
            'total_revenue_recovered' => static::where('is_recovered', true)->sum('cart_total'),
        ];
    }
}
