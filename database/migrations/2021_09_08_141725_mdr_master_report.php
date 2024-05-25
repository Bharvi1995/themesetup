<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MdrMasterReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payout_reports', function (Blueprint $table) {
            $table->decimal('merchant_discount_rate_master', 10, 2)->default(0.00)->after("merchant_discount_rate");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payout_reports', function (Blueprint $table) {
            //
        });
    }
}
