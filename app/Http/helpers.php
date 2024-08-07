<?php

use App\User;
use App\Admin;
use App\Wallet;
use App\AdminLog;
use App\MIDDetail;
use Carbon\Carbon;
use App\LogActivity;
use App\Notification;
use App\FirebaseDeviceToken;
use App\Events\UpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use phpseclib3\Crypt\RSA;
use phpseclib3\Math\BigInteger;
function notificationMsg($type, $message)
{
    Session::put($type, $message);
}

function set_active_admin($path, $active = 'start active open')
{
    return Request::is($path) ? $active : '';
}

function getUserImagePath($imagepath)
{
    if (!empty($imagepath)) {
        return "/upload/users/" . $imagepath;
    }
    return "/adminTheme/img/avatar5.png";
}
function getCategoryName($id)
{
    $data = DB::table('categories')
        ->select('name')
        ->where('id', $id)
        ->first();
    return $data ? $data->name : 'N/A';
}
function getTechnologyPartnerName($id)
{
    // dd($id);
    // dd($id);
    $data = DB::table('technology_partners')
        ->select('name')
        ->where('id', $id)
        ->first();

    return $data->name;
}

function getUserInfoOnID($id)
{
    $data = User::where('id', $id)->first();
    return $data;
}

// ================================================
/* method : return Message
 * @param  : $redirect_url = user site URL
 *           $messages (Array) = Response message
 *           $validator (Validator Object) = Laravel Validator Object
 * @Description : Response user request with proper paramaters. Return JSON if POST request and redirect user on his site if user pass the redirect URL.
 */// ==============================================
function returnMessage($redirect_url, $messages, $validator = "")
{
    if ($redirect_url == 'vt') {

        // request from VT
        if (!empty($validator)) {
            return redirect()
                ->route('virtual-terminal.index')
                ->withErrors($validator);
        } elseif (isset($messages['status']) && $messages['status'] == 'fail') {
            return redirect()
                ->route('virtual-terminal.index', ['status' => 'fail', 'message' => $messages['message']]);
        } elseif (isset($messages['status']) && $messages['status'] == 'success') {
            return redirect()
                ->route('virtual-terminal.index', ['status' => 'success', 'message' => $messages['message']]);
        } else {
            return redirect()
                ->route('virtual-terminal.index', ['status' => 'fail', 'message' => 'Something went wrong please try again.']);
        }

        // request from API with 3DS gateway
    } elseif (!empty($redirect_url)) {
        $string = http_build_query($messages);
        return redirect($redirect_url . "?" . $string);

        // direct gateway response json
    } else {
        return response()->json($messages);
    }
}

// ================================================
/* method : returnHostedMessage
 * @param  : $redirect_url = user site URL
 *           $messages (Array) = Response message
 *           $validator (Validator Object) = Laravel Validator Object
 * @Description : Response user request with proper paramaters. Return JSON if POST request and redirect user on his site if user pass the redirect URL.
 */// ==============================================
function returnHostedMessage($redirect_url, $messages, $validator = "")
{
    if ($redirect_url == 'product') {

        // request from VT
        if (!empty($validator)) {
            return redirect()
                ->route('virtual-terminal.index')
                ->withErrors($validator);
        } elseif (isset($messages['status']) && $messages['status'] == 'fail') {
            return redirect()
                ->route('virtual-terminal.index', ['status' => 'fail', 'message' => $messages['message']]);
        } elseif (isset($messages['status']) && $messages['status'] == 'success') {
            return redirect()
                ->route('virtual-terminal.index', ['status' => 'success', 'message' => $messages['message']]);
        } else {
            return redirect()
                ->route('virtual-terminal.index', ['status' => 'fail', 'message' => 'Something went wrong please try again.']);
        }

        // request from API with 3DS gateway
    } elseif (!empty($redirect_url)) {
        $string = http_build_query($messages);
        return redirect($redirect_url . "?" . $string);

        // direct gateway response json
    } else {
        return response()->json($messages);
    }
}

// ================================================
/* method : getOrderNo
* @param  :
* @Description : get order number with random string
*/// ==============================================
function getOrderNo()
{
    $data = DB::table('transaction_session')
        ->select('order_id')
        ->orderBy('id', 'DESC')
        ->first();

    if ($data) {
        $getOrderNo = substr($data->order_id, 4);
        $setInt = (int) $getOrderNo . rand(1, 9);
        $setOrderNo = date("Y") . str_pad($getOrderNo + 1, 10, 0, STR_PAD_LEFT);
        $setOrderNo = substr($setOrderNo, 0, 7) . rand(11111, 99999) . substr($setOrderNo, -7);

        return $setOrderNo;
    } else {
        return date("Y") . '0000000001';
    }
}

function getBachOrderNo()
{
    $data = DB::table('batchtransactions')
        ->select('order_id')
        ->orderBy('id', 'DESC')
        ->first();

    if ($data) {
        $getOrderNo = substr($data->order_id, 4);
        $setInt = (int) $getOrderNo;
        $setOrderNo = date("Y") . str_pad($getOrderNo + 1, 10, 0, STR_PAD_LEFT);
        return $setOrderNo;
    } else {
        return date("Y") . '0000000001';
    }
}

function getPayoutNo()
{
    $data = DB::table('payouts')
        ->select('payout_id')
        ->orderBy('id', 'DESC')
        ->first();

    if ($data) {
        $getOrderNo = substr($data->payout_id, 4);
        $setInt = (int) $getOrderNo;
        $setOrderNo = date("Y") . str_pad($getOrderNo + 1, 10, 0, STR_PAD_LEFT);
        return $setOrderNo;
    } else {
        return date("Y") . '0000000001';
    }
}

function getInvoiceNo()
{
    $data = DB::table('generatereports')
        ->select('invoice_no')
        ->orderBy('id', 'DESC')
        ->first();

    if ($data) {
        $getInvoiceNo = substr($data->invoice_no, 4);
        $setInt = (int) $getInvoiceNo;
        $setInvoiceNo = date("Y") . str_pad($getInvoiceNo + 1, 10, 0, STR_PAD_LEFT);
        return $setInvoiceNo;
    } else {
        return date("Y") . '0000000001';
    }
}

function getReportInvoiceNo()
{
    $data = DB::table('payout_reports')
        ->select('invoice_no')
        ->orderBy('id', 'DESC')
        ->first();

    if ($data) {
        $getInvoiceNo = substr($data->invoice_no, 4);
        $setInt = (int) $getInvoiceNo;
        $setInvoiceNo = date("Y") . str_pad($getInvoiceNo + 1, 10, 0, STR_PAD_LEFT);
        return $setInvoiceNo;
    } else {
        return date("Y") . '0000000001';
    }
}

// ================================================
/*  method : checkSelectedCurrencyInGBP
 * @ param  :
 * @ Description : return currency to GBP
 */// ==============================================
function checkSelectedCurrencyInGBP($currency, $amount, $selected = '')
{
    $currency = $currency;
    $selected = 'GBP';

    $data = file_get_contents('https://apilayer.net/api/live?access_key=' . config("custom.currency_converter_access_key") . '&currencies=' . $currency . '&source=' . $selected . '&format=1');
    $dd = json_decode($data);
    $selector = $selected . $currency;

    if (isset($dd->quotes->$selector)) {
        return ['amount' => (float) round(($amount / $dd->quotes->$selector), 2), 'currency' => $selected];
    } else {
        return false;
    }
}

// ================================================
/*  method : checkSelectedCurrencyTwo
 * @ param  :
 * @ Description : return currency to selected or USD currency
 */// ==============================================
function checkSelectedCurrencyTwo($currency, $amount, $selected = 'USD')
{
    if ($currency == $selected) {
        return ['amount' => (float) round($amount, 2), 'currency' => $selected];
    }
    $data = file_get_contents('https://apilayer.net/api/live?access_key=' . config("custom.currency_converter_access_key") . '&currencies=' . $currency . '&source=' . $selected . '&format=1');
    $dd = json_decode($data);
    $selector = $selected . $currency;
    if (isset($dd->quotes->$selector)) {
        return ['amount' => (float) round(($amount / $dd->quotes->$selector), 2), 'currency' => $selected];
    } else {
        return false;
    }
}


function getAllCurrenciesRates()
{
    $response = Http::get('https://apilayer.net/api/live?access_key=' . config("custom.currency_converter_access_key"))->json();
    return $response['quotes'];
}

function getConversionAmount($allRates, $currency, $amount = 0.00)
{
    if ($currency == "USD") {
        return $amount;
    }
    $usdAmt = $allRates['USD' . $currency];
    $convertedAmtInUsd = round($amount * $usdAmt, 2);
    return $convertedAmtInUsd;
}
function getConversionAmountInUsd($allRates, $currency, $amount = 0.00)
{
    if ($currency == "USD") {
        return $amount;
    }
    $usdAmt = $allRates['USD' . $currency];
    $convertedAmtInUsd = round($amount / $usdAmt, 2);
    return $convertedAmtInUsd;
}

function getConversionRate($allRates, $currency, $sourceCurrency = "USD")
{
    $rate = 0;
    if ($allRates) {
        $selector = $sourceCurrency . $currency;
        if (isset($allRates->$selector)) {
            $rate = $allRates->$selector;
        }
    }
    return $rate;
}

function checkIsRecurring($payment_gateway_id)
{
    $check = DB::table('middetails')
        ->whereNull('deleted_at')
        ->where('is_provide_reccuring', '1')
        ->where('id', $payment_gateway_id)
        ->where('id', '<>', 41)
        ->first();

    return $check;
}

// ================================================
/*  method : checkSelectedCurrency
 * @ param  :
 * @ Description : return currency to payment_gateway default
 */// ==============================================
function checkSelectedCurrency($payment_gateway_id, $currency, $amount)
{
    $check = DB::table('middetails')
        ->select('converted_currency')
        ->where('id', $payment_gateway_id)
        ->first();

    // return false
    if ($check == null) {
        return false;
    }

    // if(!in_array($currency, ['USD', 'AUD', 'GBP', 'EUR']) && in_array($payment_gateway_id, ['88', '177', '178', '183', '192'])) {
    //     $check->converted_currency = '1';
    //     $check->converted_currency = 'EUR';
    // }

    // if($currency == 'SEK' && ($check->converted_currency == '' || $check->converted_currency == null)) {
    //     $check->converted_currency = '1';
    //     $check->converted_currency = 'EUR';
    // }

    if ($check->converted_currency != '') {
        $selected = $check->converted_currency;
        $selected = ($selected != '') ? $selected : 'EUR';

        try {
            $data = file_get_contents('https://apilayer.net/api/live?access_key=' . config("custom.currency_converter_access_key") . '&currencies=' . $currency . '&source=' . $selected . '&format=1');
        } catch (\Exception $e) {
            \Log::info([
                'error_type' => 'currency_conversion_apilayer',
                'body' => $e->getMessage()
            ]);

            return false;
        }
        $dd = json_decode($data);
        $selector = $selected . $currency;

        if (isset($dd->quotes->$selector)) {
            return ['amount' => (float) round(($amount / $dd->quotes->$selector), 2), 'currency' => $check->converted_currency];
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// ================================================
/*  method : checkSelectedUsesrCurrency
 * @ param  :
 * @ Description : return currecy to user default currency
 */// ==============================================
function checkSelectedUsesrCurrency($user_id, $currency, $amount)
{
    $check = DB::table('users')
        ->select('currency')
        ->where('id', $user_id)
        ->first();

    if ($check->currency != '') {
        $currency = $currency;
        $selected = $check->currency;

        try {
            $data = file_get_contents('https://apilayer.net/api/live?access_key=' . config("custom.currency_converter_access_key") . '&currencies=' . $currency . '&source=' . $selected . '&format=1');
        } catch (\Exception $e) {
            \Log::info([
                'error_type' => 'currency_conversion_apilayer',
                'body' => $e->getMessage()
            ]);

            return false;
        }

        $dd = json_decode($data);
        $selector = $selected . $currency;

        if (isset($dd->quotes->$selector)) {
            return ['amount' => (float) round(($amount / $dd->quotes->$selector), 2), 'currency' => $check->currency];
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// ================================================
/*  method : convertMonthlyFee
 * @ param  :
 * @ Description : return currency to USD only
 */// ==============================================
function convertMonthlyFee($currency, $amount)
{
    $default_currency = 'USD';
    $selected = $currency;

    $data = file_get_contents('https://apilayer.net/api/live?access_key=' . config("custom.currency_converter_access_key") . '&currencies=' . $currency . '&source=' . $default_currency . '&format=1');
    $dd = json_decode($data);
    $selector = $default_currency . $currency;

    return (float) round(($amount / $dd->quotes->$selector), 2);
}

function checkCreaditCard($card_no)
{
    $data = file_get_contents('https://api.bincodes.com/cc/json/9ea576ebbf57367da279c7d962b5f562/' . $card_no);
    $dd = json_decode($data);
    return $dd;
}

// ================================================
/* method : checkAssignMID
* @param  :
* @Description : get all mid_details
*/// ==============================================
function checkAssignMID($id)
{
    $get_mid = MIDDetail::find($id);

    // if record and table exists
    if ($get_mid != null && Schema::hasTable($get_mid->gateway_table)) {
        // gateway all details
        $mid_details = DB::table('middetails')
            ->select($get_mid->gateway_table . '.*', 'main_gateway.*', 'middetails.*')
            ->join('main_gateway', 'main_gateway.id', 'middetails.main_gateway_mid_id')
            ->join($get_mid->gateway_table, $get_mid->gateway_table . '.id', 'middetails.assign_gateway_mid')
            ->where('middetails.id', $id)
            ->first();

        if ($mid_details != null) {
            return $mid_details;
        }
    }

    return false;
}

function getQuickPayDetails($user_id)
{
    $parent = DB::table('middetails')
        ->select('users.id as userID', 'middetails.assign_gateway_mid')
        ->join('users', 'users.mid', 'middetails.id')
        ->where('users.id', $user_id)
        ->first();

    $data = DB::table('quickpaymids')
        ->where('id', $parent->assign_gateway_mid)
        ->first();

    return $data;
}

function getCompnayName($user_id)
{
    $data = DB::table('merchantapplications')
        ->where('user_id', $user_id)
        ->first();

    return $data->company_name;
}

function getBusinessName($user_id)
{
    $data = DB::table('applications')
        ->where('user_id', $user_id)
        ->first();

    return $data->business_name;
}

function getUserCardOneDayOld($card_no, $userId = false)
{
    if (!$userId) {
        $userId = \Auth::user()->id;
    }
    $getUserCardOneDayOld = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $userId)
        ->where('card_no', $card_no)
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(1)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserCardOneDayOld;
}

function getUserEmailOneDayOld($email, $userId = false)
{
    if (!$userId) {
        $userId = \Auth::user()->id;
    }
    $getUserEmailOneDayOld = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $userId)
        ->where('email', $email)
        ->where('payment_gateway_id', '<>', '41')
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(1)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserEmailOneDayOld;
}

function getUserCardOneWeekOld($card_no, $userId = false)
{
    if (!$userId) {
        $userId = \Auth::user()->id;
    }
    $getUserCardOneWeekOld = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $userId)
        ->where('card_no', $card_no)
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(7)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserCardOneWeekOld;
}

function getUserEmailOneWeekOld($email, $userId = false)
{
    if (!$userId) {
        $userId = \Auth::user()->id;
    }
    $getUserEmailOneWeekOld = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $userId)
        ->where('email', $email)
        ->where('payment_gateway_id', '<>', '41')
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(7)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserEmailOneWeekOld;
}

function getUserCardOneMonthOld($card_no, $userId = false)
{
    if (!$userId) {
        $userId = \Auth::user()->id;
    }
    $getUserCardOneMonthOld = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $userId)
        ->where('card_no', $card_no)
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(30)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserCardOneMonthOld;
}

function getUserEmailOneMonthOld($email, $userId = false)
{
    if (!$userId) {
        $userId = \Auth::user()->id;
    }
    $getUserEmailOneMonthOld = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $userId)
        ->where('email', $email)
        ->where('payment_gateway_id', '<>', '41')
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(30)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserEmailOneMonthOld;
}

