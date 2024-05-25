<?php

namespace App\Http\Controllers\WLAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\WLRPUserExport;
use App\WLAgent;
use App\Application;
use App\Categories;
use App\Transaction;
use App\User;
use View;
use Redirect;
use Hash;
use Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        view()->share('WLAgentUserTheme', 'layouts.WLAgent.default');

        $this->middleware(function ($request, $next) {
            $userData = WLAgent::where('wl_agents.id', auth()->guard('agentUserWL')->user()->id)
                ->first();

            view()->share('userData', $userData);
            return $next($request);
        });

        $this->wlAgentUser = new WLAgent;
        $this->User = new User;
        $this->Transaction = new Transaction;
    }

    public function transactionSummaryReport(Request $request)
    {

        $input = \Arr::except($request->all(), array('_token', '_method'));

        $agentId = auth()->guard('agentUserWL')->user()->id;

        $userIds = \DB::table('users')->where('white_label_agent_id', $agentId)->pluck('id')->toArray();

        $TransactionSummary = [];
        if(!empty($userIds)) {
            $input['user_id'] = $userIds;
            $TransactionSummary = $this->Transaction->getTransactionSummaryRP($input);
        }
        return view('WLAgent.report.transaction_summary', compact('TransactionSummary'));

    }

    public function summaryReport()
    {
        return view('WLAgent.report.summary_report');
    }

    public function cardSummaryReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];
        $agentId = auth()->guard('agentUserWL')->user()->id;
        $userIds = \DB::table('users')->where('white_label_agent_id', $agentId)->pluck('id')->toArray();

        $transactions_summary = [];
        if(!empty($userIds)) {
            if(empty($input)) {
                $input['for'] = 'Daily';
            }
            $input['user_id'] = $userIds;
            $input['groupBy'] = 'card_type';
            $input['SelectFields'] = ['card_type'];
            $transactionssummary = $this->Transaction->getSummaryReportData($input);
            $transactions_summary = $this->Transaction->PorcessSumarryData('CardTypeSumamry', $transactionssummary);
        }
        $card_type = config('card.type');

        return view("WLAgent.report.card_summary_report",compact('payment_gateway_id','transactions_summary', 'card_type'));
    }


    public function paymentStatusSummaryReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $agentId = auth()->guard('agentUserWL')->user()->id;
        $userIds = \DB::table('users')->where('white_label_agent_id', $agentId)->pluck('id')->toArray();
        
        $arr_t_data = array();

        if(!empty($userIds)) {
            $input['user_id'] = $userIds;
            $input['groupBy'] = ['transactions.user_id', 'transactions.currency'];
            $input['SelectFields'] = ['transactions.user_id', 'applications.business_name'];
            $input['JoinTable'] = [
                                    'table' => 'applications',
                                    'condition' =>  'applications.user_id',
                                    'conditionjoin' => 'transactions.user_id' 
                                ];
            $transactionssummary = $this->Transaction->getSummaryReportData($input);
            $arr_t_data = $this->Transaction->PorcessSumarryData('PaymentSsummary', $transactionssummary);
        }

        $payment_status = array('1' => 'Success', '2' => 'Declined', '3' => 'Chargeback', '4' => 'Refund', '5' => 'Suspicious', '6' => 'Retrieval', '7' => 'Block');
        $payment_status_class = array('1' => 'text-success', '2' => 'text-danger', '3' => 'text-info', '4' => 'text-info', '5' => 'text-info', '6' => 'text-info', '7' => 'text-info');

        return view("WLAgent.report.payment_summary_report",compact('payment_gateway_id','arr_t_data', 'payment_status', 'payment_status_class'));
    }
}
