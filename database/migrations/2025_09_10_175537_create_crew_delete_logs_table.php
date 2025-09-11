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
        Schema::create('crew_delete_logs', function (Blueprint $table) {
            $table->id();
            $table->string('crew_id');
            $table->string('employee_id');
            $table->string('full_name');
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('ship_name')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('deleted_by');
            $table->json('crew_data'); // Store complete crew data for restore
            $table->json('documents_data')->nullable(); // Store documents data for restore
            $table->json('leaves_data')->nullable(); // Store leave credits data for restore
            $table->timestamp('restored_at')->nullable();
            $table->string('restored_by')->nullable();
            $table->unsignedBigInteger('restored_crew_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_delete_logs');
    }
};
