<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditAgentField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->enum('is_active', ['0', '1'])->default(1);
            $table->bigInteger("login_otp")->nullable()->after("is_active");
            $table->enum('is_delete',['1','0'])->default('0')->after("login_otp");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropColumn('login_otp');
            $table->dropColumn('is_delete');
        });
    }
}
