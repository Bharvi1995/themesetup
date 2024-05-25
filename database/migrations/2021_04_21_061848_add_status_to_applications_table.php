<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status',['0', '1', '2', '3', '4', '5', '6', '7', '8'])->default('0')->comment('0 = Pending, 1 = Completed, 2 = Reassign, 3 = Rejected, 4 = Approved, 5 = Agreement Send, 6 = Agreement Received, 7 = notInterested, 8 = Terminated')->after('extra_document');
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
            $table->dropColumn('status');
        });
    }
}
