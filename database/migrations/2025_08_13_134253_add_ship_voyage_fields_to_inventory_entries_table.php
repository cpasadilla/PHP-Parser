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
        Schema::table('inventory_entries', function (Blueprint $table) {
            $table->string('ship_number')->nullable();
            $table->string('voyage_number')->nullable();
            $table->boolean('is_starting_balance')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_entries', function (Blueprint $table) {
            $table->dropColumn(['ship_number', 'voyage_number', 'is_starting_balance']);
        });
    }
};
