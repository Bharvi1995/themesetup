<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeInMiddetails extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE middetails MODIFY mid_type ENUM('1', '2', '3' ,'4' ,'5') DEFAULT '1'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE middetails MODIFY mid_type ENUM('1','2','3','4') DEFAULT '1'");
    }
}