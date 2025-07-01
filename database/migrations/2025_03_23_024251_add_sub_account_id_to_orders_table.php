<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.

    public function up() {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_account_id')->nullable()->after('id');
            $table->foreign('sub_account_id')->references('id')->on('sub_accounts')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['sub_account_id']);
            $table->dropColumn('sub_account_id');
        });
    }
        */
};
