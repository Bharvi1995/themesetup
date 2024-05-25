<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInMiddetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('middetails', function (Blueprint $table) {
            $table->decimal("apm_mdr")->default(0.00)->after("descriptor");
            $table->tinyInteger("apm_type")->nullable()->after("apm_mdr")->comment("1=Card, 2=Bank ,3=Crypto ,4=UPI");
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
            $table->dropColumn(["apm_mdr", "apm_type"]);
        });
    }
}