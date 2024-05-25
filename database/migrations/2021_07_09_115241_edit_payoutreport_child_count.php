<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditPayoutreportChildCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payout_report_child', function (Blueprint $table) {
            $table->decimal('return_fee_count', 10, 2)->default(0.00)->after("return_fee");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payout_report_child', function (Blueprint $table) {
            //
        });
    }
}
