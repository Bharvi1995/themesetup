<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexInTransactionSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_session', function (Blueprint $table) {
            $table->index("order_id");
            $table->index("transaction_id");
            $table->index("gateway_id");
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
            $table->dropIndex(["order_id"]);
            $table->dropIndex(["gateway_id"]);
            $table->dropIndex(["transaction_id"]);
        });
    }
}
