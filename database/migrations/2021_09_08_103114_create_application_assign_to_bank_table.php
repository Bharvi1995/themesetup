<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationAssignToBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_assign_to_bank', function (Blueprint $table) {
            $table->id();
            $table->string('application_id')->nullable();
            $table->string('bank_user_id')->nullable();
            $table->enum('status', ['0', '1','2'])->default('0');
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
        Schema::dropIfExists('application_assign_to_bank');
    }
}
