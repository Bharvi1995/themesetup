<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\TxTry;
use App\BlockData;
use App\Transaction;
use App\TransactionSession;
use App\WebsiteUrl;
use App\Http\Controllers\Controller;
use App\Traits\Mid;
use App\Traits\RuleCheck;
use App\Traits\BinChecker;
use App\Traits\StoreTransaction;
use App\Transformers\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\Facades\Redirect;

class ApiController extends Controller
{
    use Mid, RuleCheck, StoreTransaction, BinChecker;

    protected $user, $tx_try, $transaction, $transactionSession;

    // ================================================
    /* method : __construct
     * @param  : 
     * @description : create new instance of the class
     */// ===============================================

    public function __construct(Request $request)
    {
        $this->user = new User;
        $this->tx_try = new TxTry;
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
        // $this->validateBasicAuth($request);
    }

    private function validateBasicAuth(Request $request)
    {
        $authorization = $request->header('Authorization');
        if (!$authorization || !str_starts_with($authorization, 'Basic ')) {
            $input['status'] = '6';
            $input['reason'] = 'The request lacks valid authentication credentials. Please check the provided header parameters.';
            abort(ApiResponse::unauthorised($input));
        }
        $apikey = substr($authorization, 6);
        $user = User::where("api_key",$apikey)->where("is_active","1")->whereNull("deleted_at")->first();
        if (!$user) {
            $input['status'] = '6';
            $input['reason'] = 'The request lacks valid authentication credentials. Please check the provided header parameters.';
            abort(ApiResponse::unauthorised($input));
        }
        $request->merge(['user' => $user]);
    }

    // ================================================
    /* method : store
     * @param  : $request Request
     * @description : receive api v2 request data
     */// ===============================================
    public function store(Request $request)
    {
        $privateKeyResource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $privateKey = "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCsBimrHtu4rdMxqnPSKL31Jp2f+DdA3bp1VGt6MJTn3TFhRk1hlkkmBMO/ha8y/th37XYQZPiauRgH+QT2eCEhhjG4Jc5Ne7C611g647CRtFZFAGakzKMh6SVjm+cS9cZTzek2UUPyucfTTX6a/bNHUc6uhu9bUC6P8xJIPmALbwCYKYjzYcfObTP7RuJvzKMJJCcjXFBYZFvhDgRroh1RKePVR+JmDByeydZHdVIibxnvdOa+707HYuPEvznUMLWVnpSE2SDQH/72zDPZ37325MMNrF35ZD/ag+MQfhg32ku3NAw8BSgM4fbe51eEF1mrLM4RmSBXMLDAQvCXH9D7AgMBAAECggEAKb7VHdG/QHHubn8O3FFGx9c3KTrzLaqhNRfnfR+NAzeKZDDLVoNB/Rfq6O9oNNfjcDcQD6pxKhsA2e50ZduBLsGCD04dYnBB0xWvM5tNng5FYTlXr2Z5sCEEEcgjq1Y5atztW2EdPCoZdA26S6KFfyk7Ht9f9qjeo9A4/48jf6JqaYXWCusvVD9Fx63X5nQ3tye1L9M80qX9Pok8sb6COGGmFxuOA8StGjCeMO5jOEEauEpf2jSolHfvDYRM7s89VRuLCPC1wjHGRtlDBgM2yX1YQhVM04QUDEoWKtJqIaEk04tWPvljsJ0nbXshodL828GrYZjrO6phr2pRCpOqAQKBgQDcTSro3QfXFCWQCQzmTJEkiTQJGvU/nwRxENEsuM+bE1M71nl/RP4OnrMX6Z8fuEmwtl6+fnsQDSnFzGgksLBjbY+qhwnwIsLY40+bS/MtO7pjgplmDyUtGTDV5dAuo2hr9IdyBLikOOEtD5P0pUQf3yJzsAxyyGqIB9D9pJMngwKBgQDH5kpZw+rF+/n1N/aDy3jRwui1FzRiA4CgkUrTqJkDKSwvlKvLMDTuFT6HOhfN3XTx1Av7XXvtMjse6Zsp8yT0VwvSOaDaWlAiltebqBfibRATUa5JKEzb5GFvNmNm8HbryiPyIWnVDSofqKcaSqmGTuWqrr5val6rI2QIwpH/KQKBgQCB2qKeXBrQ9jkl64/E+ADdzlnzvAYvmCXgF4+UkuMcf4miTcuT7zDpoTXjtHttEQ2usfCqzJbxYTDsPI5ugg+Wq+/xDmQXPgYoHeTAn0YZtYkqOsL825SIPr8AddP+iad1as+jr5C+jCB+lR4bKIc9WiNOmcjcp7HTuPfBao6qzQKBgCbEjMrX6iI3egSKs+5febEEoejs9SXFFB8Pznk6C2LHi2A27xJa6Qj7acMECzXLqzBzNVesi6o2wax/Fa6PDy2r7Aj7UCzIsx3iLzJq6Sbqi+GTR7+8ZxuGMTdGTwTZwdZ8v3fn7wR4pyC4rp+0tyfrCTOO7DPjZzS9ilsAkRvxAoGBALtcjYF3D1SHpnAE4LSH6WzDKfvr80KUD+K/WZE/lWOaipcgB4C7rX92Tb2jJR88BV6Nd9SAbAo9+T7dx4e+XF0qy1TX4CRWK4J5x2Kw/dceoPCz3ckjXLU7R00vKgIK5+hWTgStaxxG2Ba/S14bHk+/0E9o9WtBCRAIvSwV0Pvh";
        // dd($privateKeyResource);
        openssl_pkey_export($privateKeyResource, $privateKey);
        $publicKeyDetails = openssl_pkey_get_details($privateKeyResource);
        $publicKey = $publicKeyDetails['key'];
        // dd($publicKeyDetails);
        // file_put_contents('private_key.pem', $privateKey);
        // file_put_contents('public_key.pem', $publicKey);

        // Step 2: Encrypt Data with the Public Key
        $dataToEncrypt = '{
            "customer": {
                "firstname": "firstname",
                "lastname": "lastname",
                "mobile": "+2348158200000",
                "country": "NG",
                "email": "email@pay.dev"
            },
            "order": {
                "amount": 3,
                "reference": "326236111",
                "description": "Pay",
                "currency": "USD"
            },
            "payment": {
                "RedirectUrl": "https://www.hi.com"
            }
        }';
        // $publicKey = file_get_contents('public_key.pem');

        $publicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArAYpqx7buK3TMapz0ii99Sadn/g3QN26dVRrejCU590xYUZNYZZJJgTDv4WvMv7Yd+12EGT4mrkYB/kE9nghIYYxuCXOTXuwutdYOuOwkbRWRQBmpMyjIeklY5vnEvXGU83pNlFD8rnH001+mv2zR1HOrobvW1Auj/MSSD5gC28AmCmI82HHzm0z+0bib8yjCSQnI1xQWGRb4Q4Ea6IdUSnj1UfiZgwcnsnWR3VSIm8Z73Tmvu9Ox2LjxL851DC1lZ6UhNkg0B/+9swz2d+99uTDDaxd+WQ/2oPjEH4YN9pLtzQMPAUoDOH23udXhBdZqyzOEZkgVzCwwELwlx/Q+wIDAQAB";
        openssl_public_encrypt($dataToEncrypt, $encryptedData, $publicKey);
        $encryptedDataBase64 = base64_encode($encryptedData);
        dd($encryptedDataBase64);
        echo "Encrypted Data: " . $encryptedDataBase64 . PHP_EOL;


