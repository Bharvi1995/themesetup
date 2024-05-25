<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRpApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rp_applications', function (Blueprint $table) {
            $table->id();
            $table->integer('agent_id');
            $table->string('company_name',20)->nullable();
            $table->string('website_url',20)->nullable();
            $table->string('company_registered_number_year')->nullable();
            $table->json('authorised_individual')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_email')->nullable();
            $table->integer('avg_no_of_app')->nullable();
            $table->integer('commited_avg_volume_per_month')->nullable();
            $table->text('industries_reffered')->nullable();
            $table->string('payment_solutions_needed')->nullable();
            $table->string('major_regious')->nullable();
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
        Schema::dropIfExists('rp_applications');
    }
}