function getUserCardOneDayOldAPI($card_no, $user_id)
{
    $getUserCardOneDayOld = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $user_id)
        ->where('card_no', $card_no)
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(1)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserCardOneDayOld;
}

function getUserEmailOneDayOldAPI($email, $user_id)
{
    $getUserEmailOneDayOld = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $user_id)
        ->where('email', $email)
        ->where('payment_gateway_id', '<>', '41')
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(1)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserEmailOneDayOld;
}

function getUserCardOneWeekOldAPI($card_no, $user_id)
{
    $getUserCardOneWeekOldAPI = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $user_id)
        ->where('card_no', $card_no)
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(7)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserCardOneWeekOldAPI;
}

function getUserEmailOneWeekOldAPI($email, $user_id)
{
    $getUserEmailOneWeekOldAPI = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $user_id)
        ->where('email', $email)
        ->where('payment_gateway_id', '<>', '41')
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(7)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserEmailOneWeekOldAPI;
}

function getUserCardOneMonthOldAPI($card_no, $user_id)
{
    $getUserCardOneMonthOldAPI = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $user_id)
        ->where('card_no', $card_no)
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(30)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserCardOneMonthOldAPI;
}

function getUserEmailOneMonthOldAPI($email, $user_id)
{
    $getUserEmailOneMonthOldAPI = DB::table('transactions')
        ->whereNull('deleted_at')
        ->where('user_id', $user_id)
        ->where('email', $email)
        ->where('payment_gateway_id', '<>', '41')
        // ->where('status', '1')
        ->whereBetween('created_at', array(\Carbon\Carbon::now()->subDays(30)->toDateTimeString(), \Carbon\Carbon::now()->toDateTimeString()))
        ->count();

    return $getUserEmailOneMonthOldAPI;
}

function getPaymentInfo($input)
{
    unset($input['payment_gateway_id']);
    unset($input['order_id']);
    unset($input['reason']);
    unset($input['customer_order_id_number']);
    unset($input['status']);
    $input['card_no'] = 'XXXXXXXXXXXX' . substr($input['card_no'], -4);
    $input['ccExpiryMonth'] = '**';
    $input['ccExpiryYear'] = '****';
    $input['cvvNumber'] = '***';
    // $input['customer_order_id_number'] = $input['sulte_apt_no'];s
    return $input;
}

function CardTokenization($cardDetails)
{
    // $url = "https://demo.vivapayments.com/api/cards?key=Dpt6IcggBQztIu7TdlF29GEkaSQgzJ+PWy7EGW18r3U=";
    $url = "https://www.vivapayments.com/api/cards?key=VwT4ennzb1ejc5FBN1jmvgiBVJC9FJmLtiQ6nB8clqg=";
    // $MerchantId = '667e84b8-bbc5-4e5e-a46b-0c3bf142b3aa';
    $MerchantId = '91de837d-4121-4e7d-b385-23e200efd004';
    // $APIKey = "(3JB8d";
    $APIKey = "z=:T)L";
    $data = 'Number=' . $cardDetails['Number'] . '&CVC=' . $cardDetails['CVC'] . '&ExpirationDate=' . $cardDetails['ExpirationDate'] . '&CardHolderName=' . $cardDetails['CardHolderName'] . '';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_TIMEOUT, 90);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . base64_encode($MerchantId . ':' . $APIKey)
        )
    );

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $results = json_decode($response);
    return $results->Token;
    if ($results) {
    } else {
        return false;
    }
}

function generatechecksum($amount, $currency, $order_id)
{
    if (\Session::get('QuickpayData')) {
        \Session::forget('QuickpayData');
    }
    $params = array(
        "version" => "v10",
        "merchant_id" => 56717,
        "agreement_id" => 212274,
        "order_id" => $order_id,
        "amount" => $amount,
        "currency" => $currency,
        "continueurl" => 'http://localhost:8000/continueurl',
        "cancelurl" => 'http://localhost:8000/continueurl',
        "callbackurl" => 'http://localhost:8000/continueurl'
    );

    $params["checksum"] = sign($params, "c10cdc7023b117fc0dcdc4b1607a22d5e231e382a9e0cd0921300c86b85755d6");
    return $params["checksum"];
}

function sign($params, $api_key)
{
    $flattened_params = flatten_params($params);
    ksort($flattened_params);
    $base = implode(" ", $flattened_params);

    return hash_hmac("sha256", $base, $api_key);
}

function flatten_params($obj, $result = array(), $path = array())
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            $result = array_merge($result, flatten_params($v, $result, array_merge($path, array($k))));
        }
    } else {
        $result[implode("", array_map(function ($p) {
            return "[{$p}]";
        }, $path))] = $obj;
    }
    return $result;
}

function convertUSD($USD, $EUR)
{
    $xml = file_get_contents('http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22USDEUR%22,%20%22USDJPY%22,%20%22USDBGN%22,%20%22USDCZK%22,%20%22USDDKK%22,%20%22USDGBP%22,%20%22USDHUF%22,%20%22USDLTL%22,%20%22USDLVL%22,%20%22USDPLN%22,%20%22USDRON%22,%20%22USDSEK%22,%20%22USDCHF%22,%20%22USDNOK%22,%20%22USDHRK%22,%20%22USDRUB%22,%20%22USDTRY%22,%20%22USDAUD%22,%20%22USDBRL%22,%20%22USDCAD%22,%20%22USDCNY%22,%20%22USDHKD%22,%20%22USDIDR%22,%20%22USDILS%22,%20%22USDINR%22,%20%22USDKRW%22,%20%22USDMXN%22,%20%22USDMYR%22,%20%22USDNZD%22,%20%22USDPHP%22,%20%22USDSGD%22,%20%22USDTHB%22,%20%22USDZAR%22,%20%22USDISK%22)&env=store://datatables.org/alltableswithkeys');
    $data = simplexml_load_string($xml);

    $data = json_encode($data->results->rate[0]->Rate);

    $data = json_decode($data, true);

    return number_format(($EUR * 1) / $data[0], 2);
}

