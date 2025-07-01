<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up()
    {
        Schema::table('sub_accounts', function (Blueprint $table) {
            $table->string('sub_account_number')->after('customer_id');
        });
    }

    public function down()
    {
        Schema::table('sub_accounts', function (Blueprint $table) {
            $table->dropColumn('sub_account_number');
        });
    }

};
