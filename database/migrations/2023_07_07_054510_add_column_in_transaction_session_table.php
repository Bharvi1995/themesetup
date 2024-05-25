<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInTransactionSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_session', function (Blueprint $table) {
            $table->json("mid_payload")->after("request_data")->nullable();
            $table->json("webhook_response")->after("response_data")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_session', function (Blueprint $table) {
            $table->dropColumn(["mid_payload", "webhook_response"]);
        });
    }
}