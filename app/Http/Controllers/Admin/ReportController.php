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

class ReportController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $tx_transaction, $transaction, $middetail, $application;

    public function __construct()
    {
        parent::__construct();
        $this->tx_transaction = new TxTransaction;
        $this->transaction = new Transaction;
        $this->middetail = new MIDDetail;
        $this->application = new Application;
    }

    public function transactionsSummaryReport(Request $request)
    {
        $input = $request->all();
        if (isset($_GET['user_id']) && !empty(trim($_GET['user_id']))) {
            $input['user_id'] = trim($_GET['user_id']);
        }
        if (isset($_GET['currency']) && !empty(trim($_GET['currency']))) {
            $input['currency'] = trim($_GET['currency']);
        }
        if (isset($_GET['start_date']) && !empty(trim($_GET['start_date']))) {
            $input['start_date'] = trim($_GET['start_date']);
        }
        if (isset($_GET['end_date']) && !empty(trim($_GET['end_date']))) {
            $input['end_date'] = trim($_GET['end_date']);
        }
        $transactions_summary = $this->transaction->getTransactionSummaryRP($input);
        //echo "<pre>";print_r($transactions_summary);exit();
        $totalAmtInUSD = number_format(array_sum(array_column($transactions_summary, 'success_amount_in_usd')), 2);
        $payment_gateway_id = \DB::table('middetails')->get();

        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();

        return view("admin.reports.transaction_summary", compact('payment_gateway_id', 'companyName', 'transactions_summary', 'totalAmtInUSD'));
    }

    public function transactionsSummaryReport2(Request $request)
    {
        $input = $request->all();
        if (isset($_GET['user_id']) && !empty(trim($_GET['user_id']))) {
            $input['user_id'] = trim($_GET['user_id']);
        }
        if (isset($_GET['currency']) && !empty(trim($_GET['currency']))) {
            $input['currency'] = trim($_GET['currency']);
        }
        if (isset($_GET['start_date']) && !empty(trim($_GET['start_date']))) {
            $input['start_date'] = trim($_GET['start_date']);
        }
        if (isset($_GET['end_date']) && !empty(trim($_GET['end_date']))) {
            $input['end_date'] = trim($_GET['end_date']);
        }
        $transactions_summary = $this->transaction->getTransactionSummaryRP2($input);
        // echo "<pre>";print_r($transactions_summary);exit();
        $totalAmtInUSD = number_format(array_sum(array_column($transactions_summary, 'success_amount_in_usd')), 2);
        $payment_gateway_id = \DB::table('middetails')->get();

        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();

        return view("admin.reports.transaction_summary", compact('payment_gateway_id', 'companyName', 'transactions_summary', 'totalAmtInUSD'));
    }

    public function transactionsSummaryReportExcle(Request $request)
    {

        try {
            return (new TransactionsSummaryReportExport())->download();
        } catch (\Throwable $th) {
            dd($th);
        }

        //        return Excel::download(new TransactionsSummaryReportExport(), 'Transaction_Summary_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function suspiciousReport(Request $request)
    {
        $businessName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();
        $payment_gateway_id = \DB::table('middetails')->get();
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $finalId = [];
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $data = [];

        $payment_gateway = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if (isset($input['start_date']) && $input['start_date'] != '' && isset($input['end_date']) && $input['end_date'] != '') {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
            if (isset($input['include_email']) && $input['include_email'] == 'yes' && isset($input['nos_email']) && $input['nos_email'] > 0 && isset($input['include_card']) && $input['include_card'] == 'yes' && isset($input['nos_card']) && $input['nos_card'] > 0) {
                $includeArray = DB::table("transactions as t")
                    ->leftjoin("middetails as m", "m.id", "=", "t.payment_gateway_id")
                    ->leftjoin("applications as a", "a.user_id", "=", "t.user_id")
                    ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
                    ->whereNotIn('t.payment_gateway_id', $payment_gateway)
                    ->where(["t.is_flagged" => "0", "t.is_flagged_remove" => "0", "t.refund" => "0", "t.refund_remove" => "0", "t.chargebacks" => "0", "t.chargebacks_remove" => "0", "t.is_retrieval" => "0", "t.is_retrieval_remove" => "0"])
                    ->where('t.status', '1')
                    ->groupBy('t.card_no', 't.email')
                    ->having(DB::raw('count(t.card_no)'), '>=', $input['nos_card'])
                    ->having(DB::raw('count(t.email)'), '>=', $input['nos_email'])
                    ->pluck('t.card_no', 't.email')
                    ->toArray();
                $emailArray = array_keys($includeArray);
                $cardArray = array_values($includeArray);
                $data = DB::table("transactions as t")->select('t.id', 't.card_no', "t.order_id", "t.amount", "t.currency", "t.transaction_date", "m.bank_name", "t.email", "a.business_name")
                    ->leftjoin("middetails as m", "m.id", "=", "t.payment_gateway_id")
                    ->leftjoin("applications as a", "a.user_id", "=", "t.user_id")
                    ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
                    ->whereNotIn('t.payment_gateway_id', $payment_gateway)
                    ->where(["t.is_flagged" => "0", "t.is_flagged_remove" => "0", "t.refund" => "0", "t.refund_remove" => "0", "t.chargebacks" => "0", "t.chargebacks_remove" => "0", "t.is_retrieval" => "0", "t.is_retrieval_remove" => "0"])
                    ->where('t.status', '1');
                $data = $this->filterData($input, $data);
                $data = $data->whereIn('t.email', $emailArray)->whereIn('t.card_no', $cardArray);
                $data = $data->paginate($noList);
            } elseif (isset($input['include_card']) && $input['include_card'] == 'yes' && isset($input['nos_card']) && $input['nos_card'] > 0) {
                $data = $this->cardRecord($input, $noList);
            } else if (isset($input['include_email']) && $input['include_email'] == 'yes' && isset($input['nos_email']) && $input['nos_email'] > 0) {
                $data = $this->emailRecord($input, $noList);
            } else {
                $data = DB::table("transactions as t")->select('t.id', 't.card_no', "t.order_id", "t.amount", "t.currency", "t.transaction_date", "m.bank_name", "t.email", "a.business_name")
                    ->leftjoin("middetails as m", "m.id", "=", "t.payment_gateway_id")
                    ->leftjoin("applications as a", "a.user_id", "=", "t.user_id")
                    ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
                    ->whereNotIn('t.payment_gateway_id', $payment_gateway)
                    ->where(["t.is_flagged" => "0", "t.is_flagged_remove" => "0", "t.refund" => "0", "t.refund_remove" => "0", "t.chargebacks" => "0", "t.chargebacks_remove" => "0", "t.is_retrieval" => "0", "t.is_retrieval_remove" => "0"])
                    ->where('t.status', '1');
                $data = $this->filterData($input, $data);
                $data = $data->paginate($noList);
            }
        } else {
            notificationMsg('warning', 'Please select start date and end date.');
            $data = [];
        }
        $arrId = [];
        if (!empty($data)) {
            $arrId = $data->pluck('id')->all();
        }
        return view("admin.reports.suspicious_summary", compact('businessName', 'payment_gateway_id', 'data', 'arrId'));
    }

    public function filterData($input, $data)
    {
        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('t.country', $input['country']);
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('t.currency', $input['currency']);
        }
        if (isset($input['gateway_id']) && $input['gateway_id'] != '') {
            $data = $data->where('t.gateway_id', $input['gateway_id']);
        }
        if (isset($input['greater_then']) && $input['greater_then'] != '') {
            $data = $data->where('t.amount', '>=', $input['greater_then']);
        }
        if (isset($input['less_then']) && $input['less_then'] != '') {
            $data = $data->where('t.amount', '<=', $input['less_then']);
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('t.payment_gateway_id', $input['payment_gateway_id']);
        }
        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('t.user_id', $input['user_id']);
        }
        return $data;
    }

    public function emailRecord($input, $noList)
    {
        $payment_gateway = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];
        $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
        $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        $emailArray = DB::table("transactions as t")
            ->leftjoin("middetails as m", "m.id", "=", "t.payment_gateway_id")
            ->leftjoin("applications as a", "a.user_id", "=", "t.user_id")
            ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
            ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway)
            ->where(["t.is_flagged" => "0", "t.is_flagged_remove" => "0", "t.refund" => "0", "t.refund_remove" => "0", "t.chargebacks" => "0", "t.chargebacks_remove" => "0", "t.is_retrieval" => "0", "t.is_retrieval_remove" => "0"])
            ->where('t.status', '1')
            ->groupBy('email')
            ->having(DB::raw('count(t.email)'), '>=', $input['nos_email'])
            ->pluck('t.email', DB::raw('COUNT(t.email) as email_count'))
            ->toArray();

        $data = DB::table("transactions as t")->select('t.id', 't.card_no', "t.order_id", "t.amount", "t.currency", "t.transaction_date", "m.bank_name", "t.email", "a.business_name")
            ->leftjoin("middetails as m", "m.id", "=", "t.payment_gateway_id")
            ->leftjoin("applications as a", "a.user_id", "=", "t.user_id")
            ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
            ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway)
            ->where(["t.is_flagged" => "0", "t.is_flagged_remove" => "0", "t.refund" => "0", "t.refund_remove" => "0", "t.chargebacks" => "0", "t.chargebacks_remove" => "0", "t.is_retrieval" => "0", "t.is_retrieval_remove" => "0"])
            ->where('t.status', '1');
        $data = $this->filterData($input, $data);
        $data = $data->whereIn('t.email', $emailArray);
        $data = $data->paginate($noList);
        return $data;
    }

    public function cardRecord($input, $noList)
    {
        $payment_gateway = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];
        $finalId = [];
        $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
        $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        $cardArray = DB::table("transactions as t")
            ->leftjoin("middetails as m", "m.id", "=", "t.payment_gateway_id")
            ->leftjoin("applications as a", "a.user_id", "=", "t.user_id")
            ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
            ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway)
            ->where(["t.is_flagged" => "0", "t.is_flagged_remove" => "0", "t.refund" => "0", "t.refund_remove" => "0", "t.chargebacks" => "0", "t.chargebacks_remove" => "0", "t.is_retrieval" => "0", "t.is_retrieval_remove" => "0"])
            ->where('t.status', '1')
            ->groupBy('t.card_no')
            ->having(DB::raw('count(t.card_no)'), '>=', $input['nos_card'])
            ->pluck('t.card_no', DB::raw('COUNT(t.card_no) as card_no_count'))
            ->toArray();
        $data = DB::table("transactions as t")->select('t.id', 't.card_no', "t.order_id", "t.amount", "t.currency", "t.transaction_date", "m.bank_name", "t.email", "a.business_name")
            ->leftjoin("middetails as m", "m.id", "=", "t.payment_gateway_id")
            ->leftjoin("applications as a", "a.user_id", "=", "t.user_id")
            ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
            ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway)
            ->where(["t.is_flagged" => "0", "t.is_flagged_remove" => "0", "t.refund" => "0", "t.refund_remove" => "0", "t.chargebacks" => "0", "t.chargebacks_remove" => "0", "t.is_retrieval" => "0", "t.is_retrieval_remove" => "0"])
            ->where('t.status', '1');
        $data = $this->filterData($input, $data);
        $data = $data->whereIn('t.card_no', $cardArray);
        $data = $data->paginate($noList);
        return $data;
    }

    public function exportAllSuspicious(Request $request)
    {
        return Excel::download(new SuspiciousTransactionsExport($request->ids), 'SuspiciousTransaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function startFlag(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['selected']) && $input['selected'] == 'yes') {
            $transaction_ids = explode(',', $input['ids']);
            Transaction::whereIn('id', $transaction_ids)
                ->update([
                    'is_flagged' => '1',
                    'flagged_date' => date('Y-m-d H:i:s'),
                    'transaction_date' => date('Y-m-d H:i:s'),
                    'flagged_by' => $input['flagged_by']
                ]);
            $Arrtransaction = Transaction::select(
                'transactions.card_type as card_type',
                'transactions.id as id',
                'transactions.card_no as card_no',
                'transactions.flagged_date as flagged_date',
                'transactions.amount as amount',
                'transactions.email as email',
                'transactions.first_name as first_name',
                'transactions.last_name as last_name',
                'transactions.currency as currency',
                'transactions.order_id',
                'transactions.created_at as created_at',
                'users.id as user_id',
                'users.email as user_email'
            )
                ->join('users', 'users.id', 'transactions.user_id')
                ->whereIn('transactions.id', $transaction_ids)->get();
            if (!empty($Arrtransaction)) {
                foreach ($Arrtransaction as $key => $transaction) {

                    $input['card_type'] = 'N/A';
                    if ($transaction->card_type == '1') {
                        $input['card_type'] = 'Amex';
                    } else if ($transaction->card_type == '2') {
                        $input['card_type'] = 'Visa';
                    } else if ($transaction->card_type == '3') {
                        $input['card_type'] = 'Mastercard';
                    } else if ($transaction->card_type == '4') {
                        $input['card_type'] = 'Discover';
                    }

                    if (strlen($transaction->card_no) > 4) {
                        $transaction->card_no = 'XXXXXXXXXXXX' . substr($transaction->card_no, -4);
                    } else {
                        $transaction->card_no = $transaction->card_no;
                    }

                    $input['order_id'] = $transaction->order_id;
                    $input['first_name'] = $transaction->first_name;
                    $input['last_name'] = $transaction->last_name;
                    $input['email'] = $transaction->email;
                    $input['amount'] = $transaction->amount;
                    $input['card_no'] = $transaction->card_no;
                    $input['currency'] = $transaction->currency;
                    $input['flagged_date'] = $transaction->flagged_date;
                    $input['created_at'] = $transaction->created_at;
                    $token = $transaction->id . \Str::random(32);

                    $input['url'] = \URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=flagged&token=' . $token;
                    Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
                    $input['user_email'] = $transaction->user_email;
                    // $user_email =  $transaction->user_email;s
                    try {
                        \App\Jobs\SendSuspciousTransactionEmail::dispatch($input);
                        // Mail::to($user_email)->queue(new FlaggedTransactionMail($input));
                    } catch (\Exception $e) {
                        dd($e->getMessage());
                    }
                }
            }
            notificationMsg('success', 'Transaction suspicious completed successfully.');
            return back();
        } else {
            notificationMsg('warning', 'Please select atleast one row.');
            return back();
        }
    }

    public function merchantTransactionsReport(Request $request)
    {
        $input = $request->all();
        $input['by_merchant'] = 1;
        // $merchant_transactions = $this->transaction->getMerchantTransactionReport($input);

        $transactions_summary = $this->transaction->getTransactionSummaryRP($input);

        $payment_gateway_id = \DB::table('middetails')->get();

        $companyName = \DB::table('applications')->join('users', 'users.id', 'applications.user_id')->pluck('business_name', 'user_id')->toArray();
        return view("admin.reports.merchant_transaction", compact('payment_gateway_id', 'companyName', 'transactions_summary'));
    }

    public function merchantTransactionsReportExcle(Request $request)
    {
        return (new MerchantTransactionsReportExport())->download();
        //        return Excel::download(new MerchantTransactionsReportExport(), 'Merchant_Transaction_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function summaryReport()
    {
        return view("admin.reports.summary_report");
    }

    public function cardSummaryReport(Request $request)
    {
        $input = $request->all();
        // $transactions_summary = $this->transaction->getCardSummaryReport($input);
        if (empty($input)) {
            $input['for'] = 'Daily';
        }
        $input['groupBy'] = 'card_type';
        $input['SelectFields'] = ['card_type'];
        $transactionssummary = $this->transaction->getSummaryReportData($input);
        $transactions_summary = $this->transaction->PorcessSumarryData('CardTypeSumamry', $transactionssummary);

        $payment_gateway_id = \DB::table('middetails')->get();

        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();

        $card_type = config('card.type');

        return view("admin.reports.card_summary_report", compact('payment_gateway_id', 'companyName', 'transactions_summary', 'card_type'));
    }

    public function cardSummaryReportExcel(Request $request)
    {
        $input = $request->all();
        return Excel::download(new CardSummaryReportExport(), 'Card_Summary_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function paymentStatusSummaryReport(Request $request)
    {
        $input = $request->all();
        $merchant_transactions = array();
        $arr_t_data = array();

        if ($input) {
            // $merchant_transactions = $this->transaction->getMerchantTransactionReport($input);
            // if(!empty($merchant_transactions)) {
            //     foreach ($merchant_transactions as $k => $v) {
            //         $arr_t_data[$v['user_id']][] = $v;
            //     }
            // }
        }
        if ($input) {
            $input['groupBy'] = ['transactions.user_id', 'transactions.currency'];
            $input['SelectFields'] = ['transactions.user_id', 'applications.business_name'];
            $input['JoinTable'] = [
                'table' => 'applications',
                'condition' => 'applications.user_id',
                'conditionjoin' => 'transactions.user_id'
            ];

            $transactionssummary = $this->transaction->getSummaryReportData($input);
            $arr_t_data = $this->transaction->PorcessSumarryData('PaymentSsummary', $transactionssummary);
        }

        $payment_gateway_id = \DB::table('middetails')->get();
        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();
        $payment_status = array('1' => 'Success', '2' => 'Declined', '3' => 'Chargeback', '4' => 'Refund', '5' => 'Suspicious', '6' => 'Retrieval', '7' => 'Block');
        $payment_status_class = array('1' => 'text-success', '2' => 'text-danger', '3' => 'text-info', '4' => 'text-info', '5' => 'text-info', '6' => 'text-info', '7' => 'text-info');
        return view("admin.reports.payment_summary_report", compact('payment_gateway_id', 'companyName', 'arr_t_data', 'payment_status', 'payment_status_class'));
    }

    public function paymentStatusSummaryReportExcel(Request $request)
    {
        //        dd($request->all());
        return (new PaymentStatusReportExport($request->all()))->download();
        //        return Excel::download(new PaymentStatusReportExport(), 'Payment_Status_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function midSummaryReport(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            $input['for'] = 'Daily';
        }
        $input['groupBy'] = ['transactions.payment_gateway_id', 'transactions.currency'];
        $input['SelectFields'] = ['transactions.payment_gateway_id', 'middetails.bank_name'];
        $input['JoinTable'] = [
            'table' => 'middetails',
            'condition' => 'middetails.id',
            'conditionjoin' => 'transactions.payment_gateway_id'
        ];
        $transactionssummary = $this->transaction->getSummaryReportData($input);
        $transactions_summary = $this->transaction->PorcessSumarryData('midSummary', $transactionssummary);

        // $transactions_summary = $this->transaction->getMidSummaryReport($input);
        $payment_gateway_id = \DB::table('middetails')->get();
        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();

        // $arr_t_data = array();
        // if(!empty($transactions_summary)) {
        //     foreach ($transactions_summary as $k => $v) {
        //         $arr_t_data[$v['bank_name']][] = $v;
        //     }
        // }

        return view("admin.reports.mid_summary_report", compact('payment_gateway_id', 'companyName', 'transactions_summary'));
    }

    public function midSummaryReportOnCountry(Request $request)
    {
        $input = $request->all();
        if (empty($input)) {
            $input['for'] = 'Daily';
        }
        $input['groupBy'] = ['transactions.country'];
        $input['SelectFields'] = ['transactions.country'];
        $transactionssummary = $this->transaction->getSummaryReportData($input);
        $transactions_summary = $this->transaction->PorcessSumarryData('CountrySummary', $transactionssummary);
        // $transactions_summary = $this->transaction->getMidSummaryReportOnCountry($input);
        $payment_gateway_id = \DB::table('middetails')->get();
        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();

        return view("admin.reports.mid_summary_report_on_country", compact('payment_gateway_id', 'companyName', 'transactions_summary'));
    }

    public function midSummaryReportExcel(Request $request)
    {
        $input = $request->all();
        return Excel::download(new MidSummaryReportExport(), 'Mid_Summary_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function midSummaryReportOnCountryExcel(Request $request)
    {
        $input = $request->all();
        return Excel::download(new MidSummaryReportOnCountryExport(), 'Mid_Summary_Report_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function reasonReport(Request $request)
    {
        $businessName = $this->application->getTransactionData();
        $payment_gateway_id = $this->middetail->whereNotIn('id', [1, 2])->get();

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $finalId = [];
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }

        $data = $this->transaction->getReasonReportData($input);

        //$currencies = $data->unique('currency')->pluck('currency')->toArray();

        //dd($currencies);

        $data = $data->groupby('bank_name');

        //dd($data);
        $arrId = [];
        $authUser = auth()->guard('admin')->user();
        if ($authUser != '') {
            // addToAdminLog('transactions', "Reason report get", $authUser, 'success');
        }

        $diff_count = $total_txn = $success_fail_count = $reason_count = [];

        if (!empty($data)) {

            $arrId = $data->pluck('id')->all();

            foreach ($data as $ky => $val) {
                $data[$ky] = $val->groupby('reason');

                $diff_count[$ky] = $val->count();

                $total_txn[$ky] = $val->sum('transaction_count');

                $success_fail_count[$ky] = $val->groupby('txn_status')->sortKeys();

                foreach ($success_fail_count[$ky] as $stat => $dtls) {
                    $success_fail_count[$ky][$stat] = $dtls->sum('transaction_count');
                }

                foreach ($data[$ky] as $reason => $details) {
                    $data[$ky][$reason] = $details->groupby('card_type');

                    $reason_count[$ky][$reason] = $details->count();
                }
            }
        }

        return view(
            'admin.reports.reason_report',
            compact(
                'businessName',
                'payment_gateway_id',
                'data',
                'arrId',
                'total_txn',
                'success_fail_count',
                'diff_count',
                //'currencies',
                'reason_count'
            )
        );
    }

    public function merchantReasonReport(Request $request)
    {
        $businessName = $this->application->getTransactionData();
        $payment_gateway_id = $this->middetail->whereNotIn('id', [1, 2])->get();

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $finalId = [];
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }

        $data = $this->transaction->getMerchantReasonReportData($input);

        $data = $data->groupby('merchant_name');

        //dd($data);

        $arrId = [];
        $authUser = auth()->guard('admin')->user();
        if ($authUser != '') {
            // addToAdminLog('transactions', "Reason report get", $authUser, 'success');
        }

        $diff_count = $bank_name_txn_cnt = $total_txn = $success_fail_count = $reason_count = [];

        if (!empty($data)) {

            $arrId = $data->pluck('id')->all();

            foreach ($data as $ky => $val) {
                $total_txn[$ky] = $val->sum('transaction_count');

                $data[$ky] = $val->groupby('bank_name');

                $diff_count[$ky] = $val->count();

                $success_fail_count[$ky] = $val->groupby('txn_status')->sortKeys();

                foreach ($data[$ky] as $k => $vl) {
                    $bank_name_txn_cnt[$ky][$k] = $vl->groupby('txn_status')->sortKeys();

                    foreach ($bank_name_txn_cnt[$ky][$k] as $ke => $va) {
                        $bank_name_txn_cnt[$ky][$k][$ke] = $va->sum('transaction_count');
                    }
                }

                foreach ($success_fail_count[$ky] as $stat => $dtls) {
                    $success_fail_count[$ky][$stat] = $dtls->sum('transaction_count');
                }
            }
        }


        return view(
            'admin.reports.merchant_reason_report',
            compact(
                'businessName',
                'data',
                'arrId',
                'total_txn',
                'success_fail_count',
                'bank_name_txn_cnt',
                'diff_count'
            )
        );
    }

    public function merchantTransactionReasonReportExcle(Request $request)
    {
        $input = $request->all();

        $authUser = auth()->guard('admin')->user();
        if ($authUser != '') {
            // addToAdminLog('transaction', "Export merchant transaction report excel", $authUser, 'success');
        }

        return (new MerchantTransactionsReasonReportExport())->download();
        //        return Excel::download(new MerchantTransactionsReasonReportExport(), 'Merchant_Transactions_Reason_Report_Excel_' . date('d-m-Y_H_i_s') . '.xlsx');
    }

    public function merchantApprovalReport(Request $request)
    {
        $businessName = $this->application->getTransactionData();
        $payment_gateway_id = $this->middetail->whereNotIn('id', [1, 2])->get();

        //dd($payment_gateway_id);

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $finalId = [];
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }

        $data = $this->transaction->getMerchantTxnApprovalReportData($input);

        $data = $data->groupby('bank_name');

        //dd($data);
        $arrId = [];
        $authUser = auth()->guard('admin')->user();
        if ($authUser != '') {
            // addToAdminLog('transactions', "Reason report get", $authUser, 'success');
        }

        $diff_count = $total_txn = $success_fail_count = $reason_count = [];

        if (!empty($data)) {

            $arrId = $data->pluck('id')->all();

            foreach ($data as $ky => $val) {
                $data[$ky] = $val->groupby('business_name');

                $diff_count[$ky] = $val->count();

                $success_fail_count[$ky]['success'] = $val->sum('success_count');
                $success_fail_count[$ky]['decline'] = $val->sum('declined_count');

                foreach ($data[$ky] as $business => $details) {
                    $data[$ky][$business] = $details->groupby('card_type');
                }
            }
        }

        return view(
            'admin.reports.transaction_approval_report',
            compact(
                'businessName',
                'payment_gateway_id',
                'data',
                'arrId',
                'success_fail_count',
                'diff_count'
            )
        );
    }

    public function countrywiseTransactionReport(Request $request)
    {
        $businessName = $this->application->getTransactionData();
        $countries = $this->transaction->getAllTransactionCountry();
        $payment_gateway_id = $this->middetail->whereNotIn('id', [1, 2])->get();

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $finalId = [];
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }

        $data = $this->transaction->getCountryWiseTxnReportData($input);

        $data = $data->groupby('bank_name');

        $arrId = [];
        $authUser = auth()->guard('admin')->user();
        if ($authUser != '') {
            // addToAdminLog('transactions', "Country-wise Transaction Report get", $authUser, 'success');
        }

        $diff_count = $total_txn = $success_fail_count = [];

        if (!empty($data)) {

            $arrId = $data->pluck('id')->all();

            foreach ($data as $ky => $val) {
                $data[$ky] = $val->sortBy('country_name')->groupby('country_name');

                $diff_count[$ky] = $val->count();

                $success_fail_count[$ky]['success'] = $val->sum('success_count');
                $success_fail_count[$ky]['decline'] = $val->sum('declined_count');

                foreach ($data[$ky] as $business => $details) {
                    $data[$ky][$business] = $details->groupby('card_type');
                }
            }
        }

        return view(
            'admin.reports.countrywise_transaction_report',
            compact(
                'businessName',
                'payment_gateway_id',
                'data',
                'arrId',
                'success_fail_count',
                'diff_count',
                'countries'
            )
        );
    }

    public function merchantDailyTransactionReport(Request $request)
    {
        $input = $request->all();
        $merchant_daily_transactions = $this->transaction->getMerchantDailyTransactionReport($input);

        $total_success = $merchant_daily_transactions->sum('success_count');
        $total_amt_in_usd = $merchant_daily_transactions->sum('success_amount_in_usd');
        $total_declined = $merchant_daily_transactions->sum('declined_count');

        //dd($merchant_daily_transactions);

        return view(
            "admin.reports.merchant_daily_transaction_report",
            compact(
                'merchant_daily_transactions',
                'total_success',
                'total_declined',
                'total_amt_in_usd'
            )
        );
    }

    public function merchantCountrywiseTransactionReport(Request $request)
    {
        $businessName = $this->application->getTransactionData();
        $countries = $this->transaction->getAllTransactionCountry();
        $payment_gateway_id = $this->middetail->whereNotIn('id', [1, 2])->get();

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $finalId = [];
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }

        $data = $this->transaction->getMerchantCountryWiseTxnReportData($input);

        $data = $data->groupby('business_name');

        $arrId = [];
        $authUser = auth()->guard('admin')->user();
        if ($authUser != '') {
            // addToAdminLog('transactions', "Merchant Country-wise Transaction Report get", $authUser, 'success');
        }

        $diff_count = $total_txn = $success_fail_count = $success_fail_cntry_count = [];

        if (!empty($data)) {

            $arrId = $data->pluck('id')->all();

            foreach ($data as $ky => $val) {
                $data[$ky] = $val->sortBy('country_name')->groupby('country_name');

                $diff_count[$ky] = $val->count();

                $success_fail_count[$ky]['success'] = $val->sum('success_count');
                $success_fail_count[$ky]['decline'] = $val->sum('declined_count');

                foreach ($data[$ky] as $country => $details) {
                    $data[$ky][$country] = $details->groupby('card_type');
                    $success_fail_cntry_count[$ky][$country]['success'] = $details->sum('success_count');
                    $success_fail_cntry_count[$ky][$country]['decline'] = $details->sum('declined_count');
                }
            }
        }

        //        echo "<pre>";print_r($success_fail_count); echo "<br>";
        //        echo "<pre>";print_r($diff_count); echo "<br>";
        //        dd($data);

        return view(
            'admin.reports.merchant_countrywise_transaction_report',
            compact(
                'businessName',
                'payment_gateway_id',
                'data',
                'arrId',
                'success_fail_count',
                'diff_count',
                'success_fail_cntry_count',
                'countries'
            )
        );
    }
}