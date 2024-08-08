<?php

namespace App\Transformers;

use App\PaymentAPI;
use Illuminate\Http\Request;

class ApiResponse
{
    // ============================================= 
    /* menthod : success
    * @param  : 
    * @Description : success json response
    */// ==============================================
    public static function success($input = [])
    {
        $request = \Request::all();

        $output = [
            'status' => "Success",
            'message' => $input['reason'],
            'data' => self::getTransactionDetails($input)
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => $input['is_request_from_vt'] ?? null,
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => $input['reason'] ?? 'Success',
        ];

        PaymentAPI::create($api_log);

        return response()->json($output);
    }

    // ================================================
    /* method : redirect
    * @param  : 
    * @description : json response for redirect
    */// ===============================================
    public static function redirect($input = [])
    {
        $request = \Request::all();

        $output = [
            'status' => "Redirect",
            'message' => $input['reason'] ?? "Please redirect to the specified 'payment_link' to complete the transaction processing.",
            'payment_link' => $input['payment_link'],
            'data' => self::getTransactionDetails($input)
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => $input['is_request_from_vt'] ?? null,
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => $input['reason'] ?? 'Success',
        ];

        PaymentAPI::create($api_log);

        return response()->json($output);
    }

    // =============================================
    /* menthod : fail
    * @param  : 
    * @Description : fail json response
    */// ==============================================
    public static function fail($input = [])
    {
        $request = \Request::all();

        $output = [
            'status' => "Failed",
            'message' => $input['reason'],
            'data' => self::getTransactionDetails($input)
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => $input['is_request_from_vt'] ?? null,
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => $input['reason'] ?? 'Success',
        ];

        PaymentAPI::create($api_log);

        return response()->json($output);
    }

    // ============================================= 
    /* menthod : pending
    * @param  : 
    * @Description : pending json response
    */// ==============================================
    public static function pending($input = [])
    {
        $request = \Request::all();

        $output = [
            'status' => "Pending",
            'message' => $input['reason'],
            'data' => self::getTransactionDetails($input)
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => $input['is_request_from_vt'] ?? null,
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => $input['reason'] ?? 'Success',
        ];

        PaymentAPI::create($api_log);

        return response()->json($output);
    }

    // ================================================
    /* method : blocked
    * @param  : 
    * @description : json response for blocked request
    */// ===============================================
    public static function blocked($input = [])
    {
        $request = \Request::all();

        $output = [
            'status' => 'Blocked',
            'message' => $input['reason'],
            'data' => self::getTransactionDetails($input)
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => $input['is_request_from_vt'] ?? null,
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => $input['reason'] ?? 'Unauthorized',
        ];

        PaymentAPI::create($api_log);

        return response()->json($output);
    }

    // ================================================
    /* method : unauthorised
    * @param  : 
    * @description : json response for unauthorised or invalid request
    */// ===============================================
    public static function unauthorised($input = [])
    {
        $request = \Request::all();
        
        $output = [
            'status' => "Unauthorized",
            'message' => $input['reason']
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => $input['is_request_from_vt'] ?? null,
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => $input['reason'] ?? 'Unauthorized',
        ];

        PaymentAPI::create($api_log);
        return response()->json($output);
    }

    // ================================================
    /* method : status
    * @param  : 
    * @description : json response for status api
    */// ===============================================
    public static function status($input = [])
    {
        $request = \Request::all();
        if($input["status"] == 1){
            $status = "Success";
        }elseif($input["status"] == 0){
            $status = "Failed";
        }elseif($input["status"] == 5){
            $status = "Blocked";
        }elseif($input["status"] == 6){
            $status = "Unauthorized";
        }elseif($input["status"] == 7){
            $status = "Redirect";
        }else{
            $status = "Pending";
        }
        $output = [
            'status' => $status,
            'message' => $input['reason'],
            'data' => self::getTransactionDetails($input)
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => 'status',
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => 'Get transaction detail successfully',
        ];

        PaymentAPI::create($api_log);

        return response()->json($output);
    }

    public static function statusTransactions($input = [])
    {
        $request = \Request::all();
        if($input["status"] == 1){
            $status = "Success";
        }elseif($input["status"] == 0){
            $status = "Failed";
        }elseif($input["status"] == 5){
            $status = "Blocked";
        }elseif($input["status"] == 6){
            $status = "Unauthorized";
        }elseif($input["status"] == 7){
            $status = "Redirect";
        }else{
            $status = "Pending";
        }
        $output = [
            'status' => $status,
            'message' => $input['reason'],
            'data' => self::getTransactionDetailsOriginal($input)
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => 'status',
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => 'Get transaction detail successfully',
        ];

        PaymentAPI::create($api_log);

        return response()->json($output);
    }

