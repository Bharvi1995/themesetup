<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;

class Dixonpayvisa extends Controller
{
    use StoreTransaction;
    //const BASE_URL = 'https://secure.dixonpay.com/test/indirect/payment'; // test
    const BASE_URL = 'https://secure.dixonpay.com/indirect/payment'; // live

    public function checkout($input, $check_assign_mid)
    {
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $input["session_id"])
            ->first();
        if ($data == null) {
            return abort(404);
        }
        $inputData = json_decode($data->request_data, 1);
        $cardDetails = $input["card_no"]."_".$input["ccExpiryMonth"]."_".$input["ccExpiryYear"]."_".$input["cvvNumber"];
        $inputData["reqest_data"] = \Crypt::encryptString($cardDetails);
        \DB::table('transaction_session')->where('transaction_id', $input["session_id"])->update(["request_data"=>json_encode($inputData)]);
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect.',
            'redirect_3ds_url' => route('dixonpayvisa.form', $input['session_id'])
        ];
    }

    public function form($session_id){
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if ($data == null) {
            return abort(404);
        }
        
        $input = json_decode($data->request_data, 1);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        $input['converted_amount'] = number_format((float)$input['converted_amount'], 2, '.', '');
        $decryptCard = \Crypt::decryptString($input["reqest_data"]);
        $cardArr = explode("_", $decryptCard);
        $hash_data = $check_assign_mid->mer_no.$check_assign_mid->terminal_no.$input['session_id'].$input['converted_currency'].$input['converted_amount'].$cardArr['0'].$cardArr['2'].$cardArr['1'].$cardArr['3'].$check_assign_mid->key;
        $signature = hash('sha256', trim($hash_data));
        $request_data = [
            'merNo' => $check_assign_mid->mer_no,
            'terminalNo' => $check_assign_mid->terminal_no,
            'orderNo' => $input['session_id'],
            'orderCurrency' => $input['converted_currency'],
            'orderAmount' => $input['converted_amount'],
            'cardNo' => $cardArr['0'],
            'cardExpireMonth' => $cardArr['1'],
            'cardExpireYear' => $cardArr['2'],
            'cardSecurityCode' => $cardArr['3'],
            'firstName' => $input['first_name'],
            'lastName' => $input['last_name'],
            'email' => $input['email'],
            'returnUrl' => route('dixonpayvisa.return',$input["session_id"]),
            'notifyUrl' => route('dixonpayvisa.notify',$input["session_id"]),
            'phone' => $input['phone_no'],
            'country' => $input['country'],
            'state' => $input['state'],
            'city' => $input['city'],
            'address' => $input['address'],
            'zip' => $input['zip'],
            'encryption' => $signature,
            'url' => self::BASE_URL,
            'website' => $check_assign_mid->website
        ];
        return view("gateway.dixonpayvisa",compact('request_data'));
    }

    public function return(Request $request,$session_id){
        $response = $request->all();
        \Log::info([
            'dixonpayvisa-return' => $response,
            'id' => $session_id
        ]);
        if (! empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response["orderStatus"] == "0"){
                $input['status'] = '0';
                $input['reason'] = (isset($response['orderInfo']) ? $response['orderInfo'] : 'Your transaction could not processed.');
            }else if ($response["orderStatus"] == "1"){
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            }else {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            unset($input["reqest_data"]);
            $input['gateway_id'] = $response['tradeNo'] ?? null;
            $this->updateGatewayResponseData($input, $response);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function notify(Request $request,$session_id){
        $response = $request->all();
        \Log::info([
            'dixonpayvisa-notify' => $response,
            'id' => $session_id
        ]);
        if (! empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response["orderStatus"] == "0"){
                $input['status'] = '0';
                $input['reason'] = (isset($response['orderInfo']) ? $response['orderInfo'] : 'Your transaction could not processed.');
            }else if ($response["orderStatus"] == "1"){
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            }else {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            unset($input["reqest_data"]);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            exit("notify");
        }
    }
}
