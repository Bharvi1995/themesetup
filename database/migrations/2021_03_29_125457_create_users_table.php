<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('country_code',20)->nullable();
            $table->string('mobile_no',20)->nullable();
            $table->string('password')->nullable();
            $table->string('mid')->nullable();
            $table->enum('is_test_mode', ['0', '1'])->default('0');
            $table->string('amexmid')->nullable();
            $table->string('visamid')->nullable();
            $table->string('mastercardmid')->nullable();
            $table->string('discovermid')->nullable();
            $table->string('api_key')->nullable();
            $table->string('callback_url')->nullable();
            $table->decimal('visa_credit',10,2)->default('0.00');
            $table->decimal('visa_debit',10,2)->default('0.00');
            $table->decimal('mastercard_debit',10,2)->default('0.00');
            $table->decimal('mastercard_credit',10,2)->default('0.00');
            $table->decimal('transaction_fee',10,2)->default('0.00');
            $table->decimal('setup_fee',10,2)->default('0.00');
            $table->decimal('refund_fee',10,2)->default('0.00');
            $table->decimal('flagged_fee',10,2)->default('0.00');
            $table->decimal('retrieval_fee',10,2)->default('0.00');
            $table->decimal('pci',10,2)->default('0.00');
            $table->decimal('chargeback_fee',10,2)->default('0.00');
            $table->decimal('annual_fee',10,2)->default('0.00');
            $table->decimal('fx_margin',10,2)->default('0.00');
            $table->string('currency')->nullable();
            $table->enum('is_active', ['0', '1'])->default('0');
            $table->enum('is_delete', ['0', '1'])->default('0');
            $table->enum('is_desable_vt', ['0', '1'])->default('0');
            $table->enum('is_ip_remove', ['0', '1'])->default('0');
            $table->enum('make_refund', ['0', '1'])->default('0');
            $table->string('token')->nullable();
            $table->enum('is_otp_required', ['0','1'])->default('1');
            $table->string('otp')->nullable();
            $table->bigInteger('one_day_card_limit')->default('3');
            $table->bigInteger('one_day_email_limit')->default('3');
            $table->bigInteger('one_week_card_limit')->default('3');
            $table->bigInteger('one_week_email_limit')->default('3');
            $table->bigInteger('one_month_card_limit')->default('6');
            $table->bigInteger('one_month_email_limit')->default('6');
            $table->enum('is_multi_mid', ['0', '1'])->default('0');
            $table->string('mid_list')->nullable();
            $table->decimal('per_transaction_limit',10,2)->default('3000.00');
            $table->enum('merchant_transaction_notification', ['0', '1'])->default('0');
            $table->enum('user_transaction_notification', ['0', '1'])->default('1');
            $table->string('additional_merchant_transaction_notification')->nullable();
            $table->string('logo')->nullable();
            $table->string('iframe_logo')->nullable();
            $table->enum('enable_product_dashboard',['yes','no'])->default('no');
            $table->string('platform')->nullable();
            $table->string('website_url')->nullable();
            $table->bigInteger('agent_id')->nullable();
            $table->decimal('agent_commission',10,2)->default('0.00');
            $table->string('crypto_api_id')->nullable();
            $table->string('category')->nullable();
            $table->string('remember_token')->nullable();
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
        Schema::dropIfExists('users');
    }
}
