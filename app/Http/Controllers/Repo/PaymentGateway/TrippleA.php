<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;

class TrippleA extends Controller
{
    use StoreTransaction;

    public function __construct() {
        $this->user = new User;
        $this->Transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {
        
        $token = $this->getAccessToken($check_assign_mid);
        if (isset($token['access_token'])) {
            
            $url = "https://api.triple-a.io/api/v1/payment/request";

            if(isset($input['country_code']) && $input['country_code'] != '') {
                $country_code = $input['country_code'];
            } else {
                $country_code = '';
            }

            if(substr($input['phone_no'], 0, 1) == '+') 
                $payer_phone = $country_code.$input['phone_no'];
            else
                $payer_phone = '+'.$country_code.$input['phone_no'];
            

            $data = [
                'type' => 'triplea',
                'api_id' => $check_assign_mid->btc_api,
                //'crypto_currency' => 'testBTC',
                'crypto_currency' => 'BTC',
                //"sandbox"=> true,
                'order_currency' => $input["converted_currency"],
                'order_amount' => (float)$input["converted_amount"],
                'payer_id' => 'sfasfasf',
                'payer_name' => $input['first_name'].' '.$input['last_name'],
                'payer_email' => $input['email'],
                'payer_phone' => $payer_phone,
                'payer_address' => $input['address'],
                'success_url' => route('triplea-success-url'),
                'cancel_url' => route('triplea-cancel-url'),
                //'notify_url' => 'https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475',
                'notify_url' => route('triplea-webhook-url'),
                'webhook_data' => [
                    'order_id' => $input['order_id']
                ],
            ];
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER,[
                'Content-Type: application/json',
                'Authorization: Bearer '.$token['access_token']
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $responseData = json_decode($response, 1);
            \Log::info([
                'triple-a' => $responseData,
                'inputs' => $input
            ]);
            // $input['is_crypto'] = '1';
            // $input['crypto_converted_amount'] = $responseData['crypto_amount']??00;
            // $input['crypto_converted_currency'] = 'BTC';
            if (isset($responseData['payment_reference'])) {
                try {
                    // update transaction_session record
                    $session_update_data = TransactionSession::where('transaction_id', $input['session_id'])
                        ->first();
                    $session_request_data = json_decode($session_update_data->request_data, 1);
                    $session_request_data['gateway_id'] = $responseData['payment_reference'];
                    $session_update_data->update([
                        'request_data' => json_encode($session_request_data),
                        'response_data' => $response,
                        'gateway_id' => $responseData['payment_reference']
                    ]);
                    $session_update_data->save();
                } catch (\Exception $e) {
                    \Log::info([
                        'TrirpleA_geteway_update_erro' => $e->getMessage(),
                    ]);
                }
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $responseData['hosted_url'],
                ];
            }
            $input['status'] = '0';
            $input['reason'] = $responseData['message'];
            unset($input["api_key"]);
            $this->Transaction->storeData($input);
            $domain = parse_url($input['response_url'], PHP_URL_HOST);
            if ($domain == 'testpay.com') {
                $redirect_url = $input['response_url'];
                Session::put('error', $input['reason']);
            } else {
                if (parse_url($input['response_url'], PHP_URL_QUERY)) {
                    $redirect_url = $input['response_url'].'&status=success&reason='.$input['reason'].'&order_id='.$input['order_id'];
                } else {
                    $redirect_url = $input['response_url'].'?status=success&reason='.$input['reason'].'&order_id='.$input['order_id'];
                }
            }
            return ['status' => $input['status'], 'reason' => $input['reason'], 'order_id' => $input['order_id']];
        } else {
            $input['status'] = '0';
            $input['reason'] = 'token generation proccess fail.';
            unset($input["api_key"]);
            $this->Transaction->storeData($input);

            return ['status' => $input['status'], 'reason' => $input['reason'], 'order_id' => $input['order_id']];
        }
        exit("if222222222");   
    }

    public function getAccessToken($mid_details)
    {
        $url = "https://api.triple-a.io/api/v1/oauth/token";
        $data = [
            'client_id' => $mid_details->client_id,
            'client_secret' => $mid_details->client_secret,
            'grant_type' => 'client_credentials',
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER,[
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, 1);
        \Log::info($responseData);
        return $responseData;
    }

    public function tripleaSuccessUrl(Request $request)
    {
        $requestData = $request->all();
        \Log::info([
            'tripleA_success_Data' => $requestData
        ]);
        //echo "<pre>";print_r($requestData);exit(); 
        $data = \DB::table('transaction_session')
            ->where('gateway_id', $requestData['payment_reference'])
            ->where('is_completed', '0')
            ->first();

        if($data) {
            // Complete the transaction.
            \DB::table('transaction_session')
                ->where('gateway_id', $requestData['payment_reference'])
                ->update([
                    'is_completed' => '1'
            ]);
            $input = json_decode($data->request_data, 1);
            $domain = parse_url($input['response_url'], PHP_URL_HOST);
            if(isset($requestData['status']) && $requestData['status'] == 'paid') {
                $input['status'] = '2';
                $input['reason'] = 'Your transaction has been submitted successfully and is in pending state .Your transaction status will be updated within next 2 hours.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);

                if ($domain == 'testpay.com') {
                    $redirect_url = $input['response_url'];

                    Session::put('success', $input['reason']);
                } else {
                    if (parse_url($input['response_url'], PHP_URL_QUERY)) {
                        $redirect_url = $input['response_url'].'&status=pending&reason='.$input['reason'].'&order_id='.$input['order_id']."&payment_reference=".$requestData['payment_reference'];
                    } else {
                        $redirect_url = $input['response_url'].'?status=pending&reason='.$input['reason'].'&order_id='.$input['order_id']."&payment_reference=".$requestData['payment_reference'];
                    }
                }
            } else {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction declined';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);

                if ($domain == 'testpay.com') {
                    $redirect_url = $input['response_url'];

                    Session::put('error', $input['reason']);
                } else {
                    if (parse_url($input['response_url'], PHP_URL_QUERY)) {
                        $redirect_url = $input['response_url'].'&status=fail&reason='.$input['reason'].'&order_id='.$input['order_id'];
                    } else {
                        $redirect_url = $input['response_url'].'?status=fail&reason='.$input['reason'].'&order_id='.$input['order_id'];
                    }
                }
            }

            return redirect($redirect_url);
        }

        return response()->json(['transaction already completed, or may be transaction not found in our system']);
    }

    public function tripleaCancelUrl(Request $request)
    {
        $requestData = $request->all();
        $data = \DB::table('transaction_session')
            ->where('gateway_id', $requestData['payment_reference'])
            ->where('is_completed', '0')
            ->first();

        if($data) {
            // Complete the transaction.
            \DB::table('transaction_session')
                ->where('gateway_id', $requestData['payment_reference'])
                ->update([
                    'is_completed' => '1'
                ]);

            $input = json_decode($data->request_data, 1);

            $domain = parse_url($input['response_url'], PHP_URL_HOST);

            $input['status'] = '0';
            $input['reason'] = 'Transaction canceled by merchant.';
            unset($input["api_key"]);
            //echo "<pre>";print_r($input);exit();
            $this->Transaction->storeData($input);

            if ($domain == 'testpay.com') {
                $redirect_url = $input['response_url'];

                Session::put('error', $input['reason']);
            } else {
                if (parse_url($input['response_url'], PHP_URL_QUERY)) {
                    $redirect_url = $input['response_url'].'&status=fail&reason='.$input['reason'].'&order_id='.$input['order_id'];
                } else {
                    $redirect_url = $input['response_url'].'?status=fail&reason='.$input['reason'].'&order_id='.$input['order_id'];
                }
            }

            return redirect($redirect_url);
        }

        return response()->json(['transaction already completed, or may be transaction not found in our system']);
    }

    public function tripleaWebhook(Request $request)
    {
        // Retrieve the request's body
        $body = $request->all();
        \Log::info([
            'tripleA_webhook_Data' => $body
        ]);
        $data = \DB::table('transaction_session')
            ->where('gateway_id', $body['payment_reference'])
            ->first();

        if($data) {
            if (isset($body['payment_reference']) && $body['payment_tier'] == 'good' && $body['status'] == 'good') {
                $input = json_decode($data->request_data, 1);
                // update the transaction status
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccess successfully.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);
                # transaction confirm
                \Log::info(['type' => 'webhook', 'body' => $body['payment_reference'].' confirm.']);
                exit();
            } elseif (isset($body['payment_reference']) && $body['payment_tier'] == 'invalid') {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was invalid in blockchain.';
                unset($input["api_key"]);
                $this->Transaction->storeData($input);
                # transaction confirm
                \Log::info(['type' => 'webhook', 'body' => $body['payment_reference'].' invalid.']);
                exit();
            } else {
                # transaction not confirm
                \Log::info(['type' => 'webhook', 'body' => $body['payment_reference'].' still not confirm.']);
                exit();
            }
        }

        \Log::info(['type' => 'webhook', 'body' => 'No transaction found']);
        exit();
    }
}
