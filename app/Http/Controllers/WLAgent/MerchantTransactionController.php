<?php

namespace App\Http\Controllers\WLAgent;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Application;
use App\Transaction;
use View;
use Storage;
use Redirect;
use Hash;
use Auth;
use Str;
use Validator;
use App\Exports\WLMerchantAllTransactionExport;
use App\Exports\WLMerchantCryptoTransactionExport;
use App\Exports\WLMerchantRefundTransactionExport;
use App\Exports\WLMerchantChargebacksTransactionExport;
use App\Exports\WLMerchantSuspiciousTransactionExport;
use App\Exports\WLMerchantDeclinedTransactionExport;
use App\Exports\WLMerchantRetrievalTransactionExport;
use App\Exports\WLMerchantTestTransactionExport;

class MerchantTransactionController extends WLAgentUserBaseController
{
    protected $transaction;
    public function __construct()
    {
        parent::__construct();
        $this->transaction = new Transaction;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function allTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getAllMerchantTransactionDataWLAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('applications.user_id', 'applications.business_name')
            ->toArray();



        return view('WLAgent.merchantTransactions.index', compact('businessName', 'data'));
    }

    public function cryptoTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getMerchantCryptoTransactionDataWLAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('applications.user_id', 'applications.business_name')
            ->toArray();

        return view('WLAgent.merchantTransactions.crypto', compact('businessName', 'data'));
    }

    public function refundTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getMerchantRefundTransactionDataWLAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('applications.user_id', 'applications.business_name')
            ->toArray();

        return view('WLAgent.merchantTransactions.refund', compact('businessName', 'data'));
    }

    public function chargebacksTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getMerchantChargebacksTransactionDataWLAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('applications.user_id', 'applications.business_name')
            ->toArray();

        return view('WLAgent.merchantTransactions.chargebacks', compact('businessName', 'data'));
    }

    public function retrievalTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getMerchantRetrievalTransactionDataWLAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('applications.user_id', 'applications.business_name')
            ->toArray();

        return view('WLAgent.merchantTransactions.retrieval', compact('businessName', 'data'));
    }

    public function suspiciousTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getMerchantSuspiciousTransactionDataWLAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('applications.user_id', 'applications.business_name')
            ->toArray();

        return view('WLAgent.merchantTransactions.suspicious', compact('businessName', 'data'));
    }

    public function declinedTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getMerchantDeclinedTransactionDataWLAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('applications.user_id', 'applications.business_name')
            ->toArray();

        return view('WLAgent.merchantTransactions.declined', compact('businessName', 'data'));
    }

    public function testTransaction(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->transaction->getMerchantTestTransactionDataWLAgent($input, $noList);

        $businessName = Application::join('users', 'users.id', 'applications.user_id')
            ->orderBy('users.id', 'desc')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('applications.user_id', 'applications.business_name')
            ->toArray();

        return view('WLAgent.merchantTransactions.test', compact('businessName', 'data'));
    }

    public function exportAllTransaction(Request $request)
    {
        return (new WLMerchantAllTransactionExport())->download();
    }

    public function exportCryptoTransaction(Request $request)
    {
        return (new WLMerchantCryptoTransactionExport())->download();
    }

    public function exportRefundTransaction(Request $request)
    {
        return (new WLMerchantRefundTransactionExport())->download();
    }

    public function exportChargebacksTransaction(Request $request)
    {
        return (new WLMerchantChargebacksTransactionExport())->download();
    }

    public function exportSuspiciousTransaction(Request $request)
    {
        return (new WLMerchantSuspiciousTransactionExport())->download();
    }

    public function exportDeclinedTransaction(Request $request)
    {
        return (new WLMerchantDeclinedTransactionExport())->download();
    }

    public function exportRetrievalTransaction(Request $request)
    {
        return (new WLMerchantRetrievalTransactionExport())->download();
    }

    public function exportTestTransaction(Request $request)
    {
        return (new WLMerchantTestTransactionExport())->download();
    }

    public function refund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refund_reason' => 'required',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method', 'id'));
        $input['refund'] = '1';
        $input['refund_date'] = date("Y-m-d H:i:s", time());
        if ($validator->passes()) {

            $userIds = \DB::table('users')
                ->where('is_white_label', '1')
                ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
                ->pluck('id');

            if ($userIds) {

                $data = Transaction::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at', 'transactions.chargebacks', 'transactions.refund')
                    ->join('applications', 'applications.user_id', 'transactions.user_id')
                    ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
                    ->where('transactions.id', $request->get('id'))
                    ->whereIn('transactions.user_id', $userIds)
                    ->orderBy('transactions.id', 'DESC')->first();

                if (!empty($data)) {

                    if ($this->Transaction->updateData($request->get('id'), $input)) {
                        // try {
                        //     \Auth::user()->notify(new UserClaimRefund(Transaction::find($request->get('id'))));
                        // } catch (\Exception $e) {

                        // }
                        return response()->json(['success' => '1']);
                    } else {
                        return response()->json(['success' => '0']);
                    }
                } else {
                    return response()->json(['success' => '0']);
                }
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }
}