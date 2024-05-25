<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;
use Illuminate\Support\Facades\Hash;

class Chakra extends Controller
{
    //const BASE_URL = 'https://webdev.mychakra.io/'; // Test
    const BASE_URL = 'https://web.mychakra.io/'; //Live
    const DECRYPT_STRING = '95bff5599';
    private $response = [];

    public $transaction;
    use StoreTransaction;

    public function __construct()
    {
        $this->transaction = new Transaction();
    }

    public function checkout($input, $check_assign_mid)
    {
        try {
            $token = $this->getAccessToken($input, $check_assign_mid);
            if (isset($token['data']['accessToken'])) {
                $input['token'] = $token['data']['accessToken'];
                $preInitializePayment = $this->preInitializePayment($input, $check_assign_mid); // 1 preinitialize api for get transaction reference number
                // \Log::info([
                //     'preInitializePayment' => $preInitializePayment
                // ]);
                if (!empty($preInitializePayment)) {
                    $input['transactionRef'] = isset($preInitializePayment['data']['transactionRef']) ? $preInitializePayment['data']['transactionRef'] : '';
                    $initializePayment = $this->initializePayment($input, $check_assign_mid); // 2 initializepayment action for payment initialization
                    if (!empty($initializePayment)) {
                        $cardPayment = $this->processCardPayment($input, $check_assign_mid); // 3 processcardpayment action for get authurl
                        $input['gateway_id'] = $input['transactionRef'] ?? $input['order_id'];
                        $this->updateGatewayResponseData($input, $this->response);

                        return [
                            'status' => '7',
                            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                            'redirect_3ds_url' => $cardPayment['data']['authUrl'],
                        ];
                    }
                }
            }

            // Token generation fail.
            // \Log::info([
            //     'chakra-token-error' => $token
            // ]);

            throw new \Exception('Your transaction could not processed.');
        } catch (\Exception $e) {

            \Log::info([
                'chakra-exception' => $e->getMessage()
            ]);

            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    /*
     * For generate aceesst oken
     * */
    private function getAccessToken($input, $check_assign_mid)
    {
        $err = '';
        $requestData = [
            'merchantId' => $check_assign_mid->mid,
            'apiKey' => $check_assign_mid->api_key,
        ];

        $url = '/credentials/get-token';
        $header = [
            'Content-Type: application/json',
        ];
        $responseData = $this->curlRequest($url, $header, $requestData, $input, $check_assign_mid);
        if ($responseData['responseCode'] != '00') {
            $err = isset($responseData['responseMessage']) && !empty($responseData['responseMessage']) ?  $responseData['responseMessage'] : 'Your transaction could not processed.';
        }

        if ($err) {
            throw new \Exception($err);
        }

        return $responseData;
    }

    /*
     * For PreInitializePayment action
     * */
    private function preInitializePayment($input, $check_assign_mid)
    {
        $err = '';
        $data = [
            'currency' => $input['converted_currency'],
            'amount' => number_format($input['converted_amount'], 2, '.', ''),
            'paymentType' => '1',
            'callbackUrl' => route('chakra-callback', $input['session_id']),
            'callbackSecret' => Hash::make($check_assign_mid->mid),
            'successRedirectUrl' => route('chakra-success', $input['session_id']),
            'failureRedirectUrl' => route('chakra-failure', $input["session_id"])
        ];

        \Log::info([
            'chakra-payment-input-preInitializePayment-action' => $data
        ]);

        $encrypt = $this->encryption($data, $check_assign_mid);
        if (!empty($encrypt)) {
            $requestData = [
                'action' => 'PreInitializePayment',
                'request' => $encrypt
            ];

            $url = $this->getApiUrl($check_assign_mid);
            $header = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $input['token']
            ];
            \Log::info([
                'chakra-preinitialize-data' => $requestData
            ]);
            \Log::info([
                'chakra-preinitialize-header' => $header
            ]);
            $responseData = $this->curlRequest($url, $header, $requestData, $input, $check_assign_mid);
            \Log::info([
                'chakra-preinitialize-response' => $responseData
            ]);
            $responseData = $this->decryption($responseData, $check_assign_mid);
            if ($responseData['responseCode'] != '00') {
                $err = isset($responseData['responseMessage']) && !empty($responseData['responseMessage']) ?  $responseData['responseMessage'] : 'Your transaction could not processed.';
            }

            if ($err) {
                throw new \Exception($err);
            }

            $transactionID = isset($responseData['data']['transactionRef']) ? $responseData['data']['transactionRef'] : '';
            $this->response['preInitializePayment'] = [
                'session_id' => $input['session_id'],
                'gateway_id' => $transactionID,
                'response' => $responseData
            ];

            return $responseData;
        }
    }

    /*
     *  For InitializePayment action
     * */
    private function initializePayment($input, $check_assign_mid)
    {
        $err = '';
        $data = [
            'transRef' =>  $input['transactionRef'],
            'narration' => $input['order_id'],
            'email' => $input['email'],
            'firstName' => $input['first_name'],
            'lastName' => $input['last_name'],
            'phoneNumber' => preg_replace("/[^0-9]/", '', $input['phone_no']),
            'address' => $input['address'],
            'city' => $input['city'],
            'state' => $input['state'],
            'postalCode' => $input['zip'],
            'countryCode' => $input['country'],
            'tokenizeCard' => false
        ];

        // \Log::info([
        //     'chakra-payment-input-initializePayment-action' => $data
        // ]);
        $encrypt = $this->encryption($data, $check_assign_mid);
        if (!empty($encrypt)) {
            $requestData = [
                'action' => 'InitializePayment',
                'request' => $encrypt
            ];

            $url = $this->getApiUrl($check_assign_mid);
            $header = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $input['token']
            ];
            $responseData = $this->curlRequest($url, $header, $requestData, $input, $check_assign_mid);
            // \Log::info([
            //     'chakra-payment-input-initializePayment-response' => $responseData
            // ]);
            $responseData = $this->decryption($responseData, $check_assign_mid);
            if ($responseData['responseCode'] != '00') {
                $err = isset($responseData['responseMessage']) && !empty($responseData['responseMessage']) ?  $responseData['responseMessage'] : 'Your transaction could not processed.';
            }

            if ($err) {
                throw new \Exception($err);
            }

            $this->response['initializePayment'] =  [
                'session_id' => $input['session_id'],
                'gateway_id' => $input['transactionRef'],
                'response' => $responseData
            ];
            //$this->updateCallback($input,$check_assign_mid);

            return $responseData;
        }
    }

