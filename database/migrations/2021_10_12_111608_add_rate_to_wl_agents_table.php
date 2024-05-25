<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateToWlAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wl_agents', function (Blueprint $table) {
            $table->decimal('discount_rate', 10, 2)->after('remember_token')->default('0.00')->nullable();
            $table->decimal('discount_rate_master_card', 10, 2)->after('discount_rate')->default('0.00')->nullable();
            $table->decimal('setup_fee', 10, 2)->after('discount_rate_master_card')->default('0.00')->nullable();
            $table->decimal('setup_fee_master_card', 10, 2)->after('setup_fee')->default('0.00')->nullable();
            $table->decimal('rolling_reserve_paercentage', 10, 2)->after('setup_fee_master_card')->default('0.00')->nullable();
            $table->decimal('transaction_fee', 10, 2)->after('rolling_reserve_paercentage')->default('0.00')->nullable();
            $table->decimal('refund_fee', 10, 2)->after('transaction_fee')->default('0.00')->nullable();
            $table->decimal('chargeback_fee', 10, 2)->after('refund_fee')->default('0.00')->nullable();
            $table->decimal('flagged_fee', 10, 2)->after('chargeback_fee')->default('0.00')->nullable();
            $table->decimal('retrieval_fee', 10, 2)->after('flagged_fee')->default('0.00')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wl_agents', function (Blueprint $table) {
            $table->dropColumn([
                'discount_rate','discount_rate_master_card',
                'setup_fee','setup_fee_master_card',
                'rolling_reserve_paercentage','transaction_fee',
                'refund_fee','chargeback_fee',
                'flagged_fee','retrieval_fee'
                ]);
        });
    }
}
