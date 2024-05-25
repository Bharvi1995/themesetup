<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToPaymentApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_apis', function (Blueprint $table) {
            $table->string('order_id')->after('user_id')->nullable();
            $table->string('session_id')->after('order_id')->nullable();
            $table->string('email')->after('session_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_apis', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'session_id', 'email']);
        });
    }
}
