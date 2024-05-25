<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WonderlandVisa extends Controller
{
    use StoreTransaction;
    
    const BASE_URL = 'https://pay.wonderlandpay.com/TPInterface'; // live
    // const BASE_URL = 'https://pay.wonderlandpay.com/TestTPInterface'; // test
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    // ================================================
    /* method : transaction
     * @param  : 
     * @Description : wonderland api call
     */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        if($input["amount_in_usd"] < 3.5){
            return [
                'status' => '5',
                'reason' => 'Amount should be more than 3.5 USD',
                'order_id' => $input['order_id'],
            ];
        }
        $cardDetails = $input['card_no'] . '|' . $input['cvvNumber'];
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route('wonderlandvisa.pendingBlade', [
                Crypt::encryptString($input['session_id']),
                Crypt::encryptString($cardDetails)
            ]),
        ];
    }

    public function pendingBlade($session_id,$cardDetails){
        $session_id = Crypt::decryptString($session_id); 
        $cardDetails = Crypt::decryptString($cardDetails);
        $input = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->value('request_data');

        if ($input != null) {
            $input = json_decode($input, true);
        } else {
            return abort('404');
        }
        $cardDetails = explode('|', $cardDetails);
        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
        $signSrc = $check_assign_mid->mid_number.$check_assign_mid->gateway_no.$input['order_id'].$input["converted_currency"].$input["converted_amount"].$cardDetails['0'].$input['ccExpiryYear'].$input['ccExpiryMonth'].$cardDetails["1"].$check_assign_mid->key;
        $signInfo = hash('sha256', trim($signSrc));
        return view('gateway.wonderland.pendingBlade', compact('check_assign_mid', 'input','signInfo','cardDetails'));
    }

    public function return(Request $request,$sessionId){
        $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $sessionId)
                ->first();

        if ($transaction_session == null) {
           return abort(404);
        }
        $input = json_decode($transaction_session->request_data, 1);
        $input['gateway_id'] = $request['tradeNo'] ?? null;
        $this->updateGatewayResponseData($input, $request->toArray());
        if($request["orderStatus"]==1){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        }elseif ($request["orderStatus"] == 0) {
            $input['status'] = '0';
            $input['reason'] = (isset($request['orderInfo']) ? $request['orderInfo'] : 'Your transaction could not processed.');
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function notify(Request $request,$sessionId){
        $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $sessionId)
                ->first();

        if ($transaction_session == null) {
           return abort(404);
        }
        $input = json_decode($transaction_session->request_data, 1);
        sleep(10);
        $input['gateway_id'] = $request['tradeNo'] ?? null;
        $this->updateGatewayResponseData($input, $request->toArray());
        if($request["orderStatus"]==1){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        }elseif ($request["orderStatus"] == 0) {
            $input['status'] = '0';
            $input['reason'] = (isset($request['orderInfo']) ? $request['orderInfo'] : 'Your transaction could not processed.');
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

}