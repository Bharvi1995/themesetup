<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title',255);
            $table->text('body');
            $table->text('files');
            $table->integer('user_id');
            $table->enum('department',['1', '2', '3'])->default('1');
            $table->enum('status',['0', '1', '2', '3', '4'])->default('0')->comment('0 = panding, 1 = done, 2 = re-assign, 3 = close');
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
        Schema::dropIfExists('tickets');
    }
}
