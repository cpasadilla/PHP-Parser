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
        Schema::create('crew_embarkations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_id')->constrained()->onDelete('cascade');
            $table->foreignId('ship_id')->constrained()->onDelete('cascade');
            $table->date('embark_date');
            $table->date('disembark_date')->nullable();
            $table->string('embark_port')->nullable();
            $table->string('disembark_port')->nullable();
            $table->string('status')->default('active'); // active, completed
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['crew_id', 'status']);
            $table->index(['ship_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_embarkations');
    }
};
