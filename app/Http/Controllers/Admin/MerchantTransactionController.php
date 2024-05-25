<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\AdminController;
use Validator;
use App\PayoutReports;
use App\Transaction;
use App\MIDDetail;
use App\Application;
use App\RemoveFlaggedTransaction;
use App\User;
use Mail;
use URL;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Mail\UnFlaggedTransactionMail;
use App\Mail\ChargebacksTransactionMail;
use App\Mail\UnChargebackTransactionMail;
use App\Mail\RetrievalTransactionMail;
use App\Mail\UnRetrievalTransactionMail;
use App\Mail\PreArbitrationNoticeMail;
use App\Exports\TransactionsExport;
use App\Exports\CryptoTransactionsExport;
use App\Exports\FlaggedTransactionExport;
use App\Exports\RefundTransactionsExport;
use App\Exports\ChargebacksTransactionExport;
use App\Exports\TestTransactionsExport;
use App\Exports\DeclinedTransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RetrievalTransactionExport;
use App\Exports\RemovedFlaggedTransactionExport;
use Carbon\Carbon;
use App\Mail\RefundTransactionMail;
use App\TransactionsDocumentUpload;
use App\Imports\TransactionImport;
use App\Http\Controllers\Repo\TransactionRepo;
use Storage;
use DB;
use Exception;
use App\Transformers\ApiResponse;

class MerchantTransactionController extends AdminController
{

    protected $TransactionRepo, $Transaction, $user, $MIDDetail, $application, $RemoveFlaggedTransaction, $moduleTitleS, $moduleTitleP;
    public function __construct()
    {
        parent::__construct();
        $this->Transaction = new Transaction;
        $this->TransactionRepo = new TransactionRepo;
        $this->user = new User;
        $this->MIDDetail = new MIDDetail;
        $this->application = new Application;
        $this->RemoveFlaggedTransaction = new RemoveFlaggedTransaction;

        $this->moduleTitleS = 'Merchant Transactions';
        $this->moduleTitleP = 'admin.transactions';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getAllMerchantTransactionData($input, $noList);
        $companyList = $this->application->getCompanyName();
        $payment_gateway_id = \DB::table('middetails')->get();

        $MIDDetail = MIDDetail::select('id', 'bank_name')->pluck('bank_name', 'id')->toArray();
        $Application = Application::select('user_id', 'business_name')->pluck('business_name', 'user_id')->toArray();
        return view($this->moduleTitleP . '.index', compact('payment_gateway_id', 'data', 'noList', 'companyList', 'MIDDetail', 'Application'));
    }

    public function show($id)
    {
        $data = $this->Transaction->findData($id);
        return view($this->moduleTitleP . '.show', compact('data'));
    }

