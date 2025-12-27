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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Internal reference name
            $table->enum('position', [
                'hero_main',      // Main hero slider
                'hero_side',      // Side banner in hero
                'category_top',   // Above category sections
                'mid_page',       // Middle of page
                'footer_above'    // Above footer
            ])->default('hero_main');
            $table->string('image'); // Banner image path
            $table->string('link')->nullable(); // Click destination URL
            $table->string('link_text', 50)->nullable(); // CTA button text
            $table->string('title')->nullable(); // Banner headline
            $table->text('subtitle')->nullable(); // Banner description
            $table->dateTime('start_date')->nullable(); // When to start showing
            $table->dateTime('end_date')->nullable(); // When to stop showing
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Display order
            $table->enum('target', ['_self', '_blank'])->default('_self'); // Link target
            $table->timestamps();
            
            // Index for common queries
            $table->index(['position', 'is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
