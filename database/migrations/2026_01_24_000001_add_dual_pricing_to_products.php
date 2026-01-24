<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Adds dual pricing support:
     * - Renames existing 'price' to 'express_price' (fast delivery - higher price)
     * - Adds 'standard_price' for 7+ days delivery (cheaper option)
     * - Adds 'shipping_type' to control which options are available
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Rename existing price column to express_price
            // All existing product prices become express prices
            $table->renameColumn('price', 'express_price');
        });

        Schema::table('products', function (Blueprint $table) {
            // Add standard price column (nullable - for 7+ days delivery)
            $table->decimal('standard_price', 10, 2)->nullable()->after('express_price');
            
            // Add shipping type: determines which prices are available
            // Default to 'express_only' for existing products
            $table->enum('shipping_type', ['both', 'express_only', 'standard_only'])
                  ->default('express_only')
                  ->after('standard_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['standard_price', 'shipping_type']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('express_price', 'price');
        });
    }
};
