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
            // Add wharfage column if it doesn't exist
            if (!Schema::hasColumn('orders', 'wharfage')) {
                $table->decimal('wharfage', 20, 2)->nullable()->after('other');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop wharfage column if it exists
            if (Schema::hasColumn('orders', 'wharfage')) {
                $table->dropColumn('wharfage');
            }
        });
    }
};