function getCountry()
{
    $countries = array(
        "AF" => "Afghanistan",
        "AX" => "Åland Islands",
        "AL" => "Albania",
        "DZ" => "Algeria",
        "AS" => "American Samoa",
        "AD" => "Andorra",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AQ" => "Antarctica",
        "AG" => "Antigua and Barbuda",
        "AR" => "Argentina",
        "AM" => "Armenia",
        "AW" => "Aruba",
        "AU" => "Australia",
        "AT" => "Austria",
        "AZ" => "Azerbaijan",
        "BS" => "Bahamas",
        "BH" => "Bahrain",
        "BD" => "Bangladesh",
        "BB" => "Barbados",
        "BY" => "Belarus",
        "BE" => "Belgium",
        "BZ" => "Belize",
        "BJ" => "Benin",
        "BM" => "Bermuda",
        "BT" => "Bhutan",
        "BO" => "Bolivia",
        "BA" => "Bosnia and Herzegovina",
        "BW" => "Botswana",
        "BV" => "Bouvet Island",
        "BR" => "Brazil",
        "IO" => "British Indian Ocean Territory",
        "BN" => "Brunei Darussalam",
        "BG" => "Bulgaria",
        "BF" => "Burkina Faso",
        "BI" => "Burundi",
        "KH" => "Cambodia",
        "CM" => "Cameroon",
        "CA" => "Canada",
        "CV" => "Cape Verde",
        "KY" => "Cayman Islands",
        "CF" => "Central African Republic",
        "TD" => "Chad",
        "CL" => "Chile",
        "CN" => "China",
        "CX" => "Christmas Island",
        "CC" => "Cocos (Keeling) Islands",
        "CO" => "Colombia",
        "KM" => "Comoros",
        "CG" => "Congo",
        "CD" => "Congo, The Democratic Republic of The",
        "CK" => "Cook Islands",
        "CR" => "Costa Rica",
        "CI" => "Cote D'ivoire",
        "HR" => "Croatia",
        "CU" => "Cuba",
        "CW" => "Curacao",
        "CY" => "Cyprus",
        "CZ" => "Czech Republic",
        "DK" => "Denmark",
        "DJ" => "Djibouti",
        "DM" => "Dominica",
        "DO" => "Dominican Republic",
        "EC" => "Ecuador",
        "EG" => "Egypt",
        "SV" => "El Salvador",
        "GQ" => "Equatorial Guinea",
        "ER" => "Eritrea",
        "EE" => "Estonia",
        "ET" => "Ethiopia",
        "FK" => "Falkland Islands (Malvinas)",
        "FO" => "Faroe Islands",
        "FJ" => "Fiji",
        "FI" => "Finland",
        "FR" => "France",
        "GF" => "French Guiana",
        "PF" => "French Polynesia",
        "TF" => "French Southern Territories",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GE" => "Georgia",
        "DE" => "Germany",
        "GH" => "Ghana",
        "GI" => "Gibraltar",
        "GR" => "Greece",
        "GL" => "Greenland",
        "GD" => "Grenada",
        "GP" => "Guadeloupe",
        "GU" => "Guam",
        "GT" => "Guatemala",
        "GG" => "Guernsey",
        "GN" => "Guinea",
        "GW" => "Guinea-bissau",
        "GY" => "Guyana",
        "HT" => "Haiti",
        "HM" => "Heard Island and Mcdonald Islands",
        "VA" => "Holy See (Vatican City State)",
        "HN" => "Honduras",
        "HK" => "Hong Kong",
        "HU" => "Hungary",
        "IS" => "Iceland",
        "IN" => "India",
        "ID" => "Indonesia",
        "IR" => "Iran, Islamic Republic of",
        "IQ" => "Iraq",
        "IE" => "Ireland",
        "IM" => "Isle of Man",
        "IL" => "Israel",
        "IT" => "Italy",
        "JM" => "Jamaica",
        "JP" => "Japan",
        "JE" => "Jersey",
        "JO" => "Jordan",
        "KZ" => "Kazakhstan",
        "KE" => "Kenya",
        "KI" => "Kiribati",
        "KP" => "Korea, Democratic People's Republic of",
        "KR" => "Korea, Republic of",
        "KW" => "Kuwait",
        "KG" => "Kyrgyzstan",
        "LA" => "Lao People's Democratic Republic",
        "LV" => "Latvia",
        "LB" => "Lebanon",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "Libyan Arab Jamahiriya",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "MO" => "Macao",
        "MK" => "Macedonia, The Former Yugoslav Republic of",
        "MG" => "Madagascar",
        "MW" => "Malawi",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malta",
        "MH" => "Marshall Islands",
        "MQ" => "Martinique",
        "MR" => "Mauritania",
        "MU" => "Mauritius",
        "YT" => "Mayotte",
        "MX" => "Mexico",
        "FM" => "Micronesia, Federated States of",
        "MD" => "Moldova, Republic of",
        "MC" => "Monaco",
        "MN" => "Mongolia",
        "ME" => "Montenegro",
        "MS" => "Montserrat",
        "MA" => "Morocco",
        "MZ" => "Mozambique",
        "MM" => "Myanmar",
        "NA" => "Namibia",
        "NR" => "Nauru",
        "NP" => "Nepal",
        "NL" => "Netherlands",
        "AN" => "Netherlands Antilles",
        "NC" => "New Caledonia",
        "NZ" => "New Zealand",
        "NI" => "Nicaragua",
        "NE" => "Niger",
        "NG" => "Nigeria",
        "NU" => "Niue",
        "NF" => "Norfolk Island",
        "MP" => "Northern Mariana Islands",
        "NO" => "Norway",
        "OM" => "Oman",
        "PK" => "Pakistan",
        "PW" => "Palau",
        "PS" => "Palestinian Territory, Occupied",
        "PA" => "Panama",
        "PG" => "Papua New Guinea",
        "PY" => "Paraguay",
        "PE" => "Peru",
        "PH" => "Philippines",
        "PN" => "Pitcairn",
        "PL" => "Poland",
        "PT" => "Portugal",
        "PR" => "Puerto Rico",
        "QA" => "Qatar",
        "RE" => "Reunion",
        "RO" => "Romania",
        "RU" => "Russian Federation",
        "RW" => "Rwanda",
        "SH" => "Saint Helena",
        "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia",
        "PM" => "Saint Pierre and Miquelon",
        "VC" => "Saint Vincent and The Grenadines",
        "WS" => "Samoa",
        "SM" => "San Marino",
        "ST" => "Sao Tome and Principe",
        "SA" => "Saudi Arabia",
        "SN" => "Senegal",
        "RS" => "Serbia",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SG" => "Singapore",
        "SK" => "Slovakia",
        "SI" => "Slovenia",
        "SB" => "Solomon Islands",
        "SO" => "Somalia",
        "ZA" => "South Africa",
        "GS" => "South Georgia and The South Sandwich Islands",
        "ES" => "Spain",
        "LK" => "Sri Lanka",
        "SD" => "Sudan",
        "SR" => "Suriname",
        "SJ" => "Svalbard and Jan Mayen",
        "SZ" => "Swaziland",
        "SE" => "Sweden",
        "CH" => "Switzerland",
        "SY" => "Syrian Arab Republic",
        "TW" => "Taiwan, Province of China",
        "TJ" => "Tajikistan",
        "TZ" => "Tanzania, United Republic of",
        "TH" => "Thailand",
        "TL" => "Timor-leste",
        "TG" => "Togo",
        "TK" => "Tokelau",
        "TO" => "Tonga",
        "TT" => "Trinidad and Tobago",
        "TN" => "Tunisia",
        "TR" => "Turkey",
        "TM" => "Turkmenistan",
        "TC" => "Turks and Caicos Islands",
        "TV" => "Tuvalu",
        "UG" => "Uganda",
        "UA" => "Ukraine",
        "AE" => "United Arab Emirates",
        "GB" => "United Kingdom",
        "US" => "United States",
        "UM" => "United States Minor Outlying Islands",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan",
        "VU" => "Vanuatu",
        "VE" => "Venezuela",
        "VN" => "Viet Nam",
        "VG" => "Virgin Islands, British",
        "VI" => "Virgin Islands, U.S.",
        "WF" => "Wallis and Futuna",
        "EH" => "Western Sahara",
        "YE" => "Yemen",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe"
    );

    return $countries;
}

function getCountryFullName($data)
{
    $countries = array(
        "AF" => "Afghanistan",
        "AX" => "Åland Islands",
        "AL" => "Albania",
        "DZ" => "Algeria",
        "AS" => "American Samoa",
        "AD" => "Andorra",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AQ" => "Antarctica",
        "AG" => "Antigua and Barbuda",
        "AR" => "Argentina",
        "AM" => "Armenia",
        "AW" => "Aruba",
        "AU" => "Australia",
        "AT" => "Austria",
        "AZ" => "Azerbaijan",
        "BS" => "Bahamas",
        "BH" => "Bahrain",
        "BD" => "Bangladesh",
        "BB" => "Barbados",
        "BY" => "Belarus",
        "BE" => "Belgium",
        "BZ" => "Belize",
        "BJ" => "Benin",
        "BM" => "Bermuda",
        "BT" => "Bhutan",
        "BO" => "Bolivia",
        "BA" => "Bosnia and Herzegovina",
        "BW" => "Botswana",
        "BV" => "Bouvet Island",
        "BR" => "Brazil",
        "IO" => "British Indian Ocean Territory",
        "BN" => "Brunei Darussalam",
        "BG" => "Bulgaria",
        "BF" => "Burkina Faso",
        "BI" => "Burundi",
        "KH" => "Cambodia",
        "CM" => "Cameroon",
        "CA" => "Canada",
        "CV" => "Cape Verde",
        "KY" => "Cayman Islands",
        "CF" => "Central African Republic",
        "TD" => "Chad",
        "CL" => "Chile",
        "CN" => "China",
        "CX" => "Christmas Island",
        "CC" => "Cocos (Keeling) Islands",
        "CO" => "Colombia",
        "KM" => "Comoros",
        "CG" => "Congo",
        "CD" => "Congo, The Democratic Republic of The",
        "CK" => "Cook Islands",
        "CR" => "Costa Rica",
        "CI" => "Cote D'ivoire",
        "HR" => "Croatia",
        "CU" => "Cuba",
        "CW" => "Curacao",
        "CY" => "Cyprus",
        "CZ" => "Czech Republic",
        "DK" => "Denmark",
        "DJ" => "Djibouti",
        "DM" => "Dominica",
        "DO" => "Dominican Republic",
        "EC" => "Ecuador",
        "EG" => "Egypt",
        "SV" => "El Salvador",
        "GQ" => "Equatorial Guinea",
        "ER" => "Eritrea",
        "EE" => "Estonia",
        "ET" => "Ethiopia",
        "FK" => "Falkland Islands (Malvinas)",
        "FO" => "Faroe Islands",
        "FJ" => "Fiji",
        "FI" => "Finland",
        "FR" => "France",
        "GF" => "French Guiana",
        "PF" => "French Polynesia",
        "TF" => "French Southern Territories",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GE" => "Georgia",
        "DE" => "Germany",
        "GH" => "Ghana",
        "GI" => "Gibraltar",
        "GR" => "Greece",
        "GL" => "Greenland",
        "GD" => "Grenada",
        "GP" => "Guadeloupe",
        "GU" => "Guam",
        "GT" => "Guatemala",
        "GG" => "Guernsey",
        "GN" => "Guinea",
        "GW" => "Guinea-bissau",
        "GY" => "Guyana",
        "HT" => "Haiti",
        "HM" => "Heard Island and Mcdonald Islands",
        "VA" => "Holy See (Vatican City State)",
        "HN" => "Honduras",
        "HK" => "Hong Kong",
        "HU" => "Hungary",
        "IS" => "Iceland",
        "IN" => "India",
        "ID" => "Indonesia",
        "IR" => "Iran, Islamic Republic of",
        "IQ" => "Iraq",
        "IE" => "Ireland",
        "IM" => "Isle of Man",
        "IL" => "Israel",
        "IT" => "Italy",
        "JM" => "Jamaica",
        "JP" => "Japan",
        "JE" => "Jersey",
        "JO" => "Jordan",
        "KZ" => "Kazakhstan",
        "KE" => "Kenya",
        "KI" => "Kiribati",
        "KP" => "Korea, Democratic People's Republic of",
        "KR" => "Korea, Republic of",
        "KW" => "Kuwait",
        "KG" => "Kyrgyzstan",
        "LA" => "Lao People's Democratic Republic",
        "LV" => "Latvia",
        "LB" => "Lebanon",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "Libyan Arab Jamahiriya",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "MO" => "Macao",
        "MK" => "Macedonia, The Former Yugoslav Republic of",
        "MG" => "Madagascar",
        "MW" => "Malawi",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malta",
        "MH" => "Marshall Islands",
        "MQ" => "Martinique",
        "MR" => "Mauritania",
        "MU" => "Mauritius",
        "YT" => "Mayotte",
        "MX" => "Mexico",
        "FM" => "Micronesia, Federated States of",
        "MD" => "Moldova, Republic of",
        "MC" => "Monaco",
        "MN" => "Mongolia",
        "ME" => "Montenegro",
        "MS" => "Montserrat",
        "MA" => "Morocco",
        "MZ" => "Mozambique",
        "MM" => "Myanmar",
        "NA" => "Namibia",
        "NR" => "Nauru",
        "NP" => "Nepal",
        "NL" => "Netherlands",
        "AN" => "Netherlands Antilles",
        "NC" => "New Caledonia",
        "NZ" => "New Zealand",
        "NI" => "Nicaragua",
        "NE" => "Niger",
        "NG" => "Nigeria",
        "NU" => "Niue",
        "NF" => "Norfolk Island",
        "MP" => "Northern Mariana Islands",
        "NO" => "Norway",
        "OM" => "Oman",
        "PK" => "Pakistan",
        "PW" => "Palau",
        "PS" => "Palestinian Territory, Occupied",
        "PA" => "Panama",
        "PG" => "Papua New Guinea",
        "PY" => "Paraguay",
        "PE" => "Peru",
        "PH" => "Philippines",
        "PN" => "Pitcairn",
        "PL" => "Poland",
        "PT" => "Portugal",
        "PR" => "Puerto Rico",
        "QA" => "Qatar",
        "RE" => "Reunion",
        "RO" => "Romania",
        "RU" => "Russian Federation",
        "RW" => "Rwanda",
        "SH" => "Saint Helena",
        "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia",
        "PM" => "Saint Pierre and Miquelon",
        "VC" => "Saint Vincent and The Grenadines",
        "WS" => "Samoa",
        "SM" => "San Marino",
        "ST" => "Sao Tome and Principe",
        "SA" => "Saudi Arabia",
        "SN" => "Senegal",
        "RS" => "Serbia",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SG" => "Singapore",
        "SK" => "Slovakia",
        "SI" => "Slovenia",
        "SB" => "Solomon Islands",
        "SO" => "Somalia",
        "ZA" => "South Africa",
        "GS" => "South Georgia and The South Sandwich Islands",
        "ES" => "Spain",
        "LK" => "Sri Lanka",
        "SD" => "Sudan",
        "SR" => "Suriname",
        "SJ" => "Svalbard and Jan Mayen",
        "SZ" => "Swaziland",
        "SE" => "Sweden",
        "CH" => "Switzerland",
        "SY" => "Syrian Arab Republic",
        "TW" => "Taiwan, Province of China",
        "TJ" => "Tajikistan",
        "TZ" => "Tanzania, United Republic of",
        "TH" => "Thailand",
        "TL" => "Timor-leste",
        "TG" => "Togo",
        "TK" => "Tokelau",
        "TO" => "Tonga",
        "TT" => "Trinidad and Tobago",
        "TN" => "Tunisia",
        "TR" => "Turkey",
        "TM" => "Turkmenistan",
        "TC" => "Turks and Caicos Islands",
        "TV" => "Tuvalu",
        "UG" => "Uganda",
        "UA" => "Ukraine",
        "AE" => "United Arab Emirates",
        "GB" => "United Kingdom",
        "US" => "United States",
        "UM" => "United States Minor Outlying Islands",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan",
        "VU" => "Vanuatu",
        "VE" => "Venezuela",
        "VN" => "Viet Nam",
        "VG" => "Virgin Islands, British",
        "VI" => "Virgin Islands, U.S.",
        "WF" => "Wallis and Futuna",
        "EH" => "Western Sahara",
        "YE" => "Yemen",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe",
        "UK" => "United Kingdom",
        "EA" => "Eurasian Patent Organization"
    );

    foreach ($countries as $key => $value) {
        if ($key == $data) {
            return $value;
        }
    }

    return '';
}

