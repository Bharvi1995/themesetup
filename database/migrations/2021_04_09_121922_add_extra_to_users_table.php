<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('merchant_discount_rate', 10, 2)->after('annual_fee')->default(0)->nullable();
            $table->decimal('rolling_reserve_paercentage', 10, 2)->after('merchant_discount_rate')->default(0)->nullable();
            $table->string("additional_mail",255)->after("additional_merchant_transaction_notification")->nullable();
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
            $table->dropColumn(['additional_mail','rolling_reserve_paercentage','merchant_discount_rate']);
        });
    }
}
