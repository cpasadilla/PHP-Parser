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
            // Add interest_start_date column if it doesn't exist
            if (!Schema::hasColumn('orders', 'interest_start_date')) {
                $table->timestamp('interest_start_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove the interest_start_date column if it exists
            if (Schema::hasColumn('orders', 'interest_start_date')) {
                $table->dropColumn('interest_start_date');
            }
        });
    }
};
