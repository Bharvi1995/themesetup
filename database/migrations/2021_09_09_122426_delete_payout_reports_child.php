<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeletePayoutReportsChild extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payout_report_child', function (Blueprint $table) {
            $table->dropColumn('approve_transaction_sum_master');
            $table->dropColumn('approve_transaction_count_master');
            $table->dropColumn('declined_transaction_sum_master');
            $table->dropColumn('declined_transaction_count_master');
            $table->dropColumn('mdr_master');
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
            $table->decimal('approve_transaction_sum_master', 10, 2)->default(0.00)->after("approve_transaction_sum");
            $table->decimal('approve_transaction_count_master', 10, 2)->default(0.00)->after("approve_transaction_sum_master");
            $table->decimal('declined_transaction_sum_master', 10, 2)->default(0.00)->after("declined_transaction_sum");
            $table->decimal('declined_transaction_count_master', 10, 2)->default(0.00)->after("declined_transaction_sum_master");
            $table->decimal('mdr_master', 10, 2)->default(0.00)->after("mdr");
        });
    }
}
