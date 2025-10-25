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
        // This migration is redundant - the complete table is created in 2025_09_08_104713_create_crew_embarkations_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No table to drop as this migration has been disabled
    }
};
