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
            // Add separate columns for each hollowblock size
            $table->decimal('hollowblock_4_inch_in', 10, 3)->nullable();
            $table->decimal('hollowblock_4_inch_out', 10, 3)->nullable();
            $table->decimal('hollowblock_4_inch_balance', 10, 3)->nullable();
            
            $table->decimal('hollowblock_5_inch_in', 10, 3)->nullable();
            $table->decimal('hollowblock_5_inch_out', 10, 3)->nullable();
            $table->decimal('hollowblock_5_inch_balance', 10, 3)->nullable();
            
            $table->decimal('hollowblock_6_inch_in', 10, 3)->nullable();
            $table->decimal('hollowblock_6_inch_out', 10, 3)->nullable();
            $table->decimal('hollowblock_6_inch_balance', 10, 3)->nullable();
            
            // Add a field to store the original OUT value for PER BAG entries before conversion
            $table->decimal('out_original_bags', 10, 3)->nullable()->after('out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_entries', function (Blueprint $table) {
            $table->dropColumn([
                'hollowblock_4_inch_in',
                'hollowblock_4_inch_out', 
                'hollowblock_4_inch_balance',
                'hollowblock_5_inch_in',
                'hollowblock_5_inch_out',
                'hollowblock_5_inch_balance',
                'hollowblock_6_inch_in', 
                'hollowblock_6_inch_out',
                'hollowblock_6_inch_balance',
                'out_original_bags'
            ]);
        });
    }
};
