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
use App\Traits\StoreTransaction;
use App\Transformers\ApiResponse;
use App\Http\Controllers\Repo\PaymentGateway\TestGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TestApiController extends Controller
{
    use Mid, StoreTransaction;

    protected $user, $tx_try, $transaction, $transactionSession;

    // ================================================
    /* method : __construct
     * @param  : 
     * @description : create new instance of the class
     */// ===============================================
    public function __construct()
    {
        $this->user = new User;
        $this->tx_try = new TxTry;
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    // ================================================
    /* method : store
     * @param  : $request Request
     * @description : receive api v2 request data
     */// ===============================================
    public function store(Request $request)
    {
        $input = $request->only(['first_name', 'last_name', 'email', 'phone_no', 'amount', 'currency', 'address', 'country', 'state', 'city', 'zip', 'customer_order_id', 'response_url', 'webhook_url']);
        $api_key = $request->bearerToken();

        if (empty($api_key)) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, please pass API Key in Header';
            return ApiResponse::unauthorised($input);
        }

        // validate api_key
        $user = DB::table('users')
            ->where('api_key', $api_key)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        // if api_key is not valid or user deleted
        if (empty($user)) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, Invalid API Key or merchant deleted';
            return ApiResponse::unauthorised($input);
        }

        // gateway object
        $check_assign_mid = checkAssignMID($user->mid);
        if ($check_assign_mid == false) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, Your account is disabled.';
            return ApiResponse::unauthorised($input);
        }

        // validation checks
        $validator = Validator::make($input, [
            'first_name' => 'required|min:3|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email',
            'amount' => 'required|regex:/^\d+(\.\d{1,9})?$/',
            'currency' => 'required|max:3|min:3|regex:(\b[A-Z]+\b)',
            'country' => 'nullable|max:2|min:2|regex:(\b[A-Z]+\b)',
            'customer_order_id' => 'nullable|max:100',
            'response_url' => 'required|url',
            'webhook_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $input['status'] = '6';
            $input['reason'] = $errors[0] ?? 'Unauthorised request, please check your request payload.';
            return ApiResponse::unauthorised($input);
        }

        // check ip_restriction
        if ($user->is_ip_remove == '0') {
            $getIPData = WebsiteUrl::where('user_id', $user->id)
                ->where('ip_address', $this->getClientIP())
                ->first();

            // if IP is not added on the IP whitelist
            if (empty($getIPData)) {
                $input['status'] = '6';
                $input['reason'] = 'Unauthorised request, please whitelist this IP address(' . $this->getClientIP() . ') in your dashboard.';
                return ApiResponse::unauthorised($input);
            }

            // if IP is not approved
            if ($getIPData->is_active == '0') {
                $input['status'] = '6';
                $input['reason'] = 'Unauthorised request, IP address(' . $this->getClientIP() . ') approval pending.';
                return ApiResponse::unauthorised($input);
            }
            $input["website_url_id"] = $getIPData->id;
        }

        $block_email = BlockData::where('type', 'Email')
            ->where('field_value', $input['email'])
            ->exists();
        if (!empty($block_email)) {
            $input['status'] = '5';
            $input['reason'] = 'This email address(' . $input['email'] . ') is blocked for transaction.';
            return ApiResponse::blocked($input);
        }

        // order and session id
        $input['session_id'] = 'XR' . strtoupper(\Str::random(4)) . time();
        $input['order_id'] = 'TRN' . strtoupper(\Str::random(4)) . time() . strtoupper(\Str::random(6));

        // user ip and domain and request from api
        $input['request_from_ip'] = $request->ip();
        $input['request_origin'] = $_SERVER['HTTP_HOST'];
        $input['is_request_from_vt'] = 'API V2 TEST';
        $input['user_id'] = $user->id;
        $input['payment_gateway_id'] = 1;
        $input['amount_in_usd'] = $this->amountInUSD($input);

        // store transaction_session data
        $this->transactionSession->storeData($input);

        $input['status'] = '7';
        $input['redirect_3ds_url'] = route('api.v2.test-checkout', $input['order_id']);
        $input['reason'] = 'Please redirect to 3dsUrl.';

        return ApiResponse::redirect($input);
    }

    // ================================================
    /* method : checkout
     * @param  : $id string
     * @description : test payment mode selection view card/crypto/bank
     */// ===============================================
    public function checkout($order_id)
    {
        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }

        $user = User::select('id', 'crypto_mid', 'bank_mid', 'upi_mid', 'mid')
            ->where('id', $transaction_session->user_id)
            ->where('mid', '!=', 0)
            ->whereNotNull('mid')
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->first();

        if (empty($user)) {
            return abort(404);
        }

        $request_data = json_decode($transaction_session->request_data, true);
        $request_data['ip_address'] = $request_data['ip_address'] ?? $this->getClientIP();

        // reset input_details field
        TransactionSession::where('order_id', $order_id)
            ->where('is_completed', 0)
            ->update([
                'input_details' => null,
                'request_data' => json_encode($request_data)
            ]);

        return view('test.v2.index', compact('transaction_session', 'user'));
    }

    // ================================================
    /* method : card
     * @param  : 
     * @description : test card validation and rules apply on card method select
     */// ===============================================
    public function card($order_id)
    {
        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }

        $user = User::select('id', 'crypto_mid', 'bank_mid', 'mid')
            ->where('id', $transaction_session->user_id)
            ->where('mid', '!=', 0)
            ->whereNotNull('mid')
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if (empty($user)) {
            return abort(404);
        }

        if (empty($user->mid)) {
            return abort(404);
        }

        return view('test.v2.card', compact('order_id'));
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
            'card_type' => 'required|in:1,2,3,4,5,6,7,8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unsupported card type selected.',
            ]);
        }

        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'The link is expired, please try again.',
            ]);
        }

        // validate user and payment_gateway_id
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id', 'users.mid')
            ->where('users.id', $transaction_session->user_id)
            ->where('users.is_active', '1')
            ->whereNull('users.deleted_at')
            ->first();

        if (empty($payment_gateway_id)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'The link is expired, please try again.',
            ]);
        }

        $input_data = json_decode($transaction_session['request_data'], true);

        $input_data = array_filter($input_data, function ($a) {
            return ($a !== null) && $a !== '';
        });
        $card_data = array_filter($card_data, function ($a) {
            return ($a !== null) && $a !== '';
        });

        $input = array_merge($input_data, $card_data);

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
            ->update(['input_details' => json_encode($input)]);

        $html = view('test.v2.detailsForm', compact('data', 'input', 'card_data'))->render();

        return response()->json([
            'status' => 'success',
            'message' => 'ok.',
            'html' => $html,
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

        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'The link is expired, please try again.',
            ]);
        }

        // validate user and payment_gateway_id
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id', 'users.mid')
            ->where('users.id', $transaction_session->user_id)
            ->where('users.is_active', '1')
            ->whereNull('users.deleted_at')
            ->first();

        if (empty($payment_gateway_id)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'The link is expired, please try again.',
            ]);
        }

        // required card_details
        if (isset($card_data['card_no']) && strlen($card_data['card_no']) >= 16) {
            $card_data['card_no'] = str_replace(' ', '', trim($card_data['card_no']));
            $card_data['ccExpiryMonth'] = trim($card_data['ccExpiryMonth']);
            $card_data['ccExpiryYear'] = trim($card_data['ccExpiryYear']);
            $card_data['cvvNumber'] = trim($card_data['cvvNumber']);
        }

        $input_data = json_decode($transaction_session['request_data'], true);

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

            // card_type is different than selected
            $card_type = $this->getCreditCardType($input['card_no']);
            if ($input['card_type'] != $card_type) {
                $input['status'] = '5';
                $input['reason'] = 'You have passed different card type than selected.';
                $store_tx_try = $this->tx_try->storeData($input);
                return response()->json([
                    'status' => 'fail',
                    'message' => $input['reason'],
                ]);
            }
        }

        // new payment gateway
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $required_fields = json_decode($check_assign_mid->required_fields, true);

        $validator = Validator::make($input, [
            'first_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required|min:2|max:250',
            'country' => 'required|max:2|min:2|regex:(\b[A-Z]+\b)',
            'state' => 'required|min:2|max:250',
            'city' => 'required|min:2|max:250',
            'zip' => 'required|min:2|max:250',
            'ip_address' => 'required|ip',
            'email' => 'required|email',
            'phone_no' => 'required|min:5|max:20',
            'amount' => 'required|regex:/^\d+(\.\d{1,9})?$/',
            'currency' => 'required|max:3|min:3|regex:(\b[A-Z]+\b)',
            'card_no' => 'required|min:12|max:24',
            'ccExpiryMonth' => 'required|numeric|min:1|max:12',
            'ccExpiryYear' => 'required|numeric|min:2023|max:2045',
            'cvvNumber' => 'required|numeric|min:0|max:9999',
            'response_url' => 'required|url',
            'webhook_url' => 'url',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->messages();

            $error_array = [];
            foreach ($errors as $error) {
                array_push($error_array, $error[0]);
            }

            $input['status'] = '5';
            $input['reason'] = 'Invalid request data send.';
            $store_tx_try = $this->tx_try->storeData($input);
            return response()->json([
                'status' => 'fail',
                'message' => $input['reason'],
                'errors' => $error_array,
            ]);
        }

        // mid default currency
        $check_selected_currency = $this->midDefaultCurrencyCheck($input['payment_gateway_id'], $input['currency'], $input['amount']);
        if ($check_selected_currency) {
            $input['is_converted'] = '1';
            $input['converted_amount'] = $check_selected_currency['amount'];
            $input['converted_currency'] = $check_selected_currency['currency'];
        } else {
            $input['converted_amount'] = $input['amount'];
            $input['converted_currency'] = $input['currency'];
        }

        // gateway curl response
        $gateway_curl_response = $this->gatewayCurlResponse($input, $check_assign_mid, 'TestGateway');

        // transaction requires 3ds verification
        if ($gateway_curl_response['status'] == '7') {
            return response()->json([
                'status' => 'success',
                'message' => '3DS Link generated successfully.',
                'url' => $gateway_curl_response['redirect_3ds_url'],
            ]);
        }

        $input['status'] = $gateway_curl_response['status'];
        $input['reason'] = $gateway_curl_response['reason'];

        // transaction success
        if ($gateway_curl_response['status'] == '1') {
            $store_transaction_link = $this->storeTransactionAPIVTwo($input);

            return response()->json([
                'status' => 'success',
                'message' => $input['reason'],
                'url' => $store_transaction_link
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
    /* method : bank
     * @param  : 
     * @description : bank selected view
     */// ===============================================
    public function bank($order_id)
    {
        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }

        $input = json_decode($transaction_session['request_data'], true);

        // validate user and payment_gateway_id
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id', 'users.mid')
            ->where('users.id', $transaction_session->user_id)
            ->where('users.is_active', '1')
            ->whereNull('users.deleted_at')
            ->first();

        if (empty($payment_gateway_id)) {
            return abort(404);
        }

        // send request to paymentgateway
        return view('test.v2.bank', compact('order_id', 'input'));
    }

    // ================================================
    /* method : bankSubmit
     * @param  : 
     * @description : bank submit page
     */// ===============================================
    public function bankSubmit(Request $request, $order_id)
    {
        $card_data = $request->only([
            'address',
            'country',
            'city',
            'state',
            'zip',
            'phone_no'
        ]);

        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'The link is expired, please try again.',
            ]);
        }

        // validate user and payment_gateway_id
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id', 'users.mid')
            ->where('users.id', $transaction_session->user_id)
            ->where('users.is_active', '1')
            ->whereNull('users.deleted_at')
            ->first();

        $input_data = json_decode($transaction_session['request_data'], true);

        $input_data = array_filter($input_data, function ($a) {
            return ($a !== null) && $a !== '';
        });
        $card_data = array_filter($card_data, function ($a) {
            return ($a !== null) && $a !== '';
        });

        $input = array_merge($input_data, $card_data);

        // update input_details field
        TransactionSession::where('order_id', $order_id)
            ->update(['input_details' => json_encode($input)]);

        $input['payment_gateway_id'] = $payment_gateway_id->bank_mid;

        // payment gateway object
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        // mid default currency
        $check_selected_currency = $this->midDefaultCurrencyCheck($input['payment_gateway_id'], $input['currency'], $input['amount']);
        if ($check_selected_currency) {
            $input['is_converted'] = '1';
            $input['converted_amount'] = $check_selected_currency['amount'];
            $input['converted_currency'] = $check_selected_currency['currency'];
        } else {
            $input['converted_amount'] = $input['amount'];
            $input['converted_currency'] = $input['currency'];
        }

        // gateway curl response
        $gateway_curl_response = $this->gatewayCurlResponse($input, $check_assign_mid, 'TestBank');

        // transaction requires 3ds verification
        if ($gateway_curl_response['status'] == '7') {
            return response()->json([
                'status' => 'success',
                'message' => '3DS Link generated successfully.',
                'url' => $gateway_curl_response['redirect_3ds_url'],
            ]);
        }

        $input['status'] = $gateway_curl_response['status'];
        $input['reason'] = $gateway_curl_response['reason'];

        // transaction success
        if ($gateway_curl_response['status'] == '1') {
            $store_transaction_link = $this->storeTransactionAPIVTwo($input);

            return response()->json([
                'status' => 'success',
                'message' => $input['reason'],
                'url' => $store_transaction_link
            ]);
        }

        $store_tx_try = $this->tx_try->storeData($input);

        return response()->json([
            'status' => 'success',
            'message' => $input['reason'],
            'url' => route('api.v2.test-decline', $order_id)
        ]);
    }

    // ================================================
    /* method : crypto
     * @param  : 
     * @description : crypto select view
     */// ===============================================
    public function crypto($order_id)
    {
        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return abort(404);
        }

        $input = json_decode($transaction_session['request_data'], true);

        // validate user and payment_gateway_id
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id', 'users.mid')
            ->where('users.id', $transaction_session->user_id)
            ->where('users.is_active', '1')
            ->whereNull('users.deleted_at')
            ->first();

        if (empty($payment_gateway_id)) {
            return abort(404);
        }

        // payment gateway object
        $check_assign_mid = checkAssignMID($payment_gateway_id->mid);

        if ($check_assign_mid == false) {
            return abort(404);
        }

        $required_fields = json_decode($check_assign_mid->required_fields, true);

        $data = [];
        foreach ($required_fields as $field) {
            if (empty($input[$field]) || $input[$field] == null) {
                $data[] = $field;
            }
        }

        // update input_details field
        TransactionSession::where('order_id', $order_id)
            ->update(['input_details' => json_encode($input)]);

        // send request to paymentgateway
        return view('test.v2.crypto', compact('order_id', 'data', 'input'));
    }

    // ================================================
    /* method : cryptoSubmit
     * @param  : 
     * @description : bank submit page
     */// ===============================================
    public function cryptoSubmit(Request $request, $order_id)
    {
        $card_data = $request->only([
            'address',
            'country',
            'city',
            'state',
            'zip',
            'phone_no'
        ]);

        $transaction_session = TransactionSession::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->whereNotNull('payment_gateway_id')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($transaction_session)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'The link is expired, please try again.',
            ]);
        }

        // validate user and payment_gateway_id
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id', 'users.mid')
            ->where('users.id', $transaction_session->user_id)
            ->where('users.is_active', '1')
            ->whereNull('users.deleted_at')
            ->first();

        $input_data = json_decode($transaction_session['input_details'], true);
        $input_data = array_filter($input_data, function ($a) {
            return $a !== null;
        });

        $input = array_merge($card_data, $input_data);

        // update input_details field
        TransactionSession::where('order_id', $order_id)
            ->update(['input_details' => json_encode($input)]);

        $input['payment_gateway_id'] = $payment_gateway_id->crypto_mid;

        // payment gateway object
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        // mid default currency
        $check_selected_currency = $this->midDefaultCurrencyCheck($input['payment_gateway_id'], $input['currency'], $input['amount']);
        if ($check_selected_currency) {
            $input['is_converted'] = '1';
            $input['converted_amount'] = $check_selected_currency['amount'];
            $input['converted_currency'] = $check_selected_currency['currency'];
        } else {
            $input['converted_amount'] = $input['amount'];
            $input['converted_currency'] = $input['currency'];
        }

        // gateway curl response
        $gateway_curl_response = $this->gatewayCurlResponse($input, $check_assign_mid, 'TestCrypto');

        // transaction requires 3ds verification
        if ($gateway_curl_response['status'] == '7') {
            return response()->json([
                'status' => 'success',
                'message' => '3DS Link generated successfully.',
                'url' => $gateway_curl_response['redirect_3ds_url'],
            ]);
        }

        $input['status'] = $gateway_curl_response['status'];
        $input['reason'] = $gateway_curl_response['reason'];

        // transaction success
        if ($gateway_curl_response['status'] == '1') {
            $store_transaction_link = $this->storeTransactionAPIVTwo($input);

            return response()->json([
                'status' => 'success',
                'message' => $input['reason'],
                'url' => $store_transaction_link
            ]);
        }

        $store_tx_try = $this->tx_try->storeData($input);

        return response()->json([
            'status' => 'success',
            'message' => $input['reason'],
            'url' => route('api.v2.test-decline', $order_id)
        ]);
    }

    // ================================================
    /* method : success
     * @param  : 
     * @description : success page after transaction success
     */// ===============================================
    public function success($order_id)
    {
        $tx = TxTry::where('order_id', $order_id)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($tx)) {
            $tx = TransactionSession::where('order_id', $order_id)
                ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
                ->whereIn('payment_gateway_id', [1, 2])
                ->whereNotNull('payment_gateway_id')
                ->where('is_completed', 1)
                ->orderBy('id', 'desc')
                ->first();
        }

        if (empty($tx)) {
            return abort(404);
        }

        $transaction = Transaction::where('order_id', $order_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->whereIn('payment_gateway_id', [1, 2])
            ->where('status', 1)
            ->first();

        if (empty($transaction)) {
            return abort(404);
        }

        $input = json_decode($tx['request_data'], true);

        $input['reason'] = $input['reason'] ?? 'Transaction processed successfully.';

        $domain = parse_url($input['response_url'], PHP_URL_HOST);

        $order_id = $input['order_id'] ?? null;
        $customer_order_id = $input['customer_order_id'] ?? null;

        if (parse_url($input['response_url'], PHP_URL_QUERY)) {
            $redirect_url = $input['response_url'] . '&responseCode=' . $input['status'] . '&responseMessage=' . $input['reason'] . '&order_id=' . $order_id . '&customer_order_id=' . $customer_order_id;
        } else {
            $redirect_url = $input['response_url'] . '?responseCode=' . $input['status'] . '&responseMessage=' . $input['reason'] . '&order_id=' . $order_id . '&customer_order_id=' . $customer_order_id;
        }

        // send declined message
        return view('test.v2.success', compact('redirect_url', 'input'));
    }

    // ================================================
    /* method : decline
     * @param  : 
     * @description : decline page after transaction decline
     */// ===============================================
    public function decline($order_id)
    {
        $tx = TxTry::where('order_id', $order_id)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($tx)) {
            $tx = TransactionSession::where('order_id', $order_id)
                ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
                ->whereIn('payment_gateway_id', [1, 2])
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
        $input['reason'] = $input['reason'] ?? 'User has cancelled the transaction.';

        // send declined message
        return view('test.v2.decline', compact('input', 'order_id'));
    }

    // ================================================
    /* method : redirect
     * @param  : 
     * @description : redirect to merchant website after transaction decline
     */// ===============================================
    public function redirect($order_id)
    {
        $tx = TxTry::where('order_id', $order_id)
            ->orderBy('id', 'desc')
            ->first();

        if (empty($tx)) {
            $tx = TransactionSession::where('order_id', $order_id)
                ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
                ->whereIn('payment_gateway_id', [1, 2])
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
        $input['reason'] = $input['reason'] ?? 'User has cancelled the transaction.';

        // transaction success
        $store_transaction_link = $this->storeTransactionAPIVTwo($input);

        return redirect()->away($store_transaction_link);
    }

    // ================================================
    /* method : gatewayCurlResponse
     * @param  :
     * @description : get first response from gateway
     */// ==============================================
    public function gatewayCurlResponse($input, $check_assign_mid, $title)
    {
        try {
            $class_name = 'App\\Http\\Controllers\\Repo\\PaymentGateway\\' . $title;
            $gateway_class = new $class_name;
            $gateway_return_data = $gateway_class->checkout($input, $check_assign_mid);
        } catch (\Exception $exception) {
            \Log::info(['CardPaymentException' => $exception->getMessage()]);
            $gateway_return_data['status'] = '0';
            $gateway_return_data['reason'] = 'Problem with your transaction data or may be transaction timeout from the bank.';
        }

        return $gateway_return_data;
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
            // return false;
            return '0';
        }
    }
}