    public function transactionDetails(Request $request)
    {
        $data = $this->Transaction->findData($request->id);
        $tab = $request->tab;
        $html = view('partials.transactions.single-transaction-sidebar', compact('data', 'tab'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function crypto(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getAllMerchantCryptoTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        return view($this->moduleTitleP . '.crypto', compact('payment_gateway_id', 'data', 'noList'));
    }

    public function refunds(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getRefundsMerchantTransactionData($input, $noList);
        $companyList = $this->application->getCompanyName();
        $payment_gateway_id = \DB::table('middetails')->get();
        return view($this->moduleTitleP . '.refunds', compact('payment_gateway_id', 'data', 'noList', 'companyList'));
    }

    public function chargebacks(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getChargebacksMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        $companyList = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.chargebacks', compact('payment_gateway_id', 'data', 'noList', 'companyList'));
    }

    public function preArbitration(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getPreArbitrationMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        $companyList = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.prearbitration', compact('payment_gateway_id', 'data', 'noList', 'companyList'));
    }

    public function retrieval(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getRetrivalMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        $companyList = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.retrieval', compact('payment_gateway_id', 'data', 'noList', 'companyList'));
    }

    public function flagged(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getFlaggedMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        $companyList = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.flagged', compact('payment_gateway_id', 'data', 'noList', 'companyList'));
    }

    public function removeSuspicious(Request $request)
    {
        $id = $request->id;

        $response = $this->TransactionRepo->removeFlagged($id);

        if ($response) {
            return response()->json(['status' => 200, 'message' => 'Record updated successfully!']);
        } else {
            return response()->json(['status' => 400]);
        }
    }

    public function removeRetrieval(Request $request)
    {
        $id = $request->id;
        $transactionData = Transaction::find($id);

        $payout_report = PayoutReports::where('user_id', $transactionData->user_id)
            ->whereDate('chargebacks_start_date', '<=', date('Y-m-d', strtotime($transactionData->retrieval_date)))
            ->whereDate('chargebacks_end_date', '>=', date('Y-m-d', strtotime($transactionData->retrieval_date)))
            ->orderBy('id', 'DESC')
            ->count();

        try {
            $date = Carbon::now()->toDateTimeString();
            if ($payout_report == '0') {
                Transaction::where('id', $id)->update(['is_retrieval' => '0']);
            } else {
                Transaction::where('id', $id)->update(['is_retrieval' => '0', 'is_retrieval_remove' => '1', 'retrieval_remove_date' => $date]);
            }

            return response()->json(['status' => 200, 'message' => 'Record updated successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    public function testTransactions(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getMerchantTestTransactionData($input, $noList);
        $companyList = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.test_transactions', compact('data', 'noList', 'companyList'));
    }

    public function getRemoveFlaggedTransactions(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new RemovedFlaggedTransactionExport, 'removed_flagged_transactions.xlsx');
        }
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getMerchantRemovedFlaggedTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        $companyList = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.remove_flagged', compact('payment_gateway_id', 'data', 'noList', 'companyList'));
    }

    public function changeRefundStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refund_date' => 'required',
        ]);
        $allID = explode(",", $request->get('id'));
        $inputData = \Arr::except($request->all(), array('_token', '_method'));
        if ($validator->passes()) {
            $updateData['refund_date'] = date('Y-m-d H:i:s', strtotime($inputData['refund_date']));
            if ($request->get('type') == 'forall') {
                $updateData1 = \Arr::except($request->all(), array('_token', 'type', '_method'));
                $updateData1['refund'] = '1';
                $updateData1['refund_date'] = date('Y-m-d H:i:s', strtotime($updateData['refund_date']));
                $updateData1['transaction_date'] = date('Y-m-d H:i:s');
                foreach ($allID as $key => $value) {
                    $updateData1['id'] = $value;
                    $isRefundTransaction = Transaction::where('id', $value)->first();
                    if ($isRefundTransaction->refund == '0' && $isRefundTransaction->chargebacks == '0' && $isRefundTransaction->is_flagged == '0' && $isRefundTransaction->is_retrieval == '0' && $isRefundTransaction->status == '1') {
                        if ($this->Transaction->updateData($value, $updateData1)) {
                            if ($request->get('status') == 1) {
                                $transaction = Transaction::select('transactions.order_id as order_id', 'transactions.order_id', 'transactions.card_type', 'users.id as user_id', 'users.email as user_email', 'transactions.first_name', 'transactions.last_name', 'transactions.card_type', 'transactions.card_no', 'transactions.amount', 'transactions.created_at', 'transactions.currency', 'transactions.email', 'transactions.order_id')
                                    ->join('users', 'users.id', 'transactions.user_id')
                                    ->where('transactions.id', $value)->first();
                                $input['title'] = 'Transaction Refund';
                                $input['body'] = 'Dear merchant , your transaction <strong>Order No : ' . $transaction->order_id . '</strong> has been refunded. You can check the details of the transaction in your Dashboard.';
                                $input['first_name'] = $transaction->first_name;
                                $input['last_name'] = $transaction->last_name;
                                $input['card_type'] = $transaction->card_type;
                                $input['card_no'] = substr($transaction->card_no, 0, 6) . 'XXXXXX' . substr($transaction->card_no, -4);
                                $input['user_id'] = $transaction->user_id;
                                $input['amount'] = $transaction->amount;
                                $input['created_at'] = $transaction->created_at;
                                $input['currency'] = $transaction->currency;
                                $input['order_id'] = $transaction->order_id;
                                $input['refund_date'] = $updateData1['refund_date'];
                                $input['email'] = $transaction->email;
                                try {
                                    // Mail::to($transaction->user_email)->queue(new RefundTransactionMail($input));
                                } catch (\Exception $e) {
                                    return response()->json([
                                        'success' => false,
                                    ]);
                                }
                                // $notification = [
                                //     'user_id' => $transaction->user_id,
                                //     'sendor_id' => auth()->guard('admin')->user()->id,
                                //     'type' => 'user',
                                //     'title' => 'Transaction Refund',
                                //     'body' => 'Dear merchant , your transaction Order No : ' . $transaction->order_id . ' has been refunded. You can check the details of the transaction in your Dashboard.',
                                //     'url' => '/refunds',
                                //     'is_read' => '0'
                                // ];
                                // $realNotification = addNotification($notification);
                                // $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                                // event(new UserNotification($realNotification->toArray()));
                            }
                        }
                    }
                }
                return response()->json([
                    'success' => true,
                ]);
            } else {
                $updateData['refund'] = $inputData['status'];
                $updateData['refund_date'] = date('Y-m-d H:i:s', strtotime($inputData['refund_date']));
                $updateData['transaction_date'] = date('Y-m-d H:i:s');
                $isRefundTransaction = Transaction::where('id', $request->get('id'))->first();
                if ($isRefundTransaction->refund == '0' && $isRefundTransaction->chargebacks == '0' && $isRefundTransaction->is_flagged == '0' && $isRefundTransaction->is_retrieval == '0' && $isRefundTransaction->status == '1') {
                    if ($this->Transaction->updateData($request->get('id'), $updateData)) {
                        if ($request->get('status') == 1) {
                            $transaction = Transaction::select(
                                'transactions.order_id as order_id',
                                'transactions.order_id',
                                'users.id as user_id',
                                'users.name as userName',
                                'users.email as user_email',
                                'transactions.first_name',
                                'transactions.last_name',
                                'transactions.card_type',
                                'transactions.card_no',
                                'transactions.amount',
                                'transactions.email',
                                'transactions.created_at',
                                'transactions.currency',
                                'transactions.order_id'
                            )->join('users', 'users.id', 'transactions.user_id')->where('transactions.id', $request->get('id'))->first();
                            $input['title'] = 'Transaction Refund';
                            $input['body'] = 'Dear merchant user your transaction <strong>Order No : ' . $transaction->order_id . '</strong> is refund please check on your dashboard.';
                            $input['card_type'] = $transaction->card_type;
                            $input['first_name'] = $transaction->first_name;
                            $input['last_name'] = $transaction->last_name;
                            $input['email'] = $transaction->user_email;
                            $input['card_no'] = substr($transaction->card_no, 0, 6) . 'XXXXXX' . substr($transaction->card_no, -4);
                            $input['user_id'] = $transaction->user_id;
                            $input['amount'] = $transaction->amount;
                            $input['created_at'] = $transaction->created_at;
                            $input['currency'] = $transaction->currency;
                            $input['order_id'] = $transaction->order_id;
                            $input['refund_date'] = $updateData['refund_date'];
                            $input['userName'] = $transaction->userName;
                            try {
                                // Mail::to($transaction->user_email)->queue(new RefundTransactionMail($input));
                            } catch (\Exception $e) {
                                return response()->json([
                                    'success' => false,
                                ]);
                            }
                            // $notification = [
                            //     'user_id' => $transaction->user_id,
                            //     'sendor_id' => auth()->guard('admin')->user()->id,
                            //     'type' => 'user',
                            //     'title' => 'Transaction Refund',
                            //     'body' => 'Dear merchant , your transaction Order No : ' . $transaction->order_id . ' has been refunded. You can check the details of the transaction in your Dashboard.',
                            //     'url' => '/refunds',
                            //     'is_read' => '0'
                            // ];
                            // $realNotification = addNotification($notification);
                            // $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                            // event(new UserNotification($realNotification->toArray()));
                        }
                        return response()->json([
                            'success' => true,
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                    ]);
                }
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function changeTransactionUnRefund(Request $request)
    {
        if ($this->Transaction->updateData($request->get('id'), ['refund_remove' => '1', 'refund_remove_date' => date('Y-m-d H:i:s'), 'transaction_date' => date('Y-m-d H:i:s'), 'refund' => '0'])) {
            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.refund_date as refund_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                ->join('users', 'users.id', 'transactions.user_id')
                ->where('transactions.id', $request->get('id'))
                ->first();
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
            $input['card_no'] = $transaction->card_no;
            $input['refund_date'] = $transaction->refund_date;
            $input['amount'] = $transaction->amount;
            $input['currency'] = $transaction->currency;
            $input['email'] = $transaction->email;
            $input['created_at'] = $transaction->created_at;
            $input['first_name'] = $transaction->first_name;
            $input['last_name'] = $transaction->last_name;
            $token = $transaction->id . \Str::random(32);
            Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
            // Refund remove mail
            try {
                //Mail::to($transaction->user_email)->send(new UnRefundTransactionMail($input));
            } catch (\Exception $e) {
                \Log::info([
                    'error_type' => 'UnRefund transaction error',
                    'body' => $e->getMessage()
                ]);
            }
            $notification = [
                'user_id' => $transaction->user_id,
                'sendor_id' => auth()->guard('admin')->user()->id,
                'type' => 'user',
                'title' => 'Refund Removed',
                'body' => 'We are pleased to inform you that the refund has been removed.This sale will now show on your dashboard.',
                'url' => '/dashboard',
                'is_read' => '0'
            ];

            $realNotification = addNotification($notification);
            $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
            event(new UserNotification($realNotification->toArray()));

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function changeChargebacksStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chargebacks_date' => 'required',
        ]);
        $allID = explode(",", $request->get('id'));
        $updateData = \Arr::except($request->all(), array('_token', '_method'));
        if ($validator->passes()) {
            $updateData['chargebacks_date'] = date('Y-m-d H:i:s', strtotime($updateData['chargebacks_date']));
            if ($request->get('type') == 'forall') {
                $updateData1 = \Arr::except($request->all(), array('_token', 'type', '_method'));
                $updateData1['chargebacks'] = '1';
                $updateData1['chargebacks_date'] = date('Y-m-d H:i:s', strtotime($updateData['chargebacks_date']));
                $updateData1['transaction_date'] = date('Y-m-d H:i:s');
                foreach ($allID as $key => $value) {
                    $updateData1['id'] = $value;
                    $isChargebacksTransaction = Transaction::where('id', $value)->first();
                    if ($isChargebacksTransaction->chargebacks == '0' && $isChargebacksTransaction->status == '1') {
                        if ($this->Transaction->updateData($value, $updateData1)) {
                            if ($request->get('status') == 1) {
                                $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.flagged_date as flagged_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.created_at as transaction_date', 'transactions.chargebacks_date', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email', 'users.name as userName')
                                    ->join('users', 'users.id', 'transactions.user_id')
                                    ->where('transactions.id', $value)
                                    ->first();
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
                                $input['card_no'] = $transaction->card_no;
                                $input['flagged_date'] = $transaction->flagged_date;
                                $input['amount'] = $transaction->amount;
                                $input['currency'] = $transaction->currency;
                                $input['order_id'] = $transaction->order_id;
                                $input['transaction_date'] = $transaction->transaction_date;
                                $input['chargebacks_date'] = $transaction->chargebacks_date;
                                $input['email'] = $transaction->email;
                                $input['created_at'] = $transaction->created_at;
                                $input['card_type'] = $transaction->card_type;
                                $input['first_name'] = $transaction->first_name;
                                $input['last_name'] = $transaction->last_name;
                                $input['userName'] = $transaction->userName;
                                $token = Transaction::where('id', $transaction->id)->first();
                                $token = $transaction->id . \Str::random(32);
                                Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
                                $input['url'] = URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=chargebacks&token=' . $token;
                                $mail_data['subject'] = 'Chargeback transaction mail.';
                                $mail_data['title'] = 'You have new chargeback transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.';
                                try {
                                    Mail::to($transaction->user_email)->queue(new ChargebacksTransactionMail($input));
                                    $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                                    if ($user_additional_mail != null) {
                                        Mail::to($user_additional_mail)->queue(new ChargebacksTransactionMail($input));
                                    }
                                } catch (\Exception $e) {
                                    \Log::info([
                                        'error_type' => 'chargeback transaction error',
                                        'body' => $e->getMessage()
                                    ]);
                                }
                                $notification = [
                                    'user_id' => $transaction->user_id,
                                    'sendor_id' => auth()->guard('admin')->user()->id,
                                    'type' => 'user',
                                    'title' => 'Transaction Chargeback',
                                    'body' => 'You have new chargeback transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.',
                                    'url' => '/chargebacks',
                                    'is_read' => '0'
                                ];
                                $realNotification = addNotification($notification);
                                $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                                event(new UserNotification($realNotification->toArray()));
                            }
                        }
                    }
                }
                return response()->json([
                    'success' => true,
                ]);
            } else {
                $updateData['chargebacks'] = $updateData['status'];
                $updateData['chargebacks_date'] = date('Y-m-d H:i:s', strtotime($updateData['chargebacks_date']));
                $updateData['transaction_date'] = date('Y-m-d H:i:s');
                unset($updateData['status']);
                $isChargebacksTransaction = Transaction::where('id', $request->get('id'))->first();
                if ($isChargebacksTransaction->chargebacks == '0' && $isChargebacksTransaction->status == '1') {
                    if ($this->Transaction->updateData($request->get('id'), $updateData)) {
                        if ($request->get('status') == 1) {
                            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.chargebacks_date as chargebacks_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email', 'users.name as userName')
                                ->join('users', 'users.id', 'transactions.user_id')
                                ->where('transactions.id', $request->get('id'))
                                ->first();
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
                            $input['card_no'] = $transaction->card_no;
                            $input['chargebacks_date'] = $transaction->chargebacks_date;
                            $input['amount'] = $transaction->amount;
                            $input['currency'] = $transaction->currency;
                            $input['order_id'] = $transaction->order_id;
                            $input['email'] = $transaction->email;
                            $input['created_at'] = $transaction->created_at;
                            $input['card_type'] = $transaction->card_type;
                            $input['first_name'] = $transaction->first_name;
                            $input['last_name'] = $transaction->last_name;
                            $input['userName'] = $transaction->userName;
                            $token = Transaction::where('id', $transaction->id)->first();
                            $token = $transaction->id . \Str::random(32);
                            Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
                            $input['url'] = URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=chargebacks&token=' . $token;
                            $trx = Transaction::find($request->get('id'));
                            $mail_data['subject'] = 'Chargeback transaction mail.';
                            $mail_data['title'] = 'You have new chargeback transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.';
                            try {
                                Mail::to($transaction->user_email)->queue(new ChargebacksTransactionMail($input));
                                $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                                if ($user_additional_mail != null) {
                                    Mail::to($user_additional_mail)->queue(new ChargebacksTransactionMail($input));
                                }
                            } catch (\Exception $e) {
                                \Log::info([
                                    'error_type' => 'chargeback transaction error',
                                    'body' => $e->getMessage()
                                ]);
                            }
                            $notification = [
                                'user_id' => $transaction->user_id,
                                'sendor_id' => auth()->guard('admin')->user()->id,
                                'type' => 'user',
                                'title' => 'Transaction Chargeback',
                                'body' => 'You have new chargeback transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.',
                                'url' => '/chargebacks',
                                'is_read' => '0'
                            ];
                            $realNotification = addNotification($notification);
                            $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                            event(new UserNotification($realNotification->toArray()));
                        }
                        return response()->json([
                            'success' => true,
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                    ]);
                }
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function changeChargebacksUnChargeback(Request $request)
    {
        if ($this->Transaction->updateData($request->get('id'), ['chargebacks_remove' => '1', 'chargebacks_remove_date' => date('Y-m-d H:i:s'), 'transaction_date' => date('Y-m-d H:i:s')])) {
            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.chargebacks_date as chargebacks_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                ->join('users', 'users.id', 'transactions.user_id')
                ->where('transactions.id', $request->get('id'))
                ->first();
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
            $input['card_no'] = $transaction->card_no;
            $input['chargebacks_date'] = $transaction->chargebacks_date;
            $input['amount'] = $transaction->amount;
            $input['currency'] = $transaction->currency;
            $input['email'] = $transaction->email;
            $input['created_at'] = $transaction->created_at;
            $input['first_name'] = $transaction->first_name;
            $input['last_name'] = $transaction->last_name;
            $token = $transaction->id . \Str::random(32);
            Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
            try {
                Mail::to($transaction->email)->send(new UnChargebackTransactionMail($input));
            } catch (\Exception $e) {
                \Log::info([
                    'error_type' => 'Unchargeback transaction error',
                    'body' => $e->getMessage()
                ]);
            }
            $notification = [
                'user_id' => $transaction->user_id,
                'sendor_id' => auth()->guard('admin')->user()->id,
                'type' => 'user',
                'title' => 'Chargeback Removed',
                'body' => 'We are pleased to inform you that the acquiring bank has concluded its due diligence on the following transaction and the Chargeback has been removed.This sale will now show on your dashboard.',
                'url' => '/dashboard',
                'is_read' => '0'
            ];
            $realNotification = addNotification($notification);
            $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
            event(new UserNotification($realNotification->toArray()));
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }


    public function changeTransactionFlag(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if ($request->get('status') == '1') {
            if ($request->get('type') == 'forall') {
                $allID = explode(",", $request->get('id'));
                foreach ($allID as $key => $value) {
                    $this->TransactionRepo->markFlagged($value, $request->get('flagged_by'), $request->get('status'));
                }
                return response()->json([
                    'success' => true,
                ]);
            } else {
                if ($this->TransactionRepo->markFlagged($request->get('id'), $request->get('flagged_by'), $request->get('status'))) {
                    return response()->json([
                        'success' => true,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                    ]);
                }
            }
        }
    }

    public function changeTransactionUnFlag(Request $request)
    {
        if ($this->Transaction->updateData($request->get('id'), ['is_flagged_remove' => '1', 'flagged_remove_date' => date('Y-m-d H:i:s'), 'transaction_date' => date('Y-m-d H:i:s'),])) {
            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.flagged_date as flagged_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                ->join('users', 'users.id', 'transactions.user_id')
                ->where('transactions.id', $request->get('id'))
                ->first();
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
            $input['card_no'] = $transaction->card_no;
            $input['flagged_date'] = $transaction->flagged_date;
            $input['amount'] = $transaction->amount;
            $input['currency'] = $transaction->currency;
            $input['email'] = $transaction->email;
            $input['created_at'] = $transaction->created_at;
            $input['first_name'] = $transaction->first_name;
            $input['last_name'] = $transaction->last_name;
            $token = $transaction->id . \Str::random(32);
            $input['url'] = URL::to('/') . '/transaction-documents-upload/' . $token . '/?transactionId=' . $transaction->id . '&uploadFor=flagged';
            Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
            try {
                Mail::to($transaction->user_email)->send(new UnFlaggedTransactionMail($input));
            } catch (\Exception $e) {
                \Log::info([
                    'error_type' => 'Unflag transaction error',
                    'body' => $e->getMessage()
                ]);
            }
            $notification = [
                'user_id' => $transaction->user_id,
                'sendor_id' => auth()->guard('admin')->user()->id,
                'type' => 'user',
                'title' => 'Flagged Removed',
                'body' => 'We are pleased to inform you that the acquiring bank has concluded its due diligence on the following transaction and the flag has been removed.This sale will now show on your dashboard.',
                'url' => '/dashboard',
                'is_read' => '0'
            ];
            $realNotification = addNotification($notification);
            $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
            event(new UserNotification($realNotification->toArray()));
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function changeTransactionStatus(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $isDeclinedTransaction = Transaction::where('id', $value)->first();
                if ($isDeclinedTransaction->status == '1') {
                    $this->Transaction->updateData($value, ['status' => '0']);
                }
            }
            return response()->json([
                'success' => true,
            ]);
        }
        if ($this->Transaction->updateData($request->get('id'), ['status' => $request->get('status')])) {
            $transaction = Transaction::where('id', $request->get('id'))->first();
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function showDocumentChargebacks(Request $request)
    {
        $data = TransactionsDocumentUpload::where('transaction_id', $request->id)->where('files_for', 'chargebacks')->first();
        $id = $request->id;
        $html = view('admin.transactions.show_document_chargebacks', compact('data', 'id'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function downloadDocumentsUploade(Request $request)
    {
        return Storage::disk('s3')->download('uploads/transactionDocumentsUpload/' . $request->file, $request->file);
    }

    public function uploadDocument(Request $request)
    {
        $input = $request->all();
        $input = \Arr::except($input, array('_token', '_method'));
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|mimes:jpg,jpeg,png,pdf,zip|max:35840'
        ]);

        if ($validator->passes()) {
            try {
                $exists = TransactionsDocumentUpload::where('transaction_id', $input['transaction_id'])->where('files_for', $input['files_for'])->first();
                if ($request->hasFile('files')) {
                    $files = $request->file('files');

                    foreach ($request->file('files') as $key => $value) {
                        $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                        $imageName = $imageName . '.' . $files[$key]->getClientOriginalExtension();
                        $filePath = '/uploads/transactionDocumentsUpload/' . $imageName;
                        Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                        $input['fileso'][] = $imageName;
                    }

                    $input['files'] = json_encode($input['fileso']);
                }
                $input = \Arr::except(
                    $input,
                    array(
                        'fileso'
                    )
                );
                if ($exists == null) {
                    TransactionsDocumentUpload::create($input);
                    $transaction = Transaction::where('transactions.id', $input['transaction_id'])->first();
                    \Session::put('success', 'Document Upload successfully.');
                } else {
                    if ($exists->files == '') {
                        $fileData = [];
                    } else {
                        $fileData = json_decode($exists->files, true);
                    }
                    $fileData1 = json_decode($input['files'], true);
                    $json_merge = array_merge($fileData, $fileData1);
                    $json_merge = json_encode($json_merge);
                    TransactionsDocumentUpload::where('id', $exists->id)->update(['files' => $json_merge]);
                    \Session::put('success', 'Document Upload successfully.');
                }
                return redirect()->back();
            } catch (\Exception $e) {
                \Session::put('error', 'Something went wrong.');
                return redirect()->back();
            }
        } else {
            $str = "";
            foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                foreach ($messages as $message) {
                    $str = $message;
                }
            }
            \Session::put('error', $message);
            return redirect()->back();
        }
    }

    public function showDocumentFlagged(Request $request)
    {
        $data = TransactionsDocumentUpload::where('transaction_id', $request->id)->where('files_for', 'flagged')->first();
        $id = $request->id;
        $html = view('admin.transactions.show_document_flagged', compact('data', 'id'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function showDocumentRetrieval(Request $request)
    {
        $data = TransactionsDocumentUpload::where('transaction_id', $request->id)->where('files_for', 'retrieval')->first();
        $id = $request->id;
        $html = view('admin.transactions.show_document_retrieval', compact('data', 'id'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function changeRetrievalStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'retrieval_date' => 'required',
        ]);
        $allID = explode(",", $request->get('id'));
        $updateData = \Arr::except($request->all(), array('_token', '_method'));
        if ($validator->passes()) {
            $updateData['retrieval_date'] = date('Y-m-d H:i:s', strtotime($updateData['retrieval_date']));
            if ($request->get('type') == 'forall') {
                $updateData1 = \Arr::except($request->all(), array('_token', 'type', '_method'));
                $updateData1['is_retrieval'] = '1';
                $updateData1['retrieval_date'] = date('Y-m-d H:i:s', strtotime($updateData['retrieval_date']));
                $updateData1['transaction_date'] = date('Y-m-d H:i:s');
                foreach ($allID as $key => $value) {
                    $updateData1['id'] = $value;
                    $isChargebacksTransaction = Transaction::where('id', $value)->first();
                    if ($isChargebacksTransaction->chargebacks == '0' && $isChargebacksTransaction->refund == '0' && $isChargebacksTransaction->is_retrieval == '0' && $isChargebacksTransaction->is_flagged == '0' && $isChargebacksTransaction->status == '1') {
                        if ($this->Transaction->updateData($value, $updateData1)) {
                            if ($request->get('status') == 1) {
                                $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.flagged_date as flagged_date', 'transactions.retrieval_date as retrieval_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.created_at as transaction_date', 'transactions.chargebacks_date', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                                    ->join('users', 'users.id', 'transactions.user_id')
                                    ->where('transactions.id', $value)
                                    ->first();
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
                                $input['card_no'] = $transaction->card_no;
                                $input['flagged_date'] = $transaction->flagged_date;
                                $input['amount'] = $transaction->amount;
                                $input['currency'] = $transaction->currency;
                                $input['order_id'] = $transaction->order_id;
                                $input['transaction_date'] = $transaction->transaction_date;
                                $input['retrieval_date'] = $transaction->retrieval_date;
                                $input['email'] = $transaction->email;
                                $input['created_at'] = $transaction->created_at;
                                $input['card_type'] = $transaction->card_type;
                                $input['first_name'] = $transaction->first_name;
                                $input['last_name'] = $transaction->last_name;
                                $token = Transaction::where('id', $transaction->id)->first();
                                $token = $transaction->id . \Str::random(32);
                                Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
                                $input['url'] = URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=retrieval&token=' . $token;
                                $mail_data['subject'] = 'Retrieval transaction mail.';
                                $mail_data['title'] = 'You have new retrieval transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.';
                                try {
                                    Mail::to($transaction->user_email)->queue(new RetrievalTransactionMail($input));
                                    $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                                    if ($user_additional_mail != null) {
                                        Mail::to($user_additional_mail)->queue(new RetrievalTransactionMail($input));
                                    }
                                } catch (\Exception $e) {
                                    \Log::info([
                                        'error_type' => 'Retrieval transaction error',
                                        'body' => $e->getMessage()
                                    ]);
                                }
                                $notification = [
                                    'user_id' => $transaction->user_id,
                                    'sendor_id' => auth()->guard('admin')->user()->id,
                                    'type' => 'user',
                                    'title' => 'Transaction Retrieval',
                                    'body' => 'You have new retrieval transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.',
                                    'url' => '/retrieval',
                                    'is_read' => '0'
                                ];
                                $realNotification = addNotification($notification);
                                $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                                event(new UserNotification($realNotification->toArray()));
                            }
                        }
                    }
                }
                return response()->json(['success' => true]);
            } else {

                $updateData['is_retrieval'] = $updateData['status'];
                $updateData['retrieval_date'] = date('Y-m-d H:i:s', strtotime($updateData['retrieval_date']));
                $updateData['transaction_date'] = date('Y-m-d H:i:s');
                unset($updateData['status']);
                $isChargebacksTransaction = Transaction::where('id', $request->get('id'))->first();
                if ($isChargebacksTransaction->chargebacks == '0' && $isChargebacksTransaction->refund == '0' && $isChargebacksTransaction->is_retrieval == '0' && $isChargebacksTransaction->is_flagged == '0' && $isChargebacksTransaction->status == '1') {
                    if ($this->Transaction->updateData($request->get('id'), $updateData)) {
                        if ($request->get('status') == 1) {
                            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.chargebacks_date as chargebacks_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                                ->join('users', 'users.id', 'transactions.user_id')
                                ->where('transactions.id', $request->get('id'))
                                ->first();
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
                            $input['card_no'] = $transaction->card_no;
                            $input['retrieval_date'] = $transaction->retrieval_date;
                            $input['amount'] = $transaction->amount;
                            $input['currency'] = $transaction->currency;
                            $input['order_id'] = $transaction->order_id;
                            $input['email'] = $transaction->email;
                            $input['created_at'] = $transaction->created_at;
                            $input['card_type'] = $transaction->card_type;
                            $input['first_name'] = $transaction->first_name;
                            $input['last_name'] = $transaction->last_name;
                            $token = Transaction::where('id', $transaction->id)->first();
                            $token = $transaction->id . \Str::random(32);
                            Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
                            $input['url'] = URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=retrieval&token=' . $token;
                            $trx = Transaction::find($request->get('id'));
                            $mail_data['subject'] = 'Retrieval transaction mail.';
                            $mail_data['title'] = 'You have new retrieval transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.';
                            try {
                                Mail::to($transaction->user_email)->queue(new RetrievalTransactionMail($input));
                                $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                                if ($user_additional_mail != null) {
                                    Mail::to($user_additional_mail)->queue(new RetrievalTransactionMail($input));
                                }
                            } catch (\Exception $e) {
                                \Log::info([
                                    'error_type' => 'Retrieval transaction error',
                                    'body' => $e->getMessage()
                                ]);
                            }
                            $notification = [
                                'user_id' => $transaction->user_id,
                                'sendor_id' => auth()->guard('admin')->user()->id,
                                'type' => 'user',
                                'title' => 'Transaction Retrieval',
                                'body' => 'You have new retrieval transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.',
                                'url' => '/retrieval',
                                'is_read' => '0'
                            ];
                            $realNotification = addNotification($notification);
                            $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                            event(new UserNotification($realNotification->toArray()));
                        }
                        return response()->json([
                            'success' => true,
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                    ]);
                }
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function changeRetrievalUnRetrieval(Request $request)
    {
        if ($this->Transaction->updateData($request->get('id'), ['is_retrieval_remove' => '1', 'retrieval_remove_date' => date('Y-m-d H:i:s'), 'transaction_date' => date('Y-m-d H:i:s')])) {
            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.retrieval_date as retrieval_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                ->join('users', 'users.id', 'transactions.user_id')
                ->where('transactions.id', $request->get('id'))
                ->first();
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
            $input['card_no'] = $transaction->card_no;
            $input['retrieval_date'] = $transaction->retrieval_date;
            $input['amount'] = $transaction->amount;
            $input['currency'] = $transaction->currency;
            $input['email'] = $transaction->email;
            $input['created_at'] = $transaction->created_at;
            $input['first_name'] = $transaction->first_name;
            $input['last_name'] = $transaction->last_name;
            $token = $transaction->id . \Str::random(32);
            Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
            try {
                Mail::to($transaction->email)->send(new UnRetrievalTransactionMail($input));
            } catch (\Exception $e) {
                \Log::info([
                    'error_type' => 'Unchargeback transaction error',
                    'body' => $e->getMessage()
                ]);
            }
            $notification = [
                'user_id' => $transaction->user_id,
                'sendor_id' => auth()->guard('admin')->user()->id,
                'type' => 'user',
                'title' => 'Retrieval Removed',
                'body' => 'We are pleased to inform you that the acquiring bank has concluded its due diligence on the following transaction and the Retrieval has been removed.This sale will now show on your dashboard.',
                'url' => '/dashboard',
                'is_read' => '0'
            ];
            $realNotification = addNotification($notification);
            $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
            event(new UserNotification($realNotification->toArray()));
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function deleteTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $isDeclinedTransaction = Transaction::where('id', $value)->first();
                $this->Transaction->destroyData($value);
            }
            return response()->json([
                'success' => true,
            ]);
        }
        if ($this->Transaction->destroyData($request->get('id'))) {
            $transaction = Transaction::where('id', $request->get('id'))->first();
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function declinedTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->Transaction->getMerchantDeclinedTransactions($input, $noList);
        $companyList = $this->application->getCompanyName();
        return view($this->moduleTitleP . '.declined_transactions', compact('data', 'noList', 'companyList'));
    }

    public function sendTransactionWebhook(Request $request, $id)
    {
        $data = $this->Transaction->findData($id);

        if ($data->webhook_url != null || $data->webhook_url != '') {
            $request_data = ApiResponse::webhook($data);
            try {
                $http_response = postCurlRequest($data->webhook_url, $request_data);
            } catch (Exception $e) {
                \Log::info(['webhook_retry_fail' => $e->getMessage()]);
            }

            $updateData['webhook_status'] = ($http_response == true) ? 'SUCCESS' : 'FAILED';
            $updateData['webhook_retry'] = 1;

            // update data
            $this->Transaction->updateData($id, $updateData);

            if ($http_response == true) {
                notificationMsg('success', 'Webhook send successfully.');
            } else {
                notificationMsg('error', 'Webhook not send that the given webhook URL.');
            }
            return redirect()->back();
        } else {
            notificationMsg('warning', 'Webhook URL not found on that transaction.');
            return redirect()->back();
        }
    }

    public function sentPreArbitrationNotice(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $allID = $request->get('id');
        $resent = 0;
        if (isset($input['resent']) && $input['resent'] == 1) {
            $resent = 1;
        }

        foreach ($allID as $key => $value) {
            $transaction = Transaction::where('id', $value)->first();
            if ($transaction->is_pre_arbitration == '0' || $resent == 1) {
                $user = User::where('id', $transaction->user_id)->first();
                view()->share('data', $transaction);

                try {
                    $options = new Options();
                    $options->setIsRemoteEnabled(true);
                    $dompdf = new Dompdf($options);
                    $dompdf->loadHtml(view('admin.transactions.pre_Arbitration_PDF'));

                    $dompdf->setPaper([0, 0, 900, 700], 'landscape');

                    $dompdf->render();

                    $filePath = 'uploads/preArbitration/' . $transaction->user_id . '_notice_' . $value . '.pdf';

                    Storage::disk('s3')->put($filePath, $dompdf->output());

                    $data['first_name'] = $transaction->first_name;
                    $data['last_name'] = $transaction->last_name;
                    $data['card_no'] = $transaction->card_no;
                    $data['order_id'] = $transaction->order_id;
                    $data['amount'] = $transaction->amount;
                    $data['currency'] = $transaction->currency;
                    $data['business_name'] = getBusinessName($transaction->user_id);
                    $data['file'] = getS3Url($filePath);

                    $details = [
                        'email' => $user->email,
                        'input' => $data
                    ];

                    // send all mail in queue.
                    $job = (new \App\Jobs\PreArbitrationNoticeQueueEmail($details))->delay(now()->addSeconds(2));
                    dispatch($job);

                    if ($resent == 0) {
                        $inputData = ['is_pre_arbitration' => '1', 'pre_arbitration_date' => date('Y-m-d H:i:s'), 'pre_arbitration_sent_files' => $filePath];
                        $this->Transaction->updateData($value, $inputData);
                    }
                } catch (\Exception $e) {
                    \Log::info([
                        'PreArbitrationNoticeMailException' => $e->getMessage(),
                    ]);
                    \Session::put('error', 'Soemthing wrong! try Again later.');
                    return response()->json(['success' => false]);
                }
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function resendChargebacksEmail(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $allID = $request->get('id');

        foreach ($allID as $key => $value) {
            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.flagged_date as flagged_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.created_at as transaction_date', 'transactions.chargebacks_date', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                ->join('users', 'users.id', 'transactions.user_id')
                ->where('transactions.id', $value)
                ->first();
            if (strlen($transaction->card_no) > 4) {
                $transaction->card_no = 'XXXXXXXXXXXX' . substr($transaction->card_no, -4);
            } else {
                $transaction->card_no = $transaction->card_no;
            }
            $input['card_no'] = $transaction->card_no;
            $input['flagged_date'] = $transaction->flagged_date;
            $input['amount'] = $transaction->amount;
            $input['currency'] = $transaction->currency;
            $input['order_id'] = $transaction->order_id;
            $input['transaction_date'] = $transaction->transaction_date;
            $input['chargebacks_date'] = $transaction->chargebacks_date;
            $input['email'] = $transaction->email;
            $input['created_at'] = $transaction->created_at;
            $input['card_type'] = $transaction->card_type;
            $input['first_name'] = $transaction->first_name;
            $input['last_name'] = $transaction->last_name;
            $input['url'] = URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=chargebacks&token=' . $transaction->transactions_token;

            try {
                $details = [
                    'email' => $transaction->user_email,
                    'input' => $input
                ];
                $job = (new \App\Jobs\ChargebacksTransactionQueueMail($details))->delay(now()->addSeconds(2));
                dispatch($job);
                $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                if ($user_additional_mail != null) {
                    $details = [
                        'email' => $user_additional_mail,
                        'input' => $input
                    ];
                    $job = (new \App\Jobs\ChargebacksTransactionQueueMail($details))->delay(now()->addSeconds(2));
                    dispatch($job);
                }
            } catch (\Exception $e) {
                \Log::info([
                    'error_type' => 'chargeback email error',
                    'body' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function resendSuspiciousEmail(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $allID = $request->get('id');

        foreach ($allID as $key => $value) {
            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.flagged_date as flagged_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.created_at as transaction_date', 'transactions.chargebacks_date', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                ->join('users', 'users.id', 'transactions.user_id')
                ->where('transactions.id', $value)
                ->first();
            if (strlen($transaction->card_no) > 4) {
                $transaction->card_no = 'XXXXXXXXXXXX' . substr($transaction->card_no, -4);
            } else {
                $transaction->card_no = $transaction->card_no;
            }
            $input['card_no'] = $transaction->card_no;
            $input['flagged_date'] = $transaction->flagged_date;
            $input['amount'] = $transaction->amount;
            $input['currency'] = $transaction->currency;
            $input['order_id'] = $transaction->order_id;
            $input['transaction_date'] = $transaction->transaction_date;
            $input['chargebacks_date'] = $transaction->chargebacks_date;
            $input['email'] = $transaction->email;
            $input['created_at'] = $transaction->created_at;
            $input['card_type'] = $transaction->card_type;
            $input['first_name'] = $transaction->first_name;
            $input['last_name'] = $transaction->last_name;
            $input['url'] = URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=flagged&token=' . $transaction->transactions_token;

            try {
                $details = [
                    'email' => $transaction->user_email,
                    'input' => $input
                ];
                $job = (new \App\Jobs\FlaggedTransactionQueueMail($details))->delay(now()->addSeconds(2));
                dispatch($job);
                $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                if ($user_additional_mail != null) {
                    $details = [
                        'email' => $user_additional_mail,
                        'input' => $input
                    ];
                    $job = (new \App\Jobs\FlaggedTransactionQueueMail($details))->delay(now()->addSeconds(2));
                    dispatch($job);
                }
            } catch (\Exception $e) {
                \Log::info([
                    'error_type' => 'Flagged email error',
                    'body' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function resendRetrievalEmail(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $allID = $request->get('id');

        foreach ($allID as $key => $value) {
            $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.flagged_date as flagged_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.created_at as transaction_date', 'transactions.chargebacks_date', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                ->join('users', 'users.id', 'transactions.user_id')
                ->where('transactions.id', $value)
                ->first();
            if (strlen($transaction->card_no) > 4) {
                $transaction->card_no = 'XXXXXXXXXXXX' . substr($transaction->card_no, -4);
            } else {
                $transaction->card_no = $transaction->card_no;
            }
            $input['card_no'] = $transaction->card_no;
            $input['flagged_date'] = $transaction->flagged_date;
            $input['amount'] = $transaction->amount;
            $input['currency'] = $transaction->currency;
            $input['order_id'] = $transaction->order_id;
            $input['transaction_date'] = $transaction->transaction_date;
            $input['chargebacks_date'] = $transaction->chargebacks_date;
            $input['email'] = $transaction->email;
            $input['created_at'] = $transaction->created_at;
            $input['card_type'] = $transaction->card_type;
            $input['first_name'] = $transaction->first_name;
            $input['last_name'] = $transaction->last_name;
            $input['url'] = URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=retrieval&token=' . $transaction->transactions_token;

            try {
                $details = [
                    'email' => $transaction->user_email,
                    'input' => $input
                ];
                $job = (new \App\Jobs\RetrievalTransactionQueueMail($details))->delay(now()->addSeconds(2));
                dispatch($job);
                $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                if ($user_additional_mail != null) {
                    $details = [
                        'email' => $user_additional_mail,
                        'input' => $input
                    ];
                    $job = (new \App\Jobs\RetrievalTransactionQueueMail($details))->delay(now()->addSeconds(2));
                    dispatch($job);
                }
            } catch (\Exception $e) {
                \Log::info([
                    'error_type' => 'Retrieval email error',
                    'body' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function resendRefundEmail(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $allID = $request->get('id');

        foreach ($allID as $key => $value) {
            $transaction = Transaction::select('transactions.order_id as order_id', 'transactions.order_id', 'transactions.card_type', 'users.id as user_id', 'users.email as user_email', 'transactions.first_name', 'transactions.last_name', 'transactions.card_type', 'transactions.card_no', 'transactions.amount', 'transactions.created_at', 'transactions.currency', 'transactions.email', 'transactions.order_id')
                ->join('users', 'users.id', 'transactions.user_id')
                ->where('transactions.id', $value)->first();
            $input['title'] = 'Transaction Refund';
            $input['body'] = 'Dear merchant , your transaction <strong>Order No : ' . $transaction->order_id . '</strong> has been refunded. You can check the details of the transaction in your Dashboard.';
            $input['first_name'] = $transaction->first_name;
            $input['last_name'] = $transaction->last_name;
            $input['card_type'] = $transaction->card_type;
            $input['card_no'] = substr($transaction->card_no, 0, 6) . 'XXXXXX' . substr($transaction->card_no, -4);
            $input['user_id'] = $transaction->user_id;
            $input['amount'] = $transaction->amount;
            $input['created_at'] = $transaction->created_at;
            $input['currency'] = $transaction->currency;
            $input['order_id'] = $transaction->order_id;
            $input['refund_date'] = $transaction->refund_date;
            $input['email'] = $transaction->email;

            try {
                $details = [
                    'email' => $transaction->user_email,
                    'input' => $input
                ];
                $job = (new \App\Jobs\RefundTransactionQueueMail($details))->delay(now()->addSeconds(2));
                dispatch($job);
                $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                if ($user_additional_mail != null) {
                    $details = [
                        'email' => $user_additional_mail,
                        'input' => $input
                    ];
                    $job = (new \App\Jobs\RefundTransactionQueueMail($details))->delay(now()->addSeconds(2));
                    dispatch($job);
                }
            } catch (\Exception $e) {
                \Log::info([
                    'error_type' => 'refund email error',
                    'body' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function sendMultiTransactionWebhook(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $allID = $request->get('id');
        $details = [
            'ids' => $allID
        ];

        try {
            // send all webhook in queue.
            $job = (new \App\Jobs\SendBulkWebhookInQueue($details))->delay(now()->addSeconds(2));
            dispatch($job);
        } catch (\Exception $e) {
            \Log::info([
                'SendBulkWebhookInQueue' => $e->getMessage()
            ]);
            \Session::put('error', 'Soemthing wrong! try Again later.');
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function exportAllTransactions(Request $request)
    {
        return Excel::download(new TransactionsExport($request->ids), 'Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportCrypto(Request $request)
    {
        return Excel::download(new CryptoTransactionsExport($request->ids), 'Crypto_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportRefunds(Request $request)
    {
        return Excel::download(new RefundTransactionsExport($request->ids), 'Refund_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportChargebacks(Request $request)
    {
        return Excel::download(new ChargebacksTransactionExport($request->ids), 'ChargeBack_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportRetrieval(Request $request)
    {
        return Excel::download(new RetrievalTransactionExport($request->ids), 'Retrieval_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportFlagged(Request $request)
    {
        return Excel::download(new FlaggedTransactionExport($request->ids), 'Suspicious_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportTestTransactions(Request $request)
    {
        return Excel::download(new TestTransactionsExport($request->ids), 'Test_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportDeclinedTransactions(Request $request)
    {
        return Excel::download(new DeclinedTransactionsExport($request->ids), 'Declined_Transactions_Excel_' . date('d-m-Y') . '.xlsx');
    }
    public function exportRemoveFlaggedTransactions(Request $request)
    {
        return Excel::download(new RemovedFlaggedTransactionExport($request->ids), 'Remove_Flagged_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function updateStatus(Request $request, $id)
    {
        $input = $request->except(['_token']);
        if (!isset($input['status']) || $input['status'] == null || !isset($input['reason']) || $input['reason'] == null) {
            \Session::flash('error', 'Please select transaction status and reason.');

            return redirect()->back();
        }
        $transaction = DB::table("transactions")->where("session_id", $id)->first();
        if ($transaction == null) {
            \Session::flash('error', 'Something went wrong..!!');
            return redirect()->back();
        }
        if (isset($transaction->webhook_url) && $transaction->webhook_url != null) {
            $paymentGatewayId = $this->MIDDetail->findData($transaction->payment_gateway_id);
            $request_data['order_id'] = $transaction->order_id;
            $request_data['customer_order_id'] = $transaction->customer_order_id ?? null;
            $request_data['transaction_status'] = (isset($input['status']) && $input['status'] == '1') ? 'success' : 'fail';
            $request_data['reason'] = $input['reason'];
            $request_data['currency'] = $transaction->currency;
            $request_data['amount'] = $transaction->amount;
            $request_data['transaction_date'] = $transaction->created_at;
            $request_data["descriptor"] = $paymentGatewayId->descriptor;
            // send webhook request
            try {
                $http_response = postCurlRequest($transaction->webhook_url, $request_data);
            } catch (Exception $e) {
                $http_response = 'FAILED';
            }
            $input['webhook_status'] = $http_response;
            $input['webhook_retry'] = 1;
        }
        DB::table("transactions")->where("session_id", $id)->update(["status" => $input["status"], "reason" => $input["reason"], "transaction_date" => date("Y-m-d H:i:s")]);
        \Session::flash('success', 'Transaction updated successfully.');
        return redirect()->back();
        //return redirect()->route('admin.transactions');
    }

    public function massAction(Request $request)
    {
        return view('admin.massAction.index');
    }

    public function massActionStore(Request $request)
    {
        // dd($request->all());

        $this->validate($request, [
            'transaction_file' => 'required|mimes:csv,xlsx,xls'
        ]);

        Excel::import(new TransactionImport($request->transaction_type), $request->file('transaction_file'));

        \Session::flash('success', 'Transactions marked as ' . $request->transaction_type . 'successfully.');
        return redirect()->route('mass-transaction-action.index');
    }
}