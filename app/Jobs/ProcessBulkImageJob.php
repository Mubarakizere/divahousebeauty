<?php

namespace App\Jobs;

use App\Models\BulkImportItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

            // Try Google Cloud Vision API first, then fallback to Tesseract
            $text = $this->runGoogleVisionOCR($imagePath);
            
            if (empty($text)) {
                // Fallback to Tesseract if available
                $text = $this->runTesseractOCR($imagePath);
            }

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
     * Run OCR using Google Cloud Vision API
     */
    protected function runGoogleVisionOCR(string $imagePath): ?string
    {
        $apiKey = config('services.google_vision.api_key');
        
        Log::info("Starting Google Vision OCR for item {$this->item->id}");

        if (empty($apiKey)) {
            Log::warning("Google Vision API key not configured - Check config/services.php and .env");
            return null;
        }

        try {
            if (!file_exists($imagePath)) {
                Log::error("Image file not found at path: {$imagePath}");
                return null;
            }

            $imageContent = base64_encode(file_get_contents($imagePath));
            
            Log::info("Sending request to Google Vision API...");

            $response = Http::timeout(30)->post(
                "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}",
                [
                    'requests' => [
                        [
                            'image' => [
                                'content' => $imageContent,
                            ],
                            'features' => [
                                [
                                    'type' => 'TEXT_DETECTION',
                                    'maxResults' => 10,
                                ],
                            ],
                        ],
                    ],
                ]
            );

            Log::info("Google Vision API Response Status: " . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                // Log the full response for debugging
                Log::info("Google Vision API Response Body: " . json_encode($data));
                
                $textAnnotations = $data['responses'][0]['textAnnotations'] ?? [];
                
                if (!empty($textAnnotations)) {
                    $text = $textAnnotations[0]['description'] ?? '';
                    Log::info("Text extracted successfully: " . substr($text, 0, 50) . "...");
                    return $text;
                } else {
                    Log::warning("Google Vision API returned success but no text annotations found.");
                }
            } else {
                Log::error("Google Vision API error: " . $response->body());
                Log::error("Response Status: " . $response->status());
            }

        } catch (\Exception $e) {
            Log::error("Google Vision API exception: " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }

        return null;
    }

    /**
     * Run OCR using Tesseract (fallback)
     */
    protected function runTesseractOCR(string $imagePath): string
    {
        // Check if Tesseract is available
        $tesseractPath = $this->findTesseract();
        
        if (!$tesseractPath) {
            Log::warning("Tesseract OCR not available on this system");
            return '';
        }

        try {
            $outputFile = sys_get_temp_dir() . '/' . uniqid('ocr_') . '.txt';
            $outputBase = substr($outputFile, 0, -4); // Remove .txt extension
            
            $command = "\"{$tesseractPath}\" \"{$imagePath}\" \"{$outputBase}\" -l eng 2>&1";
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($outputFile)) {
                $text = file_get_contents($outputFile);
                unlink($outputFile);
                return trim($text);
            }

            Log::warning("Tesseract command failed with code {$returnCode}: " . implode("\n", $output));
            
        } catch (\Exception $e) {
            Log::error("Tesseract exception: " . $e->getMessage());
        }

        return '';
    }

    /**
     * Find Tesseract executable path
     */
    protected function findTesseract(): ?string
    {
        // Common paths
        $paths = [
            '/usr/bin/tesseract',
            '/usr/local/bin/tesseract',
            'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
            'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
            'tesseract', // System PATH
        ];

        foreach ($paths as $path) {
            if ($path === 'tesseract') {
                // Check if in PATH
                exec('which tesseract 2>/dev/null || where tesseract 2>nul', $output, $returnCode);
                if ($returnCode === 0 && !empty($output)) {
                    return trim($output[0]);
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        return null;
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
