<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoReportChild extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_reports_child', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('autoreport_id');
            $table->string('currency')->nullable();
            $table->decimal('mdr', 10, 2)->default(0.00);
            $table->decimal('rolling_reserve', 10, 2)->default(0.00);
            $table->decimal('total_transaction_count', 10, 2)->default(0.00);
            $table->string('total_transaction_sum')->nullable();
            $table->decimal('approve_transaction_count', 10, 2)->default(0.00);
            $table->string('approve_transaction_sum')->nullable();
            $table->decimal('declined_transaction_count', 10, 2)->default(0.00);
            $table->string('declined_transaction_sum')->nullable();
            $table->decimal('refund_transaction_count', 10, 2)->default(0.00);
            $table->string('refund_transaction_sum')->nullable();
            $table->decimal('chargeback_transaction_count', 10, 2)->default(0.00);
            $table->string('chargeback_transaction_sum')->nullable();
            $table->decimal('flagged_transaction_count', 10, 2)->default(0.00);
            $table->string('flagged_transaction_sum')->nullable();
            $table->decimal('retrieval_transaction_count', 10, 2)->default(0.00);
            $table->string('retrieval_transaction_sum')->nullable();
            $table->decimal('transaction_fee', 10, 2)->default(0.00);
            $table->decimal('refund_fee', 10, 2)->default(0.00);
            $table->decimal('chargeback_fee', 10, 2)->default(0.00);
            $table->decimal('flagged_fee', 10, 2)->default(0.00);
            $table->decimal('retrieval_fee', 10, 2)->default(0.00);
            $table->integer('remove_past_flagged')->default(0);
            $table->decimal('past_flagged_charge_amount', 10, 2)->default(0.00);
            $table->string('past_flagged_sum')->nullable();
            $table->integer('remove_past_chargebacks')->default(0);
            $table->decimal('past_chargebacks_charge_amount', 10, 2)->default(0.00);
            $table->string('past_chargebacks_sum')->nullable();
            $table->integer('remove_past_retrieval')->default(0);
            $table->decimal('past_retrieval_charge_amount', 10, 2)->default(0.00);
            $table->string('past_retrieval_sum')->nullable();
            $table->decimal('sub_total', 10, 2)->default(0.00);
            $table->string('net_settlement_amount')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('auto_reports_child');
    }
}
