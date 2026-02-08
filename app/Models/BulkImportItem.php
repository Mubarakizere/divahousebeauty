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
        'express_price',
        'standard_price',
        'shipping_type',
        'description',
        'category_id',
        'brand_id',
        'stock',
        'status',
        'error_message',
        'product_id',
    ];

    protected $casts = [
        'parsed_price' => 'decimal:2',
        'express_price' => 'decimal:2',
        'standard_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected $attributes = [
        'shipping_type' => 'express_only',
        'stock' => 10,
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    // ── Price Calculation ────────────────────────────────────────────

    /**
     * Calculate the final RWF price using the formula:
     * detected_price × 2 × 10 = RWF express price
     * Standard price = express price * 0.8 (20% cheaper)
     */
    /**
     * Calculate the final RWF price using the formula:
     * detected_price × 2 × 10 = RWF standard price
     * Express price defaults to standard price (user edits manually)
     */
    public function calculatePrice(): void
    {
        if ($this->parsed_price !== null) {
            // Standard price: original × 2 × 10 (User logic)
            $this->standard_price = $this->parsed_price * 2 * 10;
            
            // Express price: Default to standard price (placeholder)
            // User will manually edit this if needed.
            // Since express_price is required in DB, we populate it.
            $this->express_price = $this->standard_price;
            
            // Default to 'standard_only' since standard is the calculated base
            // If user adds a higher express price, we'll detecting that during insert
            $this->shipping_type = 'standard_only';
            
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

        // Try to find price patterns in text (e.g., "$25", "25.00", "RWF 5000")
        preg_match('/[\$€£]?\s*(\d+(?:[.,]\d{2})?)\s*(?:RWF|USD|KES)?/i', $text, $matches);
        
        if (!empty($matches[1])) {
            // Found a price pattern
            $priceValue = str_replace(',', '.', $matches[1]);
            $this->parsed_price = (float) $priceValue;
            
            // Remove the price from the text to get the name
            $nameText = preg_replace('/[\$€£]?\s*\d+(?:[.,]\d{2})?\s*(?:RWF|USD|KES)?/i', '', $text);
            $this->parsed_name = trim($nameText) ?: $text;
            
            $this->calculatePrice();
            $this->description = $this->parsed_name;
            $this->status = 'ready';
            $this->save();
            
            return true;
        }

        // If no price found, use the whole text as name
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
