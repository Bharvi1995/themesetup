<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimumSettlementAmountAndPaymentFrequencyToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('minimum_settlement_amount', 10, 2)->after('merchant_discount_rate_discover_card')->default(0);
            $table->integer('payment_frequency')->after('minimum_settlement_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'minimum_settlement_amount',
                'payment_frequency'
            ]);
        });
    }
}
