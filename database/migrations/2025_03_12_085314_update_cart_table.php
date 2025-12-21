<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCartTable extends Migration
{
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {

            // Remove unnecessary columns
            $table->dropColumn(['name', 'phone', 'address']);

            // Add foreign key to users table
            $table->unsignedBigInteger('users_id')->after('id');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');

            // Add image column to store multiple image paths
            $table->json('image')->nullable()->after('price');
        });
    }

    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            // Rollback changes
            $table->dropForeign(['users_id']);
            $table->dropColumn(['users_id', 'image']);

            // Re-add removed columns
            $table->string('name', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('address', 255)->nullable();
        });
    }
}
