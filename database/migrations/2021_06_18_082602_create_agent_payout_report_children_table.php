<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentPayoutReportChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_payout_report_children', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('report_id')->nullable();
            $table->string('currency')->nullable();
            $table->double('success_amount', 10, 2)->nullable();
            $table->double('success_count', 10, 2)->nullable();
            $table->double('commission_percentage', 10, 2)->nullable();
            $table->double('total_commission', 10, 2)->nullable();
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
        Schema::dropIfExists('agent_payout_report_children');
    }
}
