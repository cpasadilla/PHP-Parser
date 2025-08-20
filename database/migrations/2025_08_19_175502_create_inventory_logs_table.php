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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_entry_id')->nullable(); // Reference to inventory entry
            $table->string('action_type'); // 'create', 'update', 'delete'
            $table->string('field_name')->nullable(); // Field that was changed (for updates)
            $table->text('old_value')->nullable(); // Previous value
            $table->text('new_value')->nullable(); // New value
            $table->string('updated_by'); // User who made the change
            $table->json('entry_data')->nullable(); // Full entry data for create/delete operations
            $table->timestamps();
            
            $table->foreign('inventory_entry_id')->references('id')->on('inventory_entries')->onDelete('cascade');
            $table->index(['inventory_entry_id', 'action_type']);
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