function getTheeDigitCountry()
{
    $countries = [
        "AFG" => "Afghanistan",
        "ALA" => "Åland Islands",
        "ALB" => "Albania",
        "DZA" => "Algeria",
        "ASM" => "American Samoa",
        "AND" => "Andorra",
        "AGO" => "Angola",
        "AIA" => "Anguilla",
        "ATA" => "Antarctica",
        "ATG" => "Antigua and Barbuda",
        "ARG" => "Argentina",
        "ARM" => "Armenia",
        "ABW" => "Aruba",
        "AUS" => "Australia",
        "AUT" => "Austria",
        "AZE" => "Azerbaijan",
        "BHS" => "Bahamas",
        "BHR" => "Bahrain",
        "BGD" => "Bangladesh",
        "BRB" => "Barbados",
        "BLR" => "Belarus",
        "BEL" => "Belgium",
        "BLZ" => "Belize",
        "BEN" => "Benin",
        "BMU" => "Bermuda",
        "BTN" => "Bhutan",
        "BOL" => "Bolivia, Plurinational State of",
        "BES" => "Bonaire, Sint Eustatius and Saba",
        "BIH" => "Bosnia and Herzegovina",
        "BWA" => "Botswana",
        "BVT" => "Bouvet Island",
        "BRA" => "Brazil",
        "IOT" => "British Indian Ocean Territory",
        "BRN" => "Brunei Darussalam",
        "BGR" => "Bulgaria",
        "BFA" => "Burkina Faso",
        "BDI" => "Burundi",
        "KHM" => "Cambodia",
        "CMR" => "Cameroon",
        "CAN" => "Canada",
        "CPV" => "Cape Verde",
        "CYM" => "Cayman Islands",
        "CAF" => "Central African Republic",
        "TCD" => "Chad",
        "CHL" => "Chile",
        "CHN" => "China",
        "CXR" => "Christmas Island",
        "CCK" => "Cocos (Keeling) Islands",
        "COL" => "Colombia",
        "COM" => "Comoros",
        "COG" => "Congo",
        "COD" => "Congo, the Democratic Republic of the",
        "COK" => "Cook Islands",
        "CRI" => "Costa Rica",
        "CIV" => "Côte d'Ivoire",
        "HRV" => "Croatia",
        "CUB" => "Cuba",
        "CUW" => "Curaçao",
        "CYP" => "Cyprus",
        "CZE" => "Czech Republic",
        "DNK" => "Denmark",
        "DJI" => "Djibouti",
        "DMA" => "Dominica",
        "DOM" => "Dominican Republic",
        "ECU" => "Ecuador",
        "EGY" => "Egypt",
        "SLV" => "El Salvador",
        "GNQ" => "Equatorial Guinea",
        "ERI" => "Eritrea",
        "EST" => "Estonia",
        "ETH" => "Ethiopia",
        "FLK" => "Falkland Islands (Malvinas)",
        "FRO" => "Faroe Islands",
        "FJI" => "Fiji",
        "FIN" => "Finland",
        "FRA" => "France",
        "GUF" => "French Guiana",
        "PYF" => "French Polynesia",
        "ATF" => "French Southern Territories",
        "GAB" => "Gabon",
        "GMB" => "Gambia",
        "GEO" => "Georgia",
        "DEU" => "Germany",
        "GHA" => "Ghana",
        "GIB" => "Gibraltar",
        "GRC" => "Greece",
        "GRL" => "Greenland",
        "GRD" => "Grenada",
        "GLP" => "Guadeloupe",
        "GUM" => "Guam",
        "GTM" => "Guatemala",
        "GGY" => "Guernsey",
        "GIN" => "Guinea",
        "GNB" => "Guinea-Bissau",
        "GUY" => "Guyana",
        "HTI" => "Haiti",
        "HMD" => "Heard Island and McDonald Islands",
        "VAT" => "Holy See (Vatican City State)",
        "HND" => "Honduras",
        "HKG" => "Hong Kong",
        "HUN" => "Hungary",
        "ISL" => "Iceland",
        "IND" => "India",
        "IDN" => "Indonesia",
        "IRN" => "Iran, Islamic Republic of",
        "IRQ" => "Iraq",
        "IRL" => "Ireland",
        "IMN" => "Isle of Man",
        "ISR" => "Israel",
        "ITA" => "Italy",
        "JAM" => "Jamaica",
        "JPN" => "Japan",
        "JEY" => "Jersey",
        "JOR" => "Jordan",
        "KAZ" => "Kazakhstan",
        "KEN" => "Kenya",
        "KIR" => "Kiribati",
        "PRK" => "Korea, Democratic People's Republic of",
        "KOR" => "Korea, Republic of",
        "KWT" => "Kuwait",
        "KGZ" => "Kyrgyzstan",
        "LAO" => "Lao People's Democratic Republic",
        "LVA" => "Latvia",
        "LBN" => "Lebanon",
        "LSO" => "Lesotho",
        "LBR" => "Liberia",
        "LBY" => "Libya",
        "LIE" => "Liechtenstein",
        "LTU" => "Lithuania",
        "LUX" => "Luxembourg",
        "MAC" => "Macao",
        "MKD" => "Macedonia, the former Yugoslav Republic of",
        "MDG" => "Madagascar",
        "MWI" => "Malawi",
        "MYS" => "Malaysia",
        "MDV" => "Maldives",
        "MLI" => "Mali",
        "MLT" => "Malta",
        "MHL" => "Marshall Islands",
        "MTQ" => "Martinique",
        "MRT" => "Mauritania",
        "MUS" => "Mauritius",
        "MYT" => "Mayotte",
        "MEX" => "Mexico",
        "FSM" => "Micronesia, Federated States of",
        "MDA" => "Moldova, Republic of",
        "MCO" => "Monaco",
        "MNG" => "Mongolia",
        "MNE" => "Montenegro",
        "MSR" => "Montserrat",
        "MAR" => "Morocco",
        "MOZ" => "Mozambique",
        "MMR" => "Myanmar",
        "NAM" => "Namibia",
        "NRU" => "Nauru",
        "NPL" => "Nepal",
        "NLD" => "Netherlands",
        "NCL" => "New Caledonia",
        "NZL" => "New Zealand",
        "NIC" => "Nicaragua",
        "NER" => "Niger",
        "NGA" => "Nigeria",
        "NIU" => "Niue",
        "NFK" => "Norfolk Island",
        "MNP" => "Northern Mariana Islands",
        "NOR" => "Norway",
        "OMN" => "Oman",
        "PAK" => "Pakistan",
        "PLW" => "Palau",
        "PSE" => "Palestinian Territory, Occupied",
        "PAN" => "Panama",
        "PNG" => "Papua New Guinea",
        "PRY" => "Paraguay",
        "PER" => "Peru",
        "PHL" => "Philippines",
        "PCN" => "Pitcairn",
        "POL" => "Poland",
        "PRT" => "Portugal",
        "PRI" => "Puerto Rico",
        "QAT" => "Qatar",
        "REU" => "Réunion",
        "ROU" => "Romania",
        "RUS" => "Russian Federation",
        "RWA" => "Rwanda",
        "BLM" => "Saint Barthélemy",
        "SHN" => "Saint Helena, Ascension and Tristan da Cunha",
        "KNA" => "Saint Kitts and Nevis",
        "LCA" => "Saint Lucia",
        "MAF" => "Saint Martin (French part)",
        "SPM" => "Saint Pierre and Miquelon",
        "VCT" => "Saint Vincent and the Grenadines",
        "WSM" => "Samoa",
        "SMR" => "San Marino",
        "STP" => "Sao Tome and Principe",
        "SAU" => "Saudi Arabia",
        "SEN" => "Senegal",
        "SRB" => "Serbia",
        "SYC" => "Seychelles",
        "SLE" => "Sierra Leone",
        "SGP" => "Singapore",
        "SXM" => "Sint Maarten (Dutch part)",
        "SVK" => "Slovakia",
        "SVN" => "Slovenia",
        "SLB" => "Solomon Islands",
        "SOM" => "Somalia",
        "ZAF" => "South Africa",
        "SGS" => "South Georgia and the South Sandwich Islands",
        "SSD" => "South Sudan",
        "ESP" => "Spain",
        "LKA" => "Sri Lanka",
        "SDN" => "Sudan",
        "SUR" => "Suriname",
        "SJM" => "Svalbard and Jan Mayen",
        "SWZ" => "Swaziland",
        "SWE" => "Sweden",
        "CHE" => "Switzerland",
        "SYR" => "Syrian Arab Republic",
        "TWN" => "Taiwan, Province of China",
        "TJK" => "Tajikistan",
        "TZA" => "Tanzania, United Republic of",
        "THA" => "Thailand",
        "TLS" => "Timor-Leste",
        "TGO" => "Togo",
        "TKL" => "Tokelau",
        "TON" => "Tonga",
        "TTO" => "Trinidad and Tobago",
        "TUN" => "Tunisia",
        "TUR" => "Turkey",
        "TKM" => "Turkmenistan",
        "TCA" => "Turks and Caicos Islands",
        "TUV" => "Tuvalu",
        "UGA" => "Uganda",
        "UKR" => "Ukraine",
        "ARE" => "United Arab Emirates",
        "GBR" => "United Kingdom",
        "USA" => "United States",
        "UMI" => "United States Minor Outlying Islands",
        "URY" => "Uruguay",
        "UZB" => "Uzbekistan",
        "VUT" => "Vanuatu",
        "VEN" => "Venezuela, Bolivarian Republic of",
        "VNM" => "Viet Nam",
        "VGB" => "Virgin Islands, British",
        "VIR" => "Virgin Islands, U.S.",
        "WLF" => "Wallis and Futuna",
        "ESH" => "Western Sahara",
        "YEM" => "Yemen",
        "ZMB" => "Zambia",
        "ZWE" => "Zimbabwe",
    ];

    return $countries;
}

function countryReplace($countrycode)
{
    $countrycode = strtoupper($countrycode);
    $countries = [
        "AF" => "AFG",
        "AX" => "ALA",
        "AL" => "ALA",
        "DZ" => "DZA",
        "AS" => "ASM",
        "AD" => "AND",
        "AO" => "AGO",
        "AI" => "AIA",
        "AQ" => "ATA",
        "AG" => "ATG",
        "AR" => "ARG",
        "AM" => "ARM",
        "AW" => "ABW",
        "AU" => "AUS",
        "AT" => "AUT",
        "AZ" => "AZE",
        "BS" => "BHS",
        "BH" => "BHR",
        "BD" => "BGD",
        "BB" => "BRB",
        "BY" => "BLR",
        "BE" => "BEL",
        "BZ" => "BLZ",
        "BJ" => "BEN",
        "BM" => "BMU",
        "BT" => "BTN",
        "BO" => "BOL",
        "BA" => "BIH",
        "BW" => "BWA",
        "BV" => "BVT",
        "BR" => "BRA",
        "IO" => "IOT",
        "BN" => "BRN",
        "BG" => "BGR",
        "BF" => "BFA",
        "BI" => "BDI",
        "KH" => "KHM",
        "CM" => "CMR",
        "CA" => "CAN",
        "CV" => "CPV",
        "KY" => "CYM",
        "CF" => "CAF",
        "TD" => "TCD",
        "CL" => "CHL",
        "CN" => "CHN",
        "CX" => "CXR",
        "CC" => "CCK",
        "CO" => "COL",
        "KM" => "COM",
        "CG" => "COG",
        "CD" => "COD",
        "CK" => "COK",
        "CR" => "CRI",
        "CI" => "CIV",
        "HR" => "HRV",
        "CU" => "CUB",
        "CY" => "CYP",
        "CZ" => "CZE",
        "DK" => "DNK",
        "DJ" => "DJI",
        "DM" => "DMA",
        "DO" => "DOM",
        "EC" => "ECU",
        "EG" => "EGY",
        "SV" => "SLV",
        "GQ" => "GNQ",
        "ER" => "ERI",
        "EE" => "EST",
        "ET" => "ETH",
        "FK" => "FLK",
        "FO" => "FRO",
        "FJ" => "FJI",
        "FI" => "FIN",
        "FR" => "FRA",
        "GF" => "GUF",
        "PF" => "PYF",
        "TF" => "ATF",
        "GA" => "GAB",
        "GM" => "GMB",
        "GE" => "GEO",
        "DE" => "DEU",
        "GH" => "GHA",
        "GI" => "GIB",
        "GR" => "GRC",
        "GL" => "GRL",
        "GD" => "GRD",
        "GP" => "GLP",
        "GU" => "GUM",
        "GT" => "GTM",
        "GG" => "GGY",
        "GN" => "GIN",
        "GW" => "GNB",
        "GY" => "GUY",
        "HT" => "HTI",
        "HM" => "HMD",
        "VA" => "VAT",
        "HN" => "HND",
        "HK" => "HKG",
        "HU" => "HUN",
        "IS" => "ISL",
        "IN" => "IND",
        "ID" => "IDN",
        "IR" => "IRN",
        "IQ" => "IRQ",
        "IE" => "IRL",
        "IM" => "IMN",
        "IL" => "ISR",
        "IT" => "ITA",
        "JM" => "JAM",
        "JP" => "JPN",
        "JE" => "JEY",
        "JO" => "JOR",
        "KZ" => "KAZ",
        "KE" => "KEN",
        "KI" => "KIR",
        "KP" => "PRK",
        "KR" => "KOR",
        "KW" => "KWT",
        "KG" => "KGZ",
        "LA" => "LAO",
        "LV" => "LVA",
        "LB" => "LBN",
        "LS" => "LSO",
        "LR" => "LBR",
        "LY" => "LBY",
        "LI" => "LIE",
        "LT" => "LTU",
        "LU" => "LUX",
        "MO" => "MAC",
        "MK" => "MKD",
        "MG" => "MDG",
        "MW" => "MWI",
        "MY" => "MYS",
        "MV" => "MDV",
        "ML" => "MLI",
        "MT" => "MLT",
        "MH" => "MHL",
        "MQ" => "MTQ",
        "MR" => "MRT",
        "MU" => "MUS",
        "YT" => "MYT",
        "MX" => "MEX",
        "FM" => "FSM",
        "MD" => "MDA",
        "MC" => "MCO",
        "MN" => "MNG",
        "ME" => "MNE",
        "MS" => "MSR",
        "MA" => "MAR",
        "MZ" => "MOZ",
        "MM" => "MMR",
        "NA" => "NAM",
        "NR" => "NRU",
        "NP" => "NPL",
        "NL" => "NLD",
        "AN" => "ANT",
        "NC" => "NCL",
        "NZ" => "NZL",
        "NI" => "NZL",
        "NE" => "NER",
        "NG" => "NGA",
        "NU" => "NIU",
        "NF" => "NFK",
        "MP" => "MNP",
        "NO" => "NOR",
        "OM" => "OMN",
        "PK" => "PAK",
        "PW" => "PLW",
        "PS" => "PSE",
        "PA" => "PAN",
        "PG" => "PNG",
        "PY" => "PRY",
        "PE" => "PER",
        "PH" => "PHL",
        "PN" => "PCN",
        "PL" => "POL",
        "PT" => "PRT",
        "PR" => "PRI",
        "QA" => "QAT",
        "RE" => "REU",
        "RO" => "ROU",
        "RU" => "RUS",
        "RW" => "RWA",
        "SH" => "SHN",
        "KN" => "KNA",
        "LC" => "LCA",
        "PM" => "SPM",
        "VC" => "VCT",
        "WS" => "WSM",
        "SM" => "SMR",
        "ST" => "STP",
        "SA" => "SAU",
        "SN" => "SEN",
        "RS" => "SRB",
        "SC" => "SYC",
        "SL" => "SLE",
        "SG" => "SGP",
        "SK" => "SVK",
        "SI" => "SVN",
        "SB" => "SLB",
        "SO" => "SOM",
        "ZA" => "ZAF",
        "GS" => "SGS",
        "ES" => "ESP",
        "LK" => "LKA",
        "SD" => "SDN",
        "SR" => "SUR",
        "SJ" => "SJM",
        "SZ" => "SWZ",
        "SE" => "SWE",
        "CH" => "CHE",
        "SY" => "SYR",
        "TW" => "TWN",
        "TJ" => "TJK",
        "TZ" => "TZA",
        "TH" => "THA",
        "TL" => "TLS",
        "TG" => "TGO",
        "TK" => "TKL",
        "TO" => "TON",
        "TT" => "TTO",
        "TN" => "TUN",
        "TR" => "TUR",
        "TM" => "TKM",
        "TC" => "TCA",
        "TV" => "TUV",
        "UG" => "UGA",
        "UA" => "UKR",
        "AE" => "ARE",
        "GB" => "GBR",
        "US" => "USA",
        "UM" => "UMI",
        "UY" => "URY",
        "UZ" => "UZB",
        "VU" => "VUT",
        "VE" => "VEN",
        "VN" => "VNM",
        "VG" => "VGB",
        "VI" => "VIR",
        "WF" => "WLF",
        "EH" => "ESH",
        "YE" => "YEM",
        "ZM" => "ZMB",
        "ZW" => "ZWE",
    ];

    return $countries[$countrycode];
}

