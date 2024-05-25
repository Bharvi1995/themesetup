<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use App\DailySettlementReport;
use App\SettlementReport;
use App\Application;
use App\Transaction;
use App\PayoutReports;
use App\PayoutReportsChild;
use App\AdminAction;

class PaymentSettlementController extends AdminController
{

    protected $dailySettlementreport;
    protected $application;
    protected $transaction;
    public function __construct()
    {

        $this->dailySettlementreport = new DailySettlementReport();
        $this->settlementreport = new SettlementReport();
        $this->application = new Application();
        $this->transaction = new Transaction();
        $this->PayoutReports = new PayoutReports();
        $this->PayoutReportsChild = new PayoutReportsChild();

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $all = $request->all();

        $companyList = $this->application->getCompanyName();
        $getSettlementRepost = [];        
        $user = "";
        if( isset($all['user_id']) ){
            $user = \App\User::find( $all['user_id'] );
            $getSettlementRepostObject = $this->dailySettlementreport->where('user_id', $all['user_id'])
                ->where('paid', '=', '0')
                ->orderBy('id','DESC');
                if( isset($all['start_date']) ){
                    $startDate = date("y-m-d", strtotime( $all['start_date'] ));
                    $getSettlementRepostObject->where(\DB::raw('DATE(start_date)'), '>=', $startDate);
                }
                if( isset($all['end_date']) ){
                    $endDate = date("y-m-d", strtotime( $all['end_date'] ));
                    $getSettlementRepostObject->where(\DB::raw('DATE(end_date)'), '<=', $endDate);
                }
                $getSettlementRepost = $getSettlementRepostObject->paginate(10);
        }
        
        return view('admin.payment_settlement.index', compact('companyList', 'getSettlementRepost', 'user'));
    }

    public function settlementPAyoutReport( Request $request ){

        $all = $request->all();
        $companyList = $this->application->getCompanyName();
        $data = [];
        if( isset($all['user_id']) ){
            $getSettlementRepost = $this->settlementreport->where('user_id', $all['user_id'])->where('paid', '=', '0')->orderBy('id','DESC')->get();
            $data['totalSuccessAmount'] = 0;
            $data['totalSuccessCount'] = 0;
            $data['totalDeclinedAmount'] = 0;
            $data['totalDeclinedCount'] = 0;
            $data['chb_totalAmount'] = 0;
            $data['chb_totalCount'] = 0;
            $data['sus_totalAmount'] = 0;
            $data['sus_totalCount'] = 0;
            $data['refund_totalAmount'] = 0;
            $data['refund_totalCount'] = 0;
            $data['ret_totalAmount'] = 0;            
            $data['ret_totalCount'] = 0;            
            $data['preat_totalAmount'] = 0;            
            $data['preat_totalCount'] = 0;            
            $data['preat_totalCount'] = 0;            
            $data['total_transactions'] = 0;            
            $data['mdr_amount'] = 0;            
            $data['reserve_amount'] = 0;            
            $data['transactionsfees'] = 0;            
            $data['refund_fees'] = 0;            
            $data['highrisk_fees'] = 0;            
            $data['chb_fees'] = 0;            
            $data['retreival_fees'] = 0;
            $data['payable_amount'] = 0;
            $data['gross_payable'] = 0;
            $data['net_payable'] = 0;

            $i = 0;
            foreach( $getSettlementRepost as $key => $value ){
                
                $data['totalSuccessAmount'] += $value->totalSuccessAmount;            
                $data['totalSuccessCount'] += $value->totalSuccessCount;            
                $data['totalDeclinedAmount'] += $value->totalDeclinedAmount;            
                $data['totalDeclinedCount'] += $value->totalDeclinedCount;
                $data['chb_totalAmount'] += $value->chbtotalAmount;
                $data['chb_totalCount'] += $value->chbtotalCount;            
                $data['sus_totalAmount'] += $value->suspicioustotalAmount;            
                $data['sus_totalCount'] += $value->suspicioustotalCount;            
                $data['refund_totalAmount'] += $value->refundtotalCount;            
                $data['refund_totalCount'] += $value->refundtotalAmount;            
                $data['ret_totalAmount'] += $value->retreivaltotalAmount;            
                $data['ret_totalCount'] += $value->retreivaltotalCount;            
                $data['preat_totalAmount'] += $value->prearbitrationtotalAmount;            
                $data['preat_totalCount'] += $value->prearbitrationtotalCount;            
                $data['total_transactions'] += $value->total_transactions;            
                $data['mdr_amount'] += $value->mdr_amount;            
                $data['reserve_amount'] += $value->transactionsfees;            
                $data['transactionsfees'] += $value->refund_fees;            
                $data['refund_fees'] += $value->highrisk_fees;            
                $data['highrisk_fees'] += $value->chb_fees;            
                $data['chb_fees'] += $value->retreival_fees;            
                $data['retreival_fees'] += $value->reserve_amount;
                $data['payable_amount'] += $value->total_payable;

                if( $i >= 1 ){
                    $data['gross_payable'] += $value->total_payable;
                }
                if( $i >= 2 ){
                    $data['net_payable'] += $value->total_payable;
                }

                $i++;
            
            }
        }

        return view('admin.payment_settlement.report', compact('companyList', 'data'));

    }

