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
        Schema::table('addresses', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('addresses', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('addresses', 'label')) {
                $table->string('label')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('addresses', 'type')) {
                $table->enum('type', ['home', 'work', 'other'])->nullable()->after('label');
            }
            
            if (!Schema::hasColumn('addresses', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('addresses', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('district');
            }
            
            if (!Schema::hasColumn('addresses', 'country')) {
                $table->string('country', 100)->default('Rwanda')->after('postal_code');
            }
            
            if (!Schema::hasColumn('addresses', 'notes')) {
                $table->text('notes')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('addresses', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('notes');
            }
            
            // Add index for better performance
            if (!Schema::hasIndex('addresses', 'addresses_user_id_is_default_index')) {
                $table->index(['user_id', 'is_default']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn([
                'label', 'type', 'recipient_name', 'postal_code', 
                'country', 'notes', 'is_default'
            ]);
            $table->dropIndex(['user_id', 'is_default']);
        });
    }
};