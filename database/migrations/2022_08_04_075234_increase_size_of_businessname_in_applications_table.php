<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseSizeOfBusinessnameInApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `applications` CHANGE `business_name` `business_name` VARCHAR(150) DEFAULT NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `applications` CHANGE `business_name` `business_name` VARCHAR(20) DEFAULT NULL');
        });
    }
}
