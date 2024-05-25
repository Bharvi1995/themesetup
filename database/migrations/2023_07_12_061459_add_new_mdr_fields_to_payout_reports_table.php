<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewMdrFieldsToPayoutReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payout_reports', function (Blueprint $table) {
            $table->decimal('merchant_discount_rate_crypto', 10)->after('merchant_discount_rate_discover')->default(0.00);
            $table->decimal('merchant_discount_rate_upi', 10)->after('merchant_discount_rate_crypto')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payout_reports', function (Blueprint $table) {
            $table->dropColumn(['merchant_discount_rate_crypto', 'merchant_discount_rate_upi']);
        });
    }
}
