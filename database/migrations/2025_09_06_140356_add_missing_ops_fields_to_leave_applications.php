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
            // Check if columns don't exist before adding them
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
            $table->dropColumn(['ops_approved_by', 'ops_title']);
        });
    }
};
