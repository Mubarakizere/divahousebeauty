<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Brand;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add nullable 'slug' columns (NO unique yet)
        Schema::table('categories', function (Blueprint $t) {
            if (!Schema::hasColumn('categories', 'slug')) {
                $t->string('slug')->nullable()->after('name');
            }
        });
        Schema::table('brands', function (Blueprint $t) {
            if (!Schema::hasColumn('brands', 'slug')) {
                $t->string('slug')->nullable()->after('name');
            }
        });

        // 2) Backfill unique slugs
        // Use saveQuietly() so no observers run
        Category::orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $c) {
                if (!$c->slug || $c->slug === '') {
                    $base = Str::slug($c->name) ?: 'category-'.$c->id;
                    $slug = $base; $i = 2;
                    while (Category::where('slug', $slug)->where('id', '!=', $c->id)->exists()) {
                        $slug = "{$base}-{$i}"; $i++;
                    }
                    $c->slug = $slug;
                    $c->saveQuietly();
                }
            }
        });

        Brand::orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $b) {
                if (!$b->slug || $b->slug === '') {
                    $base = Str::slug($b->name) ?: 'brand-'.$b->id;
                    $slug = $base; $i = 2;
                    while (Brand::where('slug', $slug)->where('id', '!=', $b->id)->exists()) {
                        $slug = "{$base}-{$i}"; $i++;
                    }
                    $b->slug = $slug;
                    $b->saveQuietly();
                }
            }
        });

        // 3) Add unique indexes AFTER data is populated
        Schema::table('categories', function (Blueprint $t) {
            // only add if not already present
            $t->unique('slug', 'categories_slug_unique');
        });
        Schema::table('brands', function (Blueprint $t) {
            $t->unique('slug', 'brands_slug_unique');
        });

        // (Optional) If you want NOT NULL, you can later run a separate migration
        // with $table->string('slug')->nullable(false)->change();
        // (requires doctrine/dbal). It’s fine to keep nullable.
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $t) {
            if (Schema::hasColumn('brands', 'slug')) {
                $t->dropUnique('brands_slug_unique');
                $t->dropColumn('slug');
            }
        });
        Schema::table('categories', function (Blueprint $t) {
            if (Schema::hasColumn('categories', 'slug')) {
                $t->dropUnique('categories_slug_unique');
                $t->dropColumn('slug');
            }
        });
    }
};
