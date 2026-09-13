<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('total');
            $table->boolean('shipping_paid')->default(false)->after('shipping_cost');
            $table->timestamp('shipping_paid_at')->nullable()->after('shipping_paid');
            $table->string('shipping_transaction_id')->nullable()->after('shipping_paid_at');
            $table->text('shipping_notes')->nullable()->after('shipping_transaction_id');
        });

        // Add payment_type to payments table to distinguish order vs shipping payments
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_type')->default('order')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_cost',
                'shipping_paid',
                'shipping_paid_at',
                'shipping_transaction_id',
                'shipping_notes',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};