    // ================================================
    /* method : webhook
    * @param  : 
    * @description : json response for webhook api
    */// ===============================================
    public static function webhook($input = [])
    {
        $request = \Request::all();
        if($input["status"] == 1){
            $status = "Success";
        }elseif($input["status"] == 0){
            $status = "Failed";
        }elseif($input["status"] == 5){
            $status = "Blocked";
        }elseif($input["status"] == 6){
            $status = "Unauthorized";
        }elseif($input["status"] == 7){
            $status = "Redirect";
        }else{
            $status = "Pending";
        }
        \Log::info(["webhook input" => $input]);
        $output = [
            'status' => (string) $status,
            'message' => $input['reason'],
            'data' => self::getTransactionDetailsOriginal($input)
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => 'webhook',
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => 'Webhook sent successfully',
        ];

        PaymentAPI::create($api_log);

        return $output;
    }

    // ================================================
    /* method : returnUrl
    * @param  : 
    * @description : response for return_url
    */// ===============================================
    public static function returnUrl($input = [])
    {
        $request = \Request::all();
        $order_id = $input['order_id'] ?? null;
        $customer_order_id = $input['user_order_ref'] ?? null;
        if($input["status"] == 1){
            $status = "Success";
        }elseif($input["status"] == 0){
            $status = "Failed";
        }elseif($input["status"] == 5){
            $status = "Blocked";
        }elseif($input["status"] == 6){
            $status = "Unauthorized";
        }elseif($input["status"] == 7){
            $status = "Redirect";
        }else{
            $status = "Pending";
        }
        $output = 'status_code=' . $status . '&message=' . $input['reason'] . '&user_order_ref=' . $customer_order_id. '&order_id=' . $order_id;
        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => 'return',
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => $output,
            'message' => 'return_url sent successfully',
        ];

        PaymentAPI::create($api_log);

