<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class AllTableIndexing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AllTable:Indexing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $db_name = 'Tables_in_'.env('DB_DATABASE');
        $tables = DB::select('SHOW TABLES');
        $EffectedTables = '';
        $arrTableColuns = $this->_GetTablesColumns();
        foreach($tables as $table)
        {
            $Tbl = $table->$db_name;
            $IndexingColumnArr = [];
            if(isset($arrTableColuns[$Tbl]) && !empty($arrTableColuns[$Tbl])) {
                $IndexingColumnArr = $arrTableColuns[$Tbl];
            }
            if(!empty($IndexingColumnArr)){
                
                $TblExistIndex = DB::select('SHOW INDEX FROM '.$Tbl.';');
                $tableEffected = $this->checkIndexing($Tbl, $IndexingColumnArr, $TblExistIndex);
                if($tableEffected == 1){
                    if($EffectedTables != ""){
                        $EffectedTables = $EffectedTables . ", " . $Tbl;
                    } else {
                        $EffectedTables = $Tbl;
                    }
                }
            }
        }

        if($EffectedTables == ""){
            echo "\r\n Effected Tables :: No";
        } else {
            echo "\r\n Effected Tables :: " . $EffectedTables;
        }
    }

    public function checkIndexing($Tbl, $IndexingColumnArr, $TblExistIndex)
    {
        $tableEffected = 0;
        if(empty($TblExistIndex)){
            if(!empty($IndexingColumnArr)){
                $tableEffected = 1;
                foreach($IndexingColumnArr as $indexcolumn){
                    DB::statement('ALTER TABLE `'.$Tbl.'` ADD INDEX (`'.$indexcolumn.'`)');
                }
            }
        } else {
            $ExistIndexingColumnArr = [];
            foreach($TblExistIndex as $Existindexcolumn){
                array_push($ExistIndexingColumnArr, $Existindexcolumn->Column_name);
            }
            if(!empty($IndexingColumnArr)){
                foreach($IndexingColumnArr as $indexcolumn){
                    if(!in_array($indexcolumn, $ExistIndexingColumnArr)){
                        $tableEffected = 1;
                        DB::statement('ALTER TABLE `'.$Tbl.'` ADD INDEX (`'.$indexcolumn.'`)');
                    }
                }
            }
        }
        return $tableEffected;
    }

    public function _GetTablesColumns() {

        $arrTableColuns['admin_actions']                = ['id', 'created_at', 'deleted_at'];
        $arrTableColuns['admin_logs']                   = ['id', 'admin_id', 'action_id', 'actionvalue', 'created_at', 'deleted_at'];
        $arrTableColuns['admin_password_resets']        = ['created_at'];
        $arrTableColuns['admins']                       = ['id', 'is_active', 'is_otp_required', 'is_password_expire', 'created_at', 'deleted_at'];
        $arrTableColuns['agent_bank_details']           = ['id', 'agent_id', 'created_at', 'deleted_at'];
        $arrTableColuns['agent_payout_report_children'] = ['id', 'report_id', 'created_at', 'deleted_at'];
        $arrTableColuns['agent_payout_reports']         = ['id', 'agent_id', 'user_id', 'start_date', 'end_date', 'is_pdf', 'is_download', 'is_excel', 'is_paid', 'show_agent_side', 'deleted_at'];
        $arrTableColuns['agents']                       = ['id', 'main_agent_id', 'is_otp_required', 'agreement_status', 'deleted_at', 'created_at', 'login_otp', 'is_active'];
        $arrTableColuns['agents_password_resets']       = ['created_at'];
        $arrTableColuns['agreement_content']            = ['id', 'type', 'deleted_at'];
        $arrTableColuns['agreement_document_upload']    = ['id', 'user_id', 'application_id'];
        $arrTableColuns['application_assign_to_bank']   = ['id', 'status', 'deleted_at'];
        $arrTableColuns['application_note']             = ['id', 'user_type', 'deleted_at', 'created_at'];
        $arrTableColuns['application_note_banks']       = ['id', 'bank_id', 'user_type', 'deleted_at', 'created_at'];
        $arrTableColuns['applications']                 = ['id', 'category_id', 'status', 'is_completed', 'is_reassign', 'is_processing', 'is_placed', 'is_agreement', 'is_not_interested', 'is_terminated', 'is_reject', 'created_at', 'deleted_at'];
        $arrTableColuns['articles']                     = ['id', 'category_id', 'created_at', 'deleted_at'];
        $arrTableColuns['articles_categories']          = ['id', 'created_at'];
        $arrTableColuns['articles_tags']                = ['id', 'created_at', 'deleted_at'];
        $arrTableColuns['auto_reports']                 = ['id', 'user_id', 'company_name', 'start_date', 'end_date', 'chargebacks_start_date', 'chargebacks_end_date', 'is_pdf', 'is_download', 'is_excel', 'status', 'show_client_side', 'created_at', 'deleted_at'];
        $arrTableColuns['auto_reports_child']           = ['id', 'user_id', 'autoreport_id', 'start_date', 'end_date', 'deleted_at'];
        $arrTableColuns['bank_applications']            = ['id', 'bank_id', 'status', 'deleted_at', 'created_at'];
        $arrTableColuns['banks']                        = ['id', 'is_otp_required', 'is_active', 'deleted_at', 'created_at'];
        $arrTableColuns['banks_password_resets']        = ['created_at'];
        $arrTableColuns['block_cards']                  = ['id', 'user_id', 'status'];
        $arrTableColuns['block_data']                   = ['id', 'type', 'deleted_at', 'created_at'];
        $arrTableColuns['categories']                   = ['id', 'deleted_at'];
        $arrTableColuns['counters']                     = ['id', 'number'];
        $arrTableColuns['countries']                    = ['id'];
        $arrTableColuns['cron_managements']             = ['id', 'days_check', 'deleted_at', 'created_at'];
        $arrTableColuns['currency_rate']                = ['id', 'created_at'];
        $arrTableColuns['daily_settlement_report']      = ['id', 'user_id', 'start_date', 'end_date', 'paid', 'paid_date', 'created_at'];
        $arrTableColuns['failed_jobs']                  = ['id'];
        $arrTableColuns['jobs']                         = ['id', 'reserved_at', 'available_at', 'created_at'];
        $arrTableColuns['log_activity']                 = ['id', 'created_at'];
        $arrTableColuns['mail_tamplates']               = ['id', 'deleted_at'];
        $arrTableColuns['main_gateway']                 = ['id', 'active', 'created_at'];
        $arrTableColuns['mass_mid']                     = ['id', 'change_type', 'old_mid', 'new_mid', 'deleted_at'];
        $arrTableColuns['middetails']                   = ['id', 'mid_type', 'main_gateway_mid_id', 'assign_gateway_mid', 'is_gateway_mid', 'per_day_card', 'per_day_email', 'per_week_card', 'per_week_email', 'per_month_card', 'per_month_email', 'bank_id', 'is_provide_refund', 'is_active', 'is_card_required', 'deleted_at'];
        $arrTableColuns['migrations']                   = ['id'];
        $arrTableColuns['model_has_permissions']        = ['permission_id', 'model_id'];
        $arrTableColuns['model_has_roles']              = ['role_id', 'model_id'];
        $arrTableColuns['notifications']                = ['id', 'user_id', 'sendor_id', 'is_read', 'created_at'];
        $arrTableColuns['orders']                       = ['id', 'user_id', 'store_id', 'product_id', 'created_at'];
        $arrTableColuns['password_resets']              = ['created_at'];
        $arrTableColuns['payout_report_child']          = ['id', 'user_id', 'payoutreport_id', 'start_date', 'end_date', 'deleted_at'];
        $arrTableColuns['payout_reports']               = ['id', 'user_id', 'start_date', 'end_date', 'chargebacks_start_date', 'chargebacks_end_date', 'is_pdf', 'is_download', 'is_excel', 'status', 'show_client_side', 'deleted_at'];
        $arrTableColuns['payout_reports_child_rp']      = ['id', 'user_id', 'payoutreport_id', 'start_date', 'end_date', 'deleted_at'];
        $arrTableColuns['payout_reports_rp']            = ['id', 'user_id', 'start_date', 'end_date', 'chargebacks_start_date', 'chargebacks_end_date', 'is_pdf', 'is_download', 'is_excel', 'status', 'show_client_side', 'deleted_at'];
        $arrTableColuns['payout_schedule']              = ['id', 'from_date', 'to_date', 'issue_date', 'deleted_at'];
        $arrTableColuns['paythrone_users']              = ['id', 'created_at'];
        $arrTableColuns['permissions']                  = ['id', 'created_at'];
        $arrTableColuns['personal_access_tokens']       = ['id', 'tokenable_id', 'created_at'];
        $arrTableColuns['products']                     = ['id', 'store_id', 'deleted_at', 'created_at'];
        $arrTableColuns['required_fields']              = ['id', 'deleted_at', 'created_at'];
        $arrTableColuns['role_has_permissions']         = ['permission_id', 'role_id'];
        $arrTableColuns['roles']                        = ['id'];
        $arrTableColuns['rp_agreement_document_upload'] = ['id', 'rp_id', 'created_at'];
        $arrTableColuns['rp_applications']              = ['id', 'agent_id', 'status', 'deleted_at', 'created_at'];
        $arrTableColuns['rules']                        = ['id', 'user_id', 'status', 'created_at', 'deleted_at', 'rules_type'];
        $arrTableColuns['settlement_report']            = ['id', 'user_id', 'start_date', 'end_date', 'paid', 'paid_date', 'created_at'];
        $arrTableColuns['store_products']               = ['id', 'store_id', 'status', 'deleted_at', 'created_at'];
        $arrTableColuns['stores']                       = ['id', 'user_id', 'template_id', 'deleted_at', 'updated_at'];
        $arrTableColuns['technology_partners']          = ['id', 'created_at', 'deleted_at'];
        $arrTableColuns['ticket_reply']                 = ['id', 'ticket_id', 'user_id', 'created_at'];
        $arrTableColuns['tickets']                      = ['id', 'user_id', 'department', 'status', 'deleted_at', 'created_at'];
        $arrTableColuns['tmp_orders']                   = ['id', 'user_id', 'store_id', 'product_id', 'deleted_at', 'created_at'];
        $arrTableColuns['transaction_hosted_session']   = ['id', 'user_id', 'payment_gateway_id', 'amount', 'is_completed', 'created_at'];
        $arrTableColuns['transaction_session']          = ['id', 'user_id', 'payment_gateway_id', 'amount', 'is_completed', 'is_checkout', 'is_card', 'created_at'];
        $arrTableColuns['transactions']                 = ['id', 'user_id', 'amount', 'amount_in_usd', 'status', 'payment_gateway_id', 'chargebacks', 'chargebacks_date', 'chargebacks_remove', 'chargebacks_remove_date', 'refund', 'refund_date', 'refund_remove', 'refund_remove_date', 'is_flagged', 'flagged_date', 'is_flagged_remove', 'flagged_remove_date', 'is_retrieval', 'retrieval_date', 'is_retrieval_remove', 'retrieval_remove_date', 'is_converted', 'converted_amount', 'is_converted_user_currency',  'converted_user_amount', 'is_transaction_type', 'is_duplicate_delete', 'is_pre_arbitration', 'pre_arbitration_date', 'transaction_date', 'created_at', 'deleted_at'];
        $arrTableColuns['transactions_document_upload'] = ['id', 'transaction_id', 'created_at'];
        $arrTableColuns['tx_transactions']              = ['id', 'user_id', 'agent_id', 'transaction_date', 'created_at'];
        $arrTableColuns['tx_tries']                     = ['id', 'user_id', 'payment_gateway_id', 'is_completed', 'is_checkout', 'is_card', 'created_at'];
        $arrTableColuns['user_bank_details']            = ['id', 'user_id', 'created_at', 'deleted_at'];
        $arrTableColuns['users']                        = ['id', 'main_user_id', 'mid', 'crypto_mid', 'bank_mid', 'upi_mid', 'is_test_mode', 'api_key', 'is_active', 'is_desable_vt', 'is_ip_remove', 'is_bin_remove', 'is_disable_rule', 'is_whitelable', 'make_refund', 'is_otp_required', 'is_multi_mid', 'is_white_label', 'created_at', 'deleted_at'];
        $arrTableColuns['wallets']                      = ['id', 'created_at'];
        $arrTableColuns['website_url']                  = ['id', 'user_id', 'is_active', 'created_at'];
        $arrTableColuns['wl_agents']                    = ['id', 'agreement_status', 'is_otp_required', 'is_active', 'deleted_at', 'created_at'];
        $arrTableColuns['wl_agents_password_resets']    = ['created_at'];
        return $arrTableColuns;
    }
}
