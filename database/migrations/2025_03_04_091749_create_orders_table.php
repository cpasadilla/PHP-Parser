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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('orderId');
            $table->decimal('totalAmount', 20, 2);
            $table->decimal('freight', 20, 2);
            $table->decimal('discount', 20, 2)->nullable(); // Add nullable discount column
            $table->decimal('valuation', 20, 2)->nullable();
            $table->decimal('bir', 20, 2)->nullable(); // Ensure this column exists
            $table->decimal('value', 20, 2)->nullable();
            $table->decimal('other', 20, 2)->nullable();
            $table->decimal('wharfage', 20, 2)->nullable(); // Add wharfage column
            $table->decimal('originalFreight', 20, 2)->nullable(); // Add originalFreight column
            $table->string('cargoType');
            $table->string('shipperId');
            $table->string('shipperName');
            $table->string('shipperNum');
            $table->string('recId');
            $table->string('recName');
            $table->string('recNum');
            $table->string('origin');
            $table->string('destination');
            $table->string('shipNum');
            $table->string('voyageNum');
            $table->string('containerNum')->nullable();
            $table->string('gatePass')->nullable();
            $table->string('checkName')->nullable();
            $table->string('remark')->nullable();
            $table->string('orderCreated');
            $table->string('cargoStatus');
            $table->string('blStatus')->nullable();
            $table->string('creator')->nullable();
            $table->string('OR')->nullable(); // Add OR column
            $table->string('AR')->nullable(); // Add AR column
            $table->string('image')->nullable(); // Ensure this column exists
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
