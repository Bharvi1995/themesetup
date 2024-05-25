<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_apis', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('method')->nullable();
            $table->string('ip')->nullable();
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->string('message')->nullable();
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
        Schema::dropIfExists('payment_apis');
    }
}
