<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentFilesToAgreementDocumentUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreement_document_upload', function (Blueprint $table) {
            $table->string('sent_files')->after('files')->nullable();
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
            $table->dropColumn(['sent_files']);
        });
    }
}
