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
            $table->string('sss_number')->nullable()->after('emergency_contact_phone');
            $table->string('pagibig_number')->nullable()->after('sss_number');
            $table->string('philhealth_number')->nullable()->after('pagibig_number');
            $table->string('tin_number')->nullable()->after('philhealth_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crews', function (Blueprint $table) {
            $table->dropColumn(['sss_number', 'pagibig_number', 'philhealth_number', 'tin_number']);
        });
    }
};