        if (parse_url($input['user_redirect_url'], PHP_URL_QUERY)) {
            return $input['user_redirect_url'] . '&' . $output;
        } else {
            return $input['user_redirect_url'] . '?' . $output;
        }
    }

    public static function returnUrlTransaction($input = [])
    {
        $request = \Request::all();
        $order_id = $input['order_id'] ?? null;
        $customer_order_id = $input['customer_order_id'] ?? null;
        if($input["status"] == 1){
            $status = "Success";
        }elseif($input["status"] == 0){
            $status = "Failed";
        }elseif($input["status"] == 5){
            $status = "Blocked";
        }elseif($input["status"] == 6){
            $status = "Unauthorized";
        }elseif($input["status"] == 7){
            $status = "Redirect";
        }else{
            $status = "Pending";
        }
        $output = 'status_code=' . $status . '&message=' . urlencode($input['reason']) . '&user_order_ref=' . $customer_order_id. '&order_id=' . $order_id;
        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => 'return',
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => $output,
            'message' => 'return_url sent successfully',
        ];

        PaymentAPI::create($api_log);

        if (parse_url($input['response_url'], PHP_URL_QUERY)) {
            return $input['response_url'] . '&' . $output;
        } else {
            return $input['response_url'] . '?' . $output;
        }
    }

    // ================================================
    /* method : notFound
    * @param  : 
    * @description : json response for not found status api
    */// ===============================================
    public static function notFound($input = [])
    {
        $request = \Request::all();
        if($input["status"] == 1){
            $status = "Success";
        }elseif($input["status"] == 0){
            $status = "Failed";
        }elseif($input["status"] == 5){
            $status = "Blocked";
        }elseif($input["status"] == 6){
            $status = "Unauthorized";
        }elseif($input["status"] == 7){
            $status = "Redirect";
        }else{
            $status = "Pending";
        }
        $output = [
            'status' => (string) $status,
            'message' => $input['reason'],
        ];

        // log into database
        $api_log = [
            'user_id' => $input['user_id'] ?? null,
            'order_id' => $input['order_id'] ?? null,
            'session_id' => $input['session_id'] ?? null,
            'email' => $input['email'] ?? null,
            'type' => 'status',
            'type' => $input['is_request_from_vt'] ?? null,
            'method' => \Request::url(),
            'request' => json_encode($request),
            'ip' => \Request::ip(),
            'response' => json_encode($output),
            'message' => $input['reason'],
        ];

        PaymentAPI::create($api_log);

        return response()->json($output);
    }

    // ================================================
    /* method : getTransactionDetails
    * @param  : 
    * @description : get array of data which we are required
    */// ===============================================
    public static function getTransactionDetails($input)
    {
        $orderDetails = [
            'order_id' => $input['order_id'] ?? null,
            'transaction_ref' => $input['user_order_ref'] ?? null,
            'user_amount' => $input['user_amount'] ?? null,
            'user_currency' => $input['user_currency'] ?? null
        ];
        $userDetails = [
            'user_first_name' => $input['user_first_name'] ?? null,
            'user_last_name' => $input['user_last_name'] ?? null,
            'user_email' => $input['user_email'] ?? null,
            'user_phone_no' => $input['user_phone_no'] ?? null,
            'user_address' => $input['user_address'] ?? null,
            'user_zip' => $input['user_zip'] ?? null,
            'user_city' => $input['user_city'] ?? null,
            'user_state' => $input['user_state'] ?? null,
            'user_country' => $input['user_country'] ?? null
        ];
        $data = ["orderDetails" => $orderDetails,"clientDetails" => $userDetails];
        return $data;
        // return [
        //     'order_id' => $input['order_id'] ?? null,
        //     'transaction_ref' => $input['user_order_ref'] ?? null,
        //     'user_amount' => $input['user_amount'] ?? null,
        //     'user_currency' => $input['user_currency'] ?? null,
        //     'user_first_name' => $input['user_first_name'] ?? null,
        //     'user_last_name' => $input['user_last_name'] ?? null,
        //     'user_email' => $input['user_email'] ?? null,
        //     'user_phone_no' => $input['user_phone_no'] ?? null,
        //     'user_address' => $input['user_address'] ?? null,
        //     'user_zip' => $input['user_zip'] ?? null,
        //     'user_city' => $input['user_city'] ?? null,
        //     'user_state' => $input['user_state'] ?? null,
        //     'user_country' => $input['user_country'] ?? null,
            
        //     // 'card' => [
        //     //     'card_no' => isset($input['card_no']) && !empty($input['card_no']) ? cardMasking($input["card_no"]) : null,
        //     //     'ccExpiryMonth' => $input['ccExpiryMonth'] ?? null,
        //     //     'ccExpiryYear' => $input['ccExpiryYear'] ?? null,
        //     //     'cvvNumber' => $input['cvvNumber'] ?? null,
        //     // ],
        // ];
    }

    public static function getTransactionDetailsOriginal($input)
    {
        $orderDetails = [
            'order_id' => $input['order_id'] ?? null,
            'transaction_ref' => $input['customer_order_id'] ?? null,
            'user_amount' => $input['amount'] ?? null,
            'user_currency' => $input['currency'] ?? null
        ];
        $userDetails = [
            'user_first_name' => $input['first_name'] ?? null,
            'user_last_name' => $input['last_name'] ?? null,
            'user_email' => $input['email'] ?? null,
            'user_phone_no' => $input['phone_no'] ?? null,
            'user_address' => $input['address'] ?? null,
            'user_zip' => $input['zip'] ?? null,
            'user_city' => $input['city'] ?? null,
            'user_state' => $input['state'] ?? null,
            'user_country' => $input['country'] ?? null
        ];
        $data = ["orderDetails" => $orderDetails,"clientDetails" => $userDetails];
        return $data;
        // return [
        //     'order_id' => $input['order_id'] ?? null,
        //     'transaction_ref' => $input['customer_order_id'] ?? null,
        //     'user_amount' => $input['amount'] ?? null,
        //     'user_currency' => $input['currency'] ?? null,
        //     'user_first_name' => $input['first_name'] ?? null,
        //     'user_last_name' => $input['last_name'] ?? null,
        //     'user_email' => $input['email'] ?? null,
        //     'user_phone_no' => $input['phone_no'] ?? null,
        //     'user_address' => $input['address'] ?? null,
        //     'user_zip' => $input['zip'] ?? null,
        //     'user_city' => $input['city'] ?? null,
        //     'user_state' => $input['state'] ?? null,
        //     'user_country' => $input['country'] ?? null,
            
        //     // 'card' => [
        //     //     'card_no' => isset($input['card_no']) && !empty($input['card_no']) ? cardMasking($input["card_no"]) : null,
        //     //     'ccExpiryMonth' => $input['ccExpiryMonth'] ?? null,
        //     //     'ccExpiryYear' => $input['ccExpiryYear'] ?? null,
        //     //     'cvvNumber' => $input['cvvNumber'] ?? null,
        //     // ],
        // ];
    }
}
