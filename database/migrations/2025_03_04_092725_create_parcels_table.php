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
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('orderId');
            $table->string('itemId');
            $table->string('itemName');
            $table->decimal('itemPrice',20,2);
            $table->string('quantity');
            $table->string('unit');
            $table->decimal('length',10,2)->nullable();
            $table->decimal('width',10,2)->nullable();
            $table->decimal('height',10,2)->nullable();
            $table->decimal('weight',10,2)->nullable();
            $table->decimal('multiplier',10,2)->nullable();
            $table->string('desc');
            $table->decimal('total',20,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