function countryReplaceThreeToTwo($countrycode)
{
    $countrycode = strtoupper($countrycode);
    $countries = [
        "AFG" => "AF",
        "ALA" => "AX",
        "ALA" => "AL",
        "DZA" => "DZ",
        "ASM" => "AS",
        "AND" => "AD",
        "AGO" => "AO",
        "AIA" => "AI",
        "ATA" => "AQ",
        "ATG" => "AG",
        "ARG" => "AR",
        "ARM" => "AM",
        "ABW" => "AW",
        "AUS" => "AU",
        "AUT" => "AT",
        "AZE" => "AZ",
        "BHS" => "BS",
        "BHR" => "BH",
        "BGD" => "BD",
        "BRB" => "BB",
        "BLR" => "BY",
        "BEL" => "BE",
        "BLZ" => "BZ",
        "BEN" => "BJ",
        "BMU" => "BM",
        "BTN" => "BT",
        "BOL" => "BO",
        "BIH" => "BA",
        "BWA" => "BW",
        "BVT" => "BV",
        "BRA" => "BR",
        "IOT" => "IO",
        "BRN" => "BN",
        "BGR" => "BG",
        "BFA" => "BF",
        "BDI" => "BI",
        "KHM" => "KH",
        "CMR" => "CM",
        "CAN" => "CA",
        "CPV" => "CV",
        "CYM" => "KY",
        "CAF" => "CF",
        "TCD" => "TD",
        "CHL" => "CL",
        "CHN" => "CN",
        "CXR" => "CX",
        "CCK" => "CC",
        "COL" => "CO",
        "COM" => "KM",
        "COG" => "CG",
        "COD" => "CD",
        "COK" => "CK",
        "CRI" => "CR",
        "CIV" => "CI",
        "HRV" => "HR",
        "CUB" => "CU",
        "CYP" => "CY",
        "CZE" => "CZ",
        "DNK" => "DK",
        "DJI" => "DJ",
        "DMA" => "DM",
        "DOM" => "DO",
        "ECU" => "EC",
        "EGY" => "EG",
        "SLV" => "SV",
        "GNQ" => "GQ",
        "ERI" => "ER",
        "EST" => "EE",
        "ETH" => "ET",
        "FLK" => "FK",
        "FRO" => "FO",
        "FJI" => "FJ",
        "FIN" => "FI",
        "FRA" => "FR",
        "GUF" => "GF",
        "PYF" => "PF",
        "ATF" => "TF",
        "GAB" => "GA",
        "GMB" => "GM",
        "GEO" => "GE",
        "DEU" => "DE",
        "GHA" => "GH",
        "GIB" => "GI",
        "GRC" => "GR",
        "GRL" => "GL",
        "GRD" => "GD",
        "GLP" => "GP",
        "GUM" => "GU",
        "GTM" => "GT",
        "GGY" => "GG",
        "GIN" => "GN",
        "GNB" => "GW",
        "GUY" => "GY",
        "HTI" => "HT",
        "HMD" => "HM",
        "VAT" => "VA",
        "HND" => "HN",
        "HKG" => "HK",
        "HUN" => "HU",
        "ISL" => "IS",
        "IND" => "IN",
        "IDN" => "ID",
        "IRN" => "IR",
        "IRQ" => "IQ",
        "IRL" => "IE",
        "IMN" => "IM",
        "ISR" => "IL",
        "ITA" => "IT",
        "JAM" => "JM",
        "JPN" => "JP",
        "JEY" => "JE",
        "JOR" => "JO",
        "KAZ" => "KZ",
        "KEN" => "KE",
        "KIR" => "KI",
        "PRK" => "KP",
        "KOR" => "KR",
        "KWT" => "KW",
        "KGZ" => "KG",
        "LAO" => "LA",
        "LVA" => "LV",
        "LBN" => "LB",
        "LSO" => "LS",
        "LBR" => "LR",
        "LBY" => "LY",
        "LIE" => "LI",
        "LTU" => "LT",
        "LUX" => "LU",
        "MAC" => "MO",
        "MKD" => "MK",
        "MDG" => "MG",
        "MWI" => "MW",
        "MYS" => "MY",
        "MDV" => "MV",
        "MLI" => "ML",
        "MLT" => "MT",
        "MHL" => "MH",
        "MTQ" => "MQ",
        "MRT" => "MR",
        "MUS" => "MU",
        "MYT" => "YT",
        "MEX" => "MX",
        "FSM" => "FM",
        "MDA" => "MD",
        "MCO" => "MC",
        "MNG" => "MN",
        "MNE" => "ME",
        "MSR" => "MS",
        "MAR" => "MA",
        "MOZ" => "MZ",
        "MMR" => "MM",
        "NAM" => "NA",
        "NRU" => "NR",
        "NPL" => "NP",
        "NLD" => "NL",
        "ANT" => "AN",
        "NCL" => "NC",
        "NZL" => "NZ",
        "NZL" => "NI",
        "NER" => "NE",
        "NGA" => "NG",
        "NIU" => "NU",
        "NFK" => "NF",
        "MNP" => "MP",
        "NOR" => "NO",
        "OMN" => "OM",
        "PAK" => "PK",
        "PLW" => "PW",
        "PSE" => "PS",
        "PAN" => "PA",
        "PNG" => "PG",
        "PRY" => "PY",
        "PER" => "PE",
        "PHL" => "PH",
        "PCN" => "PN",
        "POL" => "PL",
        "PRT" => "PT",
        "PRI" => "PR",
        "QAT" => "QA",
        "REU" => "RE",
        "ROU" => "RO",
        "RUS" => "RU",
        "RWA" => "RW",
        "SHN" => "SH",
        "KNA" => "KN",
        "LCA" => "LC",
        "SPM" => "PM",
        "VCT" => "VC",
        "WSM" => "WS",
        "SMR" => "SM",
        "STP" => "ST",
        "SAU" => "SA",
        "SEN" => "SN",
        "SRB" => "RS",
        "SYC" => "SC",
        "SLE" => "SL",
        "SGP" => "SG",
        "SVK" => "SK",
        "SVN" => "SI",
        "SLB" => "SB",
        "SOM" => "SO",
        "ZAF" => "ZA",
        "SGS" => "GS",
        "ESP" => "ES",
        "LKA" => "LK",
        "SDN" => "SD",
        "SUR" => "SR",
        "SJM" => "SJ",
        "SWZ" => "SZ",
        "SWE" => "SE",
        "CHE" => "CH",
        "SYR" => "SY",
        "TWN" => "TW",
        "TJK" => "TJ",
        "TZA" => "TZ",
        "THA" => "TH",
        "TLS" => "TL",
        "TGO" => "TG",
        "TKL" => "TK",
        "TON" => "TO",
        "TTO" => "TT",
        "TUN" => "TN",
        "TUR" => "TR",
        "TKM" => "TM",
        "TCA" => "TC",
        "TUV" => "TV",
        "UGA" => "UG",
        "UKR" => "UA",
        "ARE" => "AE",
        "GBR" => "GB",
        "USA" => "US",
        "UMI" => "UM",
        "URY" => "UY",
        "UZB" => "UZ",
        "VUT" => "VU",
        "VEN" => "VE",
        "VNM" => "VN",
        "VGB" => "VG",
        "VIR" => "VI",
        "WLF" => "WF",
        "ESH" => "EH",
        "YEM" => "YE",
        "ZMB" => "ZM",
        "ZWE" => "ZW",
    ];

    return $countries[$countrycode];
}

function getCurrency()
{
    $getCurrency = array(
        "USD" => "USD",
        "HKD" => "HKD",
        "GBP" => "GBP",
        "JPY" => "JPY",
        "EUR" => "EUR",
        "AUD" => "AUD",
        "CAD" => "CAD",
        "SGD" => "SGD",
        "NZD" => "NZD",
        "TWD" => "TWD",
        "KRW" => "KRW",
        "DKK" => "DKK",
        "TRL" => "TRL",
        "MYR" => "MYR",
        "THB" => "THB",
        "INR" => "INR",
        "PHP" => "PHP",
        "CHF" => "CHF",
        "SEK" => "SEK",
        "NGN" => "NGN",
        "ILS" => "ILS",
        "ZAR" => "ZAR",
        "RUB" => "RUB",
        "NOK" => "NOK",
        "AED" => "AED",
        "BRL" => "BRL",
    );
    return $getCurrency;
}

// ================================================
/* method  : asianPaymentGatewayResponseCode
 * @ param  :
 * @ Description : get error response for asian payment gateway
 */// ==============================================
