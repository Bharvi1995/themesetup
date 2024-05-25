<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMdrMsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('merchant_discount_rate_master_card', 10, 2)->after('merchant_discount_rate')->default(0)->nullable();
            $table->decimal('setup_fee_master_card', 10, 2)->after('setup_fee')->default(0)->nullable();
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
             $table->dropColumn(['merchant_discount_rate_master_card','setup_fee_master_card']);
        });
    }
}
