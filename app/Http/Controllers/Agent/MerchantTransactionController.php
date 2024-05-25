<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Application;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantsTransactionExport;
use App\Exports\TransactionExport;
use App\Exports\MerchantsRefundTransactionExport;
use App\Exports\MerchantsFlaggedTransactionExport;
use App\Exports\MerchantsChargebackTransactionExport;
use App\Exports\MerchantsRetrievalTransactionExport;

class MerchantTransactionController extends AgentUserBaseController
{
    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     if(RpApplicationStatus(auth()->guard('agentUser')->user()->id) != 1){
        //         return redirect()->route('rp.my-application.create');
        //     }
        //     return $next($request);
        // });
        view()->share('agentUserTheme', 'layouts.agent.default');
        parent::__construct();
        $this->transaction = new Transaction;
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new MerchantsTransactionExport, 'Payments_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $data = $this->transaction->getAllMerchantTransactionDataAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->where('users.agent_id', $agentId)
            ->pluck('business_name', 'user_id')
            ->toArray();
        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        return view('agent.merchantTransactions.index', compact('businessName', 'data', 'payment_gateway_id'));
    }

    public function show($id)
    {
        $data = $this->transaction->findData($id);
        return view('agent.merchantTransactions.show', compact('data'));
    }

    public function refund(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new MerchantsRefundTransactionExport, 'RefundTransaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        $data = $this->transaction->getAllMerchantRefundTransactionDataAgent($input, $noList);
        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->where('users.agent_id', $agentId)
            ->pluck('business_name', 'user_id')
            ->toArray();
        return view('agent.merchantTransactions.refund', compact('businessName', 'data', 'payment_gateway_id'));
    }

    public function chargebacks(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new MerchantsChargebackTransactionExport, 'Chargebacktransaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $data = $this->transaction->getAllMerchantChargebacksTransactionDataAgent($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->where('users.agent_id', $agentId)
            ->pluck('business_name', 'user_id')
            ->toArray();

        return view('agent.merchantTransactions.chargebacks', compact('businessName', 'data', 'payment_gateway_id'));
    }

    public function retrieval(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new MerchantsRetrievalTransactionExport, 'Retrievaltransaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $data = $this->transaction->getAllMerchantRetrievalTransactionDataAgent($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->where('users.agent_id', $agentId)
            ->pluck('business_name', 'user_id')
            ->toArray();
        return view('agent.merchantTransactions.retrieval', compact('data', 'payment_gateway_id', 'businessName'));
    }

    public function flagged(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new MerchantsFlaggedTransactionExport, 'Suspicious_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $data = $this->transaction->getAllMerchantFlaggedTransactionDataAgent($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->where('users.agent_id', $agentId)
            ->pluck('business_name', 'user_id')
            ->toArray();
        return view('agent.merchantTransactions.flagged', compact('businessName', 'data', 'payment_gateway_id'));
    }

    public function transactionDetails(Request $request)
    {
        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }
        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');
        $data = $this->transaction->where('id', $request->id)->whereIn('user_id', $userIds)->first();
        if(!empty($data)){

            $tab = "all";
            $html = view('agent.partials.transactions.single-transaction-sidebar', compact('data', 'tab'))->render();
            return response()->json([
                'success' => '1',
                'html' => $html
            ]);
        } else {
            return response()->json([
                'success' => '0',
                'html' => 'No user details found, please try again.'
            ]);
        }

    }
}
