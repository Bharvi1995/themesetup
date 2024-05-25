<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_applications', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_id');
            $table->string('company_name',20)->nullable();
            $table->string('website_url',20)->nullable();
            $table->string('company_registered_number_year')->nullable();
            $table->json('authorised_individual')->nullable();
            $table->text('company_address')->nullable();
            $table->enum('is_license_applied',['0','1'])->default('0');
            $table->string('license_image')->nullable();
            $table->string('settlement_method_for_crypto')->nullable();
            $table->string('settlement_method_for_fiat')->nullable();
            $table->text('mcc_codes')->nullable();
            $table->text('descriptors')->nullable();
            $table->enum('status',['0', '1', '2', '3'])->default('0')->comment('0 = Pending, 1 = Approved, 2 = Rejected, 3 = Reassigned');
            $table->softDeletes();
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
        Schema::dropIfExists('bank_applications');
    }
}
