<?php

namespace App\Jobs;

use App\Models\BulkImportItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ProcessBulkImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    protected BulkImportItem $item;

    /**
     * Create a new job instance.
     */
    public function __construct(BulkImportItem $item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->item->markAsProcessing();

            // Get the absolute path to the cropped image
            $imagePath = Storage::disk('public')->path($this->item->cropped_image_path);

            if (!file_exists($imagePath)) {
                throw new \Exception("Image file not found: {$imagePath}");
            }

            // Run Tesseract OCR
            $ocr = new TesseractOCR($imagePath);
            
            // Configure Tesseract for better accuracy
            $ocr->psm(6); // Assume uniform block of text
            $ocr->lang('eng'); // English language
            
            $text = $ocr->run();

            Log::info("OCR Result for item {$this->item->id}: {$text}");

            // Mark as OCR complete with the raw text
            $this->item->markAsOcrComplete($text);

            // Parse the OCR text to extract name and price
            $success = $this->item->parseOcrText();

            // Update batch progress
            $this->item->batch->incrementProcessed($success);

            Log::info("Processed bulk import item {$this->item->id}", [
                'name' => $this->item->parsed_name,
                'price' => $this->item->calculated_price,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to process bulk import item {$this->item->id}: " . $e->getMessage());
            
            $this->item->markAsFailed($e->getMessage());
            $this->item->batch->incrementProcessed(false);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessBulkImageJob failed for item {$this->item->id}: " . $exception->getMessage());
        
        $this->item->markAsFailed($exception->getMessage());
        $this->item->batch->incrementProcessed(false);
    }
}
