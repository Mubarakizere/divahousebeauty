<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            // Slug for pretty URLs
           

            // Optional parent brand (for sub-brands)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete()
                ->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['slug', 'parent_id']);
        });
    }
};
