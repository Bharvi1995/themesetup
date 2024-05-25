<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerchantRateToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('merchant_discount_rate_amex_card', 10, 2)->after('merchant_discount_rate_master_card')->default(0);
            $table->decimal('merchant_discount_rate_discover_card', 10, 2)->after('merchant_discount_rate_amex_card')->default(0);
            $table->decimal('setup_fee_amex_card', 10, 2)->after('setup_fee_master_card')->default(0);
            $table->decimal('setup_fee_discover_card', 10, 2)->after('setup_fee_amex_card')->default(0);
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
                'merchant_discount_rate_amex_card',
                'merchant_discount_rate_discover_card',
                'setup_fee_amex_card',
                'setup_fee_discover_card'
            ]);
        });
    }
}
