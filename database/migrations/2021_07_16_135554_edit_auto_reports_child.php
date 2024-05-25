<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditAutoReportsChild extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_reports_child', function (Blueprint $table) {
            $table->decimal('past_flagged_fee', 10, 2)->default(0.00)->after("past_retrieval_sum");
            $table->decimal('transactions_fee_total', 10, 2)->default(0.00)->after("past_flagged_fee");
            $table->decimal('return_fee', 10, 2)->default(0.00)->after("transactions_fee_total");
            $table->decimal('return_fee_count', 10, 2)->default(0.00)->after("return_fee");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_reports_child', function (Blueprint $table) {
            //
        });
    }
}
