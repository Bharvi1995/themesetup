<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTxTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tx_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("user_id");
            $table->string("company_name");
            $table->string("currency",15);
            $table->bigInteger("agent_id");
            $table->string("payment_gateway_id",15);
            $table->string("status",15);
            $table->double('TXs', 8, 2);
            $table->double('TXsP', 8, 2);
            $table->double('VOLs', 8, 2);
            $table->double('TXd', 8, 2);
            $table->double('TXdP', 8, 2);
            $table->double('VOLd', 8, 2);
            $table->double('CBTX', 8, 2);
            $table->double('CBTXP', 8, 2);
            $table->double('CBV', 8, 2);
            $table->double('REFTX', 8, 2);
            $table->double('REFTXP', 8, 2);
            $table->double('REFV', 8, 2);
            $table->double('FLGTX', 8, 2);
            $table->double('FLGTXP', 8, 2);
            $table->double('FLGV', 8, 2);
            $table->double('RETTX', 8, 2);
            $table->double('RETTXP', 8, 2);
            $table->double('RETV', 8, 2);
            $table->double('TXb', 8, 2);
            $table->double('TXbP', 8, 2);
            $table->double('VOLb', 8, 2);
            $table->date("transaction_date");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tx_transactions');
    }
}
