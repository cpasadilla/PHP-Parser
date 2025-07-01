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
        Schema::table('order_delete_logs', function (Blueprint $table) {
            $table->json('order_data')->nullable(); // Store complete order data for restore
            $table->json('parcels_data')->nullable(); // Store parcels data for restore
            $table->timestamp('restored_at')->nullable(); // Track if/when order was restored
            $table->string('restored_by')->nullable(); // Track who restored the order
            $table->unsignedBigInteger('restored_order_id')->nullable(); // New order ID after restore
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_delete_logs', function (Blueprint $table) {
            $table->dropColumn(['order_data', 'parcels_data', 'restored_at', 'restored_by', 'restored_order_id']);
        });
    }
};
