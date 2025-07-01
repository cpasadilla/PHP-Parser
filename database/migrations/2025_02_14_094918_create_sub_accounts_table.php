<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('sub_accounts', function (Blueprint $table) {
            $table->id(); // Start ID from 0001
            $table->unsignedBigInteger('customer_id'); // Must match 'id' in customers table
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('roles');
    }
};
