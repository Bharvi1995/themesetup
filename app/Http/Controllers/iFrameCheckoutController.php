<?php

namespace App\Http\Controllers;

use DB;
use URL;
use Mail;
use View;
use Input;
use Session;
use Redirect;
use Validator;
use App\User;
use App\WebsiteUrl;
use App\Admin;
use App\Gateway;
use App\ImageUpload;
use App\MainMID;
use App\Transaction;
use App\TransactionSession;
use App\Merchantapplication;
use App\Http\Controllers\Controller;
use App\Mail\TransactionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\StoreTransaction;
use App\TransactionHostedSession;
use Illuminate\Support\Facades\Http;
use App\Traits\Mid;
class iFrameCheckoutController extends Controller
{
    use StoreTransaction,Mid;

    protected $user, $Transaction;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User;
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    // ================================================
    /* method : index
    * @param  : 
    * @description : Show the iframe form view.
    */ // ===============================================
    public function index($token, Request $request)
    {
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'dlwerQWbuasEwomsdvWsvmlfRErvsdsd';
        $secret_iv = '9lkkjjWevsdv67sdjnNwqeQ9veWEbeRvf';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        // decrypt token
        $iframe_json = openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv);
        if ($iframe_json == false) {
            return view('gateway.response')->with('responseMessage', 'Invalid payment link.');
        }

        $iframe_array = json_decode($iframe_json, 1);

        $userData = User::where('id', $iframe_array['user_id'])
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->first();

        if (empty($userData)) {
            return view('gateway.response')->with('responseMessage', 'Merchant account disabled or deleted.');
        }

        if ($userData->mid == '0') {
            return view('gateway.response')->with('responseMessage', 'Merchant account is temporarily disabled.');
        }

        $check_assign_mid = checkAssignMid($iframe_array['mid']);

