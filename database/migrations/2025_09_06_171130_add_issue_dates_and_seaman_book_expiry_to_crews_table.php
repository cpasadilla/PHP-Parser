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
        Schema::table('crews', function (Blueprint $table) {
            // Add issue dates for all certificates
            $table->date('seaman_book_issue_date')->nullable()->after('seaman_book_number');
            $table->date('seaman_book_expiry_date')->nullable()->after('seaman_book_issue_date');
            $table->date('medical_certificate_issue_date')->nullable()->after('medical_certificate');
            $table->date('dcoc_issue_date')->nullable()->after('dcoc_number');
            $table->date('marina_license_issue_date')->nullable()->after('marina_license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crews', function (Blueprint $table) {
            $table->dropColumn([
                'seaman_book_issue_date',
                'seaman_book_expiry_date',
                'medical_certificate_issue_date',
                'dcoc_issue_date',
                'marina_license_issue_date'
            ]);
        });
    }
};
