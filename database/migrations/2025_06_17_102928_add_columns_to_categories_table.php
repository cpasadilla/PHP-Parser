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
        // Columns already exist in the original create_categories_table migration
        // This migration is redundant and has been disabled to prevent duplicate column errors
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No columns to drop as this migration has been disabled
    }
};