function asianPaymentGatewayResponseCode($codename)
{
    $response_code = [
        'C0001' => 'payment domain name are required or not enabled',
        'C0003' => 'unbound MarsterCard issuer',
        'C0004' => 'unbound Visa card issuer',
        'C0005' => 'Gateway number or gateway payment url are required',
        'C0006' => 'Gateway haven’t been opened',
        'C0015' => 'currency type of bound issuer are required',
        'C0016' => 'Gateway temporarily not available',
        'C0017' => 'Currency are required',
        'C0021' => 'Language is not 2, or credit card payment haven\'t been enabled',
        'E0001' => 'Query merchant credit card activation info error',
        'E0002' => 'Query issuer information error',
        'E0003' => 'query error or payment domain name haven\'t bound',
        'E0004' => 'order can not be repeated submission',
        'E0005' => 'Source URL limitation error',
        'E0006' => 'Query issuer error',
        'E0007' => 'Card number encryption error',
        'I0001' => 'Merchant Number are required',
        'I0002' => 'MD5Key are required',
        'I0003' => 'Merchant Number does not exist',
        'I0004' => 'Amount are required',
        'I0005' => 'Currency are required,fixed value',
        'I0006' => 'Turn IP Address are Required',
        'I0007' => 'MD5 encryption are required',
        'I0008' => 'Amount error (Every number is required to hold only 2 digits after the decimal point.)',
        'I0009' => 'Language value error',
        'I0010' => 'Amount error',
        'I0011' => 'firstName are requied',
        'I0012' => 'lastName are requied',
        'I0013' => 'Invalid card',
        'I0014' => 'CVV are required',
        'I0015' => 'valid month are required',
        'I0016' => 'valid year are required',
        'I0017' => 'Issuer are required',
        'I0018' => 'email wrong-filled',
        'I0019' => 'phone number are required',
        'I0020' => 'post code are required',
        'I0021' => 'address are required',
        'I0022' => 'city are required',
        'I0023' => 'country are required',
        'I0031' => 'Amount more than 1 must use https protocols',
        'I0032' => 'parameter length more than 1024 or parameter contains #',
        'I0033' => 'Verification code error (selecttobank error)',
        'I0084' => 'Baseinfo parameter error',
        'I0085' => 'MD5 test error (payment domain name page verification)',
        'I0088' => 'Transaction currency error',
        'I0090' => 'MD5 test error',
        'I0092' => 'the order of additional parameters can not be disrupted.',
        'R0001' => 'Merchant IP blocked',
        'R0002' => 'Repeated Payment (Merchant Number + Merchant order number + payment status = succeed)',
        'R0003' => 'Source URL limitation',
        'R0004' => 'maximum limit (for each transaction)',
        'R0005' => 'no maximum limit (for each transaction)',
        'R0006' => 'No maximum limit (for daily transaction)',
        'R0007' => 'No maximum limit (for monthly transaction)',
        'R0008' => 'maximum limit (for daily transaction)',
        'R0009' => 'maximum limit (for weekly transaction)',
        'R0010' => 'maximum limit (for monthly transaction)',
        'R0011' => 'Card bin blocked',
        'R0012' => 'Black list website blocked',
        'R0013' => 'violate limitation rules',
        'R0016' => 'Maxmind high risk',
        'R0017' => 'Maxmind high risk',
        'R0018' => 'country of issure limited',
        'R0022' => 'blocked by Maxmind risk rules',
        'R0030' => 'Repeated Payment (selecttobank page)',
        'R0031' => 'Black list website limited',
        'U0001' => 'The URL is irregular and is not allowed to be inserted',
        'U0002' => 'merchant account in test status, payment amount can not be more than 1',
        'U0019' => 'Repeated Payment',
        'N0001' => 'Visa card is not open',
        'N0002' => 'Master card is not open',
        'N0003' => 'JCB card is not open',
        'N0004' => 'AE card is not open',
        'N0005' => 'DC card is not open',
        'N0006' => 'Merchant Number is empty',
        'N0007' => 'Merchant Number input error',
        'N0008' => 'Interface additional elements are not complete',
        'N0009' => 'cvv2 cannot be empty',
        'N0010' => 'cvv2 error',
        'N0011' => 'FirstName fills in the error',
        'N0012' => 'lastName fills in the error',
        'N0013' => 'Phone input error',
        'N0014' => 'Zip cannot be empty',
        'N0015' => 'Address cannot be empty',
        'N0016' => 'IssuingBank cannot be empty',
        'N0017' => 'cardExpireMonth fills in the error',
        'N0018' => 'cardExpireYear fills in the error',
        'N0019' => 'IP is empty',
        'N0020' => 'Channel value is incorrect',
        'N0021' => 'Currency cannot be empty',
        'N0022' => 'You are currently a test account and the amount of payment cannot exceed 1',
        'N0023' => 'Country, ip address is limited',
        'N0024' => 'National issuing bank is limited',
        'N0025' => 'Blacklist state',
        'N0026' => 'IP acquisition is empty',
        'N0027' => 'Did not get the merchant ip',
        'N0028' => 'The merchant has not opened the three-way straight connection',
        'N0029' => 'Merchant payment channel does not have a currency established',
        'N0030' => 'Channel currency is not open',
        'N0031' => 'Channel is not open',
        'N0032' => 'Query exchange rate error',
        'N0033' => 'Merchant payment times are limited',
        'N0034' => 'Not white list card number',
        'N0035' => 'Payment is blocked due to potential risks',
        'N0036' => 'High risk trading',
        'N0037' => 'Busy network',
        'N0038' => '4009-System anomaly',
        'N0039' => 'Currency conversion error',
        'N0040' => 'Data update failed (system exception)',
        'N0041' => 'Payment authorization failed',
        'N0042' => 'Payment failed',
        'N0043' => 'ET_GOODS is empty',
        'N0044' => 'Source URL collection is empty',
        'N0045' => 'The merchant opened the callback but did not pass the asynchronous notification address.',
        'N0046' => 'Blacklist country',
        'N0047' => 'The payment amount has reached the channel limit',
        'N0048' => 'Payment amount reached the channel daily limit',
        'N0049' => 'The payment amount reached the channel weekly limit',
        'N0050' => 'The payment amount has reached the limit of 2 weeks per channel',
        'N0051' => 'The payment amount has reached the limit of 3 weeks per channel',
        'N0052' => 'The payment amount reached the channel monthly limit',
        'N0053' => 'DIC card is not open',
        'N0054' => 'Channel not configured (amount limit) or channel used',
        'N0055' => 'The transaction amount cannot be less than 1 USD',
    ];

    if (array_key_exists($codename, $response_code)) {
        return $response_code[$codename];
    } else {
        return null;
    }
}

// ================================================
/* method  : asianPaymentGatewayTestResponse
 * @ param  :
 * @ Description : asian payment gateway test response
 */// ==============================================
function asianPaymentGatewayTestResponse($codename)
{
    $response_code = [
        '0000' => 'Payment successful',
        '1111' => 'Payment failure',
        '0001' => 'Merchant order number are required',
        '0002' => 'Amount are required',
        '0003' => 'SHA256Info Encryption information are required',
        '0004' => 'Email error ',
        '0005' => 'cvv2 are required',
        '0006' => 'cvv2 error',
        '0007' => 'firstName are required',
        '0008' => 'firstName wrong-filled',
        '0009' => 'lastName are required',
        '0010' => 'lastName wrong-filled',
        '0011' => 'City are required ',
        '0012' => 'Country are required ',
        '0013' => 'Zip are required',
        '0014' => 'address are required ',
        '0015' => 'issuingBank are required',
        '0016' => 'Credit card info wrong-filled',
        '0017' => 'cardExpireMonth are required ',
        '0018' => 'cardExpireMonth wrong-filled ',
        '0019' => 'cardExpireYear are required ',
        '0020' => 'cardExpireYear wrong-filled',
        '0021' => 'Merchant Number are required ',
        '0022' => 'IP haven’t been captured',
        '0023' => 'Currency are required ',
        '0024' => 'The amount of input format is not correct',
        '0025' => 'Transaction amount exceeds the limit',
        '0026' => 'Credit card number wrong-filled',
        '0027' => 'IP inputed is not legitimate',
        '0028' => 'Phone are required',
        '0029' => 'phone wrong-filled',
        '0030' => 'Country of issuing bank wrong-filled',
        '1001' => 'SHA256Info Encryption test error',
        '1002' => 'Source URL collection error',
        '1003' => 'Merchant is limited (Country, state, IP, etc.)',
        '1004' => 'Amount error (Every number is required to hold only 2 digits after the decimal point.)',
        '1005' => 'Payment amount must be positive number and decimal point, can not contain other characters',
        '1006' => 'Your current account for the test, the amount of payment can not be more than 1',
        '1007' => 'The order number exceeds the number of transactions.',
        '1008' => 'The order number is repeated within the specified time.',
        '1009' => 'The gateway are not open',
        '1010' => 'The merchant havent open the direct interface',
        '1011' => 'Website is limited for credit card merchant',
        '1013' => 'Merchant payment times are limited',
        '1014' => 'High risk card',
        '1015' => 'National Issuing Bank Limited',
        '1016' => 'Gateway value is not correct',
        '1017' => 'Error merchant IP captured ',
        '1018' => 'Merchant IP blocked',
        '1019' => 'Payment currency is not correct',
        '2001' => 'No maximum limit (for each transaction)',
        '2002' => 'No maximum limit (for daily transaction)',
        '2003' => 'No maximum limit (for monthly transaction)',
        '2004' => 'Payment amount reached a limit (for each transaction)',
        '2005' => 'Payment amount reached a limit (for daily transaction) ',
        '2006' => 'Payment amount reached a limit (for monthly transaction)',
        '2007' => 'Merchant transaction amount error',
        '2008' => 'Temporarily unavailable payment service',
        '2009' => 'There is no maximum amount limit (weekly trading)',
        '2010' => 'Payment amount reached the limit (weekly transaction)',
        '2011' => 'The payment amount has reached the channel limit',
        '2012' => 'Payment amount reached the channel daily limit',
        '2013' => 'The payment amount reached the channel weekly limit',
        '2014' => 'The payment amount has reached the limit of 2 weeks per channel',
        '2015' => 'The payment amount has reached the limit of 3 weeks per channel',
        '2016' => 'The payment amount reached the channel monthly limit',
        '2017' => 'Channel not configured (automatic route - amount) or channel used',
        '2018' => 'The transaction amount cannot be less than 1 USD',
        '3001' => 'Gateway temporarily not available',
        '3002' => 'The merchant payment gateway did not establish currency ',
        '3003' => 'Mastercard payment service are temporarily not available, welcome to use Visa payment',
        '3004' => 'Visa payment service are temporarily not available, welcome to use Mastercard payment ',
        '3005' => 'Gateway address error',
        '3006' => 'Gateway haven’t been opened',
        '3007' => 'Query exchange rate error',
        '3008' => 'Card number encryption error',
        '3009' => 'Due to potential risks, payment is blocked',
        '3010' => 'Network is busy',
        '3011' => 'Payment submission error',
        '3012' => 'Connection timed out',
        '3013' => 'Payment amount may be tampered',
        '3014' => 'Bankid is not match',
        '3015' => 'Gateway is not match',
        '3016' => 'Abnormal payment',
        '3017' => 'Currency of gateway haven’t been opened',
        '3018' => 'Payment success, but failed to send a message to the cardholder',
        '3019' => 'merNo is not correct ',
        '3020' => 'Business card blocking',
        '3021' => 'Url is empty',
        '3022' => 'High risk trading',
        '3023' => 'The URL is not allowed',
        '3024' => 'Not white list card number',
        '3025' => 'Fraudulent transactions',
    ];

    if (array_key_exists($codename, $response_code)) {
        return $response_code[$codename];
    } else {
        return null;
    }
}

// ================================================
/* method  : getRequestOnlyField
 * @ param  :
 * @ Description : get request only field
 */// ==============================================
function getRequestOnlyField()
{
    return [
        'api_key',
        'user_id',
        'order_id',
        'product_id',
        'first_name',
        'last_name',
        'address',
        'sulte_apt_no',
        'country',
        'state',
        'city',
        'zip',
        'ip_address',
        'birth_date',
        'email',
        'phone_no',
        'card_type',
        'amount',
        'currency',
        'card_no',
        'ccExpiryMonth',
        'ccExpiryYear',
        'cvvNumber',
        'is_recurring',
        'is_reccuring_date',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_address',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_zip',
        'shipping_email',
        'shipping_phone_no',
        'payment_gateway_id',
        'resubmit_transaction',
        'descriptor',
        'is_converted',
        'converted_amount',
        'converted_currency',
        'is_converted_user_currency',
        'converted_user_amount',
        'converted_user_currency',
        'is_batch_transaction',
        'batch_transaction_counter',
        'old_order_no',
        'website_url_id',
        'request_from_ip',
        'request_origin',
        'is_request_from_vt',
        'response_url',
        'redirect_url_success',
        'redirect_url_fail',
        'is_2ds',
    ];
}

function getSenderUser($id, $table)
{
    if ($table == 'admin') {
        return Admin::where('id', $id)->first();
    } elseif ($table == 'user') {
        return User::where('id', $id)->first();
    }
}

function addToLog($subject, $queryRequest, $queryType, $userID = 0)
{
    if ($queryRequest != null) {
        $queryRequest_json = json_encode($queryRequest);
    }

    $log = [];
    $log['subject'] = $subject;
    if (isset($queryRequest_json)) {
        $log['query_request'] = $queryRequest_json;
    } else {
        $log['query_request'] = json_encode($queryRequest);
    }
    $log['query_type'] = $queryType;
    if ($queryRequest != null) {
        if (array_key_exists('transaction_id', $queryRequest)) {
            $log['transaction_id'] = $queryRequest['transaction_id'];
        } else {
            $log['transaction_id'] = null;
        }
    } else {
        $log['transaction_id'] = null;
    }
    $log['url'] = request()->fullUrl();
    $log['method'] = request()->method();
    $log['ip'] = request()->ip();
    $log['agent'] = request()->header('user-agent');
    $log['user_id'] = auth()->check() ? auth()->user()->id : $userID;
    LogActivity::create($log);
}


