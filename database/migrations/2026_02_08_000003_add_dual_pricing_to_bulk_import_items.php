<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add dual pricing and per-item category/brand support to bulk import items.
     * Products now have express_price and standard_price, plus each item 
     * can have its own category and brand assignment.
     */
    public function up(): void
    {
        Schema::table('bulk_import_items', function (Blueprint $table) {
            // Rename calculated_price to express_price for clarity
            $table->renameColumn('calculated_price', 'express_price');
        });

        Schema::table('bulk_import_items', function (Blueprint $table) {
            // Add standard price (nullable - for 7+ days delivery option)
            $table->decimal('standard_price', 10, 2)->nullable()->after('express_price');
            
            // Add shipping type (matches product shipping_type enum)
            $table->enum('shipping_type', ['both', 'express_only', 'standard_only'])
                  ->default('express_only')
                  ->after('standard_price');
            
            // Per-item category selection (nullable - can use batch default)
            $table->unsignedBigInteger('category_id')->nullable()->after('description');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            
            // Per-item brand selection (nullable)
            $table->unsignedBigInteger('brand_id')->nullable()->after('category_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            
            // Initial stock for the product
            $table->integer('stock')->default(10)->after('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_import_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['standard_price', 'shipping_type', 'category_id', 'brand_id', 'stock']);
        });

        Schema::table('bulk_import_items', function (Blueprint $table) {
            $table->renameColumn('express_price', 'calculated_price');
        });
    }
};
