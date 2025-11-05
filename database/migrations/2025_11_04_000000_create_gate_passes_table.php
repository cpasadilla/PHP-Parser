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
        Schema::create('gate_passes', function (Blueprint $table) {
            $table->id();
            $table->string('gate_pass_no')->unique();
            $table->unsignedBigInteger('order_id'); // BL ID
            $table->string('container_number');
            $table->string('shipper_name');
            $table->string('consignee_name');
            $table->text('checker_notes')->nullable(); // Notes like plate number, etc.
            $table->string('checker_name');
            $table->string('checker_signature')->nullable(); // Path to signature image or actual signature
            $table->string('receiver_name');
            $table->string('receiver_signature')->nullable(); // Path to signature image or actual signature
            $table->date('release_date');
            $table->unsignedBigInteger('created_by'); // User ID who created the gate pass
            $table->string('created_by_name'); // Name of user who created it
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_passes');
    }
};
