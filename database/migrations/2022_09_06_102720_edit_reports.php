<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payout_reports', function (Blueprint $table) {
            $table->decimal('merchant_discount_rate_amex', 10, 2)->default(0)->nullable()->after("merchant_discount_rate_master");
            $table->decimal('merchant_discount_rate_discover', 10, 2)->default(0)->nullable()->after("merchant_discount_rate_amex");
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
