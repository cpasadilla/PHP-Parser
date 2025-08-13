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
        Schema::create('inventory_entries', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->date('date');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('in', 12, 4)->nullable();
            $table->decimal('out', 12, 4)->nullable();
            $table->decimal('balance', 12, 4)->nullable();
            $table->decimal('amount', 12, 4)->nullable();
            $table->string('or_ar')->nullable();
            $table->string('dr_number')->nullable();
            $table->date('onsite_date')->nullable();
            $table->decimal('onsite_in', 12, 4)->nullable();
            $table->decimal('actual_out', 12, 4)->nullable();
            $table->decimal('onsite_balance', 12, 4)->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_entries');
    }
};
