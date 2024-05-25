<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Add indexing in transactions tbl
        $sql = "ALTER TABLE `transactions` ADD INDEX (`user_id`), ADD INDEX (`order_id`), ADD INDEX (`customer_order_id`), ADD INDEX (`payment_gateway_id`), ADD INDEX (`status`), ADD INDEX (`refund`), ADD INDEX (`chargebacks`), ADD INDEX (`is_flagged`), ADD INDEX (`is_retrieval`), ADD INDEX (`created_at`), ADD INDEX (`deleted_at`)";
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