function getTutorialCategory()
{
    return ['1' => 'Technical', '2' => 'Finance', '3' => 'General'];
}

// ================================================
/*  method : integerNearBy
 * @ param  :
 * @ Description : get nearby integer value to modBase i.e. 12 => 10
 */// ==============================================
function integerNearBy($value, int $modBase = 5)
{
    // round the value to the nearest
    $roundedValue = round($value);

    // count the number of digits before the dot
    $count = strlen((int) str_replace('.', '', $roundedValue));

    // remove 3 to get how many zeros to add the mod base
    $numberOfZeros = $count - 3;

    // add the zeros to the mod base
    $mod = str_pad($modBase, $numberOfZeros + 1, '0', STR_PAD_RIGHT);

    // do the magic
    return $roundedValue - ($roundedValue % $mod);
}

// ================================================
/*  method : sendFirebaseNotification
 * @ param  :
 * @ Description : send notification in firebase and bell icon
 */// ==============================================
function sendFirebaseNotification($primary_array, $secondary_array)
{
    // save to database and receive to user side
    $notification = Notification::insert([
        'user_id' => $primary_array['user_id'],
        'sendor_id' => $primary_array['sendor_id'],
        'type' => $primary_array['type'],
        'title' => $primary_array['title'],
        'body' => $primary_array['body'],
        'url' => $secondary_array['click_action'],
        'is_read' => 0,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    // get device token
    $device_token = FirebaseDeviceToken::where('type', $primary_array['type'])
        ->where('user_id', $primary_array['user_id'])
        ->value('token');

    $data = [
        'to' => $device_token,
        'notification' => [
            'title' => $primary_array['title'],
            'body' => $primary_array['body'],
            'icon' => url('imgs/bell-192x192.png'),
            'sound' => url('sound/message.mp3')
        ],
        'data' => [
            'click_action' => $secondary_array['click_action']
        ]
    ];

    $url = config('services.firebase.notification_url');
    $server_key = config('services.firebase.server_key');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_TIMEOUT, 90);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Authorization: key=' . $server_key
        )
    );

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $results = json_decode($response);
    if ($results) {
        return response()->json(['success' => true]);
    } else {
        return false;
    }
}

// ================================================
/*  method : saveToFirebaseDatabase
 * @ param  :
 * @ Description : send realtime notification
 */// ==============================================
function saveToFirebaseDatabase($primary_array, $secondary_array = [])
{
    // create data
    $data = [
        'user_id' => $primary_array['user_id'],
        'sendor_id' => $primary_array['sendor_id'],
        'type' => $primary_array['type'],
        'title' => $primary_array['title'],
        'body' => $primary_array['body'],
        'click_action' => $secondary_array['click_action'],
        'is_read' => 0,
        'created_at' => date('Y-m-d H:i:s'),
        'user_and_type' => $primary_array['type'] . '_' . $primary_array['user_id'],
    ];

    // get device token
    $device_token = FirebaseDeviceToken::where('type', $primary_array['type'])
        ->where('user_id', $primary_array['user_id'])
        ->value('token');

    $url = config('services.firebase.url');
    $server_key = config('services.firebase.server_key');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_TIMEOUT, 90);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Authorization: key=' . $server_key
        )
    );

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $results = json_decode($response);

    if ($results) {
        return response()->json(['success' => true]);
    } else {
        return false;
    }
}

// ================================================
/*  method : addNotification
 * @ param  :
 * @ Description : add notifications for user and admin
 */// ==============================================
function addNotification($notification)
{
    $notifications = Notification::create($notification);

    return $notifications;
}

// ================================================
/*  method : getNotifications
 * @ param  :
 * @ Description : get notifications for user and admin
 */// ==============================================
function getNotifications($user_id, $type, $limit = 5)
{
    $notifications = Notification::where('user_id', $user_id)
        ->where('is_read', 0)
        ->where('type', $type)
        ->limit($limit)
        ->orderBy('created_at', 'DESC')
        ->get();

    return $notifications;
}

function getNotificationsForAdmin()
{
    $notifications = Notification::where('user_id', '1')
        ->where('is_read', 0)
        ->where('type', 'admin')
        ->limit(5)
        ->orderBy('created_at', 'DESC')
        ->get();

    return $notifications;
}

function getNotificationsForRP($user_id, $type, $limit = 5)
{
    $notifications = Notification::where('user_id', $user_id)
        ->where('is_read', 0)
        ->where('type', 'RP')
        ->limit(5)
        ->orderBy('created_at', 'DESC')
        ->get();

    return $notifications;
}

// ================================================
/* method  : pdump
 * @ param  :
 * @ Description : print response
 */// ==============================================
function pdump()
{
    [$callee] = debug_backtrace();
    $arguments = $callee['args'];
    $total_arguments = count($arguments);

    echo '<fieldset style="background: #fefefe !important; border:2px red solid; padding:5px">';
    echo '<legend style="background:lightgrey; padding:5px;">' . $callee['file'] . ' @ line: ' . $callee['line'] . '</legend><pre>';

    $i = 0;
    foreach ($arguments as $argument) {
        echo '<br/><strong>Debug #' . (++$i) . ' of ' . $total_arguments . '</strong>: ';
        print_r($argument);
    }

    echo "</pre>";
    echo "</fieldset>";
}

// ================================================
/* method  : getWondoerlandId
 * @ param  :
 * @ Description : get wondorland id for full day
 */// ==============================================
function getWondoerlandId($today = null)
{
    if (!isset($today)) {
        $today = date('d');
    }

    $remainder = $today % 3;
    return $remainder + 2 < 5 ? $remainder + 2 : 2;
}

// ASIA PAYMENT GATEWAY
function get_server_domain()
{
    $domain = 'http';
    if ($_SERVER["HTTPS"] == "on")
        $domain .= "s";
    $domain .= "://";
    if ($_SERVER["SERVER_PORT"] != "80")
        $domain .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
    else
        $domain .= $_SERVER["SERVER_NAME"];

    return $domain;
}

function get_client_ip()
{
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos)
            unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else
        $ip = "Unknow";

    return $ip;
}

// ================================================
/* method  : raveFlutterwaveGetKey
 * @ param  :
 * @ Description : rave Flutterwave API encrypt secret key
 */// ==============================================
function raveFlutterwaveGetKey($seckey)
{
    $hashedkey = md5($seckey);
    $hashedkeylast12 = substr($hashedkey, -12);

    $seckeyadjusted = str_replace("FLWSECK-", "", $seckey);
    $seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 12);

    $encryptionkey = $seckeyadjustedfirst12 . $hashedkeylast12;

    return $encryptionkey;
}

// ================================================
/* method  : raveFlutterwaveEncrypt3Des
 * @ param  :
 * @ Description : rave Flutterwave API encrypt data
 */// ==============================================
function raveFlutterwaveEncrypt3Des($data, $key)
{
    $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);

    return base64_encode($encData);
}

function print_data($data, $isDie = true)
{
    echo '<pre>';
    print_r($data);
    if ($isDie) {
        die;
    }
}

/**
 * The method currency_convert_into_usd is used for converting any currency into USD.
 *
 * @param [string] $currency [currency which should be converted into USD]
 * @param [int] $amount [amount which should be converted accoring to real time USD exchange price]
 *
 * @return [int] $converted_currency [returns the converted currency into USD]
 */
function currency_convert_into_usd($currency, $amount)
{

    /**
     * @var [string] $apiCC [currency converter API]
     */
    $apiCC = 'https://apilayer.net/api/live?access_key=' . config("custom.currency_converter_access_key") . '&currencies=USD&source=' . $currency . '&format=1';

    /**
     * @var [string] $response_data [get API JSON response]
     */
    try {
        $response_data = file_get_contents($apiCC);
    } catch (\Exception $e) {
        return false;
    }

    // Convert response json data to array format.
    $response_data = json_decode($response_data, true);

    /**
     * @var [int] $converted_currency [contains the converted currency value]
     */
    $converted_currency = 0;

    // If response data is not blank
    if (!empty($response_data)) {

        // If real time converted currency is available.
        if (isset($response_data['quotes'][$currency . 'USD']) && $response_data['quotes'][$currency . 'USD']) {

            // Convert the any currency into USD.
            $converted_currency = $response_data['quotes'][$currency . 'USD'] * $amount;
        }
    }

    return $converted_currency;
}

function checkCountryIsBanForFlutterwave($country)
{
    $countryList = [
        'AF',
    ];

    if (in_array($country, $countryList)) {
        return true;
    } else {
        return false;
    }
}

// ================================================
/* method : postCurlRequest
* @param  :
* @Description : create curl request
*/// ==============================================
function postCurlRequestBackUpOne(string $url, array $params)
{
    $post_string = json_encode($params);

    $parts = parse_url($url);
    $fp = @fsockopen(
        $parts['host'],
        isset($parts['port']) ? $parts['port'] : 80,
        $errno,
        $errstr,
        30
    );

    // check connection.
    if ($fp === false) {
        return 'FAILED';
    }

    $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
    $out .= "Host: " . $parts['host'] . "\r\n";
    $out .= "Content-Type: application/json\r\n";
    $out .= "Content-Length: " . strlen($post_string) . "\r\n";
    $out .= "Connection: Close\r\n\r\n";
    if (isset($post_string))
        $out .= $post_string;

    fwrite($fp, $out);
    print_r(fgets($fp));
    fclose($fp);
    exit();
    return 'SUCCESS';
}

function postCurlRequestBackUpTwo(string $url, array $input)
{
    $json_array = json_encode($input);
    $curl = curl_init();
    $headers = ['Content-Type: application/json'];

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_array);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        \Log::info([
            'webhook_url_error' => $error_msg
        ]);
    }

    curl_close($curl);

    if ($http_code >= 200 && $http_code < 300) {
        return 'SUCCESS';
    } else {
        \Log::info([
            'webhook_http_code' => $http_code
        ]);
        return 'FAILED';
    }
}

function postCurlRequest(string $url, array $post_array, $check_ssl = true)
{
    $cmd = "curl -L -X POST -H 'Content-Type: application/json'";
    $cmd .= " -d '" . json_encode($post_array) . "' '" . $url . "'";

    if (!$check_ssl) {
        $cmd .= "'  --insecure"; // this can speed things up, though it's not secure
    }
    $cmd .= " > /dev/null 2>&1 &"; // don't wait for response

    exec($cmd, $output, $exit);
    return $exit == 0;
}

function get_guard()
{
    if (Auth::guard('admin')->check()) {
        return "admin";
    } elseif (Auth::guard('web')->check()) {
        return "web";
    }
}


function getSuporter()
{
    $supporter = array(
        "amount" => "Amount",
        "currency" => "Currency",
        "category" => "Industry",
        "country" => "Country",
        "bin_cou_code" => "BIN Country",
        "bin_number" => "BIN Number",
        "card_type" => "Card Type",
        "card_wl" => "Card WL/FT",
        "user" => "User"
    );

    return $supporter;
}

function getwallet()
{
    $wallets = Wallet::get();

    return $wallets;
}

function getCardType()
{
    $cardType = array(
        "1" => "AMEX",
        "2" => "VISA",
        "3" => "MASTERCARD",
        "4" => "DISCOVER",
        "5" => "JCB",
        "6" => "MESTRO",
        "7" => "SWITCH",
        "8" => "SOLO",
        "9" => "UNIONPAY",
    );
    return $cardType;
}

function getAmountInUsd($amount, $currency)
{
    if ($currency == 'USD') {
        return $amount;
    }

    $getCurrencyRate = DB::table('currency_rate')
        ->where('currency', $currency)
        ->first();

    if ($getCurrencyRate) {
        return ($amount / $getCurrencyRate->converted_amount);
    } else {
        return null;
    }
}

function getS3Url($file)
{
    if (empty($file)) {
        return null;
    }

    $client = Storage::disk('s3')->getDriver()->getAdapter()->getClient();
    $command = $client->getCommand('GetObject', [
        'Bucket' => env('AWS_BUCKET'),
        'Key' => $file
    ]);
    $request = $client->createPresignedRequest($command, '+20 minutes');

    return (string) $request->getUri();
}

// function getS3Url($file)
// {
//     if (empty($file)) {
//         return null;
//     }
//     $client = new Aws\S3\S3Client([
//         'version' => 'latest',
//         'region'  => config('app.DO_DEFAULT_REGION'),
//         'endpoint' => config('app.DO_ENDPOINT'),
//         'use_path_style_endpoint' => false,
//         'credentials' => [
//             'key'    => config('app.DO_ACCESS_KEY_ID'),
//             'secret' => config('app.DO_SECRET_ACCESS_KEY'),
//         ],
//     ]);

//     //$client = Storage::disk('s3')->getClient();
//     $command = $client->getCommand('GetObject', [
//         'Bucket' => config('app.DO_BUCKET'),
//         'Key' => $file
//     ]);
//     $request = $client->createPresignedRequest($command, '+20 minutes');

//     return (string) $request->getUri();
// }

function kebabToHumanString($str)
{
    return ucwords(str_replace('-', ' ', $str));
}


