<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the document_type enum to include 'id_picture'
        DB::statement("ALTER TABLE crew_documents MODIFY COLUMN document_type ENUM('seaman_book', 'passport', 'visa', 'medical_certificate', 'basic_safety_training', 'coc', 'stcw', 'contract', 'identification', 'tax_certificate', 'id_picture', 'other') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values (without id_picture)
        // First, update any id_picture entries to 'other'
        DB::table('crew_documents')
            ->where('document_type', 'id_picture')
            ->update(['document_type' => 'other']);
            
        DB::statement("ALTER TABLE crew_documents MODIFY COLUMN document_type ENUM('seaman_book', 'passport', 'visa', 'medical_certificate', 'basic_safety_training', 'coc', 'stcw', 'contract', 'identification', 'tax_certificate', 'other') NOT NULL");
    }
};
