<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectReasonInBankApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_applications', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('status');
            $table->text('reassign_reason')->nullable()->after('reject_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_applications', function (Blueprint $table) {
            $table->dropColumn('reject_reason');
            $table->dropColumn('reassign_reason');
        });
    }
}
