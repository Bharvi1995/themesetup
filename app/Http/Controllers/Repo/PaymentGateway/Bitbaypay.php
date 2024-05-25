<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\TransactionSession;

class Bitbaypay extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'http://payments.alps.cl/justpay/check-out/SecurePayment'; // live
    //const BASE_URL = 'http://paymentscert.alps.cl/justpay/check-out/SecurePayment'; // test
    
    public function checkout($input, $check_assign_mid)
    {
        $curl = curl_init();
        $apiKey = $check_assign_mid->api_key;
        $apiSecret = $check_assign_mid->secret_key;
        $timenow = round(microtime(true));
        $params = [
            "destinationCurrency" => $input["currency"],
            "orderId" => $input["order_id"],
            "price" => (int)$input["converted_amount"],
            // "successCallbackUrl" => "https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475",
            // "failureCallbackUrl" => "https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475",
            // "notificationsUrl" => "https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475",
            "successCallbackUrl" => route('bitbaypay.success',$input['session_id']),
            "failureCallbackUrl" => route('bitbaypay.failure',$input['session_id']),
            "notificationsUrl" => route('bitbaypay.notify',$input['session_id']),
        ];
        $post = json_encode($params);
        $hash = hash_hmac("sha512", $apiKey.$timenow.$post, $apiSecret);
        $header = [
            'Content-Type: application/json',
            'Accept: application/json',
            'API-Key: '.$apiKey,
            'API-Hash: '.$hash,
            'operation-id:'.$this->GetUUID(random_bytes(16)) ,
            'Request-Timestamp: '.$timenow
        ];
        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.bitbaypay.com/rest/bitbaypay/payments",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPHEADER => $header,
          CURLOPT_POSTFIELDS => $post,
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $responseData = json_decode($response, 1);
        \Log::info([
            'Bitbaypay_geteway_response' => $responseData,
        ]);
        curl_close($curl);
        if (isset($responseData['data']['url']) && $responseData['status'] == "Ok") {
            try {
                $input['gateway_id'] = $responseData['data']["paymentId"] ?? null;
                $this->updateGatewayResponseData($input, $response);
            } catch (\Exception $e) {
                \Log::info([
                    'Bitbaypay_geteway_update_erro' => $e->getMessage(),
                ]);
            }
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $responseData['data']['url'],
            ];
        }
        $input['status'] = '0';
        $input['reason'] = $responseData['errors']['0']['reason'];
        //$transaction_response = $this->storeTransaction($input);
        return $input;
    }

    public function GetUUID($data)
    {
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data) , 4));
    }

    public function success(Request $request,$sessionId){
        $body = $request->all();
        \Log::info([
            'success_response_for_bitbaypay' => $body,
        ]);
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $sessionId)
            ->first();
        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction was paid successfully.';
        \Log::info(['type' => 'webhook', 'body' => $sessionId.' confirm.']);
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function failure(Request $request,$sessionId){
        $body = $request->all();
        \Log::info([
            'failure_response_for_bitbaypay' => $body,
        ]);

        $data = \DB::table('transaction_session')
            ->where('transaction_id', $sessionId)
            ->first();
        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction was expired.';
        \Log::info(['type' => 'webhook', 'body' => $sessionId.' expired.']);
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function notify(Request $request,$sessionId){
        $body = $request->all();
        \Log::info([
            'notify_response_for_bitbaypay' => $body,
            "sessionId" => $sessionId
        ]);
        $data = \DB::table('transaction_session')
            ->where('gateway_id', $body['paymentId'])
            ->first();
        if($data) {
            if (isset($body['paymentId']) && $body['status'] == 'COMPLETED') {
                $input = json_decode($data->request_data, 1);
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccess successfully.';
                \Log::info(['type' => 'webhook', 'body' => $body['paymentId'].' confirm.']);
            }
            else if (isset($body['paymentId']) && $body['status'] == 'EXPIRED') {
                $input = json_decode($data->request_data, 1);
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was expired.';
                \Log::info(['type' => 'webhook', 'body' => $body['paymentId'].' expired.']);
            }
            else if (isset($body['paymentId']) && $body['status'] == 'PAID') {
                $input = json_decode($data->request_data, 1);
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was paid successfully.';
                \Log::info(['type' => 'webhook', 'body' => $body['paymentId'].' paid confirm.']);
            }
            else if (isset($body['paymentId']) && $body['status'] == 'PENDING') {
                $input = json_decode($data->request_data, 1);
                $input['status'] = '2';
                $input['reason'] = 'Your transaction is in Pending.';
                \Log::info(['type' => 'webhook', 'body' => $body['paymentId'].' pending.']);
            }
            $transaction_response = $this->storeTransaction($input);
            exit();
        }
        \Log::info(['type' => 'webhook', 'body' => 'No transaction found']);
        exit();
    }

}
