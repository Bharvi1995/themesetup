<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankIdToApplicationNoteBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_note_banks', function (Blueprint $table) {
            $table->integer('bank_id')->after('application_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_note_banks', function (Blueprint $table) {
            $table->dropColumn(['bank_id']);
        });
    }
}
