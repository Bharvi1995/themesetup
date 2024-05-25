<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankIdToMiddetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('middetails', function (Blueprint $table) {
            $table->integer('bank_id')->after('farma_mid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('middetails', function (Blueprint $table) {
            $table->dropColumn(['bank_id']);
        });
    }
}
