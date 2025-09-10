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
        Schema::create('crew_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_id')->constrained('crews')->onDelete('cascade');
            $table->enum('leave_type', ['vacation', 'sick', 'emergency', 'maternity', 'paternity', 'bereavement', 'other']);
            $table->decimal('credits', 8, 2);
            $table->year('year');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_leaves');
    }
};
