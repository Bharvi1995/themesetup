<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('processor_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('date')->nullable();
            $table->decimal('merchant_discount_rate', 10, 2)->default(0)->nullable();
            $table->decimal('rolling_reserve_paercentage', 10, 2)->default(0)->nullable();
            $table->decimal('transaction_fee_paercentage', 10, 2)->default(0)->nullable();
            $table->decimal('declined_fee_paercentage', 10, 2)->default(0)->nullable();
            $table->decimal('refund_fee_paercentage', 10, 2)->default(0)->nullable();
            $table->decimal('chargebacks_fee_paercentage', 10, 2)->default(0)->nullable();
            $table->decimal('flagged_fee_paercentage', 10, 2)->default(0)->nullable();
            $table->decimal('retrieval_fee_paercentage', 10, 2)->default(0)->nullable();
            $table->decimal('wire_fee', 10, 2)->default(0)->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('chargebacks_start_date')->nullable();
            $table->date('chargebacks_end_date')->nullable();
            $table->string('genereted_by')->nullable();
            $table->enum('is_pdf', ['0', '1'])->default(0);
            $table->enum('is_download', ['0', '1'])->default(0);
            $table->enum('is_excel', ['0', '1'])->default(0);
            $table->enum('status', ['0', '1'])->default(0);
            $table->text('files')->nullable();
            $table->enum('show_client_side', ['0', '1'])->default(0);
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
        Schema::dropIfExists('auto_reports');
    }
}
