<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputDetailsToTransactionSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_session', function (Blueprint $table) {
            $table->longText('input_details')->nullable()->after('request_data');
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
            $table->dropColumn(['input_details']);
        });
    }
}
