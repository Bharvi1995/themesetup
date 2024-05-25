<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcceptedIndustryColumnInMiddetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('middetails', function (Blueprint $table) {
            $table->json("accepted_industries")->after("apm_type")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('middetails', function (Blueprint $table) {
            $table->dropColumn(["accepted_industries"]);
        });
    }
}