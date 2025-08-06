<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            if (!Schema::hasColumn('parcels', 'documents')) {
                $table->string('documents')->nullable()->after('weight');
            }
            if (!Schema::hasColumn('parcels', 'key')) {
                $table->string('key')->nullable()->after('documents');
            }
            if (!Schema::hasColumn('parcels', 'date')) {
                $table->timestamp('date')->nullable()->after('key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn(['documents', 'key', 'date']);
        });
    }
};
