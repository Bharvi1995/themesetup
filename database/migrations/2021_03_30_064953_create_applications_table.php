<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();            
            $table->string('business_type')->nullable();
            $table->string('business_category')->nullable();
            $table->enum('accept_card', ['Yes', 'No'])->default('Yes');
            $table->string('business_name',20)->nullable();
            $table->string('website_url',20)->nullable();
            $table->string('business_contact_first_name')->nullable();
            $table->string('business_contact_last_name')->nullable();
            $table->string('business_address1')->nullable();
            $table->string('business_address2')->nullable();
            $table->string('country')->nullable();
            $table->string('customer_location')->nullable();
            $table->string('processing_currency')->nullable();
            $table->string('settlement_currency')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('skype_id')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('technology_partner_id')->nullable();
            $table->string('processing_country')->nullable();
            $table->enum('company_license',['0','1','2'])->default('0');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('licence_number')->nullable();
            $table->text('passport')->nullable();
            $table->text('company_incorporation_certificate')->nullable();
            $table->text('latest_bank_account_statement')->nullable();
            $table->text('utility_bill')->nullable();
            $table->text('previous_processing_statement')->nullable();
            $table->text('owner_personal_bank_statement')->nullable();
            $table->enum('is_delete', ['0', '1'])->default('0');
            $table->enum('is_completed', ['0', '1'])->default('0');
            $table->enum('is_reassign', ['0', '1'])->default('0');
            $table->string('reason_reassign')->nullable();
            $table->enum('is_processing', ['0', '1'])->default('0');
            $table->enum('is_placed', ['0', '1'])->default('0');
            $table->enum('is_agreement', ['0', '1'])->default('0');
            $table->enum('is_not_interested', ['0', '1'])->default('0');
            $table->enum('is_terminated', ['0', '1'])->default('0');
            $table->enum('is_reject', ['0', '1'])->default('0');
            $table->rememberToken();
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
        Schema::dropIfExists('applications');
    }
}
