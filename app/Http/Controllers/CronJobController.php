<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionSession;
use Carbon\Carbon;

class CronJobController extends Controller
{
    public function __construct()
    {
        $this->Transaction = new Transaction;
        $this->TransactionSession = new TransactionSession;
    }
    // ================================================
    /* method : getCurrencyRate
    * @param  : 
    * @description : get currency rate cron
    */// ==============================================
    public function getCurrencyRate(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }

        try {
            $data = file_get_contents('https://api.currencylayer.com/live?access_key=' . config("custom.currency_converter_access_key"));
        } catch (\Exception $e) {
            return false;
        }

        if ($data) {
            //\DB::table('currency_rate')->delete();

            $arrayData = json_decode($data);

            foreach ($arrayData->quotes as $key => $value) {
                $currencyRateData = [
                    'source' => 'USD',
                    'currency' => substr($key, -3),
                    'converted_amount' => $value,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $count = \DB::table("currency_rate")->where(["source" => "USD", "currency" => substr($key, -3)])->count();
                if ($count > 0) {
                    \DB::table("currency_rate")->where(["source" => "USD", "currency" => substr($key, -3)])->update(["converted_amount" => $value, "updated_at" => date('Y-m-d H:i:s')]);
                } else {
                    \DB::table('currency_rate')->insert($currencyRateData);
                }
                // \DB::table('currency_rate')
                //  ->insert($currencyRateData);
            }
            echo 'success';
        }
    }

    public function restoreSessionTransaction(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sS458FtkT') {
            exit();
        }
        //$one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $one_hour_ago = date('Y-m-d H:i:s', strtotime('-30 minutes'));
        $arr_gateway_ids = ['6', '22', '23', '69', '96'];
        $gateway_ids = \DB::table('transaction_session')
            ->where('is_completed', '0')
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->where('created_at', '<=', $one_hour_ago)
            ->whereNotNull('gateway_id');
        if ($request->user_id && $request->user_id != '') {
            $gateway_ids = $gateway_ids->where('user_id', $request->user_id);
        }
        $gateway_ids = $gateway_ids->pluck('gateway_id')
            ->toArray();

