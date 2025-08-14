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
            $table->string('pickup_delivery_type')->nullable()->after('amount');
            $table->string('vat_type')->nullable()->after('pickup_delivery_type');
            $table->string('hollowblock_size')->nullable()->after('vat_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_entries', function (Blueprint $table) {
            $table->dropColumn(['pickup_delivery_type', 'vat_type', 'hollowblock_size']);
        });
    }
};
