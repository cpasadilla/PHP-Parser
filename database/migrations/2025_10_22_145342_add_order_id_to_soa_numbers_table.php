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
        Schema::table('soa_numbers', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['customer_id', 'ship', 'voyage']);
            
            // Add order_id column (nullable to support both old records and new per-BL records)
            $table->unsignedBigInteger('order_id')->nullable()->after('voyage');
            
            // Add foreign key for order_id
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            
            // Add new unique constraint that includes order_id
            $table->unique(['customer_id', 'ship', 'voyage', 'order_id'], 'soa_unique_with_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soa_numbers', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('soa_unique_with_order');
            
            // Drop foreign key and column
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
            
            // Restore the old unique constraint
            $table->unique(['customer_id', 'ship', 'voyage']);
        });
    }
};
