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
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Start ID from 0001
            $table->string('first_name')->nullable(); // For individuals
            $table->string('last_name')->nullable();  // For individuals
            $table->string('company_name')->nullable(); // For companies
            $table->enum('type', ['individual', 'company']);
            $table->tinyInteger('share_holder')->default(1); // 0 = Yes, 1 = No
            $table->string('email')->nullable();
           // $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // Set the starting value for the auto-increment ID to 0001
        DB::statement('ALTER TABLE customers AUTO_INCREMENT = 1001;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('roles');
    }
};
