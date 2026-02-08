<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBulkImageJob;
use App\Models\BulkImportBatch;
use App\Models\BulkImportItem;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class BulkImageImportController extends Controller
{
    /**
     * Display the upload form
     */
    public function index()
    {
        $batches = BulkImportBatch::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.bulk-import.index', compact('batches'));
    }

    /**
     * Handle multiple image upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1|max:200',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:10240', // 10MB max per image
        ]);

        // Create a new batch
        $batch = BulkImportBatch::create([
            'user_id' => Auth::id(),
            'status' => 'pending',
            'total_images' => count($request->file('images')),
        ]);

        // Store each uploaded image
        foreach ($request->file('images') as $image) {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('bulk-imports/temp/' . $batch->id, $filename, 'public');

            BulkImportItem::create([
                'batch_id' => $batch->id,
                'original_filename' => $image->getClientOriginalName(),
                'temp_image_path' => $path,
                'status' => 'uploaded',
            ]);
        }

        return redirect()->route('admin.bulk-import.crop', $batch->id)
            ->with('success', "Uploaded {$batch->total_images} images successfully. Now crop each image.");
    }

    /**
     * Show the cropping interface
     */
    public function cropPage(BulkImportBatch $batch)
    {
        $items = $batch->items()->whereIn('status', ['uploaded', 'cropped'])->get();
        
        return view('admin.bulk-import.crop', compact('batch', 'items'));
    }

    /**
     * Save cropped image and dispatch OCR job
     */
    public function saveCrop(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:bulk_import_items,id',
            'crop_data' => 'required|string', // Base64 image data
        ]);

        $item = BulkImportItem::findOrFail($request->item_id);
        $batch = $item->batch;

        try {
            // Decode base64 image
            $imageData = $request->crop_data;
            
            // Remove data URL prefix if present
            if (strpos($imageData, 'base64,') !== false) {
                $imageData = explode('base64,', $imageData)[1];
            }
            
            $imageData = base64_decode($imageData);

            // Create image using Intervention Image
            $image = Image::read($imageData);

            // Optimize: resize if too large, compress, and convert to WebP
            $maxWidth = 1200;
            $maxHeight = 1200;
            
            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->scaleDown($maxWidth, $maxHeight);
            }

            // Generate filename and save as WebP for optimization
            $filename = Str::uuid() . '.webp';
            $path = 'products/' . $filename;
            $fullPath = Storage::disk('public')->path($path);
            
            // Ensure directory exists
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save as WebP with quality 85 for good compression
            $image->toWebp(85)->save($fullPath);

            // Update item with cropped image path
            $item->markAsCropped($path);

            // Mark batch as processing if not already
            if ($batch->status === 'pending') {
                $batch->markAsProcessing();
            }

            // Dispatch OCR job
            ProcessBulkImageJob::dispatch($item);

            return response()->json([
                'success' => true,
                'message' => 'Image cropped and processing started',
                'item_id' => $item->id,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to crop image for item {$item->id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crop all images at once (skip cropping, use original)
     */
    public function cropAllAuto(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:bulk_import_batches,id',
        ]);

        $batch = BulkImportBatch::findOrFail($request->batch_id);
        $items = $batch->items()->where('status', 'uploaded')->get();

        $batch->markAsProcessing();

        foreach ($items as $item) {
            try {
                // Get original image
                $originalPath = Storage::disk('public')->path($item->temp_image_path);
                
                if (!file_exists($originalPath)) {
                    $item->markAsFailed('Original image not found');
                    continue;
                }

                // Create optimized copy
                $image = Image::read($originalPath);

                // Resize if needed
                $maxWidth = 1200;
                $maxHeight = 1200;
                
                if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                    $image->scaleDown($maxWidth, $maxHeight);
                }

                // Save as WebP
                $filename = Str::uuid() . '.webp';
                $path = 'products/' . $filename;
                $fullPath = Storage::disk('public')->path($path);
                
                $directory = dirname($fullPath);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                $image->toWebp(85)->save($fullPath);

                $item->markAsCropped($path);

                // Dispatch OCR job
                ProcessBulkImageJob::dispatch($item);

            } catch (\Exception $e) {
                Log::error("Failed to auto-crop item {$item->id}: " . $e->getMessage());
                $item->markAsFailed($e->getMessage());
            }
        }

        return redirect()->route('admin.bulk-import.preview', $batch->id)
            ->with('info', 'Processing started. OCR is running in background.');
    }

    /**
     * Show preview table with parsed data
     */
    public function preview(BulkImportBatch $batch)
    {
        $items = $batch->items()->orderBy('id')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('admin.bulk-import.preview', compact('batch', 'items', 'categories'));
    }

    /**
     * Update individual item
     */
    public function updateItem(Request $request, BulkImportItem $item)
    {
        $request->validate([
            'parsed_name' => 'nullable|string|max:255',
            'calculated_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $item->update([
            'parsed_name' => $request->parsed_name,
            'calculated_price' => $request->calculated_price,
            'description' => $request->description ?? $request->parsed_name,
            'status' => 'ready',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
        ]);
    }

    /**
     * Get batch progress (AJAX endpoint)
     */
    public function batchProgress(BulkImportBatch $batch)
    {
        $batch->refresh();
        
        $items = $batch->items()->select('id', 'status', 'parsed_name', 'calculated_price', 'error_message')->get();

        return response()->json([
            'batch' => [
                'id' => $batch->id,
                'status' => $batch->status,
                'total_images' => $batch->total_images,
                'processed_images' => $batch->processed_images,
                'successful_images' => $batch->successful_images,
                'failed_images' => $batch->failed_images,
                'progress_percentage' => $batch->progress_percentage,
                'is_complete' => $batch->is_complete,
            ],
            'items' => $items,
        ]);
    }

    /**
     * Insert all products into database
     */
    public function insertAll(Request $request, BulkImportBatch $batch)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $items = $batch->items()->where('status', 'ready')->get();
        $categoryId = $request->category_id;

        $insertedCount = 0;
        $failedCount = 0;

        foreach ($items as $item) {
            try {
                // Validate required fields
                if (empty($item->parsed_name)) {
                    $item->markAsFailed('Product name is required');
                    $failedCount++;
                    continue;
                }

                if (empty($item->calculated_price) || $item->calculated_price <= 0) {
                    $item->markAsFailed('Valid price is required');
                    $failedCount++;
                    continue;
                }

                // Create product
                $product = Product::create([
                    'name' => $item->parsed_name,
                    'slug' => Str::slug($item->parsed_name) . '-' . Str::random(5),
                    'description' => $item->description ?? $item->parsed_name,
                    'price' => $item->calculated_price,
                    'stock' => 10, // Default stock
                    'category_id' => $categoryId,
                    'images' => json_encode([$item->cropped_image_path]),
                ]);

                $item->markAsInserted($product->id);
                $insertedCount++;

            } catch (\Exception $e) {
                Log::error("Failed to insert product for item {$item->id}: " . $e->getMessage());
                $item->markAsFailed($e->getMessage());
                $failedCount++;
            }
        }

        $message = "Successfully inserted {$insertedCount} products.";
        if ($failedCount > 0) {
            $message .= " {$failedCount} items failed.";
        }

        return redirect()->route('admin.products.index')
            ->with('success', $message);
    }

    /**
     * Delete a batch and all its items
     */
    public function destroy(BulkImportBatch $batch)
    {
        // Delete temp images
        foreach ($batch->items as $item) {
            if ($item->temp_image_path) {
                Storage::disk('public')->delete($item->temp_image_path);
            }
            // Note: We don't delete cropped images as they might be used by products
        }

        // Delete temp directory
        Storage::disk('public')->deleteDirectory('bulk-imports/temp/' . $batch->id);

        $batch->delete();

        return redirect()->route('admin.bulk-import.index')
            ->with('success', 'Batch deleted successfully.');
    }
}
