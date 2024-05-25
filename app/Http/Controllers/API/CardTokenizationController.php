<?php

namespace App\Http\Controllers\API;
use Auth;
use DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use URL;
use Mail;
use App\User;
use App\WebsiteUrl;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Repo\TransactionRepo;

class CardTokenizationController extends Controller
{
    // ================================================
    /* method : __construct
    * @param  :
    * @Description : Create a new controller instance.
    */// ==============================================
    public function __construct()
    {
        $this->user = new User;
        $this->Transaction = new Transaction;
        $this->transaction_repo = new TransactionRepo;
    }

    private function validator($data)
    {
        return Validator::make($data, [
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required|max:4|min:4|',
            'cvvNumber' => 'required',
        ]);
    }

    // ================================================
    /* method : index
    * @param  :
    * @Description : create card tokenization
    */// ==============================================
    public function index(Request $request)
    {
        $input = $request->all();
        // \Log::info($request->all());
        $validator = $this->validator($input);

        if ($validator->fails()) {
            $errors = $validator->errors()->messages();

            return response()->json([
                'status' => 'fail',
                'message' => 'Some parameters are missing or invalid request data, please check \'errors\' parameter for more details.',
                'errors' => $errors,
            ]);
        }

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'dsflkIZxusugQdpMyjqTSE3sadjL5vsd';
        $secret_iv = '7sad4vdsJjas87saMLmlNi9x63MRAFLgk';
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
     
        $string = $input['card_no'].'-'.$input['ccExpiryMonth'].'-'.$input['ccExpiryYear'].'-'.$input['cvvNumber'];
        // encrypt token;
        $encryptToken = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $encryptToken = base64_encode($encryptToken);
        // decrypt token
        $decryptToken = openssl_decrypt(base64_decode($encryptToken), $encrypt_method, $key, 0, $iv);
     
        return response()->json([
            'status' => 'success',
            'card_token' => $encryptToken
        ]);
    }

    private function transactionValidator($data)
    {
        return Validator::make($data, [
            'card_token' => 'required',
            'api_key' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'country' => 'required|max:2|min:2|regex:(\b[A-Z]+\b)',
            'state' => 'required',
            'city' => 'required',
            'ip_address' => 'required',
            'zip' => 'required',
            'email' => 'required|email',
            'country_code' => 'required',
            'phone_no' => 'required',
            'amount' => 'required',
            'response_url' => 'required',
            'currency' => 'required|max:3|min:3|regex:(\b[A-Z]+\b)',
        ]);
    }

    // ================================================
    /* method : store
    * @param  :
    * @Description : transaction store 
    */// ==============================================
    public function store(Request $request)
    {
        // dd($request->all());
        $request_only = config('required_field.required_all_fields');
        $input = $request->only($request_only);

        $validator = $this->transactionValidator($input);

        if ($validator->fails()) {
            $errors = $validator->errors()->messages();

            return response()->json([
                'status' => 'fail',
                'message' => 'Some parameters are missing or invalid request data, please check \'errors\' parameter for more details.',
                'errors' => $errors,
            ]);
        }

        // get if the request from iframe
        if(isset($input['card_token']) && $input['card_token'] != '') {
            $encrypt_method = "AES-256-CBC";
            $secret_key = 'dsflkIZxusugQdpMyjqTSE3sadjL5vsd';
            $secret_iv = '7sad4vdsJjas87saMLmlNi9x63MRAFLgk';

            // hash
            $key = hash('sha256', $secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            // decrypt token
            $card_info = openssl_decrypt(base64_decode($input['card_token']), $encrypt_method, $key, 0, $iv);
            // dd($card_info);
            if($card_info == false) {
                return response()->json([
                    'status' => 'fail',
                    'errors' => 'invalid card_token.'
                ]);
            }
            $card_details = explode('-', $card_info);
        }

        $customer_order_id = isset($request->customer_order_id)?$request->customer_order_id:null;
        // if api_key is not included in request
        if(empty($input['api_key']) || $input['api_key'] == null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'api_key parameter is required.',
                'data' => [
                    'order_id' => null,
                    'amount' => isset($input['amount'])?$input['amount']:null,
                    'currency' => isset($input['currency'])?$input['currency']:null,
                    'email' => isset($input['email'])?$input['email']:null,
                    'customer_order_id' => $customer_order_id,
                ]
            ]);
        }

        // validate API key
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id','users.mid')
            ->where('users.api_key', $input['api_key'])
            ->where('users.is_active', '1')
            ->where('users.deleted_at', null)
            ->first();

