<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailySettlementReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_settlement_report', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->double('totalSuccessAmount')->default(0);
            $table->double('totalSuccessCount')->default(0);
            $table->double('totalDeclinedAmount')->default(0);
            $table->double('totalDeclinedCount')->default(0);
            $table->double('chbtotalAmount')->default(0);
            $table->double('chbtotalCount')->default(0);
            $table->double('suspicioustotalAmount')->default(0);
            $table->double('suspicioustotalCount')->default(0);
            $table->double('refundtotalAmount')->default(0);
            $table->double('refundtotalCount')->default(0);
            $table->double('retreivaltotalAmount')->default(0);
            $table->double('retreivaltotalCount')->default(0);
            $table->double('prearbitrationtotalAmount')->default(0);
            $table->double('prearbitrationtotalCount')->default(0);
            $table->double('total_transactions')->default(0);
            $table->double('mdr_amount')->default(0);
            $table->double('transactionsfees')->default(0);
            $table->double('refund_fees')->default(0);
            $table->double('highrisk_fees')->default(0);
            $table->double('chb_fees')->default(0);
            $table->double('retreival_fees')->default(0);
            $table->double('reserve_amount')->default(0);
            $table->double('total_payable')->default(0);
            $table->double('gross_payable')->default(0);
            $table->double('net_payable')->default(0);
            $table->integer('paid')->default(0);
            $table->datetime('paid_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_settlement_report');
    }
}
