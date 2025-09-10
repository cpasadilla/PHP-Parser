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
        Schema::create('crew_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_id')->constrained('crews')->onDelete('cascade');
            $table->enum('document_type', [
                'seaman_book', 'passport', 'visa', 'medical_certificate', 
                'basic_safety_training', 'coc', 'stcw', 'contract', 
                'identification', 'tax_certificate', 'other'
            ]);
            $table->string('document_name');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_documents');
    }
};
