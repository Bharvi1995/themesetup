<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('country_code',20)->nullable();
            $table->string('mobile_no',20)->nullable();
            $table->string('password')->nullable();
            $table->string('user_id')->nullable();
            $table->enum('is_active',['0','1'])->default('1');
            $table->enum('is_delete',['0','1'])->default('0');
            $table->string('token')->nullable();
            $table->enum('is_otp_required',['0','1'])->default('1');
            $table->string('otp')->nullable();
            $table->string('remember_token')->nullable();
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
        Schema::dropIfExists('sub_users');
    }
}
