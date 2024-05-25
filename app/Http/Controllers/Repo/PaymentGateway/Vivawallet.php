<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use URL;
use View;
use Input;
use Session;
use Redirect;
use Validator;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;

class Vivawallet extends Controller {

    use StoreTransaction;
    //const BASE_URL = 'https://www.vivapayments.com/api/orders'; // live
    const BASE_URL = 'https://demo.vivapayments.com/api/orders'; // test

    public function checkout($input, $check_assign_mid)
    {
        $merchant_id = $check_assign_mid->merchant_id;
        $api_key = $check_assign_mid->api_key;
        $merchant_id = "1dab64b9-cb19-49c6-ade1-aa32d201f51e";
        $api_key = ":fB[_A";
        $amount = $input["converted_amount"];
        $allow_recurring = 'true';
        $request_lang = 'en-US';
        $source = 'Default';
        $postargs = 'Amount='.urlencode($amount).'&AllowRecurring='.$allow_recurring.'&RequestLang='.$request_lang.'&SourceCode='.$source."&RedirectUrl=https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475&FailUrl=".route("vivawallet.redirect",$input['session_id']);
        $request_url = self::BASE_URL;
        $session = curl_init($request_url);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
        curl_setopt($session, CURLOPT_HEADER, true);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_USERPWD, $merchant_id.':'.$api_key);
        curl_setopt($session, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
        $response = curl_exec($session);
        $header_len = curl_getinfo($session, CURLINFO_HEADER_SIZE);
        $res_header = substr($response, 0, $header_len);
        $res_body =  substr($response, $header_len);
        curl_close($session);
        try {
            if(is_object(json_decode($res_body))){
                $result_obj=json_decode($res_body,true);
                //$url = "https://demo.vivapayments.com/web/checkout?ref=".$result_obj["OrderCode"];
                $url = "https://www.vivapayments.com/web/checkout?ref=".$result_obj["OrderCode"];
                $input["gateway_id"] = $result_obj["OrderCode"];
                $this->updateGatewayResponseData($input, $result_obj);
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'redirect_3ds_url' => $url,
                ];
            }else{
                preg_match('#^HTTP/1.(?:0|1) [\d]{3} (.*)$#m', $res_header, $match);
                throw new \Exception("API Call failed! The error was: ".trim($match[1]));
            }
        } catch( \Exception $e ) {
            echo $e->getMessage();
        }
        return [
            'status' => '0',
            'reason' => 'Your transaction was declined by issuing bank.',
            'order_id' => $input['order_id'],
        ];
    }

    public function successUrl(Request $request, $session_id)
    {
    	$input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        \Log::info([
            'opennode_success_response' => $request->all(),
            'session_id' =>$session_id
        ]);
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction was proccessed successfully.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function callBackUrl(Request $request, $session_id) {
        
        \Log::info([
            'vivawallet_callback_response' => $request->all(),
            'session_id' =>$session_id
        ]);
        
    }

}
