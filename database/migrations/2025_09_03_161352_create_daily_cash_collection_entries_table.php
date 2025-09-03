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
        Schema::create('daily_cash_collection_entries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('trading'); // 'trading' or 'shipping'
            $table->date('entry_date');
            $table->string('ar')->nullable();
            $table->string('or')->nullable();
            $table->string('customer_name');
            $table->unsignedBigInteger('customer_id')->nullable();
            
            // Trading-specific fields
            $table->decimal('gravel_sand', 10, 2)->default(0);
            $table->decimal('chb', 10, 2)->default(0);
            $table->decimal('other_income_cement', 10, 2)->default(0);
            $table->decimal('other_income_df', 10, 2)->default(0);
            $table->decimal('others', 10, 2)->default(0);
            $table->decimal('interest', 10, 2)->default(0);
            
            // Shipping-specific fields
            $table->string('vessel')->nullable();
            $table->string('container_parcel')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            
            $table->decimal('total', 10, 2)->default(0);
            $table->text('remark')->nullable();
            $table->timestamps();
            
            // Add foreign key constraint for customer
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_cash_collection_entries');
    }
};