        $this->validateBasicAuth($request);
        // $input = $request->only(['user_first_name', 'user_last_name', 'user_email', 'user_phone_no', 'user_amount', 'user_currency', 'user_address', 'user_country', 'user_state', 'user_city', 'user_zip', 'user_order_ref', 'user_redirect_url', 'user_webhook_url','user','user_card_no','user_ccexpiry_month','user_ccexpiry_year','user_cvv_number']);
        $requestData = $request->only(['payment', 'order', 'customer', 'user']);
        $input["user"] = $requestData['user'];
        if(isset($requestData["customer"])){
            if(isset($requestData["customer"]["user_first_name"])){
                $input["user_first_name"] = $requestData["customer"]["user_first_name"];
            }
            if(isset($requestData["customer"]["user_last_name"])){
                $input["user_last_name"] = $requestData["customer"]["user_last_name"];
            }
            if(isset($requestData["customer"]["user_phone_no"])){
                $input["user_phone_no"] = $requestData["customer"]["user_phone_no"];
            }
            if(isset($requestData["customer"]["user_email"])){
                $input["user_email"] = $requestData["customer"]["user_email"];
            }
            if(isset($requestData["customer"]["user_address"])){
                $input["user_address"] = $requestData["customer"]["user_address"];
            }
            if(isset($requestData["customer"]["user_country"])){
                $input["user_country"] = $requestData["customer"]["user_country"];
            }
            if(isset($requestData["customer"]["user_state"])){
                $input["user_state"] = $requestData["customer"]["user_state"];
            }
            if(isset($requestData["customer"]["user_city"])){
                $input["user_city"] = $requestData["customer"]["user_city"];
            }
            if(isset($requestData["customer"]["user_zip"])){
                $input["user_zip"] = $requestData["customer"]["user_zip"];
            }
        }
        if(isset($requestData["order"])){
            if(isset($requestData["order"]["user_amount"])){
                $input["user_amount"] = $requestData["order"]["user_amount"];
            }
            if(isset($requestData["order"]["user_currency"])){
                $input["user_currency"] = $requestData["order"]["user_currency"];
            }
            if(isset($requestData["order"]["user_order_ref"])){
                $input["user_order_ref"] = $requestData["order"]["user_order_ref"];
            }
        }
        if(isset($requestData["payment"])){
            if(isset($requestData["payment"]['user_card_no'])){
                $input["user_card_no"] = $requestData["payment"]['user_card_no'];
            }
            if(isset($requestData["payment"]['user_ccexpiry_month'])){
                $input["user_ccexpiry_month"] = $requestData["payment"]['user_ccexpiry_month'];
            }
            if(isset($requestData["payment"]['user_ccexpiry_year'])){
                $input["user_ccexpiry_year"] = $requestData["payment"]['user_ccexpiry_year'];
            }
            if(isset($requestData["payment"]['user_cvv_number'])){
                $input["user_cvv_number"] = $requestData["payment"]['user_cvv_number'];
            }
            if(isset($requestData["payment"]['user_redirect_url'])){
                $input["user_redirect_url"] = $requestData["payment"]['user_redirect_url'];
            }
            if(isset($requestData["payment"]['user_webhook_url'])){
                $input["user_webhook_url"] = $requestData["payment"]['user_webhook_url'];
            }
        }
        // if merchant on test mode
        if (in_array($input["user"]->mid, [1, 2])) {
            $input['status'] = '6';
            $input['reason'] = 'Your account is in a live mode, but you are making test requests. Please ensure your requests match the live environment.';
            return ApiResponse::unauthorised($input);
        }
        // gateway object
        $check_assign_mid = checkAssignMID($input["user"]->mid);
        if ($check_assign_mid == false) {
            $input['status'] = '6';
            $input['reason'] = 'Your account has been deactivated. Please contact the administrator for further assistance.';
            return ApiResponse::unauthorised($input);
        }

