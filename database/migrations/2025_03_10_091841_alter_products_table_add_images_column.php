<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image'); // Remove the old column
            $table->json('images')->nullable(); // Add a JSON column for multiple images
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('images');
            $table->string('image')->nullable(); // Revert back to a single image column if needed
        });
    }
};
