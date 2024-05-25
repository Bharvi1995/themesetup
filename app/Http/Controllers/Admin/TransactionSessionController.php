<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TransactionSession;
use App\Transaction;
use App\Application;
use App\MIDDetail;
use DB;
use Log;

class TransactionSessionController extends AdminController
{
    protected $TransactionSession, $Application, $MIDDetail, $moduleTitleS, $moduleTitleP;
    public function __construct()
    {
        parent::__construct();
        $this->TransactionSession = new TransactionSession;
        $this->Application = new Application;
        $this->MIDDetail = new MIDDetail;
        $this->moduleTitleS = 'Transaction session';
        $this->moduleTitleP = 'admin.transactionSession';
    }
    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }
        $payment_gateway_id = \DB::table('middetails')->get();
        $company_name = $this->Application->getCompanyName();
        $data = $this->TransactionSession->getTransactionSessionData($input);
        $userIds = [];
        $midIds = [];
        foreach ($data as $key => $value) {
            array_push($userIds, $value->user_id);
            array_push($midIds, $value->payment_gateway_id);
        }

        $companies = Application::select("user_id", "business_name")->whereIn("user_id", $userIds)->pluck("business_name", "user_id")->toArray();
        $mids = MIDDetail::select("id", "bank_name")->whereIn("id", $midIds)->pluck("bank_name", "id")->toArray();
        return view($this->moduleTitleP . '.index', compact('data', 'payment_gateway_id', 'company_name', "mids", "companies"));
    }

    public function transactionSessionShow($id)
    {
        $data = $this->TransactionSession::where('id', $id)->first();
        $json = json_decode($data->request_data);
        // dd($data, $json);
        return view($this->moduleTitleP . '.show', compact('data', 'json'));
    }

    public function restoreTransactionSession(Request $request)
    {
        try {
            $payload = $request->except(['_token']);
            // if status and reason not included
            if (!isset($payload['status']) || $payload['status'] == null || !isset($payload['reason']) || $payload['reason'] == null) {
                notificationMsg('error', 'Please select transaction status and reason.');
                return redirect()->back();
            }

            $transaction_session = DB::table('transaction_session')->where('transaction_id', $payload['session_id'])->first();
            if ($transaction_session) {
                $input = json_decode($transaction_session->request_data, true);
                $input["reason"] = $payload["reason"];
                $input["status"] = $payload["status"];
                $input['transaction_date'] = date("Y-m-d H:i:s");
                $input['order_id'] = $transaction_session->order_id;
                $input['updated_at'] = date("Y-m-d H:i:s");
                $input["created_at"] = $payload['created_at'];
                unset($input["bin_country_code"]);
                // send post request webhook job
                $transaction_id = 0;
                if (isset($input['order_id']) && !empty($input['order_id'])) {
                    $Ts = Transaction::where('order_id', $input['order_id'])->first();
                    if ($Ts) {
                        $transaction_id = $Ts->id;
                    }
                }

                if (isset($input['webhook_url']) && $input['webhook_url'] != null) {
                    $paymentGatewayId = $this->MIDDetail->findData($transaction_session->payment_gateway_id);
                    $request_data['order_id'] = $input['order_id'];
                    $request_data['customer_order_id'] = $input['customer_order_id'] ?? null;
                    $request_data['transaction_status'] = (isset($input['status']) && $input['status'] == '1') ? 'success' : 'fail';

                    $request_data['reason'] = $input['reason'];
                    $request_data['currency'] = $input['currency'];
                    $request_data['amount'] = $input['amount'];
                    $request_data['transaction_date'] = $input['created_at'];
                    $request_data["descriptor"] = $paymentGatewayId->descriptor;
                    // send webhook request
                    try {
                        $http_response = postCurlRequestBackUpTwo($input['webhook_url'], $request_data);
                    } catch (\Exception $e) {
                        $http_response = 'FAILED';
                    }
                    // send failed webhook request mail
                    if ($http_response == 'FAILED') {
                        $request_data['webhook_url'] = $input['webhook_url'];
                    }
                    $input['webhook_status'] = $http_response;
                    $input['webhook_retry'] = 1;
                }

                if (empty($input["gateway_id"])) {
                    $input["gateway_id"] = $input["session_id"];
                }
                if (!empty($transaction_id)) {
                    $Transaction = new Transaction;
                    $Transaction->updateData($transaction_id, $input);
                } else {
                    Transaction::insert($input);
                }

                DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);

                notificationMsg('success', 'Transaction restored successfully.');
            } else {
                notificationMsg('error', 'Transaction session not found.');
            }

            return redirect()->route('transaction-session');
        } catch (\Exception $err) {
            Log::info(["txn-restore-err" => $err->getMessage()]);
            notificationMsg('error', 'Something went wrong.pls try again.');
            return back();

        }

    }

    public function restoreTransactionSessionForm($transaction_id)
    {
        $input['id'] = $transaction_id;
        $input['paginate'] = '1';
        $input['is_completed'] = '0';
        if (!isset($input['noList'])) {
            $input['noList'] = 10;
        }
        // get transactions
        $data = $this->TransactionSession->getTransactionSessionData($input);

        if (count($data) == 0) {
            return abort(404);
        }
        foreach ($data as $key => $value) {
            $transaction_session[$key] = json_decode($value->request_data, 1);
            $transaction_session[$key]['session_id'] = $value->transaction_id != null ? $value->transaction_id : null;
            $transaction_session[$key]['created_at'] = $value->created_at;
            $transaction_session[$key]['company_name'] = Application::where('user_id', $transaction_session[$key]['user_id'])->value('business_name');
            $transaction_session[$key]['bank_name'] = MIDDetail::where('id', $transaction_session[$key]['payment_gateway_id'])->value('bank_name');
            $transaction_session[$key]["gateway_id"] = $value->gateway_id;
        }

        // get first array from the array
        $transaction_session = $transaction_session[0] ?? $transaction_session;

        $companyName = DB::table('applications')
            ->select('business_name', 'user_id')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'applications.user_id')
                    ->where('users.main_user_id', '0');
            })
            //->where('applications.is_delete', '0')
            ->get()
            ->toArray();
        $payment_gateway_id = \DB::table('middetails')->pluck('bank_name', 'id')->all();
        return view($this->moduleTitleP . '.restore', compact('transaction_session', 'data', 'companyName', 'payment_gateway_id'));
    }
}