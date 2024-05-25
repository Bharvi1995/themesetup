<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
                $table->id();
            $table->integer('user_id')->default(0);
            $table->string('order_id',100);
            $table->string('session_id',100)->default();
            $table->string('gateway_id',100)->default();
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('address');
            $table->string('customer_order_id',100)->nullable();
            $table->string('country',3);
            $table->string('state',30);
            $table->string('city',30);
            $table->string('zip',20);
            $table->string('ip_address',20)->nullable();
            $table->string('email',80);
            $table->string('phone_no',20);
            $table->string('card_type',20);
            $table->decimal('amount', 10, 2);
            $table->string('currency',10);
            $table->text('card_no')->nullable();
            $table->text('ccExpiryMonth')->nullable();
            $table->text('ccExpiryYear')->nullable();
            $table->text('cvvNumber')->nullable();
            $table->enum('status',['0','1','2','3','4'])->default('0');
            $table->text('reason')->nullable();
            $table->string('descriptor',200)->nullable();
            $table->string('payment_gateway_id',20)->default(0);
            $table->decimal('merchant_discount_rate', 10, 2)->nullable()->default('0');
            $table->decimal('bank_discount_rate', 10, 2)->nullable()->default('0');
            $table->decimal('net_profit_amount', 10, 2)->nullable()->default('0');
            $table->enum('chargebacks',['0','1'])->default('0');
            $table->timestamp('chargebacks_date')->nullable();
            $table->enum('chargebacks_remove',['0','1'])->default('0');
            $table->timestamp('chargebacks_remove_date')->nullable();
            $table->enum('refund',['0','1'])->default('0');
            $table->text('refund_reason')->nullable();
            $table->timestamp('refund_date')->nullable();
            $table->enum('refund_remove',['0','1'])->default('0');
            $table->timestamp('refund_remove_date')->nullable();
            $table->enum('is_flagged',['0','1'])->default(0);
            $table->string('flagged_by')->nullable();
            $table->timestamp('flagged_date')->nullable();
            $table->enum('is_flagged_remove',['0','1'])->default(0);
            $table->timestamp('flagged_remove_date')->nullable();
            $table->enum('is_retrieval',['0','1'])->default(0);
            $table->timestamp('retrieval_date')->nullable();
            $table->enum('is_retrieval_remove',['0','1'])->default(0);
            $table->timestamp('retrieval_remove_date')->nullable();
            $table->enum('is_converted',['0','1'])->default('0');
            $table->decimal('converted_amount',10,2)->default(0.00);
            $table->string('converted_currency',20)->nullable();
            $table->enum('is_converted_user_currency',['0','1'])->default('0');
            $table->decimal('converted_user_amount',10,2)->default(0.00);
            $table->string('converted_user_currency',20)->nullable();
            $table->string('request_from_ip')->nullable();
            $table->string('request_origin')->nullable();
            $table->string('is_request_from_vt', 20)->nullable();
            $table->enum('is_transaction_type',['CARD','BANK','CRYPTO'])->nullable();
            $table->integer('is_webhook')->nullable()->default('0');
            $table->text('response_url', 1000)->nullable();
            $table->text('webhook_url', 1000)->nullable();
            $table->string('webhook_status', 255)->nullable();
            $table->integer('webhook_retry')->nullable()->default(0);
            $table->string('transactions_token', 200)->nullable();
            $table->text('bin_details')->nullable();
            $table->text('transaction_hash')->nullable();
            $table->enum('is_delete', ['0', '1'])->default(0);
            $table->enum('is_duplicate_delete', ['0', '1'])->default(0);
            $table->timestamp('transaction_date', 0)->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
