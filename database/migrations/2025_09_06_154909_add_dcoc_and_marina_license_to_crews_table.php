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
            $table->date('dcoc_expiry')->nullable()->after('medical_certificate');
            $table->date('marina_license_expiry')->nullable()->after('dcoc_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crews', function (Blueprint $table) {
            $table->dropColumn(['dcoc_expiry', 'marina_license_expiry']);
        });
    }
};
