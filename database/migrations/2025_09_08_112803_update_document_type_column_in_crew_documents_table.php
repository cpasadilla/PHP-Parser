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
        Schema::table('crew_documents', function (Blueprint $table) {
            // Change document_type from enum to string to accommodate new types like 'resume'
            $table->string('document_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crew_documents', function (Blueprint $table) {
            // Revert back to enum if needed (note: this may cause data loss if new types exist)
            $table->enum('document_type', [
                'seaman_book', 'passport', 'visa', 'medical_certificate', 
                'basic_safety_training', 'coc', 'stcw', 'contract', 
                'identification', 'tax_certificate', 'other'
            ])->change();
        });
    }
};
