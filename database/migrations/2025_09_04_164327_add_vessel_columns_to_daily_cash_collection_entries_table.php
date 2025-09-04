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
        Schema::table('daily_cash_collection_entries', function (Blueprint $table) {
            // Add vessel freight columns
            $table->decimal('mv_everwin_star_1', 10, 2)->default(0)->after('status');
            $table->decimal('mv_everwin_star_2', 10, 2)->default(0)->after('mv_everwin_star_1');
            $table->decimal('mv_everwin_star_3', 10, 2)->default(0)->after('mv_everwin_star_2');
            $table->decimal('mv_everwin_star_4', 10, 2)->default(0)->after('mv_everwin_star_3');
            $table->decimal('mv_everwin_star_5', 10, 2)->default(0)->after('mv_everwin_star_4');
            
            // Add vessel other income columns
            $table->decimal('mv_everwin_star_1_other', 10, 2)->default(0)->after('mv_everwin_star_5');
            $table->decimal('mv_everwin_star_2_other', 10, 2)->default(0)->after('mv_everwin_star_1_other');
            $table->decimal('mv_everwin_star_3_other', 10, 2)->default(0)->after('mv_everwin_star_2_other');
            $table->decimal('mv_everwin_star_4_other', 10, 2)->default(0)->after('mv_everwin_star_3_other');
            $table->decimal('mv_everwin_star_5_other', 10, 2)->default(0)->after('mv_everwin_star_4_other');
            
            // Add additional charges columns
            $table->decimal('wharfage_payables', 10, 2)->default(0)->after('mv_everwin_star_5_other');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_cash_collection_entries', function (Blueprint $table) {
            $table->dropColumn([
                'mv_everwin_star_1',
                'mv_everwin_star_2',
                'mv_everwin_star_3',
                'mv_everwin_star_4',
                'mv_everwin_star_5',
                'mv_everwin_star_1_other',
                'mv_everwin_star_2_other',
                'mv_everwin_star_3_other',
                'mv_everwin_star_4_other',
                'mv_everwin_star_5_other',
                'wharfage_payables'
            ]);
        });
    }
};
