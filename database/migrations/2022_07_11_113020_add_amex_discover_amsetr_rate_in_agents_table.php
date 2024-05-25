<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmexDiscoverAmsetrRateInAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->double('add_buy_rate_master')->nullable()->after('add_buy_rate');
            $table->double('add_buy_rate_amex')->nullable()->after('add_buy_rate_master');
            $table->double('add_buy_rate_discover')->nullable()->after('add_buy_rate_amex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('add_buy_rate_master');
            $table->dropColumn('add_buy_rate_amex');
            $table->dropColumn('add_buy_rate_discover');
        });
    }
}
