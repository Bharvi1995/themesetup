<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewStatusReferredInApplicationAssignToBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_assign_to_bank', function (Blueprint $table) {
            $table->string('referred_note')->nullable()->after('declined_reason');
            DB::statement("ALTER TABLE application_assign_to_bank MODIFY COLUMN status ENUM('0','1','2','3') COMMENT '0=Pending,1=Approved,2=Declined,3=Referred'");
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
            $table->dropColumn('referred_note');
            DB::statement("ALTER TABLE application_assign_to_bank MODIFY COLUMN status ENUM('0','1','2')");
        });
    }
}
