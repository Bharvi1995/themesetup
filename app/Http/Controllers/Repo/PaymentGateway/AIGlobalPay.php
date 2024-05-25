<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;

class AIGlobalPay extends Controller
{
    use StoreTransaction;

    // const BASE_URL = 'https://try.aiglobalpay.com'; // test mode
    const BASE_URL = 'https://tran.aiglobalpay.com'; // live mode

    // ================================================
    /* method : __construct
    * @param  :
    * @Description : Create a new controller instance.
    */ // ==============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    public function checkout($input, $check_assign_mid)
    {
        $signSrc = $check_assign_mid->mid . $check_assign_mid->gateway . $input['session_id'] . $input["converted_currency"] . $input["converted_amount"]  . $input['card_no'] . $input['ccExpiryYear'] . $input['ccExpiryMonth'] . $input['cvvNumber'] . $check_assign_mid->key;
        $signInfo = hash('sha256', trim($signSrc));

        $data = [
            'merNo' => $check_assign_mid->mid,
            'gatewayNo' => $check_assign_mid->gateway,
            'orderNo' => $input['session_id'],
            'orderCurrency' => $input["converted_currency"],
            'orderAmount' => $input["converted_amount"],
            "goodsInfo" => $input["session_id"] . "#",
            'cardNo' => $input['card_no'],
            'month' => $input['ccExpiryMonth'],
            'year' => $input['ccExpiryYear'],
            'cvv' => $input['cvvNumber'],
            'firstName' => $input['first_name'],
            'lastName' => $input['last_name'],
            'ip' => $input['ip_address'],
            'email' => $input['email'],
            'phone' => $input['phone_no'],
            'country' => $input['country'],
            'state' => $input['state'],
            'city' => $input['city'],
            'address' => $input['address'],
            'zip' => $input['zip'],
            "shipFirstName" => $input['first_name'],
            "shipLastName" => $input['last_name'],
            "shipEmail" => $input['email'],
            "shipPhone" => $input['phone_no'],
            "shipCountry" => $input['country'],
            "shipState" => $input['state'],
            "shipCity" => $input['city'],
            "shipAddress" => $input['address'],
            "shipZip" => $input['zip'],
            'returnUrl' => route('aiglobalpay.return', $input["session_id"]),
            'notifyUrl' => route('aiglobalpay.notify', $input["session_id"]),
            "os" => "win10",
            "brower" => "google",
            "browerLang" => "en",
            "timeZone" => "-180",
            "resolution" => "2K",
            "isCopyCard" => "0",
            "newCookie" => "ip=127.0.0.1",
            'webSite' => $check_assign_mid->website,
            'signInfo' => $signInfo,
        ];

        $request_url = self::BASE_URL . '/payment';

        $response = $this->curlPostRequest($request_url, $data);

        // \Log::info([
        //     'aiglobalpay-response' => $response
        // ]);

        $input['gateway_id'] = $response->tradeNo ?? null;
        $this->updateGatewayResponseData($input, $response);

        if ($response->orderStatus == '1') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
            $input['descriptor'] = $check_assign_mid->descriptor;
        } else if ($response->orderStatus == '-1') {
            $input['status'] = '7';
            $input['reason'] = '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.';
            $input['redirect_3ds_url'] = $response->redirectUrl;
        } else {
            $input['status'] = '0';
            $input['reason'] = $response->orderInfo ? $response->orderInfo : 'Transaction declined.';
        }

        return $input;
    }

    public function return(Request $request, $session_id)
    {
        $response = $request->all();
        // \Log::info([
        //     'aiglobalpay-return' => $response,
        //     'id' => $session_id
        // ]);
        if (!empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response["orderStatus"] == "0") {
                $input['status'] = '0';
                $input['reason'] = (isset($response['orderInfo']) ? $response['orderInfo'] : 'Your transaction could not processed.');
            } else if ($response["orderStatus"] == "1") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            unset($input["reqest_data"]);
            // $input['gateway_id'] = $response['tradeNo'] ?? null;
            $this->updateGatewayResponseData($input, $response);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function notify(Request $request, $session_id)
    {
        $response = $request->all();
        // \Log::info([
        //     'aiglobalpay-notify' => $response,
        //     'id' => $session_id
        // ]);
        if (!empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response["orderStatus"] == "0") {
                $input['status'] = '0';
                $input['reason'] = (isset($response['orderInfo']) ? $response['orderInfo'] : 'Your transaction could not processed.');
            } else if ($response["orderStatus"] == "1") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            unset($input["request_data"]);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            exit("notify");
        }
    }

    public function curlPostRequest($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}
