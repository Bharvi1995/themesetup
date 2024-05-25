<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionHostedSession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_hosted_session', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('payment_gateway_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('gateway_id')->nullable();
            $table->longText('request_data')->nullable();
            $table->longText('response_data')->nullable();
            $table->decimal('amount', 10, 8)->nullable();
            $table->string('email')->nullable();
            $table->integer('is_completed')->default(0);
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
        Schema::dropIfExists('transaction_hosted_session');
    }
}