        // if api_key is not valid or user deleted
        if(!$payment_gateway_id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'please check your API key',
                'data' => [
                    'order_id' => null,
                    'amount' => isset($input['amount'])?$input['amount']:null,
                    'currency' => isset($input['currency'])?$input['currency']:null,
                    'email' => isset($input['email'])?$input['email']:null,
                    'customer_order_id' => $customer_order_id,
                ]
            ]);
        }

        // gateway object
        $check_assign_mid = checkAssignMID($payment_gateway_id->mid);

        // user IP and domain and request from API
        $request->merge([
            'payment_type' => $request->payment_type ?? 'card',
            'request_from_ip' => $request->ip(),
            'request_origin' => $_SERVER['HTTP_HOST'],
            'is_request_from_vt' => 'API',
            'user_id' => $payment_gateway_id->id,
            'payment_gateway_id' => $payment_gateway_id->mid,
            'is_disable_rule' =>$payment_gateway_id->is_disable_rule,
            'card_no' => $card_details[0],
            'ccExpiryMonth' => $card_details[1],
            'ccExpiryYear' => $card_details[2],
            'cvvNumber' => $card_details[3]
        ]);

        // remove api_key
        $api_key = $input['api_key'];

        // check ip_restriction
        if ($payment_gateway_id->is_ip_remove == '0') {
            $getIPData = WebsiteUrl::where('user_id', $payment_gateway_id->id)
                ->where('ip_address', request()->ip())
                ->first();

            // if IP is not added on the IP whitelist
            if(!$getIPData) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'This API key is not permitted for transactions from this IP address ('.request()->ip().'). Please add your IP from login your dashboard.',
                    'data' => [
                        'order_id' => null,
                        'amount' => $input['amount'],
                        'currency' => $input['currency'],
                        'email' => $input['email'],
                        'customer_order_id' => $customer_order_id,
                    ]
                ]);
            }

            // if IP is not approved
            if($getIPData->is_active == '0') {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Your Website URL and your IP ('.request()->ip().') is still under approval , Please contact '.config("app.name").' Support for more information',
                    'data' => [
                        'order_id' => null,
                        'amount' => $input['amount'],
                        'currency' => $input['currency'],
                        'email' => $input['email'],
                        'customer_order_id' => $customer_order_id,
                    ]
                ]);
            }

            request()->merge([
                'website_url_id' => $getIPData->id
            ]);
        }

        $requestInput = \Arr::except($request->all(), array('card_token'));
        // send request to transaction repo class
        $return_data = $this->transaction_repo->store($requestInput);

        // if return_data is null
        if(!$return_data || $return_data == null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Something went wrong, please contact technical team.',
                'data' => [
                    'order_id' => null,
                    'amount' => $input['amount'],
                    'currency' => $input['currency'],
                    'email' => $input['email'],
                    'customer_order_id' => $customer_order_id,
                ]
            ]);
        }

        // transaction requires 3DS redirect
        if($return_data['status'] == '7') {
            return response()->json([
                'status' => '3d_redirect',
                'message' => $return_data['reason'],
                'redirect_3ds_url' => $return_data['redirect_3ds_url'],
                'customer_order_id' => $customer_order_id,
                'api_key' => $api_key,
            ]);
        // transaction success
        } elseif($return_data['status'] == '1') {
            return response()->json([
                'status' => 'success',
                'message' => $return_data['reason'],
                'data' => [
                    'order_id' => $return_data['order_id'],
                    'amount' => $input['amount'],
                    'currency' => $input['currency'],
                    'email' => $input['email'],
                    'customer_order_id' => $customer_order_id,
                ]
            ]);
        // transaction pending
        } elseif ($return_data['status'] == '2') {
            return response()->json([
                'status' => 'pending',
                'message' => $return_data['reason'],
                'data' => [
                    'order_id' => $return_data['order_id'],
                    'amount' => $input['amount'],
                    'currency' => $input['currency'],
                    'email' => $input['email'],
                    'customer_order_id' => $customer_order_id,
                ]
            ]);
        // transaction fail
        } elseif ($return_data['status'] == '0') {
            return response()->json([
                'status' => 'fail',
                'message' => $return_data['reason'],
                'data' => [
                    'order_id' => isset($return_data['order_id'])?$return_data['order_id']:null,
                    'amount' => $input['amount'],
                    'currency' => $input['currency'],
                    'email' => $input['email'],
                    'customer_order_id' => $customer_order_id,
                ]
            ]);
        // transaction blocked
        } elseif ($return_data['status'] == '5') {
            return response()->json([
                'status' => 'blocked',
                'message' => $return_data['reason'],
                'data' => [
                    'order_id' => $return_data['order_id'],
                    'amount' => $input['amount'],
                    'currency' => $input['currency'],
                    'email' => $input['email'],
                    'customer_order_id' => $customer_order_id,
                ]
            ]);
        // no response
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => isset($return_data['reason']) ? $return_data['reason'] : 'Something went wrong, please contact technical team.',
                'data' => [
                    'order_id' => isset($return_data['order_id']) ? $return_data['order_id'] : null,
                    'amount' => $input['amount'],
                    'currency' => $input['currency'],
                    'email' => $input['email'],
                    'customer_order_id' => $customer_order_id,
                ]
            ]);
        }
    }
}
