<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bulk_import_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('bulk_import_batches')->onDelete('cascade');
            $table->string('original_filename');
            $table->string('temp_image_path')->nullable(); // Before cropping
            $table->string('cropped_image_path')->nullable(); // After cropping
            $table->text('ocr_raw_text')->nullable();
            $table->string('parsed_name')->nullable();
            $table->decimal('parsed_price', 10, 2)->nullable(); // Original detected price
            $table->decimal('calculated_price', 10, 2)->nullable(); // After ×2 ×10 formula
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'uploaded', 'cropped', 'processing', 'ocr_complete', 'ready', 'inserted', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('product_id')->nullable(); // After insertion
            $table->timestamps();
            
            $table->index(['batch_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_import_items');
    }
};
