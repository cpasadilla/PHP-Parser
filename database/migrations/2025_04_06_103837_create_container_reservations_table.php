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
        Schema::create('container_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('ship');
            $table->string('voyage');
            $table->string('type');
            $table->string('containerName')->nullable();
            $table->integer('quantity');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('origin')->nullable()->default(null);
            $table->string('destination')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_reservations');
    }
};
