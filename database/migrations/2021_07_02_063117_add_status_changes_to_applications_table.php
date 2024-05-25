<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusChangesToApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            DB::statement("ALTER TABLE applications CHANGE status status ENUM('0','1','2','3','4','5','6','7','8','9') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '0 = Pending 1 = Completed 2 = Reassign 3 = Rejected 4 = Approved 5 = Agreement Send 6 = Agreement Received 7 = notInterested 8 = Terminated 9 = Decline';");
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
            //
        });
    }
}
