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
        Schema::table('voyages', function (Blueprint $table) {
            $table->integer('dock_number')->default(0)->after('dock_period');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('dock_number')->default(0)->after('dock_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyages', function (Blueprint $table) {
            $table->dropColumn('dock_number');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('dock_number');
        });
    }
};