<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferredReplyInApplicationAssignToBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_assign_to_bank', function (Blueprint $table) {
            $table->text('referred_note_reply')->after('referred_note')->nullable();
            $table->text('extra_documents')->after('referred_note_reply')->nullable();
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
            $table->dropColumn('referred_note_reply');
            $table->dropColumn('extra_documents');
        });
    }
}
