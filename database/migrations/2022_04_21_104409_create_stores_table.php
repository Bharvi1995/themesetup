<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('template_id')->nullable();
            $table->string('slug', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('banner_image_1')->nullable();
            $table->text('banner_image_2')->nullable();
            $table->text('banner_image_3')->nullable();
            $table->text('contact_banner_image')->nullable();
            $table->text('about_banner_image')->nullable();
            $table->string('currency', 100)->nullable();
            $table->text('about_us')->nullable();
            $table->string('contact_us_email',100)->nullable();
            $table->text('contact_us_description')->nullable();
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
        Schema::dropIfExists('stores');
    }
}
