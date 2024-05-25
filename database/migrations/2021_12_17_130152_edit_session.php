<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditSession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_session', function (Blueprint $table) {
            $table->enum('is_checkout',['0','1'])->default("0")->after("is_completed");
            $table->enum('is_card',['0','1'])->default("0")->after("is_checkout");
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
            //
        });
    }
}
