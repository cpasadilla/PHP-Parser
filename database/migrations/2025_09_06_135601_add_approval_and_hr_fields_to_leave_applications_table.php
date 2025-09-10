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
        Schema::table('leave_applications', function (Blueprint $table) {
            // Add other_leave_type field for when leave_type is 'other'
            if (!Schema::hasColumn('leave_applications', 'other_leave_type')) {
                $table->string('other_leave_type')->nullable()->after('leave_type');
            }
            
            // Approval fields - Note: approved_by already exists as foreign key in original migration
            // We'll modify the existing approved_by column to string type instead of adding new one
            if (!Schema::hasColumn('leave_applications', 'noted_by_captain')) {
                $table->string('noted_by_captain')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('leave_applications', 'noted_by_manager')) {
                $table->string('noted_by_manager')->nullable()->after('noted_by_captain');
            }
            
            // HR Department fields
            if (!Schema::hasColumn('leave_applications', 'hr_vacation_credits')) {
                $table->integer('hr_vacation_credits')->nullable()->after('noted_by_manager');
            }
            if (!Schema::hasColumn('leave_applications', 'hr_sick_credits')) {
                $table->integer('hr_sick_credits')->nullable()->after('hr_vacation_credits');
            }
            if (!Schema::hasColumn('leave_applications', 'hr_filled_by')) {
                $table->string('hr_filled_by')->nullable()->after('hr_sick_credits');
            }
            if (!Schema::hasColumn('leave_applications', 'hr_title')) {
                $table->string('hr_title')->nullable()->after('hr_filled_by');
            }
            
            // Operations Manager fields
            if (!Schema::hasColumn('leave_applications', 'approved_days_with_pay')) {
                $table->integer('approved_days_with_pay')->nullable()->after('hr_title');
            }
            if (!Schema::hasColumn('leave_applications', 'approved_days_without_pay')) {
                $table->integer('approved_days_without_pay')->nullable()->after('approved_days_with_pay');
            }
            if (!Schema::hasColumn('leave_applications', 'disapproved_reason')) {
                $table->text('disapproved_reason')->nullable()->after('approved_days_without_pay');
            }
            if (!Schema::hasColumn('leave_applications', 'deferred_until')) {
                $table->date('deferred_until')->nullable()->after('disapproved_reason');
            }
            if (!Schema::hasColumn('leave_applications', 'ops_approved_by')) {
                $table->string('ops_approved_by')->nullable()->after('deferred_until');
            }
            if (!Schema::hasColumn('leave_applications', 'ops_title')) {
                $table->string('ops_title')->nullable()->after('ops_approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            // Drop columns that were added by this migration
            // Note: We don't drop 'approved_by' as it was part of the original table creation
            $columnsToCheck = [
                'other_leave_type',
                'noted_by_captain',
                'noted_by_manager',
                'hr_vacation_credits',
                'hr_sick_credits',
                'hr_filled_by',
                'hr_title',
                'approved_days_with_pay',
                'approved_days_without_pay',
                'disapproved_reason',
                'deferred_until',
                'ops_approved_by',
                'ops_title'
            ];
            
            $columnsToDrop = [];
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('leave_applications', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
