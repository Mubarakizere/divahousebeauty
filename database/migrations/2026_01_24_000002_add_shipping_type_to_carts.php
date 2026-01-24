<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add shipping_type to carts table to track which price was selected.
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->enum('shipping_type', ['express', 'standard'])
                  ->default('express')
                  ->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('shipping_type');
        });
    }
};
