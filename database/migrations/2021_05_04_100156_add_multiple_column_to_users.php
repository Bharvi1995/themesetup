<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('agreement',['0','1'])->default('0')->after('main_user_id');
            $table->enum('transactions',['0','1'])->default('0')->after('agreement');
            $table->enum('reports',['0','1'])->default('0')->after('transactions');
            $table->enum('settings',['0','1'])->default('0')->after('reports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['agreement',  'transactions', 'reports','settings']);
        });
    }
}
