<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Application;
use App\Transaction;
use App\PayoutSchedule;
use App\PayoutReports;
use App\PayoutReportsChild;
use App\AgentPayoutReport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AgentRpPayoutReportExport;
use App\Exports\MerchantTransactionsReportForRpMerchantExport;

class ReportController extends AgentUserBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->transaction = new Transaction;
        $this->payoutSchedule = new PayoutSchedule;
        $this->PayoutReports = new PayoutReports;
        $this->PayoutReportsChild = new PayoutReportsChild;
    }

    public function getReport(Request $request)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $user_ids = User::select('id')->where('agent_id', $agentId)->get();
        $TransactionSummary = DB::table("transactions as trans")
            ->select('agents.name as agent_name', 'trans.currency', 'trans.user_id',
                     'users.agent_commission as commission', 'users.name as user_name',
                    //  DB::raw('COUNT(trans.user_id) as successCount'),
                    //  DB::raw('SUM(trans.amount) as successAmount'),
                     'users.agent_commission_master_card as master_commission',
                     DB::raw('SUM(IF(trans.`card_type` = 3,1, 0)) AS MasterSuccessCount'),
                     DB::raw('SUM(IF(trans.`card_type` != 3,1, 0)) AS OtherSuccessCount'),
                     DB::raw('SUM(IF(trans.`card_type` = 3,trans.amount, 0)) AS MasterSuccessAmount'),
                     DB::raw('SUM(IF(trans.`card_type` != 3,trans.amount, 0)) AS OtherSuccessAmount'),
                     )
            ->join('users', 'trans.user_id', '=', 'users.id')
            ->join('agents', 'users.agent_id', '=', 'agents.id')
            ->whereIn('trans.user_id', $user_ids)
            ->where('trans.status', '1')
            ->whereNull('trans.deleted_at')
            ->whereNull('agents.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereNotIn('trans.payment_gateway_id', $payment_gateway_id)
            ->groupBy('trans.user_id')
            ->groupBy('trans.currency')
            ->orderBy('MasterSuccessAmount', 'desc');
        if ($request->user_id) {
            $TransactionSummary =   $TransactionSummary->where('trans.user_id', $request->user_id);
        }
        if ($request->start_date) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $TransactionSummary =   $TransactionSummary->where(\DB::raw('DATE(trans.created_at)'), '>=', $start_date);
            //->where('trans.transaction_date', '>=', $start_date);
        }
        if ($request->end_date) {
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $TransactionSummary =   $TransactionSummary->where(\DB::raw('DATE(trans.created_at)'), '<=', $end_date);
        }
        $TransactionSummary =   $TransactionSummary->get();
        // \Log::info([
        //     'transaction summary' => $TransactionSummary
        // ]);
        $arr_t_data = array();
        if(!empty($TransactionSummary)) {
            foreach ($TransactionSummary as $k => $v) {
                $arr_t_data[$v->user_id][] = $v;
            }
        }
        // \Log::info([
        //     'transaction arr_t_data' => $arr_t_data
        // ]);
        $companyName = Application::select('user_id', 'business_name')->whereIn('user_id', $user_ids)->get();
        return view('agent.report.index', compact('companyName', 'arr_t_data'));
    }

    public function getPayoutreport(Request $request)
    {
        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $user_ids = User::select('id')->where('agent_id', $agentId)->get();
        $companyName = Application::select('user_id', 'business_name')->whereIn('user_id', $user_ids)->get();
        $payoutReports = AgentPayoutReport::where('agent_id', $agentId)->where('show_agent_side', '1')->orderBy('id', 'desc');

        if ($request->user_id != '') {
            $payoutReports = $payoutReports->where('user_id', $request->user_id);
        }

        if ($request->is_paid != '') {
            $payoutReports = $payoutReports->where('is_paid', $request->is_paid);
        }
        $payoutReports = $payoutReports->get();
        return view('agent.report.payout_report', compact('companyName', 'payoutReports'));
    }

    public function showPayoutReport(Request $request, $id)
    {
        $data = AgentPayoutReport::where('id', $id)->with('childData')->first();
        return view('agent.report.showPayoutReport', compact('data'));
    }

    public function getPayoutReportPdf(Request $request, $id)
    {
        $data = AgentPayoutReport::where('id', $id)->with('childData')->first();
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $html = view('agent.report.showPayoutReport', compact('data'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return  $dompdf->stream(str_replace('/', '-', $data->date) . '-' . $data->company_name . '- Payout Report' . '.pdf');
    }

    public function payoutReportExcel(Request $request)
    {
        return (new AgentRpPayoutReportExport($request->ids))->download();
//        return Excel::download(new AgentRpPayoutReportExport($request->ids), 'Payout_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function agentReport(Request $request)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $TransactionSummary = DB::table("transactions as trans")
            ->select('agents.name as agent_name', 'users.agent_commission as commission', 'users.name as user_name', 'trans.currency', 'trans.user_id', DB::raw('COUNT(trans.user_id) as successCount'), DB::raw('SUM(trans.amount) as successAmount'))
            ->join('users', 'trans.user_id', '=', 'users.id')
            ->join('agents', 'users.agent_id', '=', 'agents.id')
            ->where('trans.status', '1')
            ->where("trans.refund", "0")
            ->where("trans.chargebacks", "0")
            ->where("trans.is_flagged", "0")
            ->where("trans.is_retrieval", "0")
            ->whereNotIn('trans.payment_gateway_id', $payment_gateway_id)
            ->whereNull('trans.deleted_at')
            ->whereNull('agents.deleted_at')
            ->whereNull('users.deleted_at')
            ->groupBy('trans.user_id')
            ->groupBy('trans.currency')
            ->orderBy('successAmount', 'desc');
        $TransactionSummary =   $TransactionSummary->where('users.agent_id', $agentId);

        if ($request->user_id) {
            $TransactionSummary =   $TransactionSummary->where('trans.user_id', $request->user_id);
        }
        if ($request->start_date) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $TransactionSummary =   $TransactionSummary->where('trans.created_at', '>=', $start_date . " 00:00:00");
        }
        if ($request->end_date) {
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $TransactionSummary =   $TransactionSummary->where('trans.created_at', '<=', $end_date . " 23:59:59");
        }
        $TransactionSummary =   $TransactionSummary->get()->toArray();

        $arr_t_data = array();
        if(!empty($TransactionSummary)) {
            foreach ($TransactionSummary as $k => $v) {
                $arr_t_data[$v->user_id][] = $v;
            }
        }

        $userIds = User::where('agent_id', $agentId)->pluck('id');
        $companyName = Application::select('user_id', 'business_name')->whereIn('user_id', $userIds)->get();

        return view("agent.report.agent_reports", compact('TransactionSummary', 'arr_t_data', 'companyName'));
    }

    public function generateAgentReport(Request $request)
    {
        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $payoutReports = new AgentPayoutReport;
        $payoutReports = $payoutReports->where('agent_id', $agentId)->where('show_agent_side', 1);

        if ($request->user_id) {
            $payoutReports = $payoutReports->where('user_id', $request->user_id);
        }
        if ($request->is_paid >= 0) {
            $payoutReports = $payoutReports->where('is_paid', $request->is_paid);
        }
        $payoutReports = $payoutReports->orderBy('id', 'desc')->get();

        $userIds = User::where('agent_id', $agentId)->pluck('id');
        $companyName = Application::select('user_id', 'business_name')->whereIn('user_id', $userIds)->get();
        return view("agent.report.generate_agent_reports", compact('companyName', 'payoutReports'));
    }

    public function getAgentreportPdf(Request $request, $id)
    {
        $ArrRequest = [];
        addAdminLog(AdminAction::REFERRAL_PARTNER_GENERATE_PDF, $id,$ArrRequest,"RP Report Generate PDF");
        $data = AgentPayoutReport::where('id', $id)->with('childData')->first();
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $html = view('agent.report.show_generate_rp_report', compact('data'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return  $dompdf->stream(str_replace('/', '-', $data->date) . '-' . $data->company_name . '- Payout Report' . '.pdf');
    }

    public function showAgentreport(Request $request, $id)
    {
        $data = AgentPayoutReport::where('id', $id)->with('childData')->first();
        return view('agent.report.show_generate_rp_report', compact('data'));
    }

    public function summaryReport(Request $request)
    {
        return view('agent.report.summaryreport');
    }

    public function cardSummaryReport(Request $request)
    {
        $input = $request->all();
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $user_ids = User::select('id')->where('agent_id', $agentId)->get();


        $data = DB::table("transactions")->select(
            'card_type',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS block_percentage")
        )->whereIn('transactions.user_id', $user_ids)->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('user_id', $input['user_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != null) {
            $data = $data->where('card_type', $input['card_type']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));

            $data = $data->where('transactions.transaction_date', '>=', $start_date)
                ->where('transactions.transaction_date', '<=', $end_date);
        }

        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00'))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-6 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        $data = $data->groupBy('card_type')->orderBy('success_amount', 'desc')->get()->toArray();
        $transactions_summary  = $data;
        $companyName = Application::select('user_id', 'business_name')->whereIn('user_id', $user_ids)->get();
        $card_type = array('1' => 'Amex', '2' => 'Visa', '3' => 'Mastercard', '4' => 'Discover', '5' => 'JCB', '6' => 'Maestro', '7' => 'Switch', '8' => 'Solo');

        return view('agent.report.cardsummaryreport', compact('companyName', 'transactions_summary', 'card_type'));
    }

    public function paymentStatusReport(Request $request)
    {
        $input = $request->all();
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $user_ids = User::select('id')->where('agent_id', $agentId)->get();

        $data = DB::table("transactions")->select(
            'transactions.user_id',
            'transactions.currency',
            'applications.business_name',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS block_percentage"),

        )->leftJoin('applications', 'applications.user_id', '=', 'transactions.user_id')
        ->whereIn('transactions.user_id', $user_ids)
        ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));

            $data = $data->where('transactions.transaction_date', '>=', $start_date)
                ->where('transactions.transaction_date', '<=', $end_date);
        }

        $data = $data->groupBy('transactions.user_id', 'transactions.currency')->orderBy('success_amount', 'desc')->get()->toArray();
        $transactions_summary  = array();

        $arr_t_data = array();
        if(!empty($data)) {
            foreach ($data as $k => $v) {
                $arr_t_data[$v->user_id][] = $v;
            }
        }

        $companyName = Application::select('user_id', 'business_name')->whereIn('user_id', $user_ids)->get();
        $payment_status = array('1' => 'Success', '2' => 'Declined', '3' => 'Chargeback', '4' => 'Refund', '5' => 'Suspicious', '6' => 'Retrieval', '7' => 'Block');
        $payment_status_class = array('1' => 'text-success', '2' => 'text-danger', '3' => 'text-info', '4' => 'text-info', '5' => 'text-info', '6' => 'text-info', '7' => 'text-info');

        return view('agent.report.paymentsummaryreport', compact('companyName', 'arr_t_data', 'payment_status', 'payment_status_class'));
    }

    public function merchantTransactionsReport(Request $request)
    {
        $input = $request->all();
        $input['by_merchant'] = 1;
        // $merchant_transactions = $this->transaction->getMerchantTransactionReport($input);

        $transactions_summary = $this->transaction->getTransactionSummaryForRPMerchants($input);

        $payment_gateway_id = \DB::table('middetails')->get();

        $companyName = \DB::table('applications')->join('users','users.id','applications.user_id')->where('agent_id', auth()->guard('agentUser')->user()->id)->pluck('business_name','user_id')->toArray();

        return view("agent.report.merchant_transaction",compact('payment_gateway_id','companyName', 'transactions_summary'));
    }

    public function merchantTransactionsReportExcle(Request $request)
    {
        $input = $request->all();
        return Excel::download(new MerchantTransactionsReportForRpMerchantExport(), 'Merchant_Transaction_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function commisionReport(Request $request)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $TransactionSummary = DB::table("transactions as trans")
            ->select('agents.name as agent_name', 'trans.user_id',
             'users.name as user_name',
             'users.agent_commission as commission',
             // 'users.agent_commission as commission',
             // 'users.agent_commission_master_card as master_commission',
            DB::raw('"USD" AS currency'),
            // DB::raw('SUM(IF(trans.`card_type` = 3,1, 0)) AS MasterSuccessCount'),
            DB::raw('COUNT(*) as OtherSuccessCount'),
            // DB::raw('SUM(IF(trans.`card_type` != 3,1, 0)) AS OtherSuccessCount'),
            // DB::raw('SUM(IF(trans.`card_type` = 3,trans.amount_in_usd, 0)) AS MasterSuccessAmount'),
            DB::raw('SUM(trans.amount_in_usd) AS OtherSuccessAmount'),
            )
            ->join('users', 'trans.user_id', '=', 'users.id')
            ->join('agents', 'users.agent_id', '=', 'agents.id')
            ->where('trans.status', '1')
            ->where("trans.refund", "0")
            ->where("trans.chargebacks", "0")
            ->where("trans.is_flagged", "0")
            ->where("trans.is_retrieval", "0")
            ->whereNotIn('trans.payment_gateway_id', $payment_gateway_id)
            ->whereNull('trans.deleted_at')
            ->whereNull('agents.deleted_at')
            ->whereNull('users.deleted_at')
            ->groupBy('trans.user_id')
            ->orderBy('OtherSuccessAmount', 'desc');

        $TransactionSummary =   $TransactionSummary->where('users.agent_id', auth()->guard('agentUser')->user()->id);
        if ($request->user_id) {
            $TransactionSummary =   $TransactionSummary->where('trans.user_id', $request->user_id);
        }
        if ($request->start_date) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $TransactionSummary =   $TransactionSummary->where('trans.created_at', '>=', $start_date . " 00:00:00");
        }
        if ($request->end_date) {
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $TransactionSummary =   $TransactionSummary->where('trans.created_at', '<=', $end_date . " 23:59:59");
        }
        $TransactionSummary =   $TransactionSummary->get()->toArray();

        $arr_t_data = array();
        if(!empty($TransactionSummary)) {
            foreach ($TransactionSummary as $k => $v) {
                $arr_t_data[$v->user_id][] = $v;
            }
        }
        $userIds = User::where('agent_id', auth()->guard('agentUser')->user()->id)->pluck('id');
        $companyName = Application::select('user_id', 'business_name')->whereIn('user_id', $userIds)->get();

        return view("agent.report.commision_report", compact('companyName', 'TransactionSummary', 'arr_t_data'));
    }

    public function getRpMerchantPayoutReport(Request $request)
    {
    	$input = \Arr::except($request->all(),array('_token', '_method'));
        $input["show_client_side"] = 1;
        if(isset($input['noList'])){
            $noList = $input['noList'];
        }else{
            $noList = 10;
        }
        //echo "<pre>";
        $dataT = $this->PayoutReports->getAllReportData($noList, $input);
        //print_r($dataT);exit();
        return view('agent.report.payoutReport.merchant_payout_report')->with(['data'=>$dataT]);
    }

    public function RpMerchantPayoutReportshow($id)
    {
        $data = $this->PayoutReports->findData($id);
        $MerchantData = User::where('id', $data->user_id)->first('agent_id');
        if($MerchantData->agent_id != auth()->guard('agentUser')->user()->id){
            return redirect()->back();
        }
        $childData = $this->PayoutReportsChild->findDataByReportID($id);

        $totalFlagged = \DB::table('payout_report_child')->where('payoutreport_id', $id)->sum('flagged_transaction_sum');
        $start_date = $data->start_date;
        $start_date = str_replace('/', '-', $start_date);

        if (date('d', strtotime($start_date)) < 8) {
            $annual_fee = \DB::table('users')->select('annual_fee')->where('id', $data->user_id)->first()->annual_fee;
        } else {
            $annual_fee = 0;
        }
        view()->share('data', $data);
        view()->share('childData', $childData);
        view()->share('annual_fee', $annual_fee);
        view()->share('totalFlagged', $totalFlagged);

        return view('agent.report.payoutReport.show_report_PDF', compact('data', 'childData', 'totalFlagged'));
    }

    public function RpMerchantPayoutReportgeneratePDF($id)
    {
        $data = $this->PayoutReports->findData($id);
        $MerchantData = User::where('id', $data->user_id)->first('agent_id');
        if($MerchantData->agent_id != auth()->guard('agentUser')->user()->id){
            return redirect()->back();
        }
        $childData = $this->PayoutReportsChild->findDataByReportID($id);
        $totalFlagged = \DB::table('payout_report_child')->where('payoutreport_id', $id)->sum('flagged_transaction_count');
        $start_date = $data->start_date;
        $start_date = str_replace('/', '-', $start_date);
        if(date('d',strtotime($start_date)) < 8) {
            $annual_fee = \DB::table('users')->select('annual_fee')->where('id', $data->user_id)->first()->annual_fee;
         } else {
            $annual_fee = 0;
         }
        view()->share('data',$data);
        view()->share('childData',$childData);
        view()->share('annual_fee',$annual_fee);
        view()->share('totalFlagged',$totalFlagged);
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('agent.report.payoutReport.show_report_PDF'));
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
        $dompdf->render();
        \DB::table('payout_reports')->where('id', $id)->update(['is_download' => '1']);
        $dompdf->stream(str_replace('/','-',$data->date).'-'.$data->company_name.'-'.$data->id.'-'.$data->processor_name.'.pdf');
    }

    public function riskReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $agent_id = auth()->guard('agentUser')->user()->id;

        $users = User::where('agent_id',$agent_id)->select('id','name')->get();

        if(isset($users)){
            $data = [];
        }else{
            foreach($users as $key=>$user){
                $input['user_id'] = $user->id;
                $data[$key]['merchant_name'] = $user->application->business_name;
                $data[$key]['data'] = $this->Transaction->getRiskComplianceReportData($input);
            }
        }

        return view('agent.report.risk_report',compact('data'));
    }
}
