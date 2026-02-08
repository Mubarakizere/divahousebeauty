<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkImportBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_images',
        'processed_images',
        'successful_images',
        'failed_images',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BulkImportItem::class, 'batch_id');
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_images === 0) {
            return 0;
        }
        return (int) round(($this->processed_images / $this->total_images) * 100);
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsProcessingAttribute(): bool
    {
        return $this->status === 'processing';
    }

    // ── Status Methods ───────────────────────────────────────────────

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);
    }

    public function incrementProcessed(bool $success = true): void
    {
        $this->increment('processed_images');
        
        if ($success) {
            $this->increment('successful_images');
        } else {
            $this->increment('failed_images');
        }

        // Check if all images are processed
        if ($this->processed_images >= $this->total_images) {
            $this->markAsCompleted();
        }
    }
}
