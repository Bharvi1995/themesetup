<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
             $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('country_code')->nullable();
            $table->string('mobile_no',20)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->enum('is_otp_required', ['0', '1'])->default('1');
            $table->string('otp',64)->nullable();
            $table->string('token')->nullable();
            $table->rememberToken();
            $table->enum('is_delete', ['0', '1'])->default('0');
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
        Schema::dropIfExists('admins');
    }
}