function convertDateToLocal($date, $format = 'Y-m-d H:i:s')
{
    if (!empty(\Session::get('localtimezone'))) {
        $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone(\Session::get('localtimezone')));

        return $date->format($format);
    }

    return date($format, strtotime($date));
}

if (!function_exists('storage_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function storage_asset($path, $secure = null)
    {
        return app('url')->asset('storage/' . $path, $secure);
    }
}

function getFieldsType()
{
    return [
        "string" => "String",
        "numeric" => "Amount",
        "email" => "Email"
    ];
}

function curlPost($url, $data = null, $headers = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);

    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
}


function addAdminLog($action_id, $Actionvalue = null, $queryRequest = array(), $remark = '')
{
    $log = [];
    $log['admin_id'] = auth()->guard('admin')->check() ? auth()->guard('admin')->user()->id : 0;
    $log['action_id'] = $action_id;
    $log['actionvalue'] = $Actionvalue;
    if (!empty($queryRequest)) {
        $log['request'] = json_encode($queryRequest);
    }
    $log['ip'] = request()->ip();
    $log['remark'] = $remark;
    AdminLog::create($log);
}

function prd($data = array())
{
    echo '<pre>';
    print_r($data);
    exit();
}

function _WriteLogsInFile($message = null, $file_names = null)
{
    $Current_Date = date("Y-m-d");
    if (!empty($file_names)) {
        $file_name = storage_path() . "/logs/tmp/" . $file_names . "_" . $Current_Date . ".log";
    } else {
        $file_name = storage_path() . "/logs/tmp/Custom_Log_" . $Current_Date . ".log";
    }
    if (is_array($message)) {
        $Write_message = print_r($message, true);
    } else {
        $Write_message = $message . "\r\n " . PHP_EOL;
    }

    if (file_exists($file_name)) {
        file_put_contents($file_name, $Write_message, FILE_APPEND);
    } else {
        fopen($file_name, 'w');
        file_put_contents($file_name, $Write_message, FILE_APPEND);
    }
}

function getAdminName($id)
{
    $data = DB::table('admins')
        ->select('name')
        ->where('id', $id)
        ->first();

    return $data->name;
}

function getBankName($id)
{
    $data = DB::table('banks')
        ->select('bank_name')
        ->where('id', $id)
        ->first();

    return $data->bank_name;
}

function getBankCompanyName($id)
{
    $data = DB::table('banks')
        ->select('bank_applications.company_name as bank_name')
        ->join('bank_applications', 'bank_applications.bank_id', 'banks.id')
        ->where('banks.id', $id)
        ->first();

    return $data->bank_name;
}

function getWLAgentName($id)
{
    $data = DB::table('wl_agents')
        ->select('name')
        ->where('id', $id)
        ->first();

    return $data->name;
}

function getSentBank($id)
{
    $data = DB::table('application_assign_to_bank')
        ->select("banks.bank_name", "bank_applications.company_name as bankCompanyName", "application_assign_to_bank.*")
        ->join("banks", "banks.id", "=", "application_assign_to_bank.bank_user_id")
        ->join('bank_applications', 'bank_applications.bank_id', 'banks.id')
        ->where('application_assign_to_bank.application_id', $id)
        ->where('application_assign_to_bank.deleted_at', null)
        ->get();

    return $data;
}

function getAdditionalFlaggedEmail($userId)
{
    $user_mail = User::where('id', $userId)
        ->value('additional_mail');

    if ($user_mail != null) {
        return $user_mail;
    } else {
        return;
    }
}

function getAdditionalMerchantEmail($userId)
{
    $user_mail = User::where('id', $userId)
        ->value('additional_merchant_transaction_notification');

    if ($user_mail != null) {
        return json_decode($user_mail, 1);
    } else {
        return;
    }
}

function bankApplicationStatus($bank_id)
{
    $application = DB::table('bank_applications')->whereNull('deleted_at')->where('bank_id', $bank_id)->first();
    if ($application) {
        return $application->status;
    }
    return null;
}

function RpApplicationStatus($agent_id)
{
    $application = DB::table('rp_applications')->whereNull('deleted_at')->where('agent_id', $agent_id)->first();
    if ($application) {
        return $application->status;
    }
    return null;
}

function idEncode($value)
{
    if (!$value) {
        return false;
    }

    $secret_key = 'codenest@PAYMET03022016';
    $secret_iv = 'ems@best00key!!';

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = safeb64Encode(openssl_encrypt($value, $encrypt_method, $key, 0, $iv));

    return $output;
}

function idDecode($value)
{
    if (!$value) {
        return false;
    }

    $secret_key = 'codenest@PAYMET03022016';
    $secret_iv = 'ems@best00key!!';

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_decrypt(safeb64Decode($value), $encrypt_method, $key, 0, $iv);

    return $output;
}

/**
 * safeb64Encode()
 * This function is used to encode into base64.
 *
 * @param : $string : String which you wan to encode.
 * @return string
 */
function safeb64Encode($string)
{
    $data = base64_encode($string);
    $data = str_replace([
        '+',
        '/',
        '='
    ], [
        '-',
        '_',
        ''
    ], $data);

    return $data;
}

/**
 * safeb64Decode()
 * This function is used to decode b64 safely.
 *
 * @param : $string String which you want to decode
 * @return string decode code.
 */
function safeb64Decode($string)
{
    $data = str_replace([
        '-',
        '_'
    ], [
        '+',
        '/'
    ], $string);

    $mod4 = strlen($data) % 4;

    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

function getClientIP()
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

function check_alpha_numeric_string($value)
{
    return preg_match('/^[a-z\d\-_\s\.]+$/i', $value);
}

function check_address_string($value)
{
    return preg_match('/^[a-z\d\-_\s\.\,]+$/i', $value);
}

function getApplicationStatus($status)
{
    switch ($status) {
        case 1:
            return "In Progress";
        case 2:
            return "Incomplete";
        case 3:
            return "Rejected";
        case 4:
            return "Pre Approval";
        case 5:
            return "Agreement Sent";
        case 6:
            return "Agreement Received";
        case 7:
            return "Not Interested";
        case 8:
            return "Terminated";
        case 9:
            return "Decline";
        case 10:
            return "Rate Accepted";
        case 11:
            return "Signed Agreement";
        case 12:
            return "Save Draft";
        default:
            return;
    }
}


/**
 *
 *  Getting name from ids E.g. ["1", "2"] => ["API", "Others"]
 *  couldn't join table above because 'technology_partner_id' is in array
 */
function getTechnologyPartnerNames($technologyPartnerIds)
{
    $technologyPartnerNames = [];
    foreach ($technologyPartnerIds as $id) {
        $getNameFromId = getTechnologyPartnerName($id);
        array_push($technologyPartnerNames, $getNameFromId);
    }

    return json_encode($technologyPartnerNames);
}


function getLicenseStatus($licenseSatus): string
{
    if ($licenseSatus == 0)
        return "Licensed";
    if ($licenseSatus == "1")
        return "Unlicensed";
    if ($licenseSatus == 2)
        return "NA";

    return "---";
}

function industryTypeName($applicationId): string
{
    $data = DB::table('applications')
        ->select('category_id', 'other_industry_type')
        ->where('id', $applicationId)
        ->first();
    $categoryName = getCategoryName($data->category_id);
    if ($categoryName != 'Miscellaneous') {
        return $categoryName;
    }

    if ($data->other_industry_type != null) {
        return $data->other_industry_type;
    }

    return "---";
}

// RemainingAmount means Last paypout date plus one day to currenct day
function checkRemainingAmount($user_id, $start_date, $end_date)
{
    $start_date = date('Y-m-d', strtotime($start_date . ' + 1 days'));
    $end_date = date('Y-m-d');
    $approved_transaction = DB::table('transactions')->select(DB::raw('SUM(amount_in_usd) as amount_in_usd'))
        ->where('user_id', $user_id)
        ->where('status', '1')
        ->where("deleted_at", NULL)
        ->whereNotIn('payment_gateway_id', ['1', '2'])
        ->whereDate('created_at', '>=', $start_date)
        ->whereDate('created_at', '<=', $end_date)
        ->first();
    $totalAmount = $approved_transaction->amount_in_usd;
    if ($approved_transaction->amount_in_usd == Null) {
        $totalAmount = 0;
    }
    return $totalAmount . ' USD';
}


function checkLastTransactionDateForMerchant($user_id)
{
    $transaction = DB::table('transactions')->select('created_at')
        ->where('user_id', $user_id)
        ->where('status', '1')
        ->where("deleted_at", NULL)
        ->whereNotIn('payment_gateway_id', ['1', '2'])
        ->orderBy('created_at', 'DESC')
        ->first();
    if (!empty($transaction)) {
        return date('d-m-Y', strtotime($transaction->created_at));
    } else {
        return 'N/A';
    }
}

function insertLog($filename = 'user', $array = [])
{
    config(['logging.channels.user.path' => storage_path('logs/' . $filename . '.log')]);
    \Log::channel('user')->info($array);
}

// * Return the APM type
function getAPMType($type)
{
    switch ($type) {
        case '1':
            return "Card";
        case '2':
            return "Bank";
        case "3":
            return "Crypto";
        case "4":
            return "UPI";
        default:
            return "Card";
    }
}

// * Update the CRON transaction response 
function updateGatewayData($input, $response_data, $transactionId)
{

    // update transaction_session record
    // $session_update_data = DB::table("transaction_session")->select("request_data")->where('transaction_id', $transactionId)
    //     ->first();

    // $session_request_data = json_decode($session_update_data->request_data, 1);

    // $session_request_data['gateway_id'] = $input['gateway_id'];

    $input['gateway_id'] = $input['gateway_id'] ?? '1';
    DB::table("transaction_session")->where('transaction_id', $transactionId)->update([
        'gateway_id' => $input['gateway_id'],
        'response_data' => json_encode($response_data)
    ]);
}

// * card Masking method
function cardMasking(string $cardNo): string
{
    return substr($cardNo, 0, 6) . 'XXXXXX' . substr($cardNo, -4);
}

// * Get 3 Digit country codes
function getCountryCode(string $code): string
{
    $countryCodes = config('countryCodes.countryCodes');
    if (array_key_exists($code, $countryCodes)) {
        return $countryCodes[$code];
    } else {
        return "AUS";
    }
}

// * Generate random dob
function generateRandomDob($format = "Y-m-d"): string
{
    $minAge = 22;
    $maxAge = 65;
    $randomDate = Carbon::now()
        ->subYears(rand($minAge, $maxAge))
        ->subDays(rand(0, 365))
        ->format($format);
    return $randomDate;
}

// * Get country phone code 
function getPhoneCode(string $countryCode): string
{
    $phoneCodes = config('countryCodes.phoneCodes');
    if (array_key_exists($countryCode, $phoneCodes)) {
        return $phoneCodes[$countryCode];
    } else {
        return "+61";
    }
}

function getXmlComponent($xmlstring, $field)
{
    try {
        // Load XML
        $xml = new SimpleXMLElement($xmlstring);
        // Register namespaces if any
        $namespaces = $xml->getNamespaces(true);
        foreach ($namespaces as $prefix => $namespace) {
            if ($prefix === '') {
                $prefix = 'default';
            }
            $xml->registerXPathNamespace($prefix, $namespace);
        }
        // Construct the correct XPath query
        $result = null;
        if (!empty($namespaces)) {
            foreach ($namespaces as $prefix => $namespace) {
                $prefix = $prefix ?: 'default';
                $result = $xml->xpath("//$prefix:$field");
                if ($result) {
                    break;
                }
            }
        } else {
            $result = $xml->xpath("//$field");
        }
        // Check if the element was found
        if ($result && count($result) > 0) {
            return (string)$result[0];
        } else {
            echo "Element $field not found.\n";
            return "";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return "";
    }
}

function encryptData($data, $public_xml)
{
    try {
        if (!$data) {
            throw new Exception("Data sent for encryption is empty");
        }

        // Decode the Base64 string
        $decoded_bytes = base64_decode($public_xml);
        $decoded_string = mb_convert_encoding($decoded_bytes, 'UTF-8');
        $public_xml_key = explode('!', $decoded_string)[1];

        // echo $public_xml_key;

        $modulus = getXmlComponent($public_xml_key, "Modulus");
        $exponent = getXmlComponent($public_xml_key, "Exponent");

        $modulus_bytes = base64_decode($modulus);
        $exponent_bytes = base64_decode($exponent);

        // Create an RSA public key from Modulus and Exponent
        $rsa = RSA::loadFormat('raw', [
            'n' => new BigInteger($modulus_bytes, 256),
            'e' => new BigInteger($exponent_bytes, 256)
        ]);

        // echo "Trying to encrypt data" . "\n";

        // Encrypt data
        $rsa = $rsa->withPadding(RSA::ENCRYPTION_PKCS1);
        $encrypted = $rsa->encrypt($data);

        // Convert to base 64 string
        $encrypted_base64 = base64_encode($encrypted);
        // echo "base 64 value" . "\n";
        // echo $encrypted_base64 . "\n";

        return $encrypted_base64;
    } catch (Exception $e) {
        // echo "Error: " . $e->getMessage() . "\n";
        return "No encrypted ata";
    }
}