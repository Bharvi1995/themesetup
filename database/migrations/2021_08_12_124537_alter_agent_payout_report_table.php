<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAgentPayoutReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add indexing in agent_payout_report tbl
        $sql = "ALTER TABLE `agent_payout_reports` ADD INDEX (`agent_id`), ADD INDEX (`user_id`), ADD INDEX (`start_date`), ADD INDEX (`end_date`), ADD INDEX (`is_paid`)";
        \DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
