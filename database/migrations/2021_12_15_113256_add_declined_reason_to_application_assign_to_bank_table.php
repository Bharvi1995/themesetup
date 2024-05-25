<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeclinedReasonToApplicationAssignToBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_assign_to_bank', function (Blueprint $table) {
            $table->string('declined_reason')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_assign_to_bank', function (Blueprint $table) {
            $table->dropColumn(['declined_reason']);
        });
    }
}