        if ($check_assign_mid == false) {
            return view('gateway.response')->with('responseMessage', 'Merchant account is temporarily disabled.');
        }
        $required_fields = json_decode($check_assign_mid->required_fields, 1);
        // dd($required_fields);
        return view('gateway.iframe', compact('token', 'required_fields', 'iframe_array', 'userData'));
    }

    // ================================================
    /* method : store
    * @param  : 
    * @description : submit iframe
    */ // ===============================================
    public function store(Request $request, $token)
    {
        $this->validate($request, [
            'user_first_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'user_last_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            // 'user_address' => 'required|min:2|max:250',
            // 'user_country' => 'required|max:2|min:2|regex:(\b[A-Z]+\b)',
            // 'user_state' => 'required|min:2|max:250',
            // 'user_city' => 'required|min:2|max:250',
            // 'user_zip' => 'required|min:2|max:250',
            'user_email' => 'required|email',
            'user_phone_no' => 'required|min:5|max:20',
            'user_amount' => 'required|regex:/^\d+(\.\d{1,9})?$/',
            'user_currency' => 'required|max:3|min:3|regex:(\b[A-Z]+\b)',
        ]);

        if (!empty($token)) {
            $encrypt_method = "AES-256-CBC";
            $secret_key = 'dlwerQWbuasEwomsdvWsvmlfRErvsdsd';
            $secret_iv = '9lkkjjWevsdv67sdjnNwqeQ9veWEbeRvf';

            // hash
            $key = hash('sha256', $secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            // decrypt token
            $iframe_json = openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv);

            if ($iframe_json == false) {
                return view('gateway.response')->with('responseMessage', 'Invalid payment link.');
            }

            $iframe_array = json_decode($iframe_json, 1);
        } else {
            abort(404);
        }
        $users = User::find($iframe_array["user_id"]);
        // dd($users);
        $authorization = base64_encode($users->email.":".$users->api_key);
        $input = \Arr::except($request->all(), array('_token'));
        // amount and currency assign
        if (isset($iframe_array['amount']) && !empty($iframe_array['amount'])) {
            $input['user_amount'] = $iframe_array['amount'];
        }
        if (isset($iframe_array['currency']) && !empty($iframe_array['currency'])) {
            $input['user_currency'] = $iframe_array['currency'];
        }

        // if (isset($iframe_array['type']) && $iframe_array['type'] == 'Card') {
        //     $url = env('APP_URL') . '/api/hosted/transaction';
        // } elseif (isset($iframe_array['type']) && $iframe_array['type'] == 'Crypto') {
        //     $url = env('APP_URL') . '/api/crypto/transaction';
        // } elseif (isset($iframe_array['type']) && $iframe_array['type'] == 'Bank') {
        //     $url = env('APP_URL') . '/api/bank/transaction';
        // } else {
        //     return view('gateway.response')->with('responseMessage', 'Invalid payment link.');
        // }
        $input['session_id'] = time(). strtoupper(\Str::random(2)).'ITP';
        $input['order_id'] = time(). strtoupper( \Str::random(2)).'ITP';

        // user IP and domain and request from API
        $input['request_from_ip'] = $request->ip();
        $input['payment_type'] = 'card';
        $input['request_origin'] = $_SERVER['HTTP_HOST'];
        $input['is_request_from_vt'] = 'Seamless API';
        $input['user_id'] = $users->id;
        $input['payment_gateway_id'] = $users->mid;
        $input['amount_in_usd'] = $this->amountInUSD($input);
        
        $this->transactionSession->storeData($input);

        $url = env('APP_URL') . 'api/seamless/transaction';
        // $url = "https://gateway.testpay.com/api/seamless/transaction";
        // echo $url;
        $paramsArray = [
            'user_first_name' => $input['user_first_name'],
            'user_last_name' => $input['user_last_name'],
            'user_address' => isset($input['user_address']) ? $input['user_address'] : '',
            'user_country' => isset($input['user_country']) ? $input['user_country'] : '',
            'user_state' => isset($input['user_state']) ? $input['user_state'] : '',
            'user_city' => isset($input['user_city']) ? $input['user_city'] : '',
            'user_zip' => isset($input['user_zip']) ? $input['user_zip'] : '',
            'user_email' => $input['user_email'],
            'user_phone_no' => isset($input['user_phone_no']) ? $input['user_phone_no'] : '',
            'user_amount' => $input['user_amount'],
            'user_currency' => $input['user_currency'],
            'user_redirect_url' => route('hosted-checkout-response', $token),
        ];
        $input["user_redirect_url"] = route('hosted-checkout-response', $token);
        // $authorization = "dGhha3VyeXV2cmFqMjlAZ21haWwuY29tOjJ8UndTVmlKcW82c0tkMlBRQ3FvZjU0N1pmOURQbnVrYUxqNW5uR3hxSA==";
        $requestBody = json_encode($paramsArray);
        $headers = [
            'Authorization: Basic '.$authorization,
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        \Log::info(["paramsArray" => $paramsArray]);
        \Log::info(["headers" => $headers]);
        \Log::info(["url" => $url]);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        \Log::info(["response" => $response]);
        curl_close($curl);

        $responseData = json_decode($response, true);
        $input['response_url'] = route('hosted-checkout-response', $token);
        if (isset($responseData['status']) && $responseData['status'] == 'Redirect') {
            $input['status'] = 7;
            $input['reason'] = $responseData['message']; 
            //$this->transaction->storeData($input);
            $this->storeTransaction($input);
            return redirect($responseData['payment_link']);
        } elseif (isset($responseData['status']) && $responseData['status'] == 'Success') {
            $input['status'] = 1;
            $input['reason'] = $responseData['message'];
            $this->storeTransaction($input);
            //$this->transaction->storeData($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        } elseif (isset($responseData['status']) && $responseData['status'] == 'Failed') {
            $input['status'] = 0;
            $input['reason'] = $responseData['message'];
            //$this->transaction->storeData($input);
            $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        } elseif (isset($responseData['status']) && $responseData['status'] == 'Pending') {
            $input['status'] = 2;
            $input['reason'] = $responseData['message'];
            // $this->transaction->storeData($input);
            $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        } elseif (isset($responseData['status']) && in_array($responseData['status'], ['Blocked', 'Unauthorized'])) {
            $input['status'] = 5;
            $input['reason'] = $responseData['message'];
            $this->storeTransaction($input);
            // $this->transaction->storeData($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        } else {
            return view('gateway.response')->with('responseMessage', 'Something went wrong, please try again later.');
        }
    }

    // ================================================
    /* method : hostedCheckoutResponse
    * @param  : 
    * @description : hosted and iframe response page
    */ // ===============================================
    public function hostedCheckoutResponse(Request $request, $token)
    {
        $response = $request->all();

        return view('gateway.iframecheckoutresponse', compact('response', 'token'));
    }

    // ================================================
    /* method : checkoutCancel
    * @param  : 
    * @description : cancel transaction
    */ // ===============================================
    public function checkoutCancel(Request $request, $id)
    {
        $session_data = TransactionHostedSession::where('transaction_id', $id)
            ->first();

        if (empty($session_data)) {
            abort(404);
        }

        $input = json_decode($session_data['request_data'], 1);
        unset($input["api_key"]);

        $session_data->is_completed = '1';
        $session_data->save();

        $input['status'] = '0';
        $input["reason"] = "Transaction canceled by client.";

        $count = \DB::table("transactions")->where("session_id", $id)->count();
        if ($count == 0) {
            $transaction_response = $this->transaction->storeData($input);
        }

        if (empty($input['response_url'])) {
            $input['response_url'] = route('hosted-checkout-response', 'test');
        }

        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // ================================================
    /* method : testRequest
    * @param  : 
    * @description : test iframe call and print hosted response
    */ // ===============================================
    public function testRequest(Request $request)
    {
        $url = 'https://testpay.com/api/hosted/transaction';

        $api_key = '44|uyoUyNaEOgUdz8XoYxDGx8Ws8FScUBCkQCZ6Ee8K';

        $paramsArray = [
            'api_key' => $api_key,
            'first_name' => 'testing',
            'last_name' => 'testing',
            'address' => 'testing',
            'country' => 'US',
            'state' => 'state',
            'city' => 'city',
            'zip' => '123456',
            'email' => 'email@gmail.com',
            'phone_no' => '91989898998',
            'amount' => '10',
            'currency' => 'USD',
            'response_url' => route('hosted-checkout-response'),
            'ip_address' => $this->getClientIP(),
        ];

        $requestBody = json_encode($paramsArray);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        $responseData = json_decode($response, true);
    }

    // ================================================
    /* method : getClientIP
    * @param  : 
    * @description : return client ip address
    */ // ===============================================
    public function getClientIP()
    {
        $ip_address = '';

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip_address = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip_address = 'UNKNOWN';
        }

        return $ip_address;
    }
}