    public function fethDailyUserReport( Request $request ){

        $users = $this->transaction->select('user_id')->whereNotIn('payment_gateway_id', [1,2])->groupBy('user_id')->get();
        
        foreach( $users as $user ){

            $bulkchargeback = (new \App\Jobs\MerchantDailyPayoutReportQueue( $user->user_id ))->delay(now()->addSeconds(2));
            dispatch($bulkchargeback);

        }

    }
    
    public function fethUserReport( Request $request ){

        $users = $this->transaction->select('user_id')->whereNotIn('payment_gateway_id', [1,2])->groupBy('user_id')->get();
        
        foreach( $users as $user ){

            $bulkchargeback = (new \App\Jobs\MerchantPayoutReportQueue( $user->user_id ))->delay(now()->addSeconds(2));
            dispatch($bulkchargeback);

        }

    }

    public function autoPayoutReportStore( Request $request ){
        $user_id = $request->get('user_id');
        $last_date = $request->get('start_date');
        
        $last_report_date = $this->dailySettlementreport->where('user_id', '=', $user_id)
            ->where('paid', '=', '0')
            ->orderBy('id', 'ASC')
            ->first();
        $start_date = $last_report_date->end_date;
        
        $startDate = date('Y-m-d', strtotime($start_date));
        $endDate = date('Y-m-d', strtotime($last_date));
        $chargebacksStartDate = date('Y-m-d', strtotime($start_date));
        $chargebacksEndDate = date('Y-m-d', strtotime($last_date));
        $input['user_id'] = $user_id;

        return app('App\Http\Controllers\Admin\PayoutReportController')->generatePayoutReport( $input, $startDate, $endDate, $chargebacksStartDate, $chargebacksEndDate, $this->dailySettlementreport );

    }

    public function reGenerateCalculation( Request $request ){

        $date = $request->get('date');
        $user_id = $request->get('user_id');
        
        $bulkchargeback = (new \App\Jobs\MerchantDailyPayoutReportQueue( $user_id, $date ))->delay(now()->addSeconds(2));
        dispatch($bulkchargeback);

        return redirect()->back();

    }

    public function viewTillDateReport( Request $request ){

        $date = $request->get('date');
        $user_id = $request->get('user_id');
        
        $getSettlementRepost = $this->dailySettlementreport->where('user_id', $user_id)
            ->where('paid', '=', '0')
            ->where(\DB::raw('DATE(start_date)'), '<=', $date)
            ->orderBy('id','DESC')
            ->paginate(10);

        $data['totalSuccessAmount'] = 0;
        $data['totalSuccessCount'] = 0;
        $data['totalDeclinedAmount'] = 0;
        $data['totalDeclinedCount'] = 0;
        $data['chb_totalAmount'] = 0;
        $data['chb_totalCount'] = 0;
        $data['sus_totalAmount'] = 0;
        $data['sus_totalCount'] = 0;
        $data['refund_totalAmount'] = 0;
        $data['refund_totalCount'] = 0;
        $data['ret_totalAmount'] = 0;            
        $data['ret_totalCount'] = 0;            
        $data['preat_totalAmount'] = 0;            
        $data['preat_totalCount'] = 0;            
        $data['preat_totalCount'] = 0;            
        $data['total_transactions'] = 0;            
        $data['mdr_amount'] = 0;            
        $data['reserve_amount'] = 0;            
        $data['transactionsfees'] = 0;            
        $data['refund_fees'] = 0;            
        $data['highrisk_fees'] = 0;            
        $data['chb_fees'] = 0;            
        $data['retreival_fees'] = 0;
        $data['payable_amount'] = $getSettlementRepost->first()->total_payable;
        $data['gross_payable'] = $getSettlementRepost->first()->gross_payable;
        $data['net_payable'] = $getSettlementRepost->first()->net_payable;

        foreach( $getSettlementRepost as $key => $value ){
            $data['totalSuccessAmount'] += $value->totalSuccessAmount;            
            $data['totalSuccessCount'] += $value->totalSuccessCount;            
            $data['totalDeclinedAmount'] += $value->totalDeclinedAmount;            
            $data['totalDeclinedCount'] += $value->totalDeclinedCount;
            $data['chb_totalAmount'] += $value->chbtotalAmount;
            $data['chb_totalCount'] += $value->chbtotalCount;            
            $data['sus_totalAmount'] += $value->suspicioustotalAmount;            
            $data['sus_totalCount'] += $value->suspicioustotalCount;            
            $data['refund_totalAmount'] += $value->refundtotalCount;            
            $data['refund_totalCount'] += $value->refundtotalAmount;            
            $data['ret_totalAmount'] += $value->retreivaltotalAmount;            
            $data['ret_totalCount'] += $value->retreivaltotalCount;            
            $data['preat_totalAmount'] += $value->prearbitrationtotalAmount;            
            $data['preat_totalCount'] += $value->prearbitrationtotalCount;            
            $data['total_transactions'] += $value->total_transactions;            
            $data['mdr_amount'] += $value->mdr_amount;            
            $data['reserve_amount'] += $value->transactionsfees;            
            $data['transactionsfees'] += $value->refund_fees;            
            $data['refund_fees'] += $value->highrisk_fees;            
            $data['highrisk_fees'] += $value->chb_fees;            
            $data['chb_fees'] += $value->retreival_fees;            
            $data['retreival_fees'] += $value->reserve_amount;
        }

        return view('admin.payment_settlement.report', compact('data'));
    }

}
