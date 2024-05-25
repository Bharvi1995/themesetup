<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code');
            $table->string('bank_name');
            $table->string('email');
            $table->string('password');
            $table->string('country')->nullable();
            $table->string('category_id')->nullable();
            $table->text('extra_email')->nullable();
            $table->string('excepted_country')->nullable();
            $table->enum('is_otp_required', ['0', '1'])->default('1');
            $table->string('otp')->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->string('token')->nullable();
            $table->string('remember_token')->nullable();
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
        Schema::dropIfExists('banks');
    }
}
