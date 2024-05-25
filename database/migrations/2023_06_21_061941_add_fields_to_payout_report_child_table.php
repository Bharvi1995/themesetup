<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPayoutReportChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payout_report_child', function (Blueprint $table) {
            $table->integer('apm_id')->after('user_id')->nullable();
            $table->decimal('apm_mdr', 5, 2)->after('mdr')->nullable();
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
            $table->dropColumn(['apm_id', 'apm_mdr']);
        });
    }
}
