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
        Schema::create('gate_pass_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gate_pass_id');
            $table->string('item_description'); // Name of the item (e.g., "rice 25kg", "grocery")
            $table->decimal('total_quantity', 10, 2); // Total quantity in BL
            $table->string('unit'); // Unit of measurement (sks, bxs, pcs, etc.)
            $table->decimal('released_quantity', 10, 2); // Quantity released in this gate pass
            $table->decimal('remaining_quantity', 10, 2); // Quantity still not released
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('gate_pass_id')->references('id')->on('gate_passes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_pass_items');
    }
};
