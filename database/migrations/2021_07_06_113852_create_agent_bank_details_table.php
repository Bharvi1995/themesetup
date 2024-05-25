<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentBankDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_bank_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('agent_id');
            $table->string('name', 255)->nullable();
            $table->text('address')->nullable();
            $table->text('aba_routing')->nullable();
            $table->text('swift_code')->nullable();
            $table->text('iban')->nullable();
            $table->text('account_name')->nullable();
            $table->text('account_holder_address')->nullable();
            $table->bigInteger('account_number')->nullable();
            $table->longText('additional_information')->nullable();
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
        Schema::dropIfExists('agent_bank_details');
    }
}
