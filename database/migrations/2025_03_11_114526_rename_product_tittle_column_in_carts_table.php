<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->renameColumn('product_tittle', 'product_title');
        });
    }

    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->renameColumn('product_title', 'product_tittle');
        });
    }
};
