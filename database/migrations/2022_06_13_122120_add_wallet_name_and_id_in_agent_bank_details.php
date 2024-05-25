<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletNameAndIdInAgentBankDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_bank_details', function (Blueprint $table) {
            $table->integer('wallet')->nullable()->afetr('account_number');
            $table->string('wallet_id')->nullable()->afetr('wallet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_bank_details', function (Blueprint $table) {
            $table->dropColumn('wallet');
            $table->dropColumn('wallet_id');
        });
    }
}
