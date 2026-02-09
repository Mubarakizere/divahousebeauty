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

        // 1. Clean up common OCR noise
        // Remove timestamps (e.g., 20:15) used in some screenshots
        $text = preg_replace('/\b\d{1,2}:\d{2}\b/', '', $text);
        // Remove "<-" arrows often seen in screenshots
        $text = str_replace(['<-', '->'], '', $text);
        
        $text = trim($text);
        
        // 2. Look for the @ separator (Strict format)
        if (strpos($text, '@') !== false) {
            $parts = explode('@', $text, 2);
            $this->parsed_name = trim($parts[0]);
            
            $priceText = trim($parts[1]);
            // Remove everything except numbers and dots
            $priceValue = preg_replace('/[^0-9.]/', '', $priceText);
            
            if (is_numeric($priceValue)) {
                $this->saveParsedData($this->parsed_name, (float) $priceValue);
                return true;
            }
        }

        // 3. Fallback: Try to find price patterns at the END of the string
        // Matches: 100, 100.00, 100 RWF, etc. at the end
        // EXCLUDE patterns like "24K", "100ml", "50g", "250mg" by ensuring no such suffix follows
        if (preg_match('/[\s@]+(\d+(?:[.,]\d{2})?)\s*(?:RWF|USD|KES|Frw)\.?\s*$/i', $text, $matches)) {
             $priceValue = str_replace(',', '.', $matches[1]);
             $nameText = substr($text, 0, -strlen($matches[0]));
             
             $this->saveParsedData($nameText ?: $text, (float) $priceValue);
             return true;
        }

        // 4. Fallback: Strict standalone price regex
        // Must have currency symbol OR be a large number (likely a price, not size) if no currency
        // Avoid "24" if it's "24K"
        
        // Try to find currency-prefixed price (e.g., RWF 5000)
        if (preg_match('/(?:RWF|USD|KES|Frw)\.?\s*(\d+(?:[.,]\d{2})?)/i', $text, $matches)) {
            $priceValue = str_replace(',', '.', $matches[1]);
            $nameText = str_replace($matches[0], '', $text); // Remove price part
            
            $this->saveParsedData($nameText ?: $text, (float) $priceValue);
            return true;
        }

        // 5. Last Resort: Look for number at end of line, but verify it's not a unit
        // Negative lookahead to ensure it's not followed by K, g, ml, etc.
        if (preg_match('/(\d+(?:[.,]\d{2})?)\s*$/', $text, $matches)) {
            // Check if the number is likely a price (> 100) or if it's small, it might be questionable
            // But strict exclusion of units is handled by not matching if text follows
            
            // However, typical "24K Gold" ends with Gold, so this regex only matches if string ENDS with number.
            // If string is "Product 24K", it ends with K.
            // If string is "Product 24", it ends with 24.
            
            // Let's add a check: if number is < 100, treat it as ambiguous/version unless completely alone?
            // Actually, safest is to just use it if no unit suffix.
            
            $priceValue = str_replace(',', '.', $matches[1]);
            $nameText = substr($text, 0, -strlen($matches[0]));
            
            $this->saveParsedData($nameText ?: $text, (float) $priceValue);
            return true;
        }

        // If no price found, use the whole text as name
        $this->parsed_name = $text;
        $this->description = $text;
        $this->status = 'ready';
        $this->save();
        
        return false;
    }

    private function saveParsedData(string $name, float $price)
    {
        $this->parsed_name = trim($name);
        $this->parsed_price = $price;
        $this->description = $this->parsed_name;
        
        $this->calculatePrice();
        $this->status = 'ready';
        $this->save();
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
