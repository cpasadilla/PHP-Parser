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
        Schema::table('order_update_logs', function (Blueprint $table) {
            $table->string('field_name')->nullable()->after('updated_by'); // The field that was updated
            $table->text('old_value')->nullable()->after('field_name'); // Previous value
            $table->text('new_value')->nullable()->after('old_value'); // New value
            $table->string('action_type')->default('update')->after('new_value'); // update, create, delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_update_logs', function (Blueprint $table) {
            $table->dropColumn(['field_name', 'old_value', 'new_value', 'action_type']);
        });
    }
};
