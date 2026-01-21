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
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3)->default('RWF');
            $table->string('target_currency', 3);
            $table->decimal('exchange_rate', 20, 10);
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();

            // Ensure unique combination of base and target currencies
            $table->unique(['base_currency', 'target_currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
