<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\AdminAction;
use App\MIDDetail;
use App\User;
use App\Application;
use App\PayoutReportsRP;
use App\PayoutReportsChildRP;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenerateReportExport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use App\AgentPayoutReport;
use App\AgentPayoutReportChild;
use Illuminate\Support\Facades\Storage;
use Mail;
use App\Mail\ShowReport;
use App\WLAgent;

class WLRPPayoutReportController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->wlagent = new WLAgent;
        $this->MIDDetail = new MIDDetail;
        $this->PayoutReportsRP = new PayoutReportsRP;
        $this->PayoutReportsChildRP = new PayoutReportsChildRP;
        $this->User = new User;
        $this->Application = new Application;
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $dataT = $this->PayoutReportsRP->getAllReportData($noList, $input);
        $arrId = [];
        if (!empty($dataT)) {
            $arrId = $dataT->pluck('id')->all();
        }
        $data = $this->wlagent->getData($input, $noList);
        return view("admin.payoutReportWlRP.index", compact('data', 'dataT', 'arrId'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required',
            'user_id' => 'required',
            'chargebacks_start_date' => 'required',
            'chargebacks_end_date' => 'required'
        ], [
            'start_date.required' => 'This field is required.',
            'end_date.required' => 'This field is required.',
            'user_id.required' => 'This field is required.',
            'chargebacks_start_date.required' => 'This field is required.',
            'chargebacks_end_date.required' => 'This field is required.',
        ]);
        echo "<pre>";print_r($request->all());
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $startDate = date('Y-m-d', strtotime($request->start_date));
        $endDate = date('Y-m-d', strtotime($request->end_date));
        $chargebacksStartDate = date('Y-m-d', strtotime($request->chargebacks_start_date));
        $chargebacksEndDate = date('Y-m-d', strtotime($request->chargebacks_end_date));
        $count = 0;
        if (isset($input['show_client_side']) && $input['show_client_side'] != '') {
            $show_client_side = '1';
        } else {
            $show_client_side = '0';
        }
        $userData = \DB::table("wl_agents")->where("id",$input['user_id'])->first();
        // $userData = \DB::table('users')
        //     ->select('applications.*', 'users.*')
        //     ->join('applications', 'applications.user_id', '=', 'users.id')
        //     ->where('users.id', $input['user_id'])
        //     ->first();
        $arrUserId = \DB::table("users")->where("white_label_agent_id",$input['user_id'])->pluck("id")->toArray();
        $countTransaction = \DB::table('transactions')
            ->whereIn('user_id', $arrUserId)
            ->where("deleted_at",NULL)
            ->whereNotIn('payment_gateway_id', ['1','2']);
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $countTransaction = $countTransaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $countTransaction = $countTransaction->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $endDate)
            ->count();
        if ($countTransaction == 0) {
            \Session::put('warning', 'No transaction found on this date rang');
            return redirect()->back();
        }
        $currencyArray = \DB::table('transactions')->whereIn('user_id', $arrUserId)->where("deleted_at",NULL)->whereNotIn('payment_gateway_id', ['1','2']);
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $currencyArray = $currencyArray->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $currencyArray = $currencyArray->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $endDate)->groupBy("transactions.currency")->pluck("currency")->toArray();
        \DB::beginTransaction();
        //try {
            $data = [];
            $data['user_id'] = $input['user_id'];
            $data['date'] = date('d/m/Y', time());
            $data['processor_name'] = config("app.name");
            $data['company_name'] = $userData->name;
            $data['address'] = '';
            $data['phone_no'] = '';
            $data['start_date'] = $startDate;
            $data['end_date'] = $endDate;
            $data['chargebacks_start_date'] = $chargebacksStartDate;
            $data['chargebacks_end_date'] = $chargebacksEndDate;
            $data['merchant_discount_rate'] = $userData->discount_rate; //Crerdit
            $data['merchant_discount_rate_master'] = $userData->discount_rate_master_card; //Crerdit
            $data['rolling_reserve_paercentage'] = $userData->rolling_reserve_paercentage;
            $data['transaction_fee_paercentage'] = $userData->transaction_fee;
            $data['declined_fee_paercentage'] = $userData->transaction_fee;
            $data['refund_fee_paercentage'] = $userData->refund_fee;
            $data['chargebacks_fee_paercentage'] = $userData->chargeback_fee;
            $data['flagged_fee_paercentage'] = $userData->flagged_fee;
            $data['retrieval_fee_paercentage'] = $userData->retrieval_fee;
            $data['wire_fee'] = 50; // 50
            $data['invoice_no'] = getReportInvoiceNo();
            $data['genereted_by'] = 'User';
            $data['show_client_side'] = $show_client_side;
            $payout_report = PayoutReportsRP::where('user_id', $input['user_id'])->orderBy("id", "DESC")->first();
            $reportID = $this->PayoutReportsRP->storeData($data);
            addAdminLog(AdminAction::GENERATE_PAYOUT_REPORT_RP, $reportID->id,$data,"RP Report Generated Successfully!");
            foreach ($currencyArray as $key => $value) {
                $convertTransactionFee =0;
                if ($userData->transaction_fee != 0) {
                    $convertTransactionFeearr = checkSelectedCurrencyTwo('USD', $userData->transaction_fee, $value);
                    $convertTransactionFee=$convertTransactionFeearr["amount"];
                }
                $chekTransactionInCurrency = \DB::table('transactions')
                    ->whereIn('user_id', $arrUserId)
                    ->where('currency', $value)
                    ->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $startDate)
                    ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $endDate)
                    ->where("deleted_at",NULL)
                    ->count();
                if ($chekTransactionInCurrency > 0) {
                    $checkAllOtherTransaction = $this->checkingTransaction($value,$input,$startDate,$endDate,"Other",$arrUserId);
                    
                    $checkAllMasterCardTransaction = $this->checkingTransaction($value,$input,$startDate,$endDate,"MasterCard",$arrUserId);
                    
                    $checkSuccessOtherTransaction = $this->checkOtherTransaction($value,$input,$chargebacksStartDate,$chargebacksEndDate,"Other",$arrUserId);
                    $checkSuccessMasterCardTransaction = $this->checkOtherTransaction($value,$input,$chargebacksStartDate,$chargebacksEndDate,"MasterCard",$arrUserId);
                    if ($checkAllOtherTransaction > 0 || $checkSuccessOtherTransaction> 0) {
                        $this->payoutReportChild($value,$input,$startDate,$endDate,$chargebacksStartDate,$chargebacksEndDate,$userData,$reportID,$payout_report,"Other",$convertTransactionFee,$arrUserId);
                    }
                    if ($checkAllMasterCardTransaction > 0 || $checkSuccessMasterCardTransaction> 0) {
                        $this->payoutReportChild($value,$input,$startDate,$endDate,$chargebacksStartDate,$chargebacksEndDate,$userData,$reportID,$payout_report,"MasterCard",$convertTransactionFee,$arrUserId);
                    }
                    // $start_date1 = date('Y-m-d', strtotime('-1 week', strtotime(date('Y-m-d'))));
                    // $end_date1 = date('Y-m-d');
                    //$childData['declined_fee'] = ($userData->transaction_fee*$declined_transaction->count);
                }
            }
            \DB::commit();
            \Session::put('success', 'Report Generated Successfully !');
            return redirect()->back();
        // } catch (\Exception $e) {
        //     \DB::rollback();
        //     \Session::put('error', 'Error in report generation !');
        //     return redirect()->back();
        // }
    }


    public function checkingTransaction($value,$input,$startDate,$endDate,$type,$arrUserId){
        $checkTransaction = \DB::table('transactions')
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $endDate)
            ->where("deleted_at",NULL);
        if($type == "Other"){
            $checkTransaction = $checkTransaction->where("card_type","!=","3");
        }else if($type == "MasterCard"){
            $checkTransaction = $checkTransaction->where("card_type","3");
        }
        $checkTransaction = $checkTransaction->count();
        //echo $checkTransaction."<br>";exit()
        return $checkTransaction;
    }

    public function checkOtherTransaction($value,$input,$startDate,$endDate,$type,$arrUserId){
        $chekSuccessTransaction = \DB::table('transactions')
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->where("deleted_at",NULL)
            ->where(function($query) use ($startDate, $endDate){
                $query->whereBetween(\DB::raw('DATE(transactions.chargebacks_date)'), [$startDate, $endDate])
                ->orWhereBetween(\DB::raw('DATE(transactions.flagged_date)'), [$startDate, $endDate])
                ->orWhereBetween(\DB::raw('DATE(transactions.retrieval_date)'), [$startDate, $endDate])
                ->orWhereBetween(\DB::raw('DATE(transactions.refund_date)'), [$startDate, $endDate]);
            });
        if($type == "Other"){
            $chekSuccessTransaction = $chekSuccessTransaction->where("card_type","!=","3");
        }else if($type == "MasterCard"){
            $chekSuccessTransaction = $chekSuccessTransaction->where("card_type","3");
        }
        $chekSuccessTransaction = $chekSuccessTransaction->count();
        return $chekSuccessTransaction;
    }

    public function payoutReportChild($value,$input,$startDate,$endDate,$chargebacksStartDate,$chargebacksEndDate,$userData,$reportID,$payout_report,$type,$convertTransactionFee,$arrUserId){
        $approved_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->where('status', '1')
            ->where("deleted_at",NULL)
            ->whereNotIn('payment_gateway_id', ['1','2']);
        $declined_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->where('status', '0')
            ->where("deleted_at",NULL)
            ->whereNotIn('payment_gateway_id', ['1','2']);
        if($type == "Other"){
            $approved_transaction = $approved_transaction->where("card_type","!=","3");
            $declined_transaction = $declined_transaction->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $approved_transaction = $approved_transaction->where("card_type","3");
            $declined_transaction = $declined_transaction->where("card_type","3");
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $approved_transaction = $approved_transaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $approved_transaction = $approved_transaction->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $endDate)
            ->first();
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $declined_transaction = $declined_transaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $declined_transaction = $declined_transaction->where(\DB::raw('DATE(transactions.transaction_date)'), '>=', $startDate)
            ->where(\DB::raw('DATE(transactions.transaction_date)'), '<=', $endDate)
            ->first();
        $chargebacks_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where("deleted_at",NULL)
            ->where('chargebacks', '1')->where('chargebacks_remove', '0');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $chargebacks_transaction = $chargebacks_transaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if($type == "Other"){
            $chargebacks_transaction = $chargebacks_transaction->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $chargebacks_transaction = $chargebacks_transaction->where("card_type","3");
        }
        $chargebacks_transaction = $chargebacks_transaction->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
            ->first();

        $refund_transaction = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where("deleted_at",NULL)
            ->where('refund', '1')->where('chargebacks', "0")->where('refund_remove', '0');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $refund_transaction = $refund_transaction->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if($type == "Other"){
            $refund_transaction = $refund_transaction->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $refund_transaction = $refund_transaction->where("card_type","3");
        }
        $refund_transaction = $refund_transaction->where(\DB::raw('DATE(transactions.refund_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.refund_date)'), '<=', $chargebacksEndDate)
            ->first();
        $total_flagged = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where("deleted_at",NULL)
            ->where('is_flagged', '1')->where("is_flagged_remove", "0")->where("chargebacks", "0");
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_flagged = $total_flagged->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if($type == "Other"){
            $total_flagged = $total_flagged->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $total_flagged = $total_flagged->where("card_type","3");
        }
        $total_flagged = $total_flagged->where(\DB::raw('DATE(transactions.flagged_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.flagged_date)'), '<=', $chargebacksEndDate)
            ->first();
        $total_retrieval = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where("deleted_at",NULL)
            ->where('is_retrieval', '1')->where('chargebacks', "0")->where('is_retrieval_remove', '0');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_retrieval = $total_retrieval->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if($type == "Other"){
            $total_retrieval = $total_retrieval->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $total_retrieval = $total_retrieval->where("card_type","3");
        }
        $total_retrieval = $total_retrieval->where(\DB::raw('DATE(transactions.retrieval_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.retrieval_date)'), '<=', $chargebacksEndDate)
            ->first();

        $total_past_refund = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where("deleted_at",NULL)
            ->where('refund_remove', '1');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_past_refund = $total_past_refund->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if($type == "Other"){
            $total_past_refund = $total_past_refund->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $total_past_refund = $total_past_refund->where("card_type","3");
        }
        $total_past_refund = $total_past_refund->where(\DB::raw('DATE(transactions.refund_remove_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.refund_remove_date)'), '<=', $chargebacksEndDate)
            ->first();
        $total_past_flagged = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where("deleted_at",NULL)
            ->where('is_flagged_remove', '1');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_past_flagged = $total_past_flagged->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if($type == "Other"){
            $total_past_flagged = $total_past_flagged->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $total_past_flagged = $total_past_flagged->where("card_type","3");
        }
        $total_past_flagged = $total_past_flagged->where(\DB::raw('DATE(transactions.flagged_remove_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.flagged_remove_date)'), '<=', $chargebacksEndDate)
            ->first();

        $total_past_chargeback = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where("deleted_at",NULL)
            ->where('chargebacks_remove', '1');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_past_chargeback = $total_past_chargeback->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if($type == "Other"){
            $total_past_chargeback = $total_past_chargeback->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $total_past_chargeback = $total_past_chargeback->where("card_type","3");
        }
        $total_past_chargeback = $total_past_chargeback->where(\DB::raw('DATE(transactions.chargebacks_remove_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.chargebacks_remove_date)'), '<=', $chargebacksEndDate)
            ->first();

        $total_past_retrieval = \DB::table('transactions')->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
            ->whereIn('user_id', $arrUserId)
            ->where('currency', $value)
            ->whereNotIn('payment_gateway_id', ['1','2'])
            ->where("deleted_at",NULL)
            ->where('is_retrieval_remove', '1');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id']) {
            $total_past_retrieval = $total_past_retrieval->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        if($type == "Other"){
            $total_past_retrieval = $total_past_retrieval->where("card_type","!=","3");
            
        }else if($type == "MasterCard"){
            $total_past_retrieval = $total_past_retrieval->where("card_type","3");
        }
        $total_past_retrieval = $total_past_retrieval->where(\DB::raw('DATE(transactions.retrieval_remove_date)'), '>=', $chargebacksStartDate)
            ->where(\DB::raw('DATE(transactions.retrieval_remove_date)'), '<=', $chargebacksEndDate)
            ->first();
        $childData['user_id'] = $input['user_id'];
        $childData['payoutreport_id'] = $reportID->id;
        $childData["total_transaction_count"] =  $approved_transaction->count + $declined_transaction->count;
        $childData["total_transaction_sum"] = $approved_transaction->amount + $declined_transaction->amount;
        $childData['approve_transaction_count'] = $approved_transaction->count;
        $childData['approve_transaction_sum'] = $approved_transaction->amount;
        $childData['declined_transaction_count'] = $declined_transaction->count;
        $childData['declined_transaction_sum'] = $declined_transaction->amount;
        $childData['chargeback_transaction_count'] = $chargebacks_transaction->count;
        $childData['chargeback_transaction_sum'] = $chargebacks_transaction->amount;
        $childData['refund_transaction_count'] = $refund_transaction->count;
        $childData['refund_transaction_sum'] = $refund_transaction->amount;
        $childData['flagged_transaction_count'] = $total_flagged->count;
        $childData['flagged_transaction_sum'] = $total_flagged->amount;
        $childData['retrieval_transaction_count'] = $total_retrieval->count;
        $childData['retrieval_transaction_sum'] = $total_retrieval->amount;
        $childData['currency'] = $value;

        if($type == "Other"){
            $childData['mdr'] = ($userData->discount_rate * $approved_transaction->amount)/100;
        }else if($type == "MasterCard"){
            $childData['mdr'] = ($userData->discount_rate_master_card * $approved_transaction->amount)/100;
        }
        $childData['rolling_reserve'] = ($userData->rolling_reserve_paercentage * $approved_transaction->amount) / 100;
        $tramsactionFee = ($userData->transaction_fee * ($approved_transaction->count + $declined_transaction->count));
        $transactionFeeConvertedAmount = 0;
        if ($tramsactionFee != 0) {
            $returnFee = checkSelectedCurrencyTwo('USD', $tramsactionFee, $value);
            $transactionFeeConvertedAmount = $returnFee["amount"];
        }
        $childData['transaction_fee'] = $tramsactionFee;
        $childData['refund_fee'] = ($userData->refund_fee * $refund_transaction->count);
        $chargebacks_fee = ($userData->chargeback_fee * $chargebacks_transaction->count);
        $chargebackFeeConvertedAmount = 0;
        if ($chargebacks_fee != 0) {
            $chargebackFee = checkSelectedCurrencyTwo('USD', $chargebacks_fee, $value);
            $chargebackFeeConvertedAmount = $chargebackFee["amount"];
        }
        $childData['chargeback_fee'] = $chargebacks_fee;
        $flagged_fee = ($userData->flagged_fee * $total_flagged->count);
        $flaggedFeeConvertedAmount = 0;
        if ($flagged_fee != 0) {
            $flaggedReturnFee = checkSelectedCurrencyTwo('USD', $flagged_fee, $value);
            $flaggedFeeConvertedAmount = $flaggedReturnFee["amount"];
        }
        $childData['flagged_fee'] = $flagged_fee;
        $retrieval_fee = ($userData->retrieval_fee * $total_retrieval->count);
        $retrievalFeeConvertedAmount = 0;
        if ($retrieval_fee != 0) {
            $retrievalReturnFee = checkSelectedCurrencyTwo('USD', $retrieval_fee, $value);
            $retrievalFeeConvertedAmount = $retrievalReturnFee["amount"];
        }
        $childData['retrieval_fee'] = $retrieval_fee;
        $childData["remove_past_flagged"] = $total_past_flagged->count;
        $past_flagged_fee = ($userData->flagged_fee * $total_past_flagged->count);
        $pastFlaggedFeeConvertedAmount = 0;
        if ($past_flagged_fee != 0) {
            $pastFlaggedFee = checkSelectedCurrencyTwo('USD', $past_flagged_fee, $value);
            $pastFlaggedFeeConvertedAmount = $pastFlaggedFee["amount"];
        }
        $childData["past_flagged_charge_amount"] = $past_flagged_fee;
        
        if($type == "Other"){
            $past_flagged_sum_deduction = (($userData->discount_rate * $total_past_flagged->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_flagged->amount) / 100);
        }else if($type == "MasterCard"){
            $past_flagged_sum_deduction = (($userData->discount_rate_master_card * $total_past_flagged->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_flagged->amount) / 100);
        }
        $finalPastFalggedAmount = 0;
        if ($total_past_flagged->amount != 0) {
            $finalPastFalggedAmount = ($total_past_flagged->amount) - $past_flagged_sum_deduction - ($convertTransactionFee * $total_past_flagged->count);
        }
        $childData["past_flagged_sum"] = $finalPastFalggedAmount;
        $childData["remove_past_chargebacks"] = $total_past_chargeback->count;
        $past_chargeback_fee = ($userData->chargeback_fee * $total_past_chargeback->count);
        $pastChargebackAmount = 0;
        if ($past_chargeback_fee != 0) {
            $pastChargebackFee = checkSelectedCurrencyTwo('USD', $past_chargeback_fee, $value);
            $pastChargebackAmount =  $pastChargebackFee["amount"];
        }
        $childData["past_chargebacks_charge_amount"] = $past_chargeback_fee;
        $childData["past_chargebacks_sum"] = $total_past_chargeback->amount;
        $childData["remove_past_retrieval"] = $total_past_retrieval->count;
        $past_retrieval_charge_amount = ($userData->retrieval_fee * $total_past_retrieval->count);
        $pastRetrievalAmount = 0;
        if ($past_retrieval_charge_amount != 0) {
            $pastRetrievalFee = checkSelectedCurrencyTwo('USD', $past_retrieval_charge_amount, $value);
            $pastRetrievalAmount = $pastRetrievalFee["amount"];
        }
        $childData["past_retrieval_charge_amount"] = $past_retrieval_charge_amount;
        
        if($type == "Other"){
            $past_retrieval_sum_deduction = (($userData->discount_rate * $total_past_retrieval->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_retrieval->amount) / 100);
        }else if($type == "MasterCard"){
            $past_retrieval_sum_deduction = (($userData->discount_rate_master_card * $total_past_retrieval->amount) / 100) + (($userData->rolling_reserve_paercentage * $total_past_retrieval->amount) / 100);
        }
        $finalPastRetrievalAmount = 0;
        if ($total_past_retrieval->amount != 0) {
            $finalPastRetrievalAmount = ($total_past_retrieval->amount) - $past_retrieval_sum_deduction - ($convertTransactionFee * $total_past_retrieval->count);
        }
        $childData["past_retrieval_sum"] = $finalPastRetrievalAmount;
        $returnFlaggedFee = 0;
        $totalChargebackAmount = 0;
        $totalChargebackCount = 0;
        if (isset($payout_report)) {
            $payout_start_date = date('Y-m-d', strtotime($payout_report->start_date));
            $payout_end_date = date('Y-m-d', strtotime($payout_report->end_date));
            $checkedPastFlagged = \DB::table('transactions')
                ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                ->whereIn('user_id', $arrUserId)
                ->where("deleted_at",NULL)
                ->where('currency', $value)->where(["is_flagged" => "1", "chargebacks" => "1"])
                ->where(\DB::raw('DATE(transactions.flagged_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.flagged_date)'), '<=', $payout_end_date)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                ->whereNotIn('payment_gateway_id', ['1','2']);
            if($type == "Other"){
                $checkedPastFlagged = $checkedPastFlagged->where("card_type","!=","3");
                
            }else if($type == "MasterCard"){
                $checkedPastFlagged = $checkedPastFlagged->where("card_type","3");
            }
            $checkedPastFlagged = $checkedPastFlagged->first();
            $pastFlaggedChargebackAmount = 0;
            if ($checkedPastFlagged->amount != 0) {
                $pastFlaggedChargebackAmount = ($checkedPastFlagged->amount) - (($userData->discount_rate * $checkedPastFlagged->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastFlagged->amount) / 100) - ($convertTransactionFee * $checkedPastFlagged->count);
            }
            $totalChargebackCount += $checkedPastFlagged->count;
            $checkedPastRefund = \DB::table('transactions')
                ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                ->whereIn('user_id', $arrUserId)
                ->where("deleted_at",NULL)
                ->where('currency', $value)->where(["refund" => "1", "chargebacks" => "1"])
                ->where(\DB::raw('DATE(transactions.refund_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.refund_date)'), '<=', $payout_end_date)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                ->whereNotIn('payment_gateway_id', ['1','2']);
            if($type == "Other"){
                $checkedPastRefund = $checkedPastRefund->where("card_type","!=","3");
                
            }else if($type == "MasterCard"){
                $checkedPastRefund = $checkedPastRefund->where("card_type","3");
            }
            $checkedPastRefund = $checkedPastRefund->first();
            $pastRefundChargebackAmount = 0;
            if ($checkedPastRefund->amount != 0) {
                $pastRefundChargebackAmount = ($checkedPastRefund->amount) - (($userData->discount_rate * $checkedPastRefund->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastRefund->amount) / 100) - ($convertTransactionFee * $checkedPastRefund->count);
            }
            $totalChargebackCount += $checkedPastRefund->count;
            $checkedPastRetrieval = \DB::table('transactions')
                ->select(DB::raw('SUM(amount) as amount'), DB::raw('count("*") as count'))
                ->whereIn('user_id', $arrUserId)
                ->where("deleted_at",NULL)
                ->where('currency', $value)->where(["is_retrieval" => "1", "chargebacks" => "1"])
                ->where(\DB::raw('DATE(transactions.retrieval_date)'), '>=', $payout_start_date)
                ->where(\DB::raw('DATE(transactions.retrieval_date)'), '<=', $payout_end_date)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '>=', $chargebacksStartDate)
                ->where(\DB::raw('DATE(transactions.chargebacks_date)'), '<=', $chargebacksEndDate)
                ->whereNotIn('payment_gateway_id', ['1','2']);
            if($type == "Other"){
                $checkedPastRetrieval = $checkedPastRetrieval->where("card_type","!=","3");
                
            }else if($type == "MasterCard"){
                $checkedPastRetrieval = $checkedPastRetrieval->where("card_type","3");
            }
            $checkedPastRetrieval = $checkedPastRetrieval->first();
            $pastRetrievalChargebackAmount = 0;
            if ($checkedPastRetrieval->amount != 0) {
                $pastRetrievalChargebackAmount = ($checkedPastRetrieval->amount) - (($userData->discount_rate * $checkedPastRetrieval->amount) / 100) - (($userData->rolling_reserve_paercentage * $checkedPastRetrieval->amount) / 100) - ($convertTransactionFee * $checkedPastRetrieval->count);
            }
            $totalChargebackCount += $checkedPastRetrieval->count;
            $totalChargebackAmount = $pastRetrievalChargebackAmount + $pastRefundChargebackAmount + $pastFlaggedChargebackAmount;
            $returnFlaggedFee = ($userData->flagged_fee * $checkedPastFlagged->count) + ($userData->refund_fee * $checkedPastRefund->count) + ($userData->retrieval_fee * $checkedPastRetrieval->count);
        }
        $returnFeeAmount = 0;
        if ($returnFlaggedFee != 0) {
            $returnFee = checkSelectedCurrencyTwo('USD', $returnFlaggedFee, $value);
            $returnFeeAmount = $returnFee["amount"];
        }
        $totalFee = $chargebackFeeConvertedAmount + $flaggedFeeConvertedAmount + $retrievalFeeConvertedAmount  + $transactionFeeConvertedAmount;
        $childData['return_fee'] = $totalChargebackAmount;
        $childData['return_fee_count'] = $totalChargebackCount;
        $childData["past_flagged_fee"] = $returnFeeAmount;
        $childData["transactions_fee_total"] = $totalFee;
        $childData['sub_total'] = $approved_transaction->amount - ($refund_transaction->amount + $chargebacks_transaction->amount + $total_flagged->amount + $total_retrieval->amount);
        $childData['net_settlement_amount'] = $childData['sub_total'] - ($totalFee + $childData['rolling_reserve'] + $childData['mdr']) + $childData["past_flagged_sum"] + $childData["past_retrieval_sum"] + $returnFeeAmount + $totalChargebackAmount;
        $childData['card_type'] = $type;
        $this->PayoutReportsChildRP->storeData($childData);
    }

    public function makeReportPaid(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if ($this->PayoutReportsRP->updateData($request->get('id'), ['status' => $request->get('status')])) {
            $ArrRequest = ['status' => $request->get('status')];
            addAdminLog(AdminAction::PAYOUT_REPORT_PAID_RP, $request->get('id'),$ArrRequest,"RP Payout Report Paid");
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function showReportClient(Request $request)
    {
        if ($this->PayoutReportsRP->updateData($request->get('id'), ['show_client_side' => $request->get('status')])) {
            if($request->get('status')==1){
                $input = \Arr::except($request->all(), array('_token', '_method'));
                $id= $request->get('id');
                $data = $this->PayoutReportsRP->findData($id);
                $childData = $this->PayoutReportsChildRP->findDataByReportID($id);
                $users = \DB::table('users')->find($data->user_id);
                $totalFlagged = \DB::table('payout_reports_child_rp')->where('payoutreport_id', $id)->sum('flagged_transaction_count');
                $start_date = $data->start_date;
                $start_date = str_replace('/', '-', $start_date);
                view()->share('data', $data);
                view()->share('childData', $childData);
                view()->share('totalFlagged', $totalFlagged);
                $options = new Options();
                $options->setIsRemoteEnabled(true);
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml(view('admin.payoutReportWlRP.show_report_PDF'));
                $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
                $dompdf->render();
                $fileName =  str_replace('/', '-', $data->date) . '-' . $data->company_name . '-' . $data->id . '-' . $data->processor_name . '.' . 'pdf' ;
                Storage::disk('public')->put("pdf/".$fileName,$dompdf->output());
                Mail::to($users->email)->send(new ShowReport($fileName));
                unlink(storage_path('app/public/pdf/'.$fileName));
                $ArrRequest = ['show_client_side' => $request->get('status')];
                addAdminLog(AdminAction::PAYOUT_REPORT_SHOW_RP, $request->get('id'),$ArrRequest,"Payout Report show to client");
            } else {
                $ArrRequest = ['show_client_side' => $request->get('status')];
                addAdminLog(AdminAction::PAYOUT_REPORT_SHOW_RP, $request->get('id'),$ArrRequest,"Payout Report can't show to client");
            }
            
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function massremove(Request $request)
    {
        try {
            $report_id_array = $request->input('id');
            foreach ($report_id_array as $key => $value) {
                PayoutReportsChildRP::where('payoutreport_id', $value)->delete();
                $this->PayoutReportsRP->destroyData($value);
            }
            $ArrRequest = [ 'id' => implode(",", $report_id_array)];
            addAdminLog(AdminAction::PAYOUT_REPORT_DELETE_RP, null,$ArrRequest,"RP Payout Reports Deleted");
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function reportFilesUpload(Request $request)
    {
        $this->validate($request, [
            'files' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $arr = [];
        if ($request->hasFile('files')) {
            $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageName = $imageName . '.' . $request->file('files')->getClientOriginalExtension();
            $filePath = 'uploads/generatedreportrp/' . $imageName;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('files')->getRealPath()));
            $arr['files'] = $filePath;
        } else {
            $arr['files'] = '';
        }
        $files = $this->PayoutReportsRP->findData($request->get('id'));
        if ($files == null) {
            $arr['files'] = json_encode([$arr['files']]);
        } else {
            $files = json_decode($files);
            array_push($files, $arr['files']);
            $arr['files'] = json_encode($files);
        }
        $this->PayoutReportsRP->updateData($input["report_id"], $arr);
        $ArrRequest = $arr;
        addAdminLog(AdminAction::PAYOUT_REPORT_UPLOAD_FILES_RP, $input["report_id"],$ArrRequest,"File Uploaded Successfully!");
        notificationMsg('success', 'File Uploaded Successfully!');
        return redirect()->back();
    }

    public function generateReportExport(Request $request)
    {
        $ArrRequest = [];
        if(isset($request->ids) && !empty($request->ids)){
            $ArrRequest = ['id' => implode(",", $request->ids)];    
        }
        addAdminLog(AdminAction::PAYOUT_REPORT_DOWNLOAD_EXCEL, null,$ArrRequest,"Payout Report Download Excel File");
        return Excel::download(new GenerateReportExport($request->ids), 'GenerateReport_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function show($id)
    {
        $data = $this->PayoutReportsRP->findData($id);
        $childData = $this->PayoutReportsChildRP->findDataByReportID($id);

        $totalFlagged = \DB::table('payout_reports_child_rp')->where('payoutreport_id', $id)->sum('flagged_transaction_sum');
        $start_date = $data->start_date;
        $start_date = str_replace('/', '-', $start_date);
        view()->share('data', $data);
        view()->share('childData', $childData);
        view()->share('totalFlagged', $totalFlagged);

        return view('admin.payoutReportWlRP.show_report_PDF', compact('data', 'childData', 'totalFlagged'));
    }

    public function generatePDF($id)
    {
        $data = $this->PayoutReportsRP->findData($id);
        $childData = $this->PayoutReportsChildRP->findDataByReportID($id);
        $totalFlagged = \DB::table('payout_reports_child_rp')->where('payoutreport_id', $id)->sum('flagged_transaction_count');
        $start_date = $data->start_date;
        $start_date = str_replace('/', '-', $start_date);
        view()->share('data', $data);
        view()->share('childData', $childData);
        view()->share('totalFlagged', $totalFlagged);

        $ArrRequest = [];
        addAdminLog(AdminAction::PAYOUT_REPORT_GENERATE_PDF_RP, $id,$ArrRequest,"Payout Report Generated PDF");

        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.payoutReportWlRP.show_report_PDF'));

        // (Optional) Setup the paper size and orientation
        // $dompdf->setPaper('A4', 'landscape');
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

        $dompdf->render();
        \DB::table('payout_reports_rp')->where('id', $id)->update(['is_download' => '1']);
        $dompdf->stream(str_replace('/', '-', $data->date) . '-' . $data->company_name . '-' . $data->id . '-' . $data->processor_name . '.pdf');
    }
}
