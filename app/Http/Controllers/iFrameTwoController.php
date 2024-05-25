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
use App\Admin;
use App\Gateway;
use App\BlockData;
use App\ImageUpload;
use App\MainMID;
use App\Transaction;
use App\TxTry;
use App\WebsiteUrl;
use App\Merchantapplication;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Mail\TransactionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\Mid;
use App\Traits\StoreTransaction;
use App\TransactionHostedSession;

class iFrameTwoController extends Controller
{
    use Mid, StoreTransaction;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User;
        $this->transaction = new Transaction;
        $this->tx_try = new TxTry;
        $this->transactionSession = new TransactionSession;
    }

    /**
     * Show the iframe form view.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($token, Request $request)
    {
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'dsflkIZxusugQdpMyjqTSE3sadjL5vsd';
        $secret_iv = '7sad4vdsJjas87saMLmlNi9x63MRAFLgk';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        // decrypt token
        $iframe_json = openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv);

        if($iframe_json == false) {
            return view('gateway.response')->with('responseMessage', 'Invalid payment link.');
        }

        $iframe_array = json_decode($iframe_json, 1);

        $userData = User::where('id', $iframe_array['user_id'])
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->first();

        if (empty($userData)) {
            return view('gateway.response')->with('responseMessage', 'Invalid payment link.');
        }

        if($userData->mid == '0') {
            return view('gateway.response')->with('responseMessage', 'Merchant account disabled or deleted.');
        }

        if(in_array($userData->mid, ['1', '2'])) {
            return view('gateway.response')->with('responseMessage', 'Merchant account is on Test mode.');
        }

        if(in_array($iframe_array['mid'], ['1', '2'])) {
            return view('gateway.response')->with('responseMessage', 'Can not process transaction on test MID.');
        }
        
        $check_assign_mid = checkAssignMid($iframe_array['mid']);

        if ($check_assign_mid == false) {
            return view('gateway.response')->with('responseMessage', 'Merchant account disabled or deleted.');
        }
        $required_fields = json_decode($check_assign_mid->required_fields, 1);

        return view('gateway.v2.iframe', compact('token', 'required_fields', 'iframe_array', 'userData'));
    }

    public function store(Request $request, $token)
    {
        $this->validate($request, [
            'first_name' => 'required|min:2|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|min:2|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email',
            'amount' => 'required|regex:/^\d+(\.\d{1,9})?$/',
            'currency' => 'required|max:3|min:3|regex:(\b[A-Z]+\b)',
        ]);

        if(!empty($token)) {
            $encrypt_method = "AES-256-CBC";
            $secret_key = 'dsflkIZxusugQdpMyjqTSE3sadjL5vsd';
            $secret_iv = '7sad4vdsJjas87saMLmlNi9x63MRAFLgk';

            // hash
            $key = hash('sha256', $secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            // decrypt token
            $iframe_json = openssl_decrypt(base64_decode($token), $encrypt_method, $key, 0, $iv);
            
            if($iframe_json == false) {
                return view('gateway.response')->with('responseMessage', 'Invalid payment link.');
            }

            $iframe_array = json_decode($iframe_json, 1);

        } else {
            return view('gateway.response')->with('responseMessage', 'Invalid payment link.');
        }

        $request_only = config('required_v2.required_all_fields');
        
        $input = $request->only($request_only);

        // amount and currency assign
        if (isset($iframe_array['amount']) && !empty($iframe_array['amount'])) {
            $input['amount'] = $iframe_array['amount'];
        }
        if (isset($iframe_array['currency']) && !empty($iframe_array['currency'])) {
            $input['currency'] = $iframe_array['currency'];
        }
        
        // validate API key
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id','users.mid')
            ->where('users.id', $iframe_array['user_id'])
            ->where('users.is_active', '1')
            ->whereNull('users.deleted_at')
            ->first();
        
        // if merchant on test mode
        if(in_array($payment_gateway_id->midid, ['1', '2'])) {
            notificationMsg('error', 'You are on test mode, please contact support to move live account.');
            return redirect()->back();
        }

        // gateway object
        $check_assign_mid = checkAssignMID($payment_gateway_id->midid);
        if($check_assign_mid == false) {
            notificationMsg('error', 'Your account is temporarily disabled, please contact admin.');
            return redirect()->back();
        }
        
        // check email block
        $blocked_email = BlockData::where('type', 'Email')
            ->where('field_value', $input['email'])
            ->exists();
        if($blocked_email) {
            notificationMsg('error', 'This email address(' . $input['email'] . ') is blocked for transaction.');
            return redirect()->back();
        }

        $input['response_url'] = route('iframe2.response', $token);
        $input['user_id'] = $iframe_array['user_id'];
        $input['api_key'] = $iframe_array['api_key'];

        if ($iframe_array['create_by'] == 'admin') {
            $input['payment_gateway_id'] = $iframe_array['mid'];
            $input['is_request_from_vt'] = 'IFRAMEAV2';
            $input['payment_type'] = $iframe_array['type'];
        } else {
            $input['payment_gateway_id'] = $payment_gateway_id->mid;
            $input['is_request_from_vt'] = 'IFRAMEMV2';
        }

        // order and session id
        $input['session_id'] = 'XR'. strtoupper(\Str::random(4)) . time();
        $input['order_id'] = 'TRN'. strtoupper(\Str::random(4)) . time() . strtoupper(\Str::random(6));
        
        // user IP and domain and request from API
        $input['request_from_ip'] = $request->ip();
        $input['request_origin'] = $_SERVER['HTTP_HOST'];
        $input['is_disable_rule'] = $payment_gateway_id->is_disable_rule;
        $input['ip_address'] = $this->getClientIP();

        // get amount in usd
        $input['amount_in_usd'] = $this->amountInUSD($input);

        // store transaction_session data
        $this->transactionSession->storeData($input);

        if ($input['payment_type'] == 'Card') {
            return redirect()->route('api.v2.card', $input['order_id']);
        } elseif ($input['payment_type'] == 'Bank') {
            return redirect()->route('api.v2.bank', $input['order_id']);
        } elseif ($input['payment_type'] == 'Crypto') {
            return redirect()->route('api.v2.crypto', $input['order_id']);
        } elseif ($input['payment_type'] == 'UPI') {
            return redirect()->route('api.v2.upi', $input['order_id']);
        } else {
            return redirect()->route('api.v2.card', $input['order_id']);
        }
    }

    // ================================================
    /* method : response
    * @param  : 
    * @description : gateway response
    */// ===============================================
    public function response($token, Request $request)
    {
        $response = $request->all();
        
        return view('gateway.v2.response', compact('response', 'token'));
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
}
