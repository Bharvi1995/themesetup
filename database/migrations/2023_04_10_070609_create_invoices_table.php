<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins');
            $table->foreignId('company_id')->constrained('applications');
            $table->string("invoice_no", 20);
            $table->decimal("amount_deducted_value", 10, 2);
            $table->string("usdt_erc")->nullable();
            $table->string("usdt_trc")->nullable();
            $table->string("btc_value")->nullable();
            $table->decimal("total_amount", 10, 2);
            $table->text("request_data");
            $table->string("invoice_url")->nullable();
            $table->integer("is_paid")->default('0');
            $table->string("agent_name")->nullable();
            $table->string("transaction_hash")->nullable()->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
