<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('inventory_entries', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->date('date');
            $table->unsignedBigInteger('customer_id');
            $table->string('customer_type'); // 'main' or 'sub'
            $table->decimal('in', 10, 0)->nullable();
            $table->decimal('out', 10, 3)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('or_ar')->nullable();
            $table->string('dr_number')->nullable();
            $table->decimal('onsite_in', 10, 0)->nullable();
            $table->decimal('actual_out', 10, 3)->nullable();
            $table->decimal('onsite_balance', 10, 2)->nullable();
            $table->date('updated_onsite_date')->nullable();
            $table->date('onsite_date')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('inventory_entries');
    }
};
