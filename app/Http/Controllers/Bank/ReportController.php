<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Transaction;
use App\Application;
use App\MIDDetail;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\ApplicationAssignToBank;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BankMerchantVolumeReportExport;

class ReportController extends BankUserBaseController
{
    public function __construct()
    {   
        parent::__construct();
        
        $this->Transaction = new Transaction;
        $this->Application = new Application;
    }

    public function riskReport(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $bank_id = auth()->guard('bankUser')->user()->id;

        $applications_id = ApplicationAssignToBank::where('bank_user_id',$bank_id)->where('status','1')->pluck('application_id');

        $payment_gateway_id = MIDDetail::where('bank_id',$bank_id)->pluck('id');
        $input['payment_gateway_id'] = $payment_gateway_id;

        $users = Application::whereIn('id',$applications_id)->select('user_id','business_name')->get();

        if(!$users){
            $data = [];
        }else{
            foreach($users as $key=>$user){
                $input['user_id'] = $user->user_id;
                $data[$key]['merchant_name'] = $user->business_name;
                $data[$key]['data'] = $this->Transaction->getRiskComplianceReportData($input);
            }
        }

        return view('bank.report.risk_report',compact('data'));
    }

    public function merchantVolumeReport(Request $request)
    {
        $input = $request->all();
        if(isset($_GET['user_id']) && !empty(trim($_GET['user_id']))) {
            $input['user_id'] = trim($_GET['user_id']);
        }
        if(isset($_GET['currency']) && !empty(trim($_GET['currency']))) {
            $input['currency'] = trim($_GET['currency']);
        }
        if(isset($_GET['start_date']) && !empty(trim($_GET['start_date']))) {
            $input['start_date'] = trim($_GET['start_date']);
        }
        if(isset($_GET['end_date']) && !empty(trim($_GET['end_date']))) {
            $input['end_date'] = trim($_GET['end_date']);
        }
        $transactions_summary = $this->Transaction->getTransactionBankMerchantVolume($input);
        $totalAmtInUSD = number_format(array_sum(array_column($transactions_summary, 'success_amount_in_usd')), 2);
        $payment_gateway_id = \DB::table('middetails')->get();
        
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);
        $companyName = [];
        if(isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $companyName = \DB::table('applications')
                            ->join('users','users.id','applications.user_id')
                            ->whereIn('applications.user_id', $userWithMids['user_id'])
                            ->pluck('business_name','user_id')->toArray();
        }

        return view("bank.report.merchant_volume",compact('payment_gateway_id','companyName', 'transactions_summary','totalAmtInUSD'));
    }

    public function merchantVolumeReportExport(Request $request)
    {
        $input = $request->all();
        return Excel::download(new BankMerchantVolumeReportExport(), 'Merchant_Volume_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }
}
