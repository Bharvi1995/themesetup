<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('store_id');
            $table->integer('product_id');
            $table->string('transaction_order_id')->nullable();
            $table->string('email', 100)->nullable();
            $table->double('amount')->nullable();
            $table->string('currency', 3)->nullable();
            $table->integer('quantity')->nullable();
            $table->double('total_amount')->nullable();
            $table->string('token');
            $table->softDeletes();
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
        Schema::dropIfExists('orders');
    }
}
