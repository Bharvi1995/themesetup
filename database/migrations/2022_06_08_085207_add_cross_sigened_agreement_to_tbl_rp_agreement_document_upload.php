<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrossSigenedAgreementToTblRpAgreementDocumentUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rp_agreement_document_upload', function (Blueprint $table) {
            $table->string('cross_signed_agreement')->after('sent_files')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rp_agreement_document_upload', function (Blueprint $table) {
            $table->dropColumn(['cross_signed_agreement']);
        });
    }
}
