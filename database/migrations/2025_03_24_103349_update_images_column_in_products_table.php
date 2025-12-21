<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('images')->change(); // Change from LONGTEXT to JSON
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->longText('images')->change(); // Rollback if needed
        });
    }
};
