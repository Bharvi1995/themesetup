<?php

namespace App\Http\Controllers\Bank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Application;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BankMerchantsTransactionExport;
use App\Exports\MerchantsRefundTransactionExport;
use App\Exports\BankMerchantsApprovedTransactionExport;
use App\Exports\BankMerchantsDeclinedTransactionExport;
use App\Exports\BankMerchantsChargebackTransactionExport;
use App\Exports\BankMerchantsRefundTransactionExport;


class MerchantTransactionController extends BankUserBaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $bankApplicationStatus = bankApplicationStatus(auth()->guard('bankUser')->user()->id);
            if($bankApplicationStatus != 1){
                if($bankApplicationStatus == null){
                    return redirect()->route('bank.my-application.create');
                } else {
                    return redirect()->route('bank.my-application.detail');
                }
                
            }
            return $next($request);
        });
        view()->share('bankUserTheme', 'layouts.bank.default');
        parent::__construct();
        $this->transaction = new Transaction;
        $this->Application = new Application;
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $bankId = auth()->guard('bankUser')->user()->id;
        
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new BankMerchantsTransactionExport, 'Transaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $data = $this->transaction->getAllMerchantTransactionDataBank($input, $noList);
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);
        $businessName = [];
        if(isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $businessName = Application::join('users', 'users.id', 'applications.user_id')
                                ->whereIn('applications.user_id', $userWithMids['user_id'])
                                ->pluck('business_name', 'user_id')
                                ->toArray();
        }

        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        return view('bank.merchantTransactions.index', compact('businessName', 'data', 'payment_gateway_id'));
    }

    public function transactionDetails(Request $request)
    {
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);
        $data = [];
        if(isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $data = $this->transaction->where('id', $request->id)->whereIn('user_id', $userWithMids['user_id'])->first();
        }
        
        if(!empty($data)){

            $tab = "all";
            $html = view('bank.partials.transactions.single-transaction-sidebar', compact('data', 'tab'))->render();
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

    public function approved(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new BankMerchantsApprovedTransactionExport, 'Approvedtransaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getAllMerchantApprovedTransactionDataBank($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);
        $businessName = [];
        if(isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $businessName = Application::join('users', 'users.id', 'applications.user_id')
                            ->whereIn('applications.user_id', $userWithMids['user_id'])
                            ->pluck('business_name', 'user_id')
                            ->toArray();
        }

        return view('bank.merchantTransactions.approved', compact('businessName', 'data', 'payment_gateway_id'));
    }

    public function declined(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new BankMerchantsDeclinedTransactionExport, 'Declinedtransaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getAllMerchantDeclinedTransactionDataBank($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);
        $businessName = [];
        if(isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $businessName = Application::join('users', 'users.id', 'applications.user_id')
                            ->whereIn('applications.user_id', $userWithMids['user_id'])
                            ->pluck('business_name', 'user_id')
                            ->toArray();
        }
        return view('bank.merchantTransactions.declined', compact('businessName', 'data', 'payment_gateway_id'));
    }

    public function chargebacks(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new BankMerchantsChargebackTransactionExport, 'Chargebacktransaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getAllMerchantChargebacksTransactionDataBank($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();

        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);
        $businessName = [];
        if(isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $businessName = Application::join('users', 'users.id', 'applications.user_id')
                            ->whereIn('applications.user_id', $userWithMids['user_id'])
                            ->pluck('business_name', 'user_id')
                            ->toArray();
        }

        return view('bank.merchantTransactions.chargebacks', compact('businessName', 'data', 'payment_gateway_id'));
    }

    public function refund(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new BankMerchantsRefundTransactionExport, 'RefundTransaction_Excel_' . date('d-m-Y') . '.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $userIds = Application::join('users', 'users.id', 'applications.user_id')
                    ->join('application_assign_to_bank','applications.id','=','application_assign_to_bank.application_id')
                    ->where('application_assign_to_bank.bank_user_id', auth()->guard('bankUser')->user()->id)
                    ->where('application_assign_to_bank.deleted_at', null)
                    ->orderBy('application_assign_to_bank.created_at', 'desc')
                    ->distinct()->pluck('users.id');
        
        $payment_gateway_id = \DB::table('middetails')->whereNull('deleted_at')->get();
        $data = $this->transaction->getAllMerchantRefundTransactionDataBank($input, $noList);
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);
        $businessName = [];
        if(isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $businessName = Application::join('users', 'users.id', 'applications.user_id')
                            ->whereIn('applications.user_id', $userWithMids['user_id'])
                            ->pluck('business_name', 'user_id')
                            ->toArray();
        }
        return view('bank.merchantTransactions.refund', compact('businessName', 'data', 'payment_gateway_id'));
    }
    
}
