<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTxTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add indexing Tx Transactions tbl
        $sql = "ALTER TABLE `tx_transactions` ADD INDEX (`user_id`), ADD INDEX (`currency`), ADD INDEX (`agent_id`), ADD INDEX (`payment_gateway_id`), ADD INDEX (`status`), ADD INDEX (`transaction_date`), ADD INDEX (`created_at`)";
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
