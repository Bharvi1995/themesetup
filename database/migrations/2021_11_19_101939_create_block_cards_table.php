<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_cards', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->index();;
            $table->string('order_id',100)->index();;
            $table->string('country',3);
            $table->string('card_type',20);
            $table->decimal('amount',10,2);
            $table->decimal('amount_in_usd',10,2)->default('0.00');
            $table->string('currency',10);
            $table->text('card_no')->nullable();
            $table->integer('status')->default(0)->index();;
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->softDeletes()->index();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_cards');
    }
}
