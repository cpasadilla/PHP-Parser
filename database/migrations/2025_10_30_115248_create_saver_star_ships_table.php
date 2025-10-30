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
        Schema::create('saver_star_ships', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('M/V Saver Star'); // Ship name
            $table->string('status')->default('READY'); // Ship status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saver_star_ships');
    }
};
