<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Transaction;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SuspiciousTransactionsExport;
use App\Exports\TransactionsExport;
use App\Exports\TransactionsSummaryReportExport;
use App\Exports\MerchantTransactionsReportExport;
use App\Exports\PaymentStatusReportExport;
use App\Exports\CardSummaryReportExport;
use App\Exports\MidSummaryReportExport;
use App\Exports\MidSummaryReportOnCountryExport;
use App\Exports\MerchantTransactionsReasonReportExport;
use App\Jobs\SendSuspciousTransactionEmail;
use App\Mail\FlaggedTransactionMail;
use Mail;
use App\TxTransaction;
use PhpParser\Node\Expr\Empty_;
use App\MIDDetail;
use App\Application;
use App\Exports\MerchantReportExport;

class MerchantReportController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   
        parent::__construct();
        $this->tx_transaction = new TxTransaction;
        $this->transaction = new Transaction;
        $this->middetail = new MIDDetail;
        $this->application = new Application;
    }

    public function index(Request $request)
    {
        $input = $request->all();
        
        $data = $this->transaction->getMerchantTransactionReports($input);
        
        $CompanyName = "";
        if(isset($input['user_id']) && !empty($input['user_id'])){

            $companyDetails = \DB::table('applications')
            ->join('users','users.id','applications.user_id')
            ->where('users.id', $input['user_id'])->first();
            if(!empty($companyDetails)){
                $CompanyName = $companyDetails->business_name;
            }
        } else {
            $data = array();
        }

        $companyList = \DB::table('applications')
                        ->join('users','users.id','applications.user_id')
                        ->pluck('business_name','user_id')->toArray();

        return view("admin.reports.merchant-transaction-report",compact('companyList', 'CompanyName', 'data'));
    }

    public function merchantReportExport(Request $request)
    {
        $input = $request->all();
        return Excel::download(new MerchantReportExport(), 'Merchant_Transaction_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }
}
