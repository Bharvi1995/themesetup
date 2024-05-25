<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_activity', function (Blueprint $table) {
            $table->id();
            $table->string('subject',255);
            $table->text('query_request')->nullable();
            $table->string('query_type',255);
            $table->bigInteger('transaction_id')->nullable();
            $table->text('url');
            $table->string('method',255);
            $table->string('ip',255);
            $table->string('agent',255);
            $table->bigInteger('user_id');
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
        Schema::dropIfExists('log_activity');
    }
}
