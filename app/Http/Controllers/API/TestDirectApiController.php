<?php

namespace App\Http\Controllers\API;

use App\WebsiteUrl;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Repo\TestTransactionRepo;
use App\Transformers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
class TestDirectApiController extends Controller
{
    protected $test_transaction_repo;

    // ================================================
    /* method : __construct
     * @param  :
     * @Description : Create a new controller instance.
     */// ==============================================
    public function __construct()
    {
        $this->test_transaction_repo = new TestTransactionRepo;
    }

    // ================================================
    /* method : store
     * @param  :
     * @Description : create transaction API $request
     */// ==============================================
    
    private function validateBasicAuth(Request $request)
    {
        $authorization = $request->header('Authorization');
        if (!$authorization || !str_starts_with($authorization, 'Basic ')) {
            $input['status'] = '6';
            $input['reason'] = 'The request lacks valid authentication credentials. Please check the provided header parameters.';
            abort(ApiResponse::unauthorised($input));
        }
        $credentials = base64_decode(substr($authorization, 6));
        list($username, $password) = explode(':', $credentials, 2);
        $user = User::where('email', $username)->where("api_key",$password)->first();
        if (!$user) {
            $input['status'] = '6';
            $input['reason'] = 'The request lacks valid authentication credentials. Please check the provided header parameters.';
            abort(ApiResponse::unauthorised($input));
        }
        $request->merge(['user' => $user]);
    }

    public function store(Request $request)
    {
        $this->validateBasicAuth($request);
        // only accept parameters that are available
        // $request_only = config('required_field.fields');
        $input = $request->only(['user_first_name', 'user_last_name', 'user_email', 'user_phone_no', 'user_amount', 'user_currency', 'user_address', 'user_country', 'user_state', 'user_city', 'user_zip', 'user_order_ref', 'user_redirect_url', 'user_webhook_url','user','user_card_no','user_ccexpiry_month','user_ccexpiry_year','user_cvv_number']);
        // user IP and domain and request from API
        $input['payment_type'] = 'card';
        $input['request_from_ip'] = $request->ip();
        $input['request_origin'] = $_SERVER['HTTP_HOST'];
        $input['is_request_from_vt'] = 'TESTAPI';
        $input['user_id'] = $input["user"]->id;
        $input['payment_gateway_id'] = 1;

        // check only user assigned gateway is active
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
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $input['status'] = '6';
            $input['reason'] = $errors[0] ?? 'Kindly review your request payload to ensure all required fields are provided.';
            return ApiResponse::unauthorised($input);
        }

        // check ip_restriction
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

        // send request to transaction repo class
        $return_input = $this->test_transaction_repo->store($input, $input["user"], $check_assign_mid);

        // if return_input is null
        if (empty($return_input)) {
            $input['status'] = '6';
            $input['reason'] = 'Something went wrong with your request. Kindly try again';
            return ApiResponse::unauthorised($input);
        }

        $input = array_merge($input, $return_input);

        // transaction requires 3ds redirect
        if ($return_input['status'] == '7') {
            return ApiResponse::redirect($input);
            // transaction success
        } elseif ($return_input['status'] == '1') {
            return ApiResponse::success($input);
            // transaction pending
        } elseif ($return_input['status'] == '2') {
            return ApiResponse::pending($input);
            // transaction fail
        } elseif ($return_input['status'] == '0') {
            return ApiResponse::fail($input);
            // transaction blocked
        } elseif ($return_input['status'] == '5') {
            return ApiResponse::blocked($return_input);
            // no response
        } else {
            $input['status'] = '6';
            $input['reason'] = 'Something went wrong with your request. Kindly try again';
            return ApiResponse::unauthorised($input);
        }
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


    public function demoApi(Request $request)
    {
        $payload = $request->validate([
            "name" => "required|min:2|max:60",
            "city" => "required|min:2"
        ]);

        return [
            "name" => $payload["name"],
            "city" => $payload["city"],
            "Request Url" => $request->fullUrl(),
            "Request Domain" => $request->getHost(),
            "Request IP" => $request->ip(),
            "Http Method" => $request->method(),
            "headers meta" => $request->headers->all(),
        ];
    }
}