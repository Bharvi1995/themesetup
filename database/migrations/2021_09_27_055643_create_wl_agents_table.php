<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWlAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wl_agents', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code')->nullable();
            $table->string('name',255);
            $table->string('email',255);
            $table->string('password',255);
            $table->string('token',255)->nullable();
            $table->decimal('commission',6,2)->default(0.00);
            $table->enum('agreement_status', ['0', '1', '2', '3'])->default('0')->comment('0 = Pending, 1 = Sent, 2 = Received, 3 = re-assign');
            $table->enum('is_otp_required',['0','1'])->default('0');
            $table->string('otp',50)->nullable();
            $table->enum('is_active', ['0', '1'])->default(1);
            $table->string('remember_token',200)->nullable();
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
        Schema::dropIfExists('wl_agents');
    }
}
