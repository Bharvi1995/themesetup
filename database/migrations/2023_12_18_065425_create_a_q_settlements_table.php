<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAQSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aq_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId("middetail_id")->constrained()->onDelete('CASCADE');
            $table->timestamp("from_date");
            $table->timestamp("to_date");
            $table->string("txn_hash", 191)->unique();
            $table->timestamp("paid_date")->nullable();
            $table->string("payment_receipt", 400)->nullable();
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
        Schema::dropIfExists('a_q_settlements');
    }
}
