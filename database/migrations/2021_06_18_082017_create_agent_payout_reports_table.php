<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentPayoutReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_payout_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_no')->nullable();
            $table->foreignId('agent_id');
            $table->string('agent_name')->nullable();
            $table->foreignId('user_id');
            $table->string('company_name')->nullable();
            $table->string('date')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->enum('is_pdf', ['0', '1'])->default(('0'));
            $table->enum('is_download', ['0', '1'])->default(('0'));
            $table->enum('is_excel', ['0', '1'])->default(('0'));
            $table->enum('is_paid', ['0', '1'])->default(('0'));
            $table->enum('show_agent_side', ['0', '1'])->default(('0'));
            $table->longText('files')->nullable();
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
        Schema::dropIfExists('agent_payout_reports');
    }
}
