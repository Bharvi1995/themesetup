<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMidTypeToMiddetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('middetails', function (Blueprint $table) {
            $table->enum('mid_type',['1','2','3'])->default('1')->after('mid_no');
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
            $table->dropColumn('mid_type');
        });
    }
}
