<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonToAgreementDocumentUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreement_document_upload', function (Blueprint $table) {
            $table->string('reassign_reason')->after('sent_files')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreement_document_upload', function (Blueprint $table) {
            $table->dropColumn(['reassign_reason']);
        });
    }
}
