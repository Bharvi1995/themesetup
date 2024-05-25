<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnToSubUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_users', function (Blueprint $table) {
            $table->enum('agreement',['0','1'])->default('0');
            $table->enum('transactions',['0','1'])->default('0');
            $table->enum('reports',['0','1'])->default('0');
            $table->enum('settings',['0','1'])->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_users', function (Blueprint $table) {
            $table->dropColumn(['agreement',  'transactions', 'reports','settings']);
        });
    }
}
