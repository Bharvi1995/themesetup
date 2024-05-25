<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToPayoutReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payout_reports', function (Blueprint $table) {
            $table->text('merchant_discount_rate_apm')->nullable()->after('merchant_discount_rate_discover');
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
            $table->dropColumn(['merchant_discount_rate_apm']);
        });
    }
}
