<?php

namespace App\Http\Controllers\API;

use App\User;
use App\BlockData;
use App\WebsiteUrl;
use App\Transaction;
use App\TransactionHostedSession;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Repo\TestTransactionRepo;
use App\Traits\StoreTransaction;
use App\Transformers\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TestHostedAPIController extends Controller
{
    use StoreTransaction;

    protected $user, $Transaction, $TransactionRepo, $transactionHostedSession;

    // ================================================
    /* method : __construct
    * @param  :
    * @Description : Create a new controller instance.
    */// ==============================================
    public function __construct()
    {
        $this->user = new User;
        $this->Transaction = new Transaction;
        $this->TransactionRepo = new TestTransactionRepo;
        $this->transactionHostedSession = new TransactionHostedSession;
    }

    // ================================================
    /* method : store
    * @param  :
    * @Description : create transaction API $request
    */// ==============================================
    public function store(Request $request)
    {
        // only accept parameters that are available
        $request_only = config('required_field.fields');

        $input = $request->only($request_only);
        $api_key = $request->bearerToken();

        // if api_key is not included in request
        if (empty($api_key)) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, please pass API Key in Header';
            return ApiResponse::unauthorised($input);
        }

        // validate api_key
        $user = DB::table('users')
            ->where('api_key', $api_key)
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->first();

        // if api_key is not valid or user deleted
        if (empty($user)) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, Invalid API Key or merchant deleted';
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
                $input['message'] = 'Unauthorised request, IP address(' . $this->getClientIP() . ') approval pending.';
                return ApiResponse::unauthorised($input);
            }
            $input["website_url_id"] = $getIPData->id;
        }

        $check_assign_mid = checkAssignMID($user->mid);

        if ($check_assign_mid == false) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, Your account is disabled.';
            return ApiResponse::unauthorised($input);
        }

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
            'response_url' => 'required|url',
            'webhook_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            $input['status'] = '6';
            $input['reason'] = $errors[0] ?? 'Unauthorised request, please check your request payload.';
            return ApiResponse::unauthorised($input);
        }

        $block_email = BlockData::where('type', 'Card')
            ->where('field_value', $input['email'])
            ->exists();
        if (!empty($block_email)) {
            $input['status'] = '5';
            $input['reason'] = 'This email address(' . $input['email'] . ') is blocked for transaction.';
            return $input;
        }

        $input['payment_type'] = $request->payment_type ?? 'card';
        $input['user_id'] = $user->id;
        $input['payment_gateway_id'] = 1;

        $input['session_id'] = 'XR'. strtoupper(\Str::random(4)) . time();
        $input['order_id'] = 'TRN'. strtoupper(\Str::random(4)) . time() . strtoupper(\Str::random(6));

        // saving to transaction_hosted_session
        $this->transactionHostedSession->storeData($input);

        $input['status'] = 7;
        $input['redirect_3ds_url'] = route('test.hostedAPI.cardForm', $input['session_id']);

        // card page
        return ApiResponse::redirect($input);
    }

    // ================================================
    /* method : cardForm
    * @param  :
    * @Description : credit card view page
    */// ==============================================
    public function cardForm(Request $request, $session_id)
    {
        // Get all input data
        $session_data = TransactionHostedSession::where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHours(2)->toDateTimeString())
            ->where('is_completed', '0')
            ->orderBy('id', 'desc')
            ->first();

        if (empty($session_data)) {
            return view('gateway.hosted.error');
        }

        $input = json_decode($session_data->request_data, 1);

        if (!in_array($input['payment_gateway_id'], [1, 2])) {
            abort(404);
        }

        $userData = User::select('iframe_logo')
            ->where('id', $input['user_id'])
            ->first();

        return view('gateway.hosted.testhosted', compact('session_id', 'userData', 'input'));
    }

    // ================================================
    /* method : cardSubmit
    * @param  :
    * @Description : submit credit card page
    */// ==============================================
    public function cardSubmit(Request $request, $session_id)
    {
        $this->validate($request, [
            'card_no' => 'required',
            'ccExpiryMonthYear' => 'required',
            'cvvNumber' => 'required',
        ]);

        $input_session = TransactionHostedSession::where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHours(2)->toDateTimeString())
            ->where('is_completed', '0')
            ->orderBy('id', 'desc')
            ->first();

        if (empty($input_session)) {
            return view('gateway.hosted.error');
        }

        $input = json_decode($input_session['request_data'], 1);

        if (!in_array($input['payment_gateway_id'], [1, 2])) {
            abort(404);
        }

        $ccExpiryMonth = substr($request->ccExpiryMonthYear, 0, 2);
        $ccExpiryYear = substr($request->ccExpiryMonthYear, -2);

        $input['card_no'] = str_replace(" ", "", $request->card_no);
        $input['ccExpiryMonth'] = $ccExpiryMonth;
        $input['ccExpiryYear'] = '20'.$ccExpiryYear;
        $input['cvvNumber'] = $request->cvvNumber;
        $input['request_from_ip'] = $this->getClientIP();
        $input['request_origin'] = $_SERVER['HTTP_HOST'];
        $input['is_request_from_vt'] = 'TESTHOSTED';
        $input['ip_address'] = $this->getClientIP();

        $user = DB::table('users')
            ->where('id', $input['user_id'])
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->first();

        $check_assign_mid = checkAssignMID($user->mid);

        TransactionHostedSession::where('transaction_id', $session_id)
            ->update(['is_completed' => '1']);

        $return_data = $this->TransactionRepo->store($input, $user, $check_assign_mid);

        // transaction requires 3DS redirect
        if ($return_data['status'] == '7') {
            return redirect($return_data['redirect_3ds_url']);
        }

        $input['status'] = $return_data['status'];
        $input['reason'] = $return_data['reason'];

        $store_transaction_link = $this->getRedirectLink($input);
        
        return redirect($store_transaction_link);
    }

    // ================================================
    /* method : getClientIP
    * @param  :
    * @description : get client public ip
    */// ==============================================
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
