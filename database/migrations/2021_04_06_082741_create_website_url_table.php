<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsiteUrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_url', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('website_name',200)->nullable();
            $table->string('ip_address',200)->nullable();
            $table->enum('is_active',['0','1'])->default('0');
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
        Schema::dropIfExists('website_url');
    }
}
