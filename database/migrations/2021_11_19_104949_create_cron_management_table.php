<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_managements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cron_name', 100);
            $table->string('command', 100);
            $table->string('description')->nullable();
            $table->string('pid')->nullable();
            $table->dateTime('last_run_at');
            $table->tinyInteger('status')->default(1)->comment('1=Active, 2=Inactive');
            $table->tinyInteger('current_status')->default(1)->comment('1=Start, 2=Stop');
            $table->text('keywords')->nullable();
            $table->integer('days_check')->nullable()->default(7);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cron_managements');
    }
}
