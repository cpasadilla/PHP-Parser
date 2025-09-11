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
        Schema::create('crew_document_delete_logs', function (Blueprint $table) {
            $table->id();
            $table->string('document_id');
            $table->string('crew_id');
            $table->string('crew_name');
            $table->string('employee_id');
            $table->string('document_type');
            $table->string('document_name');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('status')->nullable();
            $table->string('deleted_by');
            $table->json('document_data'); // Store complete document data for restore
            $table->timestamp('restored_at')->nullable();
            $table->string('restored_by')->nullable();
            $table->unsignedBigInteger('restored_document_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_document_delete_logs');
    }
};
