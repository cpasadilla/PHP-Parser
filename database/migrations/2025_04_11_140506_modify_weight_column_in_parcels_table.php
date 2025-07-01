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
        Schema::table('parcels', function (Blueprint $table) {
            $table->string('weight')->nullable()->change(); // Change the weight column to VARCHAR and make it nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable(false)->change(); // Revert the weight column back to DECIMAL and make it not nullable
        });
    }
};
