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
        Schema::table('leave_applications', function (Blueprint $table) {
            // Check if the column is already a string (varchar)
            $columnType = DB::select("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'leave_applications' AND COLUMN_NAME = 'approved_by' AND TABLE_SCHEMA = DATABASE()");
            
            if (!empty($columnType) && $columnType[0]->DATA_TYPE === 'bigint') {
                // Only modify if it's currently a bigint (foreign key)
                
                // Get all foreign key constraints for this table
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                    WHERE TABLE_NAME = 'leave_applications' 
                    AND COLUMN_NAME = 'approved_by' 
                    AND CONSTRAINT_NAME != 'PRIMARY' 
                    AND TABLE_SCHEMA = DATABASE()
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                // Drop foreign key constraints if they exist
                foreach ($foreignKeys as $fk) {
                    try {
                        DB::statement("ALTER TABLE leave_applications DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                    } catch (Exception $e) {
                        // Continue if constraint doesn't exist
                    }
                }
                
                // Change the column type from bigint to varchar
                $table->string('approved_by')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            // Change back to foreign key
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->change();
        });
    }
};
