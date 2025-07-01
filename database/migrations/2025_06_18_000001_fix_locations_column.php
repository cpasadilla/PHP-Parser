<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('locations') && !Schema::hasColumn('locations', 'name')) {
            // If the table exists but doesn't have 'name' column
            Schema::table('locations', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
            });
            
            // Copy data from 'location' column to 'name' column
            DB::statement('UPDATE locations SET name = location');
            
            // Make 'name' column unique
            Schema::table('locations', function (Blueprint $table) {
                $table->unique('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('locations') && Schema::hasColumn('locations', 'name')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
