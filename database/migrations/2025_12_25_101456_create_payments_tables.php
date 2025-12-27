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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('request_token')->unique();
            $table->string('payment_ref')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('RWF');
            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending');
            $table->text('iframe_url')->nullable();
            $table->json('customer_data')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('transfer_ref')->nullable();
            $table->decimal('amount', 12, 2);
            $table->integer('percentage')->default(100);
            $table->string('recipient_number');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transfers');
        Schema::dropIfExists('payments');
    }
};
