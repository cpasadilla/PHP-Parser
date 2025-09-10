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
        // Update any existing records with removed document types to 'other'
        DB::table('crew_documents')
            ->whereIn('document_type', ['passport', 'visa', 'stcw'])
            ->update([
                'document_type' => 'other',
                'notes' => DB::raw("CONCAT(COALESCE(notes, ''), IF(notes IS NULL OR notes = '', '', '\n'), 'Original type: ', document_type)")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be easily reversed since we don't store the original document types
        // The original document type information is preserved in the notes field
    }
};
