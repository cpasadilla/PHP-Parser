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
        Schema::table('orders', function (Blueprint $table) {
            // Add bl_computed column if it doesn't exist
            if (!Schema::hasColumn('orders', 'bl_computed')) {
                $table->boolean('bl_computed')->default(false)->after('blStatus');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'bl_computed')) {
                $table->dropColumn('bl_computed');
            }
        });
    }
};
