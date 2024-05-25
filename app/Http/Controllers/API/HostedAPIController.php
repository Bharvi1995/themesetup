<?php

namespace App\Http\Controllers\API;

use App\User;
use App\BlockData;
use App\WebsiteUrl;
use App\Transaction;
use App\TransactionHostedSession;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Repo\TransactionRepo;
use App\Traits\StoreTransaction;
use App\Transformers\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HostedAPIController extends Controller
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
        $this->TransactionRepo = new TransactionRepo;
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

        $input['user_id'] = $input['user_id'] ?? $user->id;
        $input['is_request_from_vt'] = $input['is_request_from_vt'] ?? 'HOSTED API';

        // if api_key is not valid or user deleted
        if (empty($user)) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, Invalid API Key or merchant deleted';
            return ApiResponse::unauthorised($input);
        }

        // if request from iframe
        if (isset($input['token']) && !empty($input['token'])) {
            $encrypt_method = "AES-256-CBC";
            $secret_key = 'dsflkIZxusugQdpMyjqTSE3sadjL5vsd';
            $secret_iv = '7sad4vdsJjas87saMLmlNi9x63MRAFLgk';

            // hash
            $key = hash('sha256', $secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            // decrypt token
            $iframe_json = openssl_decrypt(base64_decode($input['token']), $encrypt_method, $key, 0, $iv);

            if ($iframe_json == false) {
                $input['status'] = '6';
                $input['message'] = 'Invalid token iframe code.';
                return ApiResponse::unauthorised($input);
            }

            $iframe_array = json_decode($iframe_json, 1);

            if (isset($iframe_array['create_by']) && $iframe_array['create_by'] == 'admin') {
                $input['is_request_from_vt'] = 'IFRAMEAV1';
            } else {
                $input['is_request_from_vt'] = 'IFRAMEUV1';
            }

            $input['payment_gateway_id'] = $iframe_array['mid'];

            unset($input['token']);
        } else {
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

            $input['is_request_from_vt'] = 'HOSTED API';
            $input['payment_gateway_id'] = $user->mid;
        }

        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        if ($check_assign_mid == false) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, Your account is disabled.';
            return ApiResponse::unauthorised($input);
        }

        $validator = Validator::make($input, [
            'first_name' => 'required|min:3|max:100|regex:/^[a-zA-Z\s]+$/',
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
        $input['ip_address'] = $this->getClientIP();
        $input['request_from_ip'] = $this->getClientIP();
        $input['request_origin'] = $_SERVER['HTTP_HOST'];
        $input['user_id'] = $user->id;

        $input['session_id'] = 'XR' . strtoupper(\Str::random(4)) . time();
        $input['order_id'] = 'TRN' . strtoupper(\Str::random(4)) . time() . strtoupper(\Str::random(6));

        // saving to transaction_hosted_session
        $this->transactionHostedSession->storeData($input);

        if (in_array($input['payment_gateway_id'], [1, 2])) {
            $input['status'] = 7;
            $input['redirect_3ds_url'] = route('test.hostedAPI.cardForm', $input['session_id']);
        } else {
            $input['status'] = 7;
            $input['redirect_3ds_url'] = route('hostedAPI.cardForm', $input['session_id']);
        }

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
            abort(404);
        }

        $input = json_decode($session_data->request_data, 1);

        if (in_array($input['payment_gateway_id'], [1, 2])) {
            abort(404);
        }

        $userData = User::select('iframe_logo')
            ->where('id', $session_data->user_id)
            ->first();

        return view('gateway.hosted.index', compact('session_id', 'userData', 'input'));
    }

    // ================================================
    /* method : cardSubmit
     * @param  :
     * @Description : submit credit card page
     */// ==============================================
    public function cardSubmit(Request $request, $session_id)
    {
        $this->validate($request, [
            'card_no' => 'required|min:12|max:24',
            'ccExpiryMonthYear' => 'required',
            'cvvNumber' => 'required|numeric|min:0|max:9999',
        ]);

        // Get all input data
        $input_session = TransactionHostedSession::where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHours(2)->toDateTimeString())
            ->where('is_completed', '0')
            ->orderBy('id', 'desc')
            ->first();

        if ($input_session == null) {
            abort(404);
        }

        $input = json_decode($input_session['request_data'], 1);

        if (in_array($input['payment_gateway_id'], [1, 2])) {
            abort(404);
        }

        $ccExpiryMonthYear = explode('/', str_replace(' ', '', $request->ccExpiryMonthYear));
        if (isset($ccExpiryMonthYear[0]) && $ccExpiryMonthYear[0] >= 01 && $ccExpiryMonthYear[0] <= 12) {
            $input['ccExpiryMonth'] = $ccExpiryMonthYear[0];
        } else {
            return redirect()->back()->withInput()->with('error', 'Card expiry month not valid');
        }

        if (isset($ccExpiryMonthYear[1]) && $ccExpiryMonthYear[1] >= date('Y') && $ccExpiryMonthYear[1] <= 2050) {
            $input['ccExpiryYear'] = $ccExpiryMonthYear[1];
        } else {
            return redirect()->back()->withInput()->with('error', 'Card expiry year not valid');
        }

        $ccExpiryYear = substr($request->ccExpiryMonthYear, -2);
        $check_assign_mid = checkAssignMID($input_session['payment_gateway_id']);

        // validate user
        $user = DB::table('users')
            ->where('id', $input['user_id'])
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->first();

        $input['card_no'] = str_replace(" ", "", $request->card_no);
        $input['cvvNumber'] = $request->cvvNumber;
        $input['ip_address'] = $this->getClientIP();
        $input['card_type'] = $this->getCreditCardType($input['card_no']);

        TransactionHostedSession::where('transaction_id', $session_id)
            ->update(['is_completed' => '1']);

        // send request to transaction repo class
        $return_data = $this->TransactionRepo->store($input, $user, $check_assign_mid);

        // transaction requires 3ds redirect
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