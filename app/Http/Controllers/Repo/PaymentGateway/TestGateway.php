<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;

class TestGateway extends Controller
{
    use StoreTransaction;

    // ================================================
    /* method : __construct
    * @param  : 
    * @description : create new instance of the class
    */ // ===============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    // ================================================
    /* method : stripeForm
    * @param  :
    * @Description : Load stripe test form
    */ // ==============================================
    public function checkout($input, $check_assign_mid)
    {
        $input['gateway_id'] = 'ACQ'. strtoupper(\Str::random(4)) . time();

        $this->updateGatewayResponseData($input, $input);

        // redirect 3ds page
        if ($input['user_card_no'] == '4000056655665556') {
            return [
                'status' => '7',
                'reason' => "Please redirect to the specified 'payment_link' to complete the transaction processing.",
                'payment_link' => route('test-stripe', $input['session_id']),
                'gateway_id' => $input['gateway_id'],
            ];
        // success
        } elseif ($input['user_card_no'] == '4000002500001001') {
            return [
                'status' => '1',
                'reason' => 'Your payment has been successfully completed.',
                'order_id' => $input['order_id'],
                'gateway_id' => $input['gateway_id'],
            ];
        } elseif ($input['user_card_no'] == '4000000320000021') {
            return [
                'status' => '0',
                'reason' => 'Insufficient fund.',
                'order_id' => $input['order_id'],
                'gateway_id' => $input['gateway_id'],
            ];
        } else {
            return [
                'status' => '0',
                'reason' => 'Card not supported for testing.',
                'order_id' => $input['order_id'],
                'gateway_id' => $input['gateway_id'],
            ];
        }
    }

    public function stripeForm(Request $request, $session_id)
    {
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $request_data = json_decode($transaction_session->request_data, 1);

        if (!in_array($request_data['payment_gateway_id'], [1, 2])) {
            abort(404);
        }

        return view('gateway.test3dSecure', compact('session_id', 'request_data'));
    }

    // ================================================
    /* method : test3DSFormSubmit
    * @param  :
    * @Description : submit the test 3DS form
    */ // ==============================================
    public function test3DSFormSubmit(Request $request, $session_id)
    {
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_completed', 0)
            ->first();

        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, 1);

        if (!in_array($input['payment_gateway_id'], [1, 2])) {
            abort(404);
        }

        if (isset($request->status) && $request->status == '1') {
            $input['status'] = '1';
            $input['reason'] = 'Your payment has been successfully completed.';
        } elseif (isset($request->status) && $request->status == '0') {
            $input['status'] = '0';
            $input['reason'] = 'Test transaction was declined by user.';
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Test transaction was declined by user.';
        }

        // update transaction_session as completed
        try {
            $session_update_data = TransactionSession::where('transaction_id', $session_id)
                ->first();

            $session_update_data->update([
                'is_completed' => 1,
            ]);

            $session_update_data->save();
        } catch (\Exception $e) {
            \Log::info([
                'session_update_error' => $e->getMessage()
            ]);
        }

        // store transaction
        $transaction_response = $this->storeTransaction($input);

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }
}
