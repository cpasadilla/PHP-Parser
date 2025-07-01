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
            // Remove the cargoStatus column
            $table->dropColumn('cargoStatus');

            // Update the blStatus column to have default options
            $table->string('blStatus')->nullable()->default(' ')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add the cargoStatus column back
            $table->string('cargoStatus')->nullable();

            // Revert the blStatus column to its previous state
            $table->string('blStatus')->nullable()->change();
        });
    }
};
