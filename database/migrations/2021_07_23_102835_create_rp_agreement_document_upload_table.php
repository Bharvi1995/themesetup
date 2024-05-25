<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRpAgreementDocumentUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rp_agreement_document_upload', function (Blueprint $table) {
            $table->id();
            $table->integer('rp_id');
            $table->string('token')->nullable();
            $table->string('files')->nullable();
            $table->string('sent_files')->nullable();
            $table->string('reassign_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rp_agreement_document_upload');
    }
}