        foreach ($gateway_ids as $value) {

            // get $input data
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();

            if ($input_json == null) {
                continue;
            }

            $input = json_decode($input_json['request_data'], true);

            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            if (!isset($check_assign_mid->secret_key)) {
                \Log::info(['sessionn_id' => $input['session_id']]);
                //continue;
            }
            $status_url = 'https://cashierapi.opayweb.com/api/v3/transaction/status';

            $status_data = [
                'orderNo' => $value,
                'reference' => $input['session_id'],
            ];

            $signature = hash_hmac('sha512', json_encode($status_data), $check_assign_mid->secret_key);
            //$signature = hash_hmac('sha512', json_encode($status_data), 'OPAYPRV16273837720270.961375534253676');
            $status_headers = [
                'Authorization: Bearer ' . $signature,
                'MerchantId: ' . $check_assign_mid->merchant_id,
                // 'MerchantId: 256621072719836',
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $status_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($status_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $status_headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $status_body = curl_exec($ch);

            curl_close($ch);

            $status_response = json_decode($status_body, true);
            // status successful
            $input['is_webhook'] = '3';
            if (isset($status_response['data']['status']) && $status_response['data']['status'] == 'SUCCESS') {

                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';

                // store transaction
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                if ($input['reason'] == 'order not exist [A.A.I.04013]') {
                    $input['reason'] = 'transaction session time-out';
                }
                $this->Transaction->storeData($input);

                // transaction pending 
            } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['PENDING', 'INITIAL'])) {

                $input['status'] = '2';
                $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';

                // redirect to acquirer server
            } elseif (isset($status_response['data']['status']) && $status_response['data']['status'] == '3DSECURE') {

                // $input['status'] = '2';
                // $input['reason'] = 'Transaction is pending for authentication, please check after few minutes.';
                $input['status'] = '0';
                $input['reason'] = 'Transaction timeout.';
                // store transaction
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                if ($input['reason'] == 'order not exist [A.A.I.04013]') {
                    $input['reason'] = 'transaction session time-out';
                }
                $this->Transaction->storeData($input);

                // declined
            } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['FAIL', 'CLOSE'])) {

                $input['status'] = '0';
                $input['reason'] = $status_response['data']['failureReason'] ?? 'Transaction authentication failed.';
                // store transaction
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                if ($input['reason'] == 'order not exist [A.A.I.04013]') {
                    $input['reason'] = 'transaction session time-out';
                }
                $this->Transaction->storeData($input);

            } else {
                \Log::info(['opay_cron_else' => $status_response]);

                $input['status'] = '0';
                $input['reason'] = $status_response['message'] ?? 'Transaction authentication failed.';
                // store transaction
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                if ($input['reason'] == 'order not exist [A.A.I.04013]') {
                    $input['reason'] = 'transaction session time-out';
                }
                $this->Transaction->storeData($input);

            }

            // update transaction_session record if not pending
            if ($input['status'] != '2') {

                \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
            }
        }

        // cron completed
        \Log::info('opay_cron_completed');
        exit();
    }

    public function opayPendingTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $arr_gateway_ids = ['6', '22', '23', '69', '96'];
        //$arr_gateway_ids = ['4','25'];
        $gateway_ids = \DB::table('transactions')
            ->where('status', '2')
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull('gateway_id')
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                //->where('is_completed', '0')
                ->first();

            // if ($input_json == null) {
            //     continue;
            // }
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $status_url = 'https://cashierapi.opayweb.com/api/v3/transaction/status';

            $status_data = [
                'orderNo' => $value,
                'reference' => $input['session_id'],
            ];
            //$signature = hash_hmac('sha512', json_encode($status_data), 'OPAYPRV16273837720270.961375534253676');
            $signature = hash_hmac('sha512', json_encode($status_data), $check_assign_mid->secret_key);
            $status_headers = [
                'Authorization: Bearer ' . $signature,
                //'MerchantId: 256621072719836',
                'MerchantId: ' . $check_assign_mid->merchant_id,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $status_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($status_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $status_headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $status_body = curl_exec($ch);

            curl_close($ch);
            $status_response = json_decode($status_body, true);
            if (isset($status_response['data']['status']) && $status_response['data']['status'] == 'SUCCESS') {

                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';

                // transaction pending 
            } elseif (isset($status_response['data']['status']) && in_array($status_response['data']['status'], ['PENDING', 'INITIAL'])) {

                $input['status'] = '2';
                $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';

                // redirect to acquirer server
            } elseif (isset($status_response['data']['status']) && $status_response['data']['status'] == '3DSECURE') {

                // $input['status'] = '2';
                // $input['reason'] = 'Transaction is pending for authentication, please check after few minutes.';
                $input['status'] = '2';
                $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';

                // declined
            } else {
                if (isset($status_response['data']['failureReason'])) {
                    $reason = $status_response['data']['failureReason'];
                } elseif (isset($status_response['message'])) {
                    $reason = $status_response['message'];
                } else {
                    $reason = "Transaction declined";
                }
                $input['status'] = '0';
                $input['reason'] = $reason;
            }

            // store transaction
            unset($input['api_key']);
            unset($input['country_code']);
            unset($input['is_disable_rule']);
            unset($input['bin_country_code']);
            unset($input['request_from_type']);
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
                $request_data['currency'] = $input['currency'];
                $request_data['amount'] = $input['amount'];
                // $request_data['test'] = in_array($input['payment_gateway_id'], ['16', '41']) ? true : false;
                $request_data['transaction_date'] = date("Y-m-d H:i:s", strtotime($input_json['created_at']));
                $request_data["descriptor"] = $check_assign_mid->descriptor;
                // send webhook request
                try {
                    // $http_response = postCurlRequestBackUpTwo($input['webhook_url'], $request_data);
                    $http_response = postCurlRequest($input['webhook_url'], $request_data);
                } catch (Exception $e) {
                    $http_response = 'FAILED';
                    \Log::info([
                        'webhook error pending' => $e->getMessage()
                    ]);
                }
            }
            \DB::table("transactions")->where("session_id", $input['session_id'])->update(["status" => $input['status'], "reason" => $input['reason'], "is_webhook" => "8"]);
            //$this->Transaction->storeData($input);
            if ($input['status'] != '2') {

                \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
            }
        }
        \Log::info('opay_status_cron_completed');
        exit();
    }

    public function chakraPendingTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        //$arr_gateway_ids = ['47','46','45','44'];
        $arr_gateway_ids = ['58', '51', '61', '62', '63'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();

            if ($input_json == null) {
                continue;
            }
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $chakraToken = $this->getChakraToken($check_assign_mid);
            if ($chakraToken->responseCode == "00" && $chakraToken->responseDescription == "Successful") {
                $accessToken = $chakraToken->data->accessToken;
                $charaStatus = $this->getChakraStatus($value, $check_assign_mid, $accessToken);
                if (isset($charaStatus) && $charaStatus->responseCode == "00") {
                    if ($charaStatus->data->responseCode == "00" && $charaStatus->data->transactionStatus == 'SUCCESSFUL') {
                        $input['status'] = '1';
                        $input['reason'] = 'Your transaction was proccessed successfully.';
                    } else {
                        $input['status'] = '0';
                        $input['reason'] = $charaStatus->data->responseMessage;
                    }
                    unset($input['api_key']);
                    unset($input['country_code']);
                    unset($input['is_disable_rule']);
                    unset($input['bin_country_code']);
                    unset($input['request_from_type']);
                    $this->Transaction->storeData($input);
                }
            }
            \DB::table('transaction_session')
                ->where('transaction_id', $input['session_id'])
                ->update(['is_completed' => '1']);
        }
        \Log::info('chakra_cron_completed');
        exit();
    }

    public function getChakraToken($check_assign_mid)
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://web.mychakra.io/credentials/get-token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                "merchantId": "' . $check_assign_mid->merchant_id . '",
                "apiKey": "' . $check_assign_mid->api_key . '"
            }',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json'
                ),
            )
        );
        $response = curl_exec($curl);
        $response_code = json_decode($response);
        curl_close($curl);
        return $response_code;
    }

    public function getChakraStatus($id, $check_assign_mid, $accessToken)
    {
        $credential = base64_encode($check_assign_mid->merchant_id . ':' . $check_assign_mid->api_key);
        $url = "https://web.mychakra.io/acq/get-transaction-status?transRef=" . $id . "&chakra-credentials=" . $credential;
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $accessToken
                ),
            )
        );
        $response = curl_exec($curl);
        $response_code = json_decode($response);
        curl_close($curl);
        return $response_code;
    }

    public function pendingTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $pendingTxn = Transaction::where('status', 2)
            ->whereIn('payment_gateway_id', $payment_gateway_id)
            ->get()
            ->toArray();

        foreach ($pendingTxn as $key => $value) {

            if (isset($value['webhook_url']) && $value['webhook_url'] != null) {

                // send webhook request
                try {

                    $transaction_session = TransactionSession::where('transaction_id', $value['session_id'])
                        ->orderBy('id', 'desc')
                        ->first();
                    $input = json_decode($transaction_session->request_data, true);
                    $input['status'] = 1;
                    $input['reason'] = 'Your transaction has been processed successfully.';

                    // store transaction
                    unset($input['api_key']);
                    unset($input['country_code']);
                    unset($input['is_disable_rule']);
                    unset($input['bin_country_code']);
                    unset($input['request_from_type']);
                    $this->Transaction->storeData($input);
                    if ($input['status'] != '2') {
                        \DB::table('transaction_session')
                            ->where('transaction_id', $input['session_id'])
                            ->update(['is_completed' => '1']);
                    }

                } catch (Exception $e) {
                    \Log::info([
                        'pendingTransactionStatusChange-exception' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    public function pendingEzipayTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $arr_gateway_ids = ['88'];
        //$arr_gateway_ids = ['57'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();

            if ($input_json == null) {
                continue;
            }
            $input = json_decode($input_json['request_data'], true);
            $data = 'MerchantId=40adb264-6b48-495f-85b0-5852df9bc30f&TokenId=' . $value;
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://payments.ezipaysarl.com/api/status',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded'
                    ),
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $responseData = json_decode($response);
            if (isset($responseData->StatusCode) && $responseData->StatusCode == 200 && $responseData->Message == "SUCCESSFUL") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = isset($responseData->Message) ? $responseData->Message : 'Your transaction could not processed.';
            }
            unset($input['api_key']);
            unset($input['country_code']);
            unset($input['is_disable_rule']);
            unset($input['bin_country_code']);
            unset($input['request_from_type']);
            $this->Transaction->storeData($input);
            \DB::table('transaction_session')
                ->where('transaction_id', $input['session_id'])
                ->update(['is_completed' => '1']);
        }
        \Log::info('ezipay_cron_completed');
        exit();

    }

    public function pendingStanbicTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $arr_gateway_ids = ['81', '92'];
        //$arr_gateway_ids = '55';
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->pluck('gateway_id')
            ->toArray();
        //\Log::info(['stanbic_cron' => $gateway_ids]);
        //$check_assign_mid = checkAssignMID($arr_gateway_ids);
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();

            if ($input_json == null) {
                continue;
            }
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMID($input_json["payment_gateway_id"]);
            $token = $this->getAccessTokenStanbic($check_assign_mid);
            if (isset($token['access_token'])) {
                $url = "https://api-gateway.ngenius-payments.com/transactions/outlets/" . $check_assign_mid->reference . "/orders/" . $value;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        "Authorization: Bearer " . $token['access_token'],
                    )
                );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = json_decode(curl_exec($ch));
                $responseData = json_decode(json_encode($output), true);
                if ($responseData['_embedded']['payment'][0]["state"] == "PURCHASED") {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction has been processed successfully.';
                } else {
                    $input['status'] = '0';
                    $input['reason'] = isset($responseData['_embedded']['payment'][0]["3ds"]["summaryText"]) ? $responseData['_embedded']['payment'][0]["3ds"]["summaryText"] : 'Your transaction could not processed.';
                }
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
            }
        }
        \Log::info('stanbicpay_cron_completed');
        exit();
    }

    public function checkOrder(Request $request)
    {
        $id = "a41bbd9e-0e7d-4b46-b117-324c5faf8e32";
        $url = "https://api-gateway.ngenius-payments.com/transactions/outlets/332c3dac-35d5-4890-898e-4c07b6d8ee70/orders/" . $request->id;
        $arr_gateway_ids = '81';
        $check_assign_mid = checkAssignMID($arr_gateway_ids);
        $token = $this->getAccessTokenStanbic($check_assign_mid);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "Authorization: Bearer " . $token['access_token'],
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = json_decode(curl_exec($ch));

        $responseData = json_decode(json_encode($output), true);
        echo "<pre>";
        print_r($responseData);
        exit();
    }

    public function getAccessTokenStanbic($check_assign_mid)
    {
        $apikey = $check_assign_mid->api_key;
        $data = [
            'realmName' => $check_assign_mid->realm_name
        ];
        $url = 'https://api-gateway.ngenius-payments.com/identity/auth/access-token';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "accept: application/vnd.ni-identity.v1+json",
                "authorization: Basic " . $apikey,
                "content-type: application/vnd.ni-identity.v1+json"
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $output = json_decode(curl_exec($ch));
        $responseData = json_decode(json_encode($output), true);
        return $responseData;
    }

    public function successEzipayTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $arr_gateway_ids = ['88'];
        //$arr_gateway_ids = ['57'];
        $gateway_ids = \DB::table('transactions')
            ->where('status', '1')
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull('gateway_id')
            ->where(\DB::raw('DATE(created_at)'), '=', date("Y-m-d"))
            ->take('10')
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $data = 'MerchantId=40adb264-6b48-495f-85b0-5852df9bc30f&TokenId=' . $value;
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://payments.ezipaysarl.com/api/status',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded'
                    ),
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $responseData = json_decode($response);
            if (isset($responseData->StatusCode) && $responseData->StatusCode == 200 && $responseData->Message == "SUCCESSFUL") {
            } else {
                $reason = isset($responseData->Message) ? $responseData->Message : 'Your transaction could not processed.';
                \DB::table("transactions")->where("gateway_id", $value)->update(["status" => "0", "reason" => $reason]);
            }
        }
        exit();
    }

    public function pendingqikpayTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $arr_gateway_ids = ['93'];
        //$arr_gateway_ids = ['57'];
        $gateway_ids = \DB::table("transaction_session")
            ->select("id", "request_data", "response_data", "order_id")
            //->where("is_completed","0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->get();
        echo "<pre>";

        foreach ($gateway_ids as $key => $value) {
            if (!empty($value->response_data)) {
                $input = json_decode($value->request_data, true);
                $arrResponseData = json_decode($value->response_data);
                $data = array(
                    "PAY_ID" => '1223511112155805',
                    'ORDER_ID' => $value->order_id,
                    'AMOUNT' => $arrResponseData->TOTAL_AMOUNT,
                    'TXNTYPE' => 'STATUS',
                    'CURRENCY_CODE' => '356'
                );
                foreach ($data as $key => $value) {
                    $responceParamsJoined[] = "$key=$value";
                }
                $SALT = "29a06f25e34542ae";
                $HASH = $this->GenHash($responceParamsJoined, $SALT);
                $data["HASH"] = $HASH;
                $postvars = json_encode($data);
                $cURL = curl_init();
                curl_setopt($cURL, CURLOPT_URL, 'https://secure.qikpay.co.in/pgws/transact');
                curl_setopt($cURL, CURLOPT_POST, 1);
                curl_setopt($cURL, CURLOPT_POSTFIELDS, $postvars);
                curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $cURL,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($postvars)
                    )
                );
                $server_output = curl_exec($cURL);
                $statusArray = json_decode($server_output, true);
                curl_close($cURL);
                if (isset($statusArray) && isset($statusArray["RESPONSE_CODE"]) && $statusArray["RESPONSE_CODE"] == "000") {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction has been processed successfully.';
                } else {
                    $input['status'] = '0';
                    $input['reason'] = (isset($statusArray['PG_TXN_MESSAGE']) ? $statusArray['PG_TXN_MESSAGE'] : 'Your transaction could not processed.');
                }
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
            }
        }
    }

    public function GenHash($data, $SALT)
    {
        sort($data);
        $merchant_data_string = implode('~', $data);
        $format_Data_string = $merchant_data_string . $SALT;
        $hashData_uf = hash('sha256', $format_Data_string);
        $hashData = strtoupper($hashData_uf);
        return $hashData;

    }

    public function pendingAvalancheTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $arr_gateway_ids = ['67', '77', '79', '80'];
        //$arr_gateway_ids = ['22'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->take(20)
            ->pluck('order_id')
            ->toArray();
        //\Log::info(['avalanche_gateway' => $gateway_ids]);
        //echo "<pre>";print_r($gateway_ids);exit();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('order_id', $value)
                ->where('is_completed', '0')
                ->first();

            if ($input_json == null) {
                continue;
            }
            $input = json_decode($input_json['request_data'], true);
            if (is_array($input)) {
                //\Log::info(['avalanche_cron_input' => $input]);
                $input['status'] = '0';
                $input['reason'] = 'Your transaction could not processed.';
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
            }
        }
    }

    public function getTokenAvalanche()
    {
        $curl = curl_init();
        $arr = array('email' => 'sales@testpay.com', 'password' => 'Start123!');
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://avalanchepay.com/api/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_POSTFIELDS => '{"email": "sales@testpay.com","password": "Start123!"}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }





    public function pendingBoombillTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        //$arr_gateway_ids = ['69'];
        $arr_gateway_ids = ['103'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->take(50)
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();

            if ($input_json == null) {
                continue;
            }
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            \Log::info([
                'boombill' => $input['payment_gateway_id'],
                'order_id' => $input["order_id"]
            ]);
            $data = [
                "key" => $check_assign_mid->key,
                "orderid" => $input["order_id"],
                "paymentId" => $value
            ];
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://gateway.boom-bill.com/rest/v1/paymentStatus',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_SLASHES),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Access-Control-Allow-Origin:api',
                        'APPKEY:' . $check_assign_mid->app_key,
                        'Header-Token:' . $check_assign_mid->header_token
                    ),
                )
            );
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $responseData = json_decode($response, true);
            // \Log::info([
            //     'boombill-cron-response' => $responseData,
            // ]);
            if ($responseData["success"]) {
                if ($responseData["message"] == "APPROVED") {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction was processed successfully.';
                } elseif ($responseData["message"] == "DECLINED" || $responseData["message"] == "ERROR") {
                    $input['status'] = '0';
                    $input['reason'] = (isset($responseData["data"]["gatewayResponse"]) && !empty($responseData["data"]["gatewayResponse"]) ? $responseData["data"]["gatewayResponse"] : 'Your transaction could not processed.');
                }
            }
            if (isset($input["status"])) {
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
            }
        }
        \Log::info('boombill_cron_completed');
        exit();
    }

    public function pendingPaypoundTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $arr_gateway_ids = ['21'];
        //$arr_gateway_ids = ['17'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            //->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->take(50)
            ->pluck('order_id')
            ->toArray();
        //print_r($gateway_ids);exit();
        // \Log::info([
        //     'paypound-gatewayid' => $gateway_ids,
        // ]);
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('order_id', $value)
                ->where('is_completed', '0')
                ->first();

            if ($input_json == null) {
                continue;
            }
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $data = [
                "api_key" => $check_assign_mid->api_key,
                "customer_order_id" => $input_json["order_id"],
            ];
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://portal.paypound.ltd/api/get-transaction-details',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $data,
                )
            );
            $response = curl_exec($curl);
            // \Log::info([
            //     'paypound-cron-response' => $response,
            // ]);
            $err = curl_error($curl);
            $responseData = json_decode($response, true);
            if (isset($responseData["status"]) && $responseData["status"] == "fail") {
                $input['status'] = '0';
                $input['reason'] = (isset($responseData["message"]) && !empty($responseData["message"]) ? $responseData["message"] : 'Your transaction could not processed.');
            } else if (isset($responseData["status"]) && $responseData["status"] == "success") {
                if (isset($responseData["transaction"]["transaction_status"]) && $responseData["transaction"]["transaction_status"] == "success") {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction was processed successfully.';
                } else if (isset($responseData["transaction"]["transaction_status"]) && ($responseData["transaction"]["transaction_status"] == "declined") || ($responseData["transaction"]["transaction_status"] == "blocked")) {
                    $input['status'] = '0';
                    $input['reason'] = (isset($responseData["transaction"]["reason"]) && !empty($responseData["transaction"]["reason"]) ? $responseData["transaction"]["reason"] : 'Your transaction could not processed.');
                }
            }
            if (isset($input["status"])) {
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
            }
        }
        //echo "<pre>";print_r($gateway_ids);exit();
        \Log::info('paypound_cron_completed');
        exit();
    }

    public function honeypayPendingTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        //$arr_gateway_ids = ['89'];
        $arr_gateway_ids = ['9', '22', '23'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->take(150)
            ->pluck('gateway_id')
            ->toArray();
        // \Log::info([
        //     'honeypay-cron-gateway-response' => $gateway_ids,
        // ]);
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();
            if ($input_json == null) {
                continue;
            }
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://pay.pay-gate.io/payment-invoices/' . $value,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_USERPWD => $check_assign_mid->account_id . ':' . $check_assign_mid->password,
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $response_data = json_decode($response);
            // \Log::info([
            //     'honeypay-response' => $response_data,
            // ]);
            if (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'processed') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } elseif (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'process_failed') {
                $input['status'] = '0';
                $input['reason'] = 'Transaction authentication failed.';
            } elseif (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'process_pending') {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';
            }
            // \Log::info([
            //     'honeypay-input' => $input,
            // ]);
            if (isset($input['status'])) {
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('gateway_id', $value)
                    ->update(['is_completed' => '1']);
            }
        }
        \Log::info('honeypay_cron_completed');
        exit();
    }

    public function pendingTransactionStatusChangeHoneypay(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        //$arr_gateway_ids = ['89'];
        $arr_gateway_ids = ['9', '22', '23'];
        $gateway_ids = \DB::table('transactions')
            ->where('status', '2')
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull('gateway_id')
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)->first();
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://pay.pay-gate.io/payment-invoices/' . $value,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_USERPWD => $check_assign_mid->account_id . ':' . $check_assign_mid->password,
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $response_data = json_decode($response);
            if (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'processed') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } elseif (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'process_failed') {
                $input['status'] = '0';
                $input['reason'] = 'Transaction authentication failed.';
            }
            if (isset($input['status'])) {
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
                    $request_data['currency'] = $input['currency'];
                    $request_data['amount'] = $input['amount'];
                    $request_data['transaction_date'] = date("Y-m-d H:i:s", strtotime($input_json['created_at']));
                    $request_data["descriptor"] = $check_assign_mid->descriptor;
                    // send webhook request
                    try {
                        $http_response = postCurlRequest($input['webhook_url'], $request_data);
                    } catch (Exception $e) {
                        $http_response = 'FAILED';
                        \Log::info([
                            'webhook error pending' => $e->getMessage()
                        ]);
                    }
                }
                \DB::table("transactions")->where("session_id", $input['session_id'])->update(["status" => $input['status'], "reason" => $input['reason']]);
            }
        }
        \Log::info('honeypay_status_cron_completed');
        exit();
    }

    public function pendingdaysTransactionStatusChangeHoneypay(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        //$arr_gateway_ids = ['89'];
        $arr_gateway_ids = ['9', '22', '23'];
        $gateway_ids = \DB::table('transactions')
            ->where('status', '2')
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull('gateway_id')
            ->where('created_at', '<', Carbon::now()->subDays(2)->toDateTimeString())
            ->take(150)
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)->first();
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://pay.pay-gate.io/payment-invoices/' . $value,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_USERPWD => $check_assign_mid->account_id . ':' . $check_assign_mid->password,
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $response_data = json_decode($response);
            if (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'processed') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } elseif (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'process_failed') {
                $input['status'] = '0';
                $input['reason'] = isset($response_data->data->attributes->resolution) ? $response_data->data->attributes->resolution : 'Transaction authentication failed.';
            } else {
                $input['status'] = '0';
                $input['reason'] = 'Transaction authentication failed.';
            }
            if (isset($input['status'])) {
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
                    $request_data['currency'] = $input['currency'];
                    $request_data['amount'] = $input['amount'];
                    $request_data['transaction_date'] = date("Y-m-d H:i:s", strtotime($input_json['created_at']));
                    $request_data["descriptor"] = $check_assign_mid->descriptor;
                    // send webhook request
                    try {
                        $http_response = postCurlRequest($input['webhook_url'], $request_data);
                    } catch (Exception $e) {
                        $http_response = 'FAILED';
                        \Log::info([
                            'webhook error pending' => $e->getMessage()
                        ]);
                    }
                }
                \DB::table("transactions")->where("session_id", $input['session_id'])->update(["status" => $input['status'], "reason" => $input['reason']]);
            }
        }
        \Log::info('honeypay_status_cron_completed');
        exit();
    }

    public function everpayPendingTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        //$arr_gateway_ids = ['89'];
        $arr_gateway_ids = ['9', '22', '23'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->take(150)
            ->pluck('gateway_id')
            ->toArray();
        // \Log::info([
        //     'honeypay-cron-gateway-response' => $gateway_ids,
        // ]);
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();
            if ($input_json == null) {
                continue;
            }
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://platform.everpayinc.com/payment-invoices/' . $value,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_USERPWD => $check_assign_mid->account_id . ':' . $check_assign_mid->password,
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $response_data = json_decode($response);
            // \Log::info([
            //     'honeypay-response' => $response_data,
            // ]);
            if (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'processed') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } elseif (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'process_failed') {
                $input['status'] = '0';
                $input['reason'] = 'Transaction authentication failed.';
            } elseif (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'process_pending') {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';
            }
            // \Log::info([
            //     'honeypay-input' => $input,
            // ]);
            if (isset($input['status'])) {
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);
                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('gateway_id', $value)
                    ->update(['is_completed' => '1']);
            }
        }
        \Log::info('everpay_cron_completed');
        exit();
    }

    public function pendingTransactionStatusChangeEverpay(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        //$arr_gateway_ids = ['89'];
        $arr_gateway_ids = ['9', '22', '23'];
        $gateway_ids = \DB::table('transactions')
            ->where('status', '2')
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull('gateway_id')
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)->first();
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://platform.everpayinc.com/payment-invoices/' . $value,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_USERPWD => $check_assign_mid->account_id . ':' . $check_assign_mid->password,
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $response_data = json_decode($response);
            if (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'processed') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } elseif (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'process_failed') {
                $input['status'] = '0';
                $input['reason'] = 'Transaction authentication failed.';
            }
            if (isset($input['status'])) {
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
                    $request_data['currency'] = $input['currency'];
                    $request_data['amount'] = $input['amount'];
                    $request_data['transaction_date'] = date("Y-m-d H:i:s", strtotime($input_json['created_at']));
                    $request_data["descriptor"] = $check_assign_mid->descriptor;
                    // send webhook request
                    try {
                        $http_response = postCurlRequest($input['webhook_url'], $request_data);
                    } catch (Exception $e) {
                        $http_response = 'FAILED';
                        \Log::info([
                            'webhook error pending' => $e->getMessage()
                        ]);
                    }
                }
                \DB::table("transactions")->where("session_id", $input['session_id'])->update(["status" => $input['status'], "reason" => $input['reason']]);
            }
        }
        \Log::info('everpay_status_cron_completed');
        exit();
    }

    public function pendingdaysTransactionStatusChangeEverpay(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        //$arr_gateway_ids = ['89'];
        $arr_gateway_ids = ['9', '22', '23'];
        $gateway_ids = \DB::table('transactions')
            ->where('status', '2')
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull('gateway_id')
            ->where('created_at', '<', Carbon::now()->subDays(2)->toDateTimeString())
            ->take(150)
            ->pluck('gateway_id')
            ->toArray();
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)->first();
            $input = json_decode($input_json['request_data'], true);
            $check_assign_mid = checkAssignMid($input['payment_gateway_id']);
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://platform.everpayinc.com/payment-invoices/' . $value,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_USERPWD => $check_assign_mid->account_id . ':' . $check_assign_mid->password,
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            $response_data = json_decode($response);
            if (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'processed') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } elseif (isset($response_data->data->attributes->status) && $response_data->data->attributes->status == 'process_failed') {
                $input['status'] = '0';
                $input['reason'] = isset($response_data->data->attributes->resolution) ? $response_data->data->attributes->resolution : 'Transaction authentication failed.';
            } else {
                $input['status'] = '0';
                $input['reason'] = 'Transaction authentication failed.';
            }
            if (isset($input['status'])) {
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
                    $request_data['currency'] = $input['currency'];
                    $request_data['amount'] = $input['amount'];
                    $request_data['transaction_date'] = date("Y-m-d H:i:s", strtotime($input_json['created_at']));
                    $request_data["descriptor"] = $check_assign_mid->descriptor;
                    // send webhook request
                    try {
                        $http_response = postCurlRequest($input['webhook_url'], $request_data);
                    } catch (Exception $e) {
                        $http_response = 'FAILED';
                        \Log::info([
                            'webhook error pending' => $e->getMessage()
                        ]);
                    }
                }
                \DB::table("transactions")->where("session_id", $input['session_id'])->update(["status" => $input['status'], "reason" => $input['reason']]);
            }
        }
        \Log::info('everpay_status_cron_completed');
        exit();
    }

    public function changeTransactionStatusOculus($input_json, $value)
    {
        $request_url = "https://prod.mycardstorage.com/api/api.asmx";
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

        if (!isset($input['converted_amount'])) {
            $input['converted_amount'] = $input['amount'];
        }

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $request_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:myc="https://MyCardStorage.com/">
            <soapenv:Header>
                <myc:AuthHeader>
                    <myc:ApiKey>' . $check_assign_mid->api_key . '</myc:ApiKey>
                </myc:AuthHeader>
            </soapenv:Header>
            <soapenv:Body>
                <myc:GetTransactionResult_Soap>
                    <myc:ccSearch>
                        <myc:ServiceSecurity>
                            <myc:MCSAccountID>' . $check_assign_mid->msc_account_id . '</myc:MCSAccountID>
                        </myc:ServiceSecurity>
                        <myc:TransactionCCSearchData>
                            <myc:Amount>' . $input['converted_amount'] . '</myc:Amount>
                            <myc:TicketNumber>' . $input['session_id'] . '</myc:TicketNumber>
                        </myc:TransactionCCSearchData>
                    </myc:ccSearch>
                </myc:GetTransactionResult_Soap>
            </soapenv:Body>
            </soapenv:Envelope>',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: text/xml; charset=utf-8',
                    'Cache-Control: no-cache'
                ),
            )
        );
        $response = curl_exec($curl);
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $response);
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $response_data = json_decode($json, true);

        $data = [];
        $data['input'] = $input;
        $data['response_data'] = $response_data;

        return $data;

    }

    public function oculusPendingTransactionStatusChange(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }
        $arr_gateway_ids = ['25'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->take(10)
            ->pluck('gateway_id')
            ->toArray();
        // \Log::info([
        //     'Oculus-cron-gateway' => $gateway_ids
        // ]);
        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();
            if ($input_json == null) {
                continue;
            }
            $data = $this->changeTransactionStatusOculus($input_json, $value);
            $input = $data['input'];
            $response_data = $data['response_data'];

            if (isset($response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultCode']) && $response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultCode'] == 0) {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            } else if (isset($response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultCode']) && $response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultCode'] == 1) {
                $input['status'] = '0';
                $input['reason'] = $response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultDetail'] ?? 'Transaction authentication failed.';
            } else if (isset($response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultCode']) && $response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultCode'] == 2) {
                $input['status'] = '0';
                $input['reason'] = $response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultDetail'] ?? 'Transaction authentication failed.';
            }

            if (isset($input['status'])) {
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);

                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('gateway_id', $value)
                    ->update(['is_completed' => '1']);
            }
        }
        \Log::info('oculus_cron_completed');
        exit();
    }

    public function oculusPendingTransactionStatusToDecline(Request $request)
    {
        if ($request->password != 'fnsdk34naSdkc23VC111sShiu235Ha') {
            exit();
        }

        $arr_gateway_ids = ['25'];
        $gateway_ids = \DB::table("transaction_session")
            ->where("is_completed", "0")
            ->whereIn('payment_gateway_id', $arr_gateway_ids)
            ->whereNotNull("gateway_id")
            ->where('gateway_id', '!=', '1')
            ->where('created_at', '<', Carbon::now()->subMinutes(120)->toDateTimeString())
            ->pluck('gateway_id')
            ->toArray();

        foreach ($gateway_ids as $key => $value) {
            $input_json = TransactionSession::where('gateway_id', $value)
                ->where('is_completed', '0')
                ->first();
            if ($input_json == null) {
                continue;
            }

            $data = $this->changeTransactionStatusOculus($input_json, $value);

            $input = $data['input'];
            $response_data = $data['response_data'];

            if (isset($response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultCode']) && $response_data['soapBody']['GetTransactionResult_SoapResponse']['GetTransactionResult_SoapResult']['TransacationResults']['TransactionCCSearchResultData']['ResultCode'] == 4) {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was not completed.';
            }

            if (isset($input['status'])) {
                unset($input['api_key']);
                unset($input['country_code']);
                unset($input['is_disable_rule']);
                unset($input['bin_country_code']);
                unset($input['request_from_type']);

                $this->Transaction->storeData($input);
                \DB::table('transaction_session')
                    ->where('gateway_id', $value)
                    ->update(['is_completed' => '1']);
            }
        }
        \Log::info('oculus_cron_decline_completed');
        exit();
    }
}