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
        Schema::create('order_delete_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // Store the original order ID
            $table->string('bl_number')->nullable(); // Store the BL number for reference
            $table->string('ship_name')->nullable(); // Store ship name
            $table->string('voyage_number')->nullable(); // Store voyage number
            $table->string('shipper_name')->nullable(); // Store shipper name
            $table->string('consignee_name')->nullable(); // Store consignee name
            $table->decimal('total_amount', 15, 2)->nullable(); // Store total amount
            $table->string('deleted_by'); // Who deleted the order
            $table->timestamps(); // When it was deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_delete_logs');
    }
};
