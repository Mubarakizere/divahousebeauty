<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkImportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'original_filename',
        'temp_image_path',
        'cropped_image_path',
        'ocr_raw_text',
        'parsed_name',
        'parsed_price',
        'calculated_price',
        'description',
        'status',
        'error_message',
        'product_id',
    ];

    protected $casts = [
        'parsed_price' => 'decimal:2',
        'calculated_price' => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function batch(): BelongsTo
    {
        return $this->belongsTo(BulkImportBatch::class, 'batch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ── Price Calculation ────────────────────────────────────────────

    /**
     * Calculate the final RWF price using the formula:
     * detected_price × 2 × 10 = RWF price
     */
    public function calculatePrice(): void
    {
        if ($this->parsed_price !== null) {
            $this->calculated_price = $this->parsed_price * 2 * 10;
            $this->save();
        }
    }

    /**
     * Parse the OCR text in format "Product Name @ Price"
     * and extract name and price
     */
    public function parseOcrText(): bool
    {
        if (empty($this->ocr_raw_text)) {
            return false;
        }

        $text = trim($this->ocr_raw_text);
        
        // Look for the @ separator
        if (strpos($text, '@') !== false) {
            $parts = explode('@', $text, 2);
            
            $this->parsed_name = trim($parts[0]);
            
            // Extract numeric value from price part
            $priceText = trim($parts[1]);
            $priceValue = preg_replace('/[^0-9.]/', '', $priceText);
            
            if (is_numeric($priceValue)) {
                $this->parsed_price = (float) $priceValue;
                $this->calculatePrice();
            }
            
            // Set description as name by default
            $this->description = $this->parsed_name;
            $this->status = 'ready';
            $this->save();
            
            return true;
        }

        // If no @ found, use the whole text as name
        $this->parsed_name = $text;
        $this->description = $text;
        $this->status = 'ready';
        $this->save();
        
        return false;
    }

    // ── Status Methods ───────────────────────────────────────────────

    public function markAsUploaded(string $tempPath): void
    {
        $this->update([
            'temp_image_path' => $tempPath,
            'status' => 'uploaded',
        ]);
    }

    public function markAsCropped(string $croppedPath): void
    {
        $this->update([
            'cropped_image_path' => $croppedPath,
            'status' => 'cropped',
        ]);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsOcrComplete(string $ocrText): void
    {
        $this->update([
            'ocr_raw_text' => $ocrText,
            'status' => 'ocr_complete',
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
    }

    public function markAsInserted(int $productId): void
    {
        $this->update([
            'product_id' => $productId,
            'status' => 'inserted',
        ]);
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'uploaded', 'cropped']);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
