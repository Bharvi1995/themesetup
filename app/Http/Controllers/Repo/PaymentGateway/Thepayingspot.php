<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Jobs\HSRTransactionRestoreJob;
use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;

class Thepayingspot extends Controller
{
    use StoreTransaction;

    protected $transaction;
    const BASE_URL = 'https://api.thepayingspot.com/api/Payment/Create'; // live
    const STATUS_API = "https://api.thepayingspot.com/api/Payment/GetTxStatus";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    // ================================================
    /* method : transaction
     * @param  : 
     * @Description : wonderland api call
     */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        try {
            if ($input['card_type'] == '1') {
                $CardType = 'AMEX';
            } elseif ($input['card_type'] == '2') {
                $CardType = 'VISA';
            } elseif ($input['card_type'] == '3') {
                $CardType = 'MASTERCARD';
            } elseif ($input['card_type'] == '4') {
                $CardType = 'DISCOVER';
            }

            $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
            $data = [
                'firstname' => $input['first_name'],
                'lastname' => $input['last_name'],
                'address' => $input['address'],
                'orderid' => $input['order_id'],
                'country' => $input['country'],
                'state' => $input['state'],
                // if your country US then use only 2 letter state code.
                'city' => $input['city'],
                'postalcode' => $input['zip'],
                'clientip' => $input['ip_address'],
                'email' => $input['email'],
                'phone' => $input['phone_no'],
                'amount' => $input['converted_amount'],
                'currency' => $input['converted_currency'],
                'cardtype' => $CardType,
                'cardnumber' => $input['card_no'],
                'expirymonth' => $input['ccExpiryMonth'],
                'expiryyear' => str_replace("20", "", $input['ccExpiryYear']),
                'cvv' => $input['cvvNumber'],
                'cardname' => $input['first_name'] . " " . $input['last_name'],
                'callbackurl' => route('thepayingspot.callback', $input['session_id']),
                // 'callbackurl' => "https://webhook.site/67efe271-cca5-40b6-9d54-6e2bdead8db7",
                'returnurl' => route('thepayingspot.return', $input['session_id']),
            ];

            $sigString = $input['first_name'] . $input['last_name'] . $input['phone_no'] . $input['email'] . $input['converted_amount'];
            $sig256 = hash('sha256', $sigString . $check_assign_mid->secret);
            $signature = hash_hmac('sha512', $sig256, $check_assign_mid->key);

            $request_url = self::BASE_URL;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $request_url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'TPS-KEY: ' . $check_assign_mid->key,
                'TPS-SIGNATURE: ' . $signature,
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $responseData = json_decode($response, true);
            $input['gateway_id'] = (isset($responseData['transactionresponse']['tx_id']) && !empty($responseData['transactionresponse']['tx_id'])) ? $responseData['transactionresponse']['tx_id'] : null;

            // * Store the mid request payload 
            $data["cardnumber"] = cardMasking($data["cardnumber"]);
            $data["cvv"] = "XXX";
            $this->storeMidPayload($input["session_id"], json_encode($data));

            $this->updateGatewayResponseData($input, $responseData);

            if (empty($responseData)) {
                return [
                    'status' => '0',
                    'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                    'order_id' => $input['order_id'],
                ];
            }

            if (isset($responseData['status']) && $responseData['status'] == 'success') {
                if (isset($responseData['transactionresponse'])) {
                    if (isset($responseData['transactionresponse']['tx_status']) && $responseData['transactionresponse']['tx_status'] == "APPROVED") {
                        return [
                            'status' => '1',
                            'reason' => 'Your transaction has been processed successfully.',
                            'order_id' => $input['order_id']
                        ];
                    } elseif (isset($responseData['transactionresponse']['tx_status']) && $responseData['transactionresponse']['tx_status'] == "DECLINED") {
                        $message = "";
                        if (isset($responseData['message']) && !empty($responseData['message'])) {
                            $message = $responseData['message'];
                        } elseif (isset($responseData['transactionresponse']['tx_message']) && !empty($responseData['transactionresponse']['tx_message'])) {
                            $message = $responseData['transactionresponse']['tx_message'];
                        } else {
                            $message = "Transaction was declined by bank.";
                        }

                        return [
                            'status' => '0',
                            'reason' => $message,
                            'order_id' => $input['order_id'],
                        ];
                    } else {
                        if (isset($responseData['transactionresponse']['tx_paymenturl']) && !empty($responseData['transactionresponse']['tx_paymenturl'])) {
                            return [
                                'status' => '7',
                                'reason' => '3DS link generated successful, please redirect.',
                                'redirect_3ds_url' => $responseData['transactionresponse']['tx_paymenturl']
                            ];
                        } else {
                            return [
                                'status' => '2',
                                'reason' => 'Your transaction was pending from the bank side please contact us for more details.',
                                'order_id' => $input['order_id'],
                            ];
                        }
                    }
                }
            } elseif (isset($responseData['txstatus']) && $responseData['txstatus'] == 'DECLINED') {
                return [
                    'status' => '0',
                    'reason' => $responseData['tx_message'] ?? "Transaction was declined by bank. please contact us for more details.",
                    'order_id' => $input['order_id'],
                ];
            } else {
                return [
                    'status' => '0',
                    'reason' => (isset($responseData['message']) && !empty(isset($responseData['message']))) ? $responseData['message'] : "Transaction was declined by bank. please contact us for more details.",
                    'order_id' => $input['order_id'],
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => '0',
                'reason' => $e->getMessage(),
                // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function redirectUrl(Request $request, $id)
    {
        $response = $request->all();
        $transaction_session = \DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, 1);
        $transactionStatus = $this->getTransactionStatus($response['txid'], $input);

        if (isset($transactionStatus['status']) && $transactionStatus['status'] == 'success') {
            if (isset($transactionStatus['transaction']['tran_status']) && $transactionStatus['transaction']['tran_status'] == "APPROVED") {
                $input['status'] = '1';
                $input['reason'] = "Your transaction has been processed successfully.";
            } elseif (isset($transactionStatus['transaction']['tran_status']) && $transactionStatus['transaction']['tran_status'] == "PENDING") {
                $input['status'] = '2';
                $input['reason'] = (isset($transactionStatus['transaction']['tran_message']) && !empty($transactionStatus['transaction']['tran_message'])) ? $transactionStatus['transaction']['tran_message'] : "Transaction is pending in bank, Please contact us for more details.";
            } elseif (isset($transactionStatus['transaction']['tran_status']) && $transactionStatus['transaction']['tran_status'] == "DECLINED") {
                $input['status'] = '0';
                $input['reason'] = (isset($transactionStatus['transaction']['tran_message']) && !empty($transactionStatus['transaction']['tran_message'])) ? $transactionStatus['transaction']['tran_message'] : "Transaction was declined by bank, Please contact us for more details.";
            } else {
                $input['status'] = '0';
                $input['reason'] = isset($transactionStatus['transaction']['tran_message']) ? $transactionStatus['transaction']['tran_message'] : "Transaction was declined by bank, Please contact us for more details.";
            }

        } else {
            $input['status'] = '0';
            $input['reason'] = (isset($transactionStatus['message']) && !empty($transactionStatus['message'])) ? $transactionStatus['message'] : "Transaction was declined by bank, Please contact us for more details.";
        }

        // store transaction
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);

    }

    public function getTransactionStatus($txid, $input)
    {
        $check_assign_mid = checkAssignMid($input['payment_gateway_id']);

        $data = [
            'txid' => $txid
        ];
        $sigString = $txid;
        $sig256 = hash('sha256', $sigString . $check_assign_mid->secret);
        $signature = hash_hmac('sha512', $sig256, $check_assign_mid->key);

        $requestURL = "https://api.thepayingspot.com/api/Payment/GetTxStatus";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestURL);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'TPS-KEY: ' . $check_assign_mid->key,
            'TPS-SIGNATURE: ' . $signature,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, true);
        return $responseData;

    }

    public function webhookDetails(Request $request, $id)
    {
        $response = $request->all();
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        // * Store webhook to DB
        $this->storeMidWebhook($id, json_encode($response));
        $input = json_decode($input_json['request_data'], true);

        if (isset($response['txstatus']) && $response['txstatus'] == "APPROVED") {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } elseif (isset($response['txstatus']) && $response['txstatus'] == "PENDING") {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is pending in bank, Please contact us for more details';
        } elseif (isset($response['txstatus']) && $response['txstatus'] == "DECLINED") {
            $message = "";
            if (isset($response['res_message']) && !empty($response['res_message'])) {
                $message = $response['res_message'];
            } elseif (isset($response['tx_message']) && !empty($response['tx_message'])) {
                $message = $response['tx_message'];
            }

            $input['status'] = '0';
            $input['reason'] = !empty($message) ? $message : "Transaction was declined by bank, Please contact us for more details.";
        } else {
            $input['status'] = '0';
            $input['reason'] = isset($response['res_message']) ? $response['res_message'] : "Transaction was declined by bank, Please contact us for more details.";
        }

        $input['gateway_id'] = $response['txid'];
        // $this->updateGatewayResponseData($input, $response);
        $this->storeTransaction($input);

    }

    // * Transaction CRON to restore the pending transactions
    public function restoreTransactions(Request $request)
    {
        if ($request->password != '6fe14a8a47a987d362d8aebcf686234c20ad98b8758c') {
            exit();
        }
        try {
            $midId = "6"; // Live mid id
            // $midId = "25"; // Local mid id
            $check_assign_mid = checkAssignMid($midId);
            // * Call the transaction restore job
            HSRTransactionRestoreJob::dispatch($check_assign_mid, self::STATUS_API);
            return response()->json(["status" => 200, "message" => "Cron Processed successfully!"]);
        } catch (\Exception $err) {
            Log::error(["HSR-CRON-error" => $err->getMessage()]);
            return response()->json(["status" => 200, "message" => "Something went wrong!"]);

        }

    }
}