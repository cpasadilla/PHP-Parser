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
            // Check if columns exist before dropping them
            if (Schema::hasColumn('crews', 'passport_number')) {
                $table->dropColumn('passport_number');
            }
            if (Schema::hasColumn('crews', 'visa_number')) {
                $table->dropColumn('visa_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crews', function (Blueprint $table) {
            // Add back the columns if rolling back
            $table->string('passport_number')->nullable()->after('seaman_book_number');
            $table->string('visa_number')->nullable()->after('passport_number');
        });
    }
};
