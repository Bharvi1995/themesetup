<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['card_no', 'ccExpiryMonth', 'ccExpiryYear','cvvNumber','chargebacks','chargebacks_remove','refund','refund_remove','is_flagged','is_flagged_remove','is_retrieval','is_retrieval_remove','is_converted','is_converted_user_currency','is_duplicate_delete','is_pre_arbitration']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('card_no',40)->nullable();
            $table->string('ccExpiryMonth',3)->nullable();
            $table->string('ccExpiryYear',5)->nullable();
            $table->string('cvvNumber', 10)->nullable();
            $table->unsignedInteger('chargebacks')->default(0);
            $table->unsignedTinyInteger('chargebacks_remove')->default(0);
            $table->unsignedTinyInteger('refund')->default(0);
            $table->unsignedTinyInteger('refund_remove')->default(0);
            $table->unsignedTinyInteger('is_flagged')->default(0);
            $table->unsignedTinyInteger('is_flagged_remove')->default(0);
            $table->unsignedTinyInteger('is_retrieval')->default(0);
            $table->unsignedTinyInteger('is_retrieval_remove')->default(0);
            $table->unsignedTinyInteger('is_converted')->default(0);
            $table->unsignedTinyInteger('is_converted_user_currency')->default(0);
            $table->unsignedTinyInteger('is_duplicate_delete')->default(0);
            $table->unsignedTinyInteger('is_pre_arbitration')->default(0);
        });

        Schema::table('transaction_session', function (Blueprint $table) {
            $table->dropColumn(['is_checkout', 'is_card']);
        });

        Schema::table('transaction_session', function (Blueprint $table) {            
            $table->unsignedTinyInteger('is_checkout')->default(0);
            $table->unsignedTinyInteger('is_card')->default(0);            
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['agreement', 'transactions','reports','settings','application_show','is_test_mode','is_active','is_desable_vt','is_ip_remove','is_bin_remove','is_disable_rule','is_whitelable','make_refund','is_otp_required','is_multi_mid','merchant_transaction_notification','user_transaction_notification','is_rate_sent','agent_commission_master_card','is_white_label','is_login_wl_merchant']);
        });

        Schema::table('users', function (Blueprint $table) {            
            $table->unsignedTinyInteger('agreement')->default(0);
            $table->unsignedTinyInteger('transactions')->default(0);
            $table->unsignedTinyInteger('reports')->default(0);
            $table->unsignedTinyInteger('settings')->default(0);
            $table->unsignedTinyInteger('application_show')->default(0);
            $table->unsignedTinyInteger('is_test_mode')->default(0);
            $table->unsignedTinyInteger('is_active')->default(0);
            $table->unsignedTinyInteger('is_desable_vt')->default(0);
            $table->unsignedTinyInteger('is_ip_remove')->default(0);
            $table->unsignedTinyInteger('is_bin_remove')->default(0);
            $table->unsignedTinyInteger('is_disable_rule')->default(0);
            $table->unsignedTinyInteger('is_whitelable')->default(0);
            $table->unsignedTinyInteger('make_refund')->default(0);
            $table->unsignedTinyInteger('is_otp_required')->default(1);
            $table->unsignedTinyInteger('is_multi_mid')->default(0);
            $table->unsignedTinyInteger('merchant_transaction_notification')->default(0);
            $table->unsignedTinyInteger('user_transaction_notification')->default(0);
            $table->unsignedTinyInteger('is_rate_sent')->default(0);
            $table->unsignedTinyInteger('agent_commission_master_card')->default(0);
            $table->unsignedTinyInteger('is_white_label')->default(0);
            $table->unsignedTinyInteger('is_login_wl_merchant')->default(0);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['company_license', 'status','is_completed','is_reassign','is_processing','is_placed','is_agreement','is_not_interested','is_terminated','is_reject']);
        });

        Schema::table('applications', function (Blueprint $table) {            
            $table->unsignedTinyInteger('company_license')->default(0);
            $table->unsignedTinyInteger('status')->default(0)->comment('0 = Pending 1 = Completed 2 = Reassign 3 = Rejected 4 = Approved 5 = Agreement Send 6 = Agreement Received 7 = notInterested 8 = Terminated 9 = Decline 10 = Rate Accepted 11 = Signed Agreement 12=Save Draft');
            $table->unsignedTinyInteger('is_completed')->default(0);
            $table->unsignedTinyInteger('is_reassign')->default(0);
            $table->unsignedTinyInteger('is_processing')->default(0);
            $table->unsignedTinyInteger('is_placed')->default(0);
            $table->unsignedTinyInteger('is_agreement')->default(0);
            $table->unsignedTinyInteger('is_not_interested')->default(0);
            $table->unsignedTinyInteger('is_terminated')->default(0);
            $table->unsignedTinyInteger('is_reject')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['card_no', 'ccExpiryMonth', 'ccExpiryYear','cvvNumber','chargebacks','chargebacks_remove','refund','refund_remove','is_flagged','is_flagged_remove','is_retrieval','is_retrieval_remove','is_converted','is_converted_user_currency','is_duplicate_delete','is_pre_arbitration']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->text('card_no')->nullable();
            $table->text('ccExpiryMonth')->nullable();
            $table->text('ccExpiryYear')->nullable();
            $table->text('cvvNumber')->nullable();
            $table->enum('chargebacks',['0','1'])->default('0');
            $table->enum('chargebacks_remove',['0','1'])->default('0');
            $table->enum('refund',['0','1'])->default('0');
            $table->enum('refund_remove',['0','1'])->default('0');
            $table->enum('is_flagged',['0','1'])->default(0);
            $table->enum('is_flagged_remove',['0','1'])->default(0);
            $table->enum('is_retrieval',['0','1'])->default(0);
            $table->enum('is_retrieval_remove',['0','1'])->default(0);
            $table->enum('is_converted',['0','1'])->default('0');
            $table->enum('is_converted_user_currency',['0','1'])->default('0');
            $table->enum('is_duplicate_delete', ['0', '1'])->default(0);
            $table->enum('is_pre_arbitration', ['0', '1'])->default(0);
        });

        Schema::table('transaction_session', function (Blueprint $table) {
            $table->dropColumn(['is_checkout', 'is_card']);
        });

        Schema::table('transaction_session', function (Blueprint $table) {
            $table->enum('is_checkout', ['0', '1'])->default(0);
            $table->enum('is_card', ['0', '1'])->default(0);     
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['agreement', 'transactions','reports','settings','application_show','is_test_mode','is_active','is_desable_vt','is_ip_remove','is_bin_remove','is_disable_rule','is_whitelable','make_refund','is_otp_required','is_multi_mid','merchant_transaction_notification','user_transaction_notification','is_rate_sent','agent_commission_master_card','is_white_label','is_login_wl_merchant']);
        });

        Schema::table('users', function (Blueprint $table) {            
            $table->enum('agreement',['0', '1'])->default('0');
            $table->enum('transactions',['0', '1'])->default('0');
            $table->enum('reports',['0', '1'])->default('0');
            $table->enum('settings',['0', '1'])->default('0');
            $table->enum('application_show',['0', '1'])->default('0');
            $table->enum('is_test_mode',['0', '1'])->default('0');
            $table->enum('is_active',['0', '1'])->default('0');
            $table->enum('is_desable_vt',['0', '1'])->default('0');
            $table->enum('is_ip_remove',['0', '1'])->default('0');
            $table->enum('is_bin_remove',['0', '1'])->default('0');
            $table->enum('is_disable_rule',['0', '1'])->default('0');
            $table->enum('is_whitelable',['0', '1'])->default('0');
            $table->enum('make_refund',['0', '1'])->default('0');
            $table->enum('is_otp_required',['0', '1'])->default('1');
            $table->enum('is_multi_mid',['0', '1'])->default('0');
            $table->enum('merchant_transaction_notification',['0', '1'])->default('0');
            $table->enum('user_transaction_notification',['0', '1'])->default('0');
            $table->enum('is_rate_sent',['0', '1'])->default('0');
            $table->enum('agent_commission_master_card',['0', '1'])->default('0');
            $table->enum('is_white_label',['0', '1'])->default('0');
            $table->enum('is_login_wl_merchant',['0', '1'])->default('0');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['company_license', 'status','is_completed','is_reassign','is_processing','is_placed','is_agreement','is_not_interested','is_terminated','is_reject']);
        });

        Schema::table('applications', function (Blueprint $table) {            
            $table->enum('company_license',['0', '1','2'])->default('0');
            $table->enum('status',['0', '1','2','3','4','5','6','7','8','9','10','11','12'])->default('0')->comment('0 = Pending 1 = Completed 2 = Reassign 3 = Rejected 4 = Approved 5 = Agreement Send 6 = Agreement Received 7 = notInterested 8 = Terminated 9 = Decline 10 = Rate Accepted 11 = Signed Agreement 12=Save Draft');
            $table->enum('is_completed',['0', '1'])->default('0');
            $table->enum('is_reassign',['0', '1'])->default('0');
            $table->enum('is_processing',['0', '1'])->default('0');
            $table->enum('is_placed',['0', '1'])->default('0');
            $table->enum('is_agreement',['0', '1'])->default('0');
            $table->enum('is_not_interested',['0', '1'])->default('0');
            $table->enum('is_terminated',['0', '1'])->default('0');
            $table->enum('is_reject',['0', '1'])->default('0');
        });
    }
}
