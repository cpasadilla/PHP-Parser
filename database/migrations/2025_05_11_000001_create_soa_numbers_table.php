<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('soa_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('ship');
            $table->string('voyage');
            $table->string('soa_number');
            $table->integer('year');
            $table->integer('sequence');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unique(['customer_id', 'ship', 'voyage']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('soa_numbers');
    }
};
