<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Target ID (Keep this one)
        $keepId = 38;
        // 2. Duplicate ID (Remove this one)
        $removeId = 58;

        // Ensure both exist or at least the one to remove exists to avoid errors if run twice
        $brandToKeep = DB::table('brands')->where('id', $keepId)->first();
        $brandToRemove = DB::table('brands')->where('id', $removeId)->first();

        if ($brandToRemove) {
            // Move products
            DB::table('products')
                ->where('brand_id', $removeId)
                ->update(['brand_id' => $keepId]);

            // Delete duplicate brand
            DB::table('brands')->where('id', $removeId)->delete();
        }

        if ($brandToKeep) {
            // Ideally ensure naming is consistent
            DB::table('brands')
                ->where('id', $keepId)
                ->update(['name' => 'Lash Care']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible without tracking which products moved.
        // We accept this as a data fix.
    }
};
