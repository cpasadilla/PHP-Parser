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
        Schema::create('daily_report_settings', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->enum('report_type', ['trading', 'shipping']);
            $table->string('dccr_number')->nullable();
            $table->decimal('add_collection', 10, 2)->nullable();
            $table->string('collected_by_name')->nullable();
            $table->string('collected_by_title')->nullable();
            $table->timestamps();
            
            // Unique constraint for report_date and report_type combination
            $table->unique(['report_date', 'report_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_settings');
    }
};
