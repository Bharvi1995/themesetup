<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletDetailInUserBankDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_bank_details', function (Blueprint $table) {
            $table->integer('wallet')->nullable()->after('additional_information');
            $table->string('wallet_id')->nullable()->after('additional_information');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_bank_details', function (Blueprint $table) {
            $table->dropColumn('wallet');
            $table->dropColumn('wallet_id');
        });
    }
}
