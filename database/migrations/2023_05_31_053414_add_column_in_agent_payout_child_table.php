<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInAgentPayoutChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_payout_report_children', function (Blueprint $table) {
            $table->decimal("success_amount_in_usd")->default(0.00)->after("success_count");
            $table->decimal("total_commission_in_usd")->default(0.00)->after("total_commission");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_payout_report_children', function (Blueprint $table) {
            $table->dropColumn(["success_amount_in_usd", "total_commission_in_usd"]);
        });
    }
}