        $validator = Validator::make($input, [
            'user_first_name' => 'required|min:3|max:100|regex:/^[a-zA-Z\s]+$/',
            'user_last_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'user_email' => 'required|email',
            'user_amount' => 'required|regex:/^\d+(\.\d{1,9})?$/',
            'user_currency' => 'required|max:3|min:3|regex:(\b[A-Z]+\b)',
            'user_address' => 'nullable|min:2|max:250',
            'user_country' => 'nullable|max:2|min:2|regex:(\b[A-Z]+\b)',
            'user_state' => 'nullable|min:2|max:250',
            'user_city' => 'nullable|min:2|max:250',
            'user_zip' => 'nullable|min:2|max:250',
            'user_phone_no' => 'nullable|min:5|max:20',
            'user_order_ref' => 'nullable|max:100',
            'user_redirect_url' => 'required|url',
            'user_webhook_url' => 'nullable|url',
            'user_card_no' => 'required',
            'user_ccexpiry_month' => 'required',
            'user_ccexpiry_year' => 'required',
            'user_cvv_number' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $input['status'] = '6';
            $input['reason'] = $errors[0] ?? 'Kindly review your request payload to ensure all required fields are provided.';
            return ApiResponse::unauthorised($input);
        }
        if ($input["user"]->is_ip_remove == '0') {
            $getIPData = WebsiteUrl::where('user_id', $input["user"]->id)
                ->where('ip_address', $this->getClientIP())
                ->first();

            // if IP is not added on the IP whitelist
            if (empty($getIPData)) {
                $input['status'] = '6';
                $input['reason'] = "Please add the requesting IP address to the IP section in your account settings. (".$this->getClientIP().")";
                return ApiResponse::unauthorised($input);
            }

            // if IP is not approved
            if ($getIPData->is_active == '0') {
                $input['status'] = '6';
                $input['reason'] = "Your request IP is currently under review for approval.";
                return ApiResponse::unauthorised($input);
            }
            $input["website_url_id"] = $getIPData->id;
        }
        $block_email = BlockData::where('type', 'Email')
            ->where('field_value', $input['user_email'])
            ->exists();
        if (!empty($block_email)) {
            $input['status'] = '5';
            $input['reason'] = "This email address is blocked for transactions. Please contact the administrator for assistance.";
            return ApiResponse::blocked($input);
        }
        // order and session id
        $input['session_id'] = 'PLAKSA'.time(). strtoupper(\Str::random(2));
        $input['order_id'] = time(). strtoupper( \Str::random(5)).'PL';

        // user IP and domain and request from API
        $input['request_from_ip'] = $request->ip();
        $input['payment_type'] = 'card';
        $input['request_origin'] = $_SERVER['HTTP_HOST'];
        $input['is_request_from_vt'] = 'Seamless API';
        $input['user_id'] = $input["user"]->id;
        $input['payment_gateway_id'] = $input["user"]->mid;
        $input['amount_in_usd'] = $this->amountInUSD($input);
        
        $this->transactionSession->storeData($input);
        $user_mid_response = $this->checkUserLastMID($input, $input["user"]);

        // if all validation fails
        if (isset($user_mid_response['status']) && $user_mid_response['status'] == 0) {
            $input['status'] = $user_mid_response['mid']['status'];
            $input['reason'] = $user_mid_response['mid']['reason'];
        } else {
            $input['payment_gateway_id'] = $user_mid_response['mid'];
        }
        $input['payment_type'] = 'Card';

        // payment gateway object
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $required_fields = json_decode($check_assign_mid->required_fields, true);
        $data = [];
        foreach ($required_fields as $field) {
            if (empty($input[$field])) {
                $data[] = $field;
            }
        }
        $user = $input["user"];
        unset($input["user"]);
        // if (empty($data)) {
            // mid default currency
            
            if (isset($input['user_card_no']) && !empty($input['user_card_no'])) {
                $card_no = substr($input["user_card_no"], 0, 6) . 'XXXXXX' . substr($input["user_card_no"], -4);
                $block_card = BlockData::where('type', 'Card')
                    ->where('field_value', $card_no)
                    ->exists();
                $input["bin_number"] = substr($input["user_card_no"], 0, 6);
                if ($block_card) {
                    $input['status'] = '5';
                    $input['reason'] = 'The following card has blocked on our system. Please contact to administrator.';
                    $store_tx_try = $this->tx_try->storeData($input);
                    return ApiResponse::blocked($input);
                    // return redirect()->route('api.v2.block', $input['order_id']);
                }
                if (isset($input['user_ccexpiry_month']) && isset($input['user_ccexpiry_year'])) {
                    if (strtotime($input['user_ccexpiry_year'] . '-' . $input['user_ccexpiry_month']) < strtotime(date('Y-m'))) {
                        $input['status'] = '5';
                        $input['reason'] = 'The following card has expired.';
                        $store_tx_try = $this->tx_try->storeData($input);
                        return ApiResponse::blocked($input);
                        // return redirect()->route('api.v2.block', $input['order_id']);
                    }
                }
                $input['card_type'] = $this->getCreditCardType($input['user_card_no']);
            }
            $mid_blocked = $this->cardTypeMIDBlocked($input, $user);
            if ($mid_blocked) {
                $input['status'] = $mid_blocked['status'];
                $input['reason'] = $mid_blocked['reason'];
                $store_tx_try = $this->tx_try->storeData($input);
                return ApiResponse::blocked($input);
                // return redirect()->route('api.v2.block', $input['order_id']);
            }
            // user last mid
            $user_mid_response = $this->checkUserLastMID($input, $user);

            // if all validation fails
            if (isset($user_mid_response['status']) && $user_mid_response['status'] == 0) {
                $input['status'] = $user_mid_response['mid']['status'];
                $input['reason'] = $user_mid_response['mid']['reason'];

                $store_tx_try = $this->tx_try->storeData($input);
                // return redirect()->route('api.v2.block', $input['order_id']);
                return ApiResponse::blocked($input);
            } else {
                $input['payment_gateway_id'] = $user_mid_response['mid'];
            }

            // new payment gateway
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            $input['mid_type'] = $check_assign_mid->mid_type;

            $required_fields = json_decode($check_assign_mid->required_fields, true);
            $new_validations = [];
            // create validations array
            foreach ($required_fields as $value) {
                if (in_array($value, array_keys(config('required_v2.validate')))) {
                    $new_validations[$value] = config('required_v2.validate.' . $value);
                }
            }
            // dd($new_validations);
            $validator = Validator::make($input, $new_validations);
            if ($validator->fails()) {
                $errors = $validator->errors()->messages();

                $error_array = [];
                foreach ($errors as $error) {
                    array_push($error_array, $error[0]);
                }

                $input['status'] = '5';
                $input['reason'] = "There are missing or invalid parameters in the request data. Please check the 'errors' parameter for additional details.";
                $store_tx_try = $this->tx_try->storeData($input);
                return ApiResponse::blocked($input);
                // return redirect()->route('api.v2.block', $input['order_id']);
            }

            $check_selected_currency = $this->midDefaultCurrencyCheck($input['payment_gateway_id'], $input['user_currency'], $input['user_amount']);
            if ($check_selected_currency) {
                $input['is_converted'] = '1';
                $input['converted_amount'] = $check_selected_currency['amount'];
                $input['converted_currency'] = $check_selected_currency['currency'];
            } else {
                $input['converted_amount'] = $input['user_amount'];
                $input['converted_currency'] = $input['user_currency'];
            }
            // dd($input);
            // update payment_gateway_id in session data
            TransactionSession::where('order_id', $input["order_id"])
                ->where('is_completed', 0)
                ->update([
                    'request_data' => json_encode($input),
                    'payment_gateway_id' => $input['payment_gateway_id']
                ]);

            // gateway curl response
            $gateway_curl_response = $this->gatewayCurlResponse($input, $check_assign_mid);

            // // transaction requires 3ds verification
            // if ($gateway_curl_response['status'] == '7') {
            //     return redirect()->away($gateway_curl_response['payment_link']);
            // }
            $input['status'] = $gateway_curl_response['status'];
            $input['reason'] = $gateway_curl_response['reason'];
            $store_tx_try = $this->tx_try->storeData($input);

            $store_transaction_link = $this->storeTransactionAPIVTwo($input);
            // transaction success
            if ($gateway_curl_response['status'] == '1') {
                return ApiResponse::success($input);
            } elseif ($gateway_curl_response['status'] == '2'){
                return ApiResponse::pending($input);
            }  elseif($gateway_curl_response['status'] == '7'){
                $input['payment_link'] = $gateway_curl_response["payment_link"];
                return ApiResponse::redirect($input);
            } elseif ($gateway_curl_response['status'] == '0') {
                return ApiResponse::fail($input);
            } elseif ($gateway_curl_response['status'] == '5') {
                return ApiResponse::blocked($input);
            } else {
                return ApiResponse::fail($input);
            }
        // }
        // $input['status'] = '7';
        // $input['payment_link'] = route('api.v2.card', $input['order_id']);
        // $input['reason'] = "Please redirect to the specified 'payment_link' to complete the transaction processing.";
        // return ApiResponse::redirect($input);
    }

    // ================================================
    /* method : checkout
     * @param  : $id string
     * @description : payment mode selection view card/crypto/bank
     */// ===============================================
    public function checkout($order_id, Request $request)
    {
        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }

        $user = User::select('id', 'crypto_mid', 'bank_mid', 'upi_mid', 'mid')
            ->where('id', $transaction_session->user_id)
            ->where('is_active', '1')
            ->whereNotNull('mid')
            ->whereNotIn('mid', [0, 1, 2])
            ->whereNull('deleted_at')
            ->first();

        if (empty($user)) {
            return abort(404);
        }

        // reset input_details field
        if ($request->retry) {
            $request_data = json_decode($transaction_session->input_details, true);
            $request_data['session_id'] = time(). strtoupper(\Str::random(2)).'ITP';
            $request_data['ip_address'] = $request_data['ip_address'] ?? $this->getClientIP();
            TransactionSession::where('order_id', $order_id)
                ->where('is_completed', 0)
                ->update([
                    'request_data' => json_encode($request_data),
                    'input_details' => json_encode($request_data),
                    'transaction_id' => $request_data['session_id']
                ]);
        } else {
            $request_data = json_decode($transaction_session->input_details, true);
            $request_data['ip_address'] = $request_data['ip_address'] ?? $this->getClientIP();
            TransactionSession::where('order_id', $order_id)
                ->where('is_completed', 0)
                ->update([
                    'request_data' => json_encode($request_data),
                    'input_details' => json_encode($request_data)
                ]);
        }

