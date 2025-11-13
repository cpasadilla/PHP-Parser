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
        Schema::table('gate_passes', function (Blueprint $table) {
            // Drop the unique constraint on gate_pass_no
            $table->dropUnique(['gate_pass_no']);
            
            // Make checker_name and receiver_name nullable
            $table->string('checker_name')->nullable()->change();
            $table->string('receiver_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            // Re-add the unique constraint
            $table->unique('gate_pass_no');
            
            // Make checker_name and receiver_name required again
            $table->string('checker_name')->nullable(false)->change();
            $table->string('receiver_name')->nullable(false)->change();
        });
    }
};