    /*
     * For ProcessCardPayment
     * */
    private function processCardPayment($input, $check_assign_mid)
    {
        $err = '';
        $data = [
            'transRef' => $input['transactionRef'], // transaction reference number
            'pan' => $input['card_no'],
            'expiredMonth' => $input['ccExpiryMonth'],
            'expiredYear' => $input['ccExpiryYear'],
            'cvv' => $input['cvvNumber']
        ];

        // \Log::info([
        //     'chakra-payment-input-processCardPayment-action' => $data
        // ]);

        $encrypt = $this->encryption($data, $check_assign_mid);
        if (!empty($encrypt)) {
            $requestData = [
                'action' => 'ProcessCardPayment',
                'request' => $encrypt
            ];
            $url = $this->getApiUrl($check_assign_mid);
            $header = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $input['token']
            ];
            $responseData = $this->curlRequest($url, $header, $requestData, $input, $check_assign_mid);
            $responseData = $this->decryption($responseData, $check_assign_mid);
            if ($responseData['responseCode'] != '00') {
                $err = isset($responseData['responseMessage']) && !empty($responseData['responseMessage']) ?  $responseData['responseMessage'] : 'Your transaction could not processed.';
            }

            if ($err) {
                throw new \Exception($err);
            }

            $this->response['processCardPayment'] = [
                'session_id' => $input['session_id'],
                'gateway_id' => $input['transactionRef'],
                'response' => $responseData
            ];

            return $responseData;
        }
    }


    /*
     * For generate encryption string
     * */
    private function encryption($data, $check_assign_mid)
    {
        $curl = curl_init();
        $url = 'cryptography/encrypt-req?merchantId=' . $check_assign_mid->mid;
        // \Log::info([
        //     'chakra-encryption-url' => $url,
        //     'data' => $data
        // ]);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: text/plain'
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($curl);
        curl_close($curl);

        // \Log::info([
        //     'chakra-encryption-response' => $response
        // ]);

        return $response;
    }

    /*
     * For decryption string
     * */
    private function decryption($data, $check_assign_mid)
    {
        $clientData = [
            'client-data' => $check_assign_mid->ivKey . self::DECRYPT_STRING . $check_assign_mid->secretKey
        ];

        $url = 'cryptography/decrypt-req?' . http_build_query($clientData);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: text/plain'
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data['response']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, 1);
        // \Log::info([
        //     'chakra-decrypt-input' => $data
        // ]);
        // \Log::info([
        //     'chakra-decrypt-response' => $responseData
        // ]);
        return $responseData;
    }

    /*
     * For curl request
     * */
    private function curlRequest($url, $header, $requestData, $input, $check_assign_mid)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            $header
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $info = curl_getinfo($curl);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $responseData = json_decode($response, 1);
        return $responseData;
    }

    /*
     * For UpdateCallback
    * */
    private function updateCallback($input, $check_assign_mid)
    {
        $err = '';
        $requestData = [
            'merchantId' => $check_assign_mid->mid,
            'callbackUrl' => route('chakra-callback', $input['session_id']),
            'callbackSecret' => Hash::make($check_assign_mid->mid),
            'responseUrl' => route('chakra-returnUrl', $input['session_id']),
        ];
        // \Log::info([
        //     'chakra-callback-update-input' => $requestData
        // ]);

        $credential = [
            'chakra-credentials' => base64_encode($check_assign_mid->mid . ':' . $check_assign_mid->api_key)
        ];
        $credential = http_build_query($credential);
        $url = 'credentials/update-callback?' . $credential;
        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $input['token']
        ];

        $responseData = $this->curlRequest($url, $header, $requestData, $input, $check_assign_mid);
        // \Log::info([
        //         'chakra-callback-update' => $responseData
        // ]);
        if ($responseData['responseCode'] != '00') {
            $err = isset($responseData['responseMessage']) && !empty($responseData['responseMessage']) ?  $responseData['responseMessage'] : 'Your transaction could not processed.';
        }

        if ($err) {
            throw new \Exception($err);
        }
        return 0;
    }

    private function getApiUrl($check_assign_mid)
    {
        $credential = [
            'chakra-credentials' => base64_encode($check_assign_mid->mid . ':' . $check_assign_mid->api_key)
        ];

        $credential = http_build_query($credential);
        $url = 'acq/send-request?' . $credential;
        \Log::info([
            'chakra-apiurl' => $url
        ]);
        return $url;
    }

    /*
     * For callbackUrl
    * */
    public function callback(Request $request, $id)
    {
        // Update callback response
        $header = $request->header('callback-secret');
        $response = $request->all();

        // \Log::info([
        //     'chakra-callback' => $response
        // ]);

        // \Log::info([
        //     'chakra-callback-header' => $header
        // ]);

        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        if ($transaction_session == null) {

            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, 1);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        if (Hash::check($check_assign_mid->mid, $header)) {
            if ($response['responseCode'] == '00' && ($response['responseDescription'] == 'Successful' || $response['responseDescription'] == 'successful')) {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($response['responseMessage']) ? $response['responseMessage'] : 'Your transaction could not processed.');
            }
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
            // \Log::info([
            //     'chakra-invalid-secretkey' => $header
            // ]);
        }
        // \Log::info([
        //     'chakra-callback-reason' => $input['reason']
        // ]);
        DB::table("transactions")->where('session_id', $input['session_id'])
            ->update([
                'reason' => $input['reason'],
                'status' => $input['status']
            ]);
        if (isset($input['webhook_url']) && $input['webhook_url'] != null) {
            if (isset($input['status']) && $input['status'] == '1') {
                $transactionStatus = 'success';
            } elseif (isset($input['status']) && $input['status'] == '2') {
                $transactionStatus = 'pending';
            } elseif (isset($input['status']) && $input['status'] == '5') {
                $transactionStatus = 'blocked';
            } else {
                $transactionStatus = 'fail';
            }

            $request_data['order_id'] = $input['order_id'];
            $request_data['customer_order_id'] = $input['customer_order_id'] ?? null;
            $request_data['transaction_status'] = $transactionStatus;
            $request_data['reason'] = $input['reason'];
            $request_data['currency'] = 'NGN';
            $request_data['amount'] = $input['converted_amount'];
            // $request_data['test'] = in_array($input['payment_gateway_id'], ['16', '41']) ? true : false;
            $request_data['transaction_date'] = date('Y-m-d H:i:s');
            $request_data["descriptor"] = $check_assign_mid->descriptor;
            // send webhook request
            try {
                $http_response = postCurlRequest($input['webhook_url'], $request_data);
            } catch (Exception $e) {
                $http_response = 'FAILED';
            }
        }
        //$transaction_response = $this->storeTransaction($input);
        exit();
    }

    /*
     * For responseUrl
    * */
    public function returnUrl(Request $request, $id)
    {
        $response = $request->all();
        // \Log::info([
        //     'chakra-return' => $response
        // ]);
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        // if ($transaction_session == null) {
        //     return abort(404);
        // }

        $input = json_decode($transaction_session->request_data, true);
        $transactions = DB::table('transactions')
            ->where('order_id', $transaction_session->order_id)
            ->first();

        $input['status'] = $transactions->status ?? 0;
        $input['reason'] = $transactions->reason ?? 'Your transaction could not processed.';

        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function success(Request $request, $id)
    {
        $response = $request->all();
        // \Log::info([
        //     'chakra-success' => $response
        // ]);
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction was proccessed successfully.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function failure(Request $request, $id)
    {
        $response = $request->all();
        // \Log::info([
        //     'chakra-failure' => $response
        // ]);
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, true);
        // $check_assign_mid = checkAssignMid($transaction_session->payment_gateway_id);
        // $credential = base64_encode($check_assign_mid->mid . ':' . $check_assign_mid->api_key);
        // $chakraToken = $this->getChakraApiToken($check_assign_mid);
        // $accessToken = $chakraToken->data->accessToken;
        // $url = "https://web.mychakra.io/acq/get-transaction-status?transRef=".$response["transRef"]."&chakra-credentials=".$credential;
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => $url,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'GET',
        //     CURLOPT_HTTPHEADER => array(
        //         'Authorization: Bearer '.$accessToken
        //     ),
        // ));
        // $response = curl_exec($curl);
        // $response_code = json_decode($response);
        // \Log::info([
        //     'chakra-failure_status' => $response_code
        // ]);
        // curl_close($curl);
        // if(isset($response_code) && $response_code->responseCode == "00"){
        //     if($response_code->data->responseCode == "00" && $response_code->data->transactionStatus == 'SUCCESSFUL'){
        //         $input['status'] = '1';
        //         $input['reason'] = 'Your transaction was proccessed successfully.';
        //     }else{
        //         $input['status'] = '0';
        //         $input['reason'] = $response_code->data->responseMessage ?? 'Your transaction could not processed.' ;
        //     }

        // }else{
        //     $input['status'] = '0';
        //     $input['reason'] = 'Your transaction could not processed.' ;
        // }
        $input['status'] = '0';
        $input['reason'] = 'Your transaction could not processed.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function getChakraApiToken($check_assign_mid)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web.mychakra.io/credentials/get-token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "merchantId": "' . $check_assign_mid->mid . '",
                "apiKey": "' . $check_assign_mid->api_key . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $response_code = json_decode($response);
        curl_close($curl);
        return $response_code;
    }
}