        return view('gateway.apiv2.index', compact('transaction_session', 'user'));
    }

    // ================================================
    /* method : card
     * @param  : 
     * @description : card validation and rules apply on card method select
     */// ===============================================
    public function card($order_id)
    {
        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }

        // validate user
        $user = DB::table('users')
            ->where('id', $transaction_session->user_id)
            ->where('is_active', 1)
            ->whereNotNull('mid')
            ->whereNotIn('mid', [0, 1, 2])
            ->whereNull('deleted_at')
            ->first();

        if (empty($user)) {
            return abort(404);
        }

        $input_data = json_decode($transaction_session['input_details'], true);
        //dd($input_data);
        $input = array_filter($input_data, function ($a) {
            return ($a !== null) && $a !== '';
        });
        // user last mid
        $user_mid_response = $this->checkUserLastMID($input, $user);

        // if all validation fails
        if (isset($user_mid_response['status']) && $user_mid_response['status'] == 0) {
            $input['status'] = $user_mid_response['mid']['status'];
            $input['reason'] = $user_mid_response['mid']['reason'];
        } else {
            $input['payment_gateway_id'] = $user_mid_response['mid'];
        }
        $input['payment_type'] = 'Card';

        // payment gateway object
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $required_fields = json_decode($check_assign_mid->required_fields, true);
        $data = [];
        foreach ($required_fields as $field) {
            if (empty($input_data[$field])) {
                $data[] = $field;
            }
        }
        if (empty($data)) {
            // mid default currency
            
            if (isset($input['user_card_no']) && !empty($input['user_card_no'])) {
                $card_no = substr($input["user_card_no"], 0, 6) . 'XXXXXX' . substr($input["user_card_no"], -4);
                $block_card = BlockData::where('type', 'Card')
                    ->where('field_value', $card_no)
                    ->exists();
                $input["bin_number"] = substr($input["user_card_no"], 0, 6);
                if ($block_card) {
                    $input['status'] = '5';
                    $input['reason'] = 'The following card has blocked on our system. Please contact to administrator.';
                    $store_tx_try = $this->tx_try->storeData($input);
                    return redirect()->route('api.v2.block', $input['order_id']);
                }
                if (isset($input['user_ccexpiry_month']) && isset($input['user_ccexpiry_year'])) {
                    if (strtotime($input['user_ccexpiry_year'] . '-' . $input['user_ccexpiry_month']) < strtotime(date('Y-m'))) {
                        $input['status'] = '5';
                        $input['reason'] = 'The following card has expired.';
                        $store_tx_try = $this->tx_try->storeData($input);
                        return redirect()->route('api.v2.block', $input['order_id']);
                    }
                }
                // try {
                //     $bin_response = $this->binChecking($input);
                //     if (isset($bin_response['card-brand']) && !empty($bin_response['card-brand'])) {
                //         $card_type = config('card.bin_response.' . $bin_response['card-brand']);
                //     }
                // } catch (\Exception $e) {
                //     $bin_response = false;
                //     \Log::info(['bin_response_error' => $e->getMessage()]);
                // }
                // card type by card_no
                // $input['card_type'] = $card_type ?? $this->getCreditCardType($input['user_card_no']);
                $input['card_type'] = $this->getCreditCardType($input['user_card_no']);
            }
            $mid_blocked = $this->cardTypeMIDBlocked($input, $user);
            if ($mid_blocked) {
                $input['status'] = $mid_blocked['status'];
                $input['reason'] = $mid_blocked['reason'];
                $store_tx_try = $this->tx_try->storeData($input);
                return redirect()->route('api.v2.block', $input['order_id']);
            }

            // if ( isset($input["user_card_no"]) && !empty($input["user_card_no"]) && isset($input["user_country"]) && !empty($input["user_country"])) {
            //     $input['bin_details'] = json_encode($bin_response);
            //     if ($user->is_bin_remove == '0') {
            //         $bin_response = $bin_response ?? false;

            //         // bin checker api
            //         if ($bin_response != false && isset($bin_response['country-code'])) {
            //             $input['bin_country_code'] = $bin_response['country-code'];

            //             if ($input["user_country"] == 'UK') {
            //                 $input["user_country"] = 'GB';
            //             }
            //             if ($bin_response["country-code"] != $input["user_country"]) {
            //                 $input['status'] = '5';
            //                 $input['reason'] = 'The country of card issuance does not match the selected country.';
            //                 $store_tx_try = $this->tx_try->storeData($input);
            //                 return redirect()->route('api.v2.block', $input['order_id']);
            //             }
            //         } else {
            //             $input['status'] = '5';
            //             $input['reason'] = 'The country of card issuance does not match the selected country.';
            //             $store_tx_try = $this->tx_try->storeData($input);
            //             return redirect()->route('api.v2.block', $input['order_id']);
            //         }
            //     }
            // }

            // user last mid
            $user_mid_response = $this->checkUserLastMID($input, $user);

            // if all validation fails
            if (isset($user_mid_response['status']) && $user_mid_response['status'] == 0) {
                $input['status'] = $user_mid_response['mid']['status'];
                $input['reason'] = $user_mid_response['mid']['reason'];

                $store_tx_try = $this->tx_try->storeData($input);
                return redirect()->route('api.v2.block', $input['order_id']);
            } else {
                $input['payment_gateway_id'] = $user_mid_response['mid'];
            }

            // new payment gateway
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            $input['mid_type'] = $check_assign_mid->mid_type;

            $required_fields = json_decode($check_assign_mid->required_fields, true);
            $new_validations = [];
            // create validations array
            foreach ($required_fields as $value) {
                if (in_array($value, array_keys(config('required_v2.validate')))) {
                    $new_validations[$value] = config('required_v2.validate.' . $value);
                }
            }
            // dd($new_validations);
            $validator = Validator::make($input, $new_validations);
            if ($validator->fails()) {
                $errors = $validator->errors()->messages();

                $error_array = [];
                foreach ($errors as $error) {
                    array_push($error_array, $error[0]);
                }

                $input['status'] = '5';
                $input['reason'] = "There are missing or invalid parameters in the request data. Please check the 'errors' parameter for additional details.";
                $store_tx_try = $this->tx_try->storeData($input);
                return redirect()->route('api.v2.block', $input['order_id']);
            }

            $check_selected_currency = $this->midDefaultCurrencyCheck($input['payment_gateway_id'], $input['user_currency'], $input['user_amount']);
            if ($check_selected_currency) {
                $input['is_converted'] = '1';
                $input['converted_amount'] = $check_selected_currency['amount'];
                $input['converted_currency'] = $check_selected_currency['currency'];
            } else {
                $input['converted_amount'] = $input['user_amount'];
                $input['converted_currency'] = $input['user_currency'];
            }
            
            // update payment_gateway_id in session data
            TransactionSession::where('order_id', $order_id)
                ->where('is_completed', 0)
                ->update([
                    'request_data' => json_encode($input),
                    'payment_gateway_id' => $input['payment_gateway_id']
                ]);

            // gateway curl response
            $gateway_curl_response = $this->gatewayCurlResponse($input, $check_assign_mid);

            // transaction requires 3ds verification
            if ($gateway_curl_response['status'] == '7') {
                return redirect()->away($gateway_curl_response['payment_link']);
            }

            $input['status'] = $gateway_curl_response['status'];
            $input['reason'] = $gateway_curl_response['reason'];

            $store_tx_try = $this->tx_try->storeData($input);

            $store_transaction_link = $this->storeTransactionAPIVTwo($input);
            // transaction success
            if ($gateway_curl_response['status'] == '1') {
                return redirect()->away($store_transaction_link);
            } else {
                return redirect()->route('api.v2.decline', $input['order_id']);
            }
        }

        // update payment_gateway_id in session data
        TransactionSession::where('order_id', $order_id)
            ->where('is_completed', 0)
            ->update([
                'request_data' => json_encode($input),
                'payment_gateway_id' => $input['payment_gateway_id']
            ]);
        return view('gateway.apiv2.card', compact('order_id','data'));
    }

    // ================================================
    /* method : cardSelect
     * @param  : 
     * @description : ajax request on card type select
     */// ===============================================
    public function cardSelect(Request $request, $order_id)
    {
        $card_data = $request->only([
            'address',
            'country',
            'city',
            'state',
            'zip',
            'phone_no',
            'card_no',
            'ccExpiryMonth',
            'ccExpiryYear',
            'cvvNumber',
            'card_type'
        ]);

        // validation checks
        $validator = Validator::make($card_data, [
            'address' => 'nullable|min:2|max:250',
            'country' => 'nullable|max:2|min:2|regex:(\b[A-Z]+\b)',
            'city' => 'nullable|min:2|max:250',
            'state' => 'nullable|min:2|max:250',
            'zip' => 'nullable|min:2|max:250',
            'phone_no' => 'nullable|min:5|max:20',
            'card_no' => 'nullable|min:12|max:24',
            'ccExpiryMonth' => 'nullable|numeric|min:1|max:12',
            'ccExpiryYear' => 'nullable|numeric|min:2023|max:2045',
            'cvvNumber' => 'nullable|numeric|min:0|max:9999',
            'card_type' => 'required|in:1,2,3,4,5,6,7,8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unsupported card type selected.',
            ]);
        }

        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong with your request. Kindly try again.',
            ]);
        }

        // validate user
        $user = DB::table('users')
            ->where('id', $transaction_session->user_id)
            ->where('is_active', 1)
            ->whereNotNull('mid')
            ->whereNotIn('mid', [0, 1, 2])
            ->whereNull('deleted_at')
            ->first();

        if (empty($user)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong with your request. Kindly try again.',
            ]);
        }

        $input_data = json_decode($transaction_session['input_details'], true);

        $input_data = array_filter($input_data, function ($a) {
            return ($a !== null) && $a !== '';
        });
        $card_data = array_filter($card_data, function ($a) {
            return ($a !== null) && $a !== '';
        });

        $input = array_merge($input_data, $card_data);

        // user specific card_type blocked
        $mid_blocked = $this->cardTypeMIDBlocked($input, $user);
        if ($mid_blocked) {
            $input['status'] = $mid_blocked['status'];
            $input['reason'] = $mid_blocked['reason'];

            $store_tx_try = $this->tx_try->storeData($input);
            $html = view('gateway.apiv2.abortForm')->render();
            return response()->json([
                'status' => 'fail',
                'message' => $input['reason'],
                'html' => $html
            ]);
        }

        // user last mid
        $user_mid_response = $this->checkUserLastMID($input, $user);

        // if all validation fails
        if (isset($user_mid_response['status']) && $user_mid_response['status'] == 0) {
            $input['status'] = $user_mid_response['mid']['status'];
            $input['reason'] = $user_mid_response['mid']['reason'];

            $store_tx_try = $this->tx_try->storeData($input);
            $html = view('gateway.apiv2.abortForm')->render();
            return response()->json([
                'status' => 'fail',
                'message' => $input['reason'],
                'html' => $html,
            ]);
        } else {
            $input['payment_gateway_id'] = $user_mid_response['mid'];
        }

        // new payment gateway
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $required_fields = json_decode($check_assign_mid->required_fields, true);

        $data = [];
        foreach ($required_fields as $field) {
            if (empty($input_data[$field])) {
                $data[] = $field;
            }
        }

        // update input_details field
        TransactionSession::where('order_id', $order_id)
            ->where('is_completed', 0)
            ->update([
                'request_data' => json_encode($input),
                'payment_gateway_id' => $input['payment_gateway_id']
            ]);

        $html = view('gateway.apiv2.detailsForm', compact('data', 'input', 'card_data'))->render();

        return response()->json([
            'status' => 'success',
            'message' => 'ok.',
            'html' => $html,
        ]);
    }

    // ================================================
    /* method : liveAjaxValidation
     * @param  : 
     * @description : submit extra details form
     */// ===============================================
    public function liveAjaxValidation(Request $request, $order_id)
    {
        $card_data = $request->only([
            'address',
            'country',
            'city',
            'state',
            'zip',
            'phone_no',
            'card_no',
            'ccExpiryMonth',
            'ccExpiryYear',
            'cvvNumber'
        ]);

        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong with your request. Kindly try again.',
            ]);
        }

        // validate user
        $user = DB::table('users')
            ->where('id', $transaction_session->user_id)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if (empty($user)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong with your request. Kindly try again.',
            ]);
        }

        if (empty($user->mid)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong with your request. Kindly try again.',
            ]);
        }

        // required card_details
        if (isset($card_data['card_no']) && strlen($card_data['card_no']) >= 16) {
            $card_data['card_no'] = str_replace(' ', '', trim($card_data['card_no']));
            $card_data['card_type'] = $this->getCreditCardType($card_data['card_no']);

            $card_data['ccExpiryMonth'] = trim($card_data['ccExpiryMonth']);
            $card_data['ccExpiryYear'] = trim($card_data['ccExpiryYear']);
            $card_data['cvvNumber'] = trim($card_data['cvvNumber']);
        }

        $input_data = json_decode($transaction_session['input_details'], true);

        $input_data = array_filter($input_data, function ($a) {
            return ($a !== null) && $a !== '';
        });
        $card_data = array_filter($card_data, function ($a) {
            return ($a !== null) && $a !== '';
        });

        $input = array_merge($input_data, $card_data);

        // card block decline
        if (isset($input['card_no']) && $input['card_no'] != null) {
            $card_no = substr($input["card_no"], 0, 6) . 'XXXXXX' . substr($input["card_no"], -4);
            $block_card = BlockData::where('type', 'Card')
                ->where('field_value', $card_no)
                ->exists();
            if ($block_card) {
                $input['status'] = '5';
                $input['reason'] = 'This card(' . $card_no . ') is blocked for transaction.';
                $store_tx_try = $this->tx_try->storeData($input);
                return response()->json([
                    'status' => 'fail',
                    'message' => $input['reason'],
                ]);
            }

            try {
                $bin_response = $this->binChecking($input);

                $input['card_type'] = config('card.bin_response.' . $bin_response['card-brand']);
            } catch (\Exception $e) {
                $bin_response = false;
            }

            // card type by card_no
            $input['card_type'] = $input['card_type'] ?? $this->getCreditCardType($input['card_no']);
        }

        // user specific card_type blocked
        $mid_blocked = $this->cardTypeMIDBlocked($input, $user);
        if ($mid_blocked) {
            return response()->json([
                'status' => 'fail',
                'message' => $mid_blocked['reason'],
            ]);
        }

        // bin checker only to run if country and card exists
        if (
            isset($input["card_no"]) && $input["card_no"] != null &&
            isset($input["country"]) && $input["country"] != null && false
        ) {
            $bin_response = $bin_response ?? false;

            // bin checker api
            if ($bin_response != false && isset($bin_response['country-code'])) {
                $input['bin_country_code'] = $bin_response['country-code'];
                $input['bin_details'] = json_encode($bin_response);

                if ($input["country"] == 'UK') {
                    $input["country"] = 'GB';
                }
                if ($bin_response["country-code"] != $input["country"]) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'The card issuing country is different than the country selected.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'The card issuing country is different than the country selected.',
                ]);
            }
        }

        // user last mid
        $user_mid_response = $this->checkUserLastMID($input, $user);

        // if all validation fails
        if (isset($user_mid_response['status']) && $user_mid_response['status'] == 0) {
            return response()->json([
                'status' => 'fail',
                'message' => $user_mid_response['mid']['reason'],
            ]);
        } else {
            $input['payment_gateway_id'] = $user_mid_response['mid'];
        }

        // new payment gateway
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $required_fields = json_decode($check_assign_mid->required_fields, true);

        $data = [];
        foreach ($required_fields as $field) {
            if (empty($input_data[$field])) {
                $data[] = $field;
            }
        }

        if (!empty($data)) {
            $html = view('gateway.apiv2.detailsForm', compact('data', 'input', 'card_data'))->render();

            return response()->json([
                'status' => 'success',
                'message' => 'ok.',
                'html' => $html,
                'cardType' => $input['card_type'] ?? 2,
            ]);
        }

        return response()->json([
            'status' => 'hold',
            'message' => 'ok.'
        ]);
    }

    // ================================================
    /* method : extraDetailsFormSubmit
     * @param  : 
     * @description : submit extra details form
     */// ===============================================
    public function extraDetailsFormSubmit(Request $request, $order_id)
    {
        $card_data = $request->only([
            'user_address',
            'user_country',
            'user_city',
            'user_state',
            'user_zip',
            'user_phone_no',
            'user_card_no',
            'user_ccexpiry_month',
            'user_ccexpiry_year',
            'user_cvv_number'
        ]);

        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();
        if (empty($transaction_session)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong with your request. Kindly try again',
            ]);
        }

        // validate user
        $user = DB::table('users')
            ->where('id', $transaction_session->user_id)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if (empty($user)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong with your request. Kindly try again',
            ]);
        }

        if (empty($user->mid)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong with your request. Kindly try again',
            ]);
        }

        // required card_details
        if (isset($card_data['user_card_no']) && strlen($card_data['user_card_no']) >= 16) {
            $card_data['user_card_no'] = str_replace(' ', '', trim($card_data['user_card_no']));
        }
        if (isset($card_data['user_ccexpiry_month']) && ($card_data['user_ccexpiry_month'] >= 01 && $card_data['user_ccexpiry_month'] <= 12)) {
            $card_data['user_ccexpiry_month'] = trim($card_data['user_ccexpiry_month']);
        }
        if (isset($card_data['user_ccexpiry_year']) && ($card_data['user_ccexpiry_year'] >= 2023 && $card_data['user_ccexpiry_year'] <= 2050)) {
            $card_data['user_ccexpiry_year'] = trim($card_data['user_ccexpiry_year']);
        }
        if (isset($card_data['user_cvv_number']) && ($card_data['user_cvv_number'] >= 0000 && $card_data['user_cvv_number'] <= 9999)) {
            $card_data['user_cvv_number'] = trim($card_data['user_cvv_number']);
        }

        $input_data = json_decode($transaction_session['input_details'], true);

        $input_data = array_filter($input_data, function ($a) {
            return ($a !== null) && $a !== '';
        });
        $card_data = array_filter($card_data, function ($a) {
            return ($a !== null) && $a !== '';
        });

        $input = array_merge($input_data, $card_data);

        // card block or expired
        if (isset($input['user_card_no']) && !empty($input['user_card_no'])) {
            $card_no = substr($input["user_card_no"], 0, 6) . 'XXXXXX' . substr($input["user_card_no"], -4);
            $block_card = BlockData::where('type', 'Card')
                ->where('field_value', $card_no)
                ->exists();
            $input["bin_number"] = substr($input["user_card_no"], 0, 6);
            if ($block_card) {
                $input['status'] = '5';
                $input['reason'] = 'The following card has expired.';
                $store_tx_try = $this->tx_try->storeData($input);
                return response()->json([
                    'status' => 'fail',
                    'message' => $input['reason'],
                ]);
            }
            if (isset($card_data['user_ccexpiry_month']) && isset($card_data['user_ccexpiry_year'])) {
                if (strtotime($card_data['user_ccexpiry_year'] . '-' . $card_data['user_ccexpiry_month']) < strtotime(date('Y-m'))) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'The following card has expired.',
                    ]);
                }
            }

            $input['card_type'] = $this->getCreditCardType($input['user_card_no']);
        }
        // user specific card_type blocked
        $mid_blocked = $this->cardTypeMIDBlocked($input, $user);
        if ($mid_blocked) {
            $input['status'] = $mid_blocked['status'];
            $input['reason'] = $mid_blocked['reason'];
            $store_tx_try = $this->tx_try->storeData($input);
            return response()->json([
                'status' => 'fail',
                'message' => $input['reason'],
            ]);
        }
        // user last mid
        $user_mid_response = $this->checkUserLastMID($input, $user);

        // if all validation fails
        if (isset($user_mid_response['status']) && $user_mid_response['status'] == 0) {
            $input['status'] = $user_mid_response['mid']['status'];
            $input['reason'] = $user_mid_response['mid']['reason'];

            $store_tx_try = $this->tx_try->storeData($input);
            return response()->json([
                'status' => 'fail',
                'message' => $input['reason'],
            ]);
        } else {
            $input['payment_gateway_id'] = $user_mid_response['mid'];
        }

        // new payment gateway
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $input['mid_type'] = $check_assign_mid->mid_type;

        $required_fields = json_decode($check_assign_mid->required_fields, true);
        $new_validations = [];
        // create validations array
        foreach ($required_fields as $value) {
            if (in_array($value, array_keys(config('required_v2.validate')))) {
                $new_validations[$value] = config('required_v2.validate.' . $value);
            }
        }
        // dd($new_validations);
        $validator = Validator::make($input, $new_validations);
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();

            $error_array = [];
            foreach ($errors as $error) {
                array_push($error_array, $error[0]);
            }

            $input['status'] = '5';
            $input['reason'] = "There are missing or invalid parameters in the request data. Please check the 'errors' parameter for additional details.";
            $store_tx_try = $this->tx_try->storeData($input);
            return response()->json([
                'status' => 'fail',
                'message' => $input['reason'],
                'errors' => $error_array,
            ]);
        }

        // mid default currency
        $check_selected_currency = $this->midDefaultCurrencyCheck($input['payment_gateway_id'], $input['user_currency'], $input['user_amount']);
        if ($check_selected_currency) {
            $input['is_converted'] = '1';
            $input['converted_amount'] = $check_selected_currency['amount'];
            $input['converted_currency'] = $check_selected_currency['currency'];
        } else {
            $input['converted_amount'] = $input['user_amount'];
            $input['converted_currency'] = $input['user_currency'];
        }
        
        // update transaction_session
        $input_data = array_merge($input_data, $card_data);
        $input_data["user_card_no"] = $card_no ?? null;
        $input_data["card_type"] = $card_type ?? '2';
        $input_data["user_cvv_number"] = isset($input['user_cvv_number']) ? 'XXX' : null;
        $input_data['payment_gateway_id'] = $input['payment_gateway_id'];
        $input_data['mid_type'] = $input['mid_type'];

        $input_data['is_converted'] = $input['is_converted'] ?? '0';
        $input_data['converted_amount'] = $input['converted_amount'];
        $input_data['converted_currency'] = $input['converted_currency'];
        TransactionSession::where('order_id', $order_id)
            ->where('is_completed', 0)
            ->update([
                'request_data' => json_encode($input_data),
                'payment_gateway_id' => $input_data['payment_gateway_id']
            ]);

        // gateway curl response
        $gateway_curl_response = $this->gatewayCurlResponse($input, $check_assign_mid);
        \Log::info(["gateway_curl_response" => $gateway_curl_response]);
        // transaction requires 3ds verification
        if ($gateway_curl_response['status'] == '7') {
            return response()->json([
                'status' => 'success',
                'message' => 'Payment link has been generated successfully.',
                'url' => $gateway_curl_response['payment_link'],
            ]);
        }

        $input['status'] = $gateway_curl_response['status'];
        $input['reason'] = $gateway_curl_response['reason'];

        // transaction success
        $store_transaction_link = $this->storeTransactionAPIVTwo($input);
        if ($gateway_curl_response['status'] == '1') {

            return response()->json([
                'status' => 'success',
                'message' => $input['reason'],
                'url' => route('api.v2.success', $input['order_id'])
            ]);
        }

        $store_tx_try = $this->tx_try->storeData($input);

        return response()->json([
            'status' => 'success',
            'message' => $input['reason'],
            'url' => route('api.v2.decline', $order_id)
        ]);
    }

    // ================================================
    /* method : checkUserLastMID
     * @param  : 
     * @description : return user mid after limits and validation
     */// ===============================================
    private function checkUserLastMID($input, $user)
    {
        $mid_resume = true;
        $mid_validations = false;
        // dd($input);
        if (isset($input['is_request_from_vt']) && $input['is_request_from_vt'] == 'IFRAMEAV2' && isset($input['payment_type']) && $input['payment_type'] == 'Card') {
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            if ($check_assign_mid !== false) {
                $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);
            }
        } else {
            if (isset($user->is_disable_rule) && $user->is_disable_rule == '0') {
                $user_rule_gateway_id = $this->userCardRulesCheck($input, $user);
                if ($user_rule_gateway_id != false) {

                    $mid_resume = false;
                    $input['payment_gateway_id'] = $user_rule_gateway_id;
                } else {
                    $rule_gateway_id = $this->cardRulesCheck($input, $user);
                    if ($rule_gateway_id != false) {

                        $mid_resume = false;
                        $input['payment_gateway_id'] = $rule_gateway_id;
                    }
                }
            }

            // user specific card_type mid for 1/2/3/4 only
            if ($mid_resume == true) {
                $user_specific_mid = $this->userCardTypeMID($input, $user);

                if ($user_specific_mid != false) {
                    $input['payment_gateway_id'] = $user_specific_mid;

                    // payment gateway object
                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            $mid_resume = false;
                        }
                    }
                }
            }

            // user default mid
            if ($mid_resume == true) {
                $input['payment_gateway_id'] = $user->mid;

                // payment gateway object
                $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                if ($check_assign_mid !== false) {
                    $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                    if ($mid_validations == false) {
                        $mid_resume = false;
                    }
                }
            }

            // if user has selected visa and merchant has multiple visa mid
            if (
                isset($input['card_type']) && $input['card_type'] == '2' &&
                !empty($user->multiple_mid) && $mid_resume == true
            ) {
                $visa_mid = $this->multipleVisa($input, $user);

                if ($visa_mid) {
                    $input['payment_gateway_id'] = $visa_mid;

                    $mid_resume = false;
                    $mid_validations = false;
                }
            }

            // if user has selected mastercard and merchant has multiple master mid
            if (
                isset($input['card_type']) && $input['card_type'] == '3' &&
                !empty($user->multiple_mid_master) && $mid_resume == true
            ) {
                $master_mid = $this->multipleMaster($input, $user);

                if ($master_mid) {
                    $input['payment_gateway_id'] = $master_mid;

                    $mid_resume = false;
                    $mid_validations = false;
                }
            }
        }

        // if mid limits reached
        if ($mid_validations) {
            return [
                'status' => 0,
                'mid' => $mid_validations
            ];
        } else {
            return [
                'status' => 1,
                'mid' => $input['payment_gateway_id']
            ];
        }
    }

    // ================================================
    /* method : multipleVisa
     * @param  : 
     * @description : multiple visa
     */// ===============================================
    public function multipleVisa($input, $user)
    {
        if ($input['card_type'] == '2' && !empty($user->multiple_mid)) {
            $multiple_mid = json_decode($user->multiple_mid);

            foreach ($multiple_mid as $value) {
                $input['payment_gateway_id'] = $value;
                $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
                if ($check_assign_mid == false) {
                    continue;
                }

                // mid validation
                $mid_limit_response = $this->getMIDLimitResponse($input, $check_assign_mid, $user);
                if ($mid_limit_response != false) {
                    continue;
                }

                return $value;
            }
        }
        return false;
    }

    // ================================================
    /* method : multipleMaster
     * @param  : 
     * @description : multiple master mid
     */// ===============================================
    public function multipleMaster($input, $user)
    {
        if ($input['card_type'] == '3' && !empty($user->multiple_mid_master)) {
            $multiple_mid = json_decode($user->multiple_mid_master);

            foreach ($multiple_mid as $value) {
                $input['payment_gateway_id'] = $value;
                $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
                if ($check_assign_mid == false) {
                    continue;
                }

                // mid validation
                $mid_limit_response = $this->getMIDLimitResponse($input, $check_assign_mid, $user);
                if ($mid_limit_response != false) {
                    continue;
                }

                return $value;
            }
        }
        return false;
    }

    // ================================================
    /* method : success
     * @param  : 
     * @description : success page after transaction success
     */// ===============================================
    public function success($order_id)
    {
        $input = Transaction::where('order_id', $order_id)
            ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($input)) {
            return abort(404);
        }
        $redirect_url = $this->getRedirectLinkTransactions($input);
        if($input->is_request_from_vt == "Seamless API"){
            return Redirect::to($redirect_url);
        }
        // send declined message
        return view('gateway.apiv2.success', compact('input', 'redirect_url'));
    }

    // ================================================
    /* method : decline
     * @param  : 
     * @description : decline page after transaction decline
     */// ===============================================
    public function decline($order_id)
    {
        $input = Transaction::where('order_id', $order_id)
            ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('status', 0)
            ->orderBy('id', 'desc')
            ->first();
        if (empty($input)) {
            return abort(404);
        }

        $redirect_url = $this->getRedirectLinkTransactions($input);
        if($input->is_request_from_vt == "Seamless API"){
            \Log::info(["link" => $redirect_url]);
            return Redirect::to($redirect_url);
        }
        // send declined message
        return view('gateway.apiv2.decline', compact('input'));
    }

    public function blocked($order_id){
        $tx = TxTry::where('order_id', $order_id)
            ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->orderBy('id', 'desc')
            ->first();
        if (empty($tx)) {
            $tx = TransactionSession::where('order_id', $order_id)
                ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
                ->whereNotIn('payment_gateway_id', [0, 1, 2])
                ->whereNotNull('payment_gateway_id')
                ->where('is_completed', 0)
                ->orderBy('id', 'desc')
                ->first();
        }
        if (empty($tx)) {
            return abort(404);
        }

        $input = json_decode($tx['request_data'], true);
        if(!isset($input["status"])){
            $input['status'] = '5';
            $input['reason'] = $input['reason'] ?? 'Transaction has been denied.';
        }
        $store_transaction_link = $this->storeTransactionAPIVTwo($input);
        $redirect_url = $this->getRedirectLink($input);
        if($input["is_request_from_vt"]== "Seamless API"){
            return Redirect::to($redirect_url);
        }
        // send declined message
        return view('gateway.apiv2.decline', compact('input'));
    }

    // ================================================
    /* method : redirect
     * @param  : 
     * @description : redirect to merchant website after transaction decline
     */// ===============================================
    public function redirect($order_id)
    {
        $tx = TxTry::where('order_id', $order_id)
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereNotNull('payment_gateway_id')
            ->orderBy('id', 'desc')
            ->first();

        if (empty($tx)) {
            $tx = TransactionSession::where('order_id', $order_id)
                ->where('created_at', '>', Carbon::now()->subHour(2)->toDateTimeString())
                ->whereNotIn('payment_gateway_id', [0, 1, 2])
                ->whereNotNull('payment_gateway_id')
                ->where('is_completed', 0)
                ->orderBy('id', 'desc')
                ->first();
        }

        if (empty($tx)) {
            return abort(404);
        }

        $input = json_decode($tx['request_data'], true);

        $input['status'] = $input['status'] ?? '3';
        $input['reason'] = $input['reason'] ?? 'Transaction canceled by the user.';

        // transaction success
        $store_transaction_link = $this->storeTransactionAPIVTwo($input);

        return redirect()->away($store_transaction_link);
    }

    // ================================================
    /* method : gatewayCurlResponse
     * @param  :
     * @description : get first response from gateway
     */// ==============================================
    public function gatewayCurlResponse($input, $check_assign_mid)
    {
        try {
            $class_name = 'App\\Http\\Controllers\\Repo\\PaymentGateway\\' . $check_assign_mid->title;
            if (class_exists($class_name)) {
                $gateway_class = new $class_name;
                $gateway_return_data = $gateway_class->checkout($input, $check_assign_mid);
            } else {
                $gateway_return_data['status'] = '0';
                $gateway_return_data['reason'] = 'Payment gateway not available.';
            }
        } catch (\Exception $exception) {
            \Log::info([
                'CardPaymentException' => $exception->getMessage()
            ]);
            $gateway_return_data['status'] = '0';
            $gateway_return_data['reason'] = 'Problem with your transaction data or may be transaction timeout from the bank.';
        }

        return $gateway_return_data;
    }

    // ================================================
    /* method : getMIDLimitResponse
     * @param  :
     * @description : validate mid all limits
     * @description : all methods in this method are extended from Mid trait
     */// ==============================================
    public function getMIDLimitResponse($input, $check_assign_mid, $user)
    {
        // per transaction maximum and minimum amount limit
        $per_transaction_limit_response = $this->perTransactionLimitCheck($input, $check_assign_mid, $user);
        if ($per_transaction_limit_response != false) {
            return $per_transaction_limit_response;
        }

        // mid daily limit
        $mid_daily_limit = $this->perDayAmountLimitCheck($input, $check_assign_mid, $user);
        if ($mid_daily_limit != false) {
            return $mid_daily_limit;
        }

        $transactions_check = \DB::table('transactions')
            ->whereNull('deleted_at')
            ->where('status', '<>', '5')
            ->where('user_id', $input['user_id'])
            ->where('payment_gateway_id', $input['payment_gateway_id']);

        // if there is card_no
        if (isset($input['card_no']) && $input['card_no'] != null) {

            $daily_card_decline_check = \DB::table('transactions')
                ->whereNull('deleted_at')
                ->where('status', '0')
                ->where('user_id', $input['user_id'])
                ->where('card_no', substr($input['card_no'], 0, 6) . 'XXXXXX' . substr($input['card_no'], -4))
                ->whereNotIn('payment_gateway_id', [0, 1, 2])
                ->whereBetween('created_at', [Carbon::now()->subMinutes(30)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($daily_card_decline_check >= $user->daily_card_decline_limit) {
                return [
                    'status' => 'Blocked',
                    'reason' => 'The daily limit for declined card transactions has been exceeded.'
                ];
            }

            $card_transactions_check = $transactions_check->where('card_no', substr($input['card_no'], 0, 6) . 'XXXXXX' . substr($input['card_no'], -4));

            // daily card limit check
            $card_daily_transactions = $card_transactions_check->whereBetween('created_at', [Carbon::now()->subDays(1)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($card_daily_transactions >= $check_assign_mid->per_day_card && $card_daily_transactions >= $user->one_day_card_limit) {
                return [
                    'status' => 'Blocked',
                    'reason' => "The daily limit for card transactions has been exceeded."
                ];
            }

            // card per-week limit
            $card_weekly_transactions = $card_transactions_check->whereBetween('created_at', [Carbon::now()->subDays(7)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($card_weekly_transactions >= $check_assign_mid->per_week_card && $card_weekly_transactions >= $user->one_week_card_limit) {
                return [
                    'status' => 'Blocked',
                    'reason' => "The weekly card transaction limit has been exceeded."
                ];
            }

            // card per-month limit
            $card_monthly_transactions = $card_transactions_check->whereBetween('created_at', [Carbon::now()->subDays(30)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($card_monthly_transactions >= $check_assign_mid->per_month_card && $card_monthly_transactions >= $user->one_month_card_limit) {
                return [
                    'status' => 'Blocked',
                    'reason' => "The monthly card transaction limit has been exceeded."
                ];
            }
        }

        // if there is email
        if (isset($input['email']) && $input['email'] != null) {

            $email_transactions_check = $transactions_check->where('email', $input['email']);

            // email per-day limit
            $email_daily_transactions = $email_transactions_check->whereBetween('created_at', [Carbon::now()->subDays(1)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($email_daily_transactions >= $check_assign_mid->per_day_email && $email_daily_transactions >= $user->one_day_email_limit) {
                return [
                    'status' => 'Blocked',
                    'reason' => "The daily email transaction limit has been exceeded."
                ];
            }

            // email per-week limit
            $email_weekly_transactions = $email_transactions_check->whereBetween('created_at', [Carbon::now()->subDays(7)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($email_weekly_transactions >= $check_assign_mid->per_week_email && $email_weekly_transactions >= $user->one_week_email_limit) {
                return [
                    'status' => 'Blocked',
                    'reason' => '"The weekly email transaction limit has been exceeded."'
                ];
            }

            // email per-month limit
            $email_monthly_transactions = $email_transactions_check->whereBetween('created_at', [Carbon::now()->subDays(30)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($email_monthly_transactions >= $check_assign_mid->per_month_card && $email_monthly_transactions >= $user->one_month_email_limit) {
                return [
                    'status' => 'Blocked',
                    'reason' => '"The monthly email transaction limit has been exceeded."'
                ];
            }
        }

        // blocked country validation
        if (isset($input['country']) && $input['country'] != null) {
            $blocked_country_response = $this->validateBlockedCountry($input, $check_assign_mid);
            if ($blocked_country_response != false) {
                return $blocked_country_response;
            }
        }

        return false;
    }

    // ================================================
    /* method : getClientIP
     * @param  : 
     * @description : get client ip address perfectly
     */// ===============================================
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

    // ================================================
    /* method : getCreditCardType
     * @param  :
     * @Description : return card_type
     */// ==============================================
    public function getCreditCardType($card_no)
    {
        if (empty($card_no)) {
            return false;
        }
        $cardtype = array(
            "visa" => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            "mastercard" => "/^5[1-5][0-9]{14}$/",
            "amex" => "/^3[47]\d{13,14}$/",
            "jcb" => "/^(?:2131|1800|35\d{3})\d{11}$/",
            "solo" => "/^(6334|6767)[0-9]{12}|(6334|6767)[0-9]{14}|(6334|6767)[0-9]{15}$/",
            "maestro" => "/^(5018|5020|5038|6304|6759|6761|6763|6768)[0-9]{8,15}$/",
            "discover" => "/^65[4-9][0-9]{13}|64[4-9][0-9]{13}|6011[0-9]{12}|(622(?:12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|9[01][0-9]|92[0-5])[0-9]{10})$/",
            "switch" => "/^(4903|4905|4911|4936|6333|6759)[0-9]{12}|(4903|4905|4911|4936|6333|6759)[0-9]{14}|(4903|4905|4911|4936|6333|6759)[0-9]{15}|564182[0-9]{10}|564182[0-9]{12}|564182[0-9]{13}|633110[0-9]{10}|633110[0-9]{12}|633110[0-9]{13}$/",
            "unionpay" => "/^(62[0-9]{14,17})$/",
        );

        if (preg_match($cardtype['visa'], $card_no)) {
            return '2';
        } else if (preg_match($cardtype['mastercard'], $card_no)) {
            return '3';
        } else if (preg_match($cardtype['amex'], $card_no)) {
            return '1';
        } else if (preg_match($cardtype['discover'], $card_no)) {
            return '4';
        } else if (preg_match($cardtype['jcb'], $card_no)) {
            return '5';
        } else if (preg_match($cardtype['maestro'], $card_no)) {
            return '6';
        } else if (preg_match($cardtype['switch'], $card_no)) {
            return '7';
        } else if (preg_match($cardtype['solo'], $card_no)) {
            return '8';
        } else if (preg_match($cardtype['unionpay'], $card_no)) {
            return '9';
        } else {
            return '0';
        }
    }
}