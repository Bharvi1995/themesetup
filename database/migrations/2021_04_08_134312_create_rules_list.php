<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules_list', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("rulesId");
            $table->string("selector");
            $table->string("operator");
            $table->string("value_fields");
            $table->enum('status',['1','0'])->default('0');
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
        Schema::dropIfExists('rules_list');
    }
}
