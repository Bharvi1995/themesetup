<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiddetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('middetails', function (Blueprint $table) {
            $table->id();
            $table->string('mid_no',100);
            $table->string('bank_name',150);
            $table->string('gateway_table',255);
            $table->bigInteger('main_gateway_mid_id');
            $table->bigInteger('assign_gateway_mid');
            $table->enum('is_gateway_mid',['0','1'])->default('0');
            $table->string('converted_currency',20);
            $table->text('blocked_country');
            $table->string('per_transaction_limit',10);
            $table->string('per_day_limit',10);
            $table->string('farma_mid',10)->nullable();
            $table->enum('is_active',['0','1'])->default('1');
            $table->enum('is_delete',['0','1'])->default('0');
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
        Schema::dropIfExists('middetails');
    }
}
