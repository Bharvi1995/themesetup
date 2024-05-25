<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class Itexpay extends Controller
{
    use StoreTransaction;

    protected $transaction;

    const BASE_URL = 'https://staging.itexpay.com/api/v1';
    const SECRET_HASH = '92db778a641311eeae50a27756c086e3';

    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {
        $token_url = self::BASE_URL . '/direct/transaction/authenticate';
        $token_data = [
            'publickey' => $check_assign_mid->public_key,
            'privatekey' => $check_assign_mid->private_key
        ];

        $token_response = Http::withHeaders(["Content-Type" => "application/json"])->post($token_url, $token_data)->json();

        // store gateway response
        $input['gateway_id'] = 1;
        $this->updateGatewayResponseData($input, $token_response, json_encode($token_data));

        if (isset($token_response['access_token']) && !empty($token_response['access_token'])) {
            $encrypt_array = [
                'card_no' => $input['card_no'],
                'cvvNumber' => $input['cvvNumber'],
            ];
            $encrypt_json = Crypt::encryptString(json_encode($encrypt_array));
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => route('itexpay.form', ['id' => $input['session_id'], 'encrypt' => $encrypt_json])
            ];
        } elseif (isset($token_response['message']) && !empty($token_response['message'])) {
            return [
                'status' => 0,
                'reason' => $token_response['message'] ?? 'Transaction initialization failed.',
            ];
        } else {
            return [
                'status' => 0,
                'reason' => 'Transaction initialization failed.',
            ];
        }
    }

    public function form($session_id, $encrypt)
    {
        $transaction_session = DB::table('transaction_session')
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('transaction_id', $session_id)
            ->first();
        if (empty($transaction_session)) {
            abort(404);
        }
        try {
            $decrypt_json = Crypt::decryptString(json_encode($encrypt));
        } catch (\Exception $e) {
            abort(404);
        }
        $response = json_decode($transaction_session->response_data, 1);

        if (isset($response['access_token']) && !empty($response['access_token'])) {
            return view('gateway.itexpay.form', compact('session_id', 'encrypt'));
        } else {
            abort(404);
        }
    }

    public function submit(Request $request, $session_id, $encrypt)
    {
        $transaction_session = DB::table('transaction_session')
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('transaction_id', $session_id)
            ->first();
        if (empty($transaction_session)) {
            abort(404);
        }
        try {
            $decrypt_json = Crypt::decryptString(json_encode($encrypt));
        } catch (\Exception $e) {
            abort(404);
        }

        // card data
        $card_data = json_decode($decrypt_json, true);

        $input = json_decode($transaction_session->request_data, 1);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $token_response = json_decode($transaction_session->response_data, true);

        if (
            isset($input) && !empty($input) &&
            isset($token_response['access_token']) && !empty($token_response['access_token'])
        ) {
            $charge_url = self::BASE_URL . '/direct/transaction/charge';
            $charge_data = [
                'transaction' => [
                    'merchantreference' => $input['session_id'],
                    'callbackurl' => route('itexpay.callback', $input['session_id']),
                    'redirecturl' => route('itexpay.redirect', $input['session_id']),
                    'authoption' => '3DS',
                    'paymentmethod' => 'card',
                ],
                'order' => [
                    'amount' => $input['converted_amount'],
                    'description' => 'testpay transaction ' . $input["order_id"],
                    'currency' => $input['converted_currency'],
                    'country' => $input['country'],
                ],
                'source' => [
                    'customer' => [
                        'firstname' => $input['first_name'],
                        'lastname' => $input['last_name'],
                        'email' => $input['email'],
                        'msisdn' => $this->getFormatNumber($input),
                        'card' => [
                            'number' => $card_data['card_no'],
                            'expirymonth' => $input['ccExpiryMonth'],
                            'expiryyear' => str_replace('20', '', $input['ccExpiryYear']),
                            'cvv' => $card_data['cvvNumber'],
                        ],
                        'device' => [
                            'fingerprint' => $request->device,
                            'ip' => $input['ip_address'],
                        ]
                    ],
                ],
            ];
            $encryptedPublicKey = $check_assign_mid->encryption_key;
            $charge_json = json_encode($charge_data, JSON_UNESCAPED_SLASHES);

            $enc_data = $this->encryptData($charge_json, $encryptedPublicKey);

            if (
                isset($enc_data['ctx']) && !empty($enc_data['ctx']) &&
                isset($enc_data['data']) && !empty($enc_data['data'])
            ) {

                $response = Http::withHeaders(["Content-Type" => "application/json", "Authorization" => "Bearer " . $token_response['access_token']])->post($charge_url, $enc_data)->json();

                // $encrypt_payload = json_encode($enc_data, JSON_UNESCAPED_SLASHES);
                // $headers = [
                //     'Content-Type: application/json',
                //     'Authorization: Bearer ' . $token_response['access_token'],
                // ];
                // $curl = curl_init();
                // curl_setopt($curl, CURLOPT_URL, $charge_url);
                // curl_setopt($curl, CURLOPT_POST, true);
                // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                // curl_setopt($curl, CURLOPT_POSTFIELDS, $encrypt_payload);
                // $response_json = curl_exec($curl);
                // curl_close($curl);
                // // dd(['charge_url' => $charge_url, 'charge_json' => $charge_json, 'encrypt_payload' => $encrypt_payload, 'response_json' => $response_json]);

                // $response = json_decode($response_json, true);

                $input['gateway_id'] = $response['transaction']['reference'] ?? 1;
                $this->updateGatewayResponseData($input, $response);

                // * Store Mid payload
                $charge_data["source"]["customer"]["card"]["number"] = cardMasking($charge_data["source"]["customer"]["card"]["number"]);
                $charge_data["source"]["customer"]["card"]["cvv"] = "XXX";
                $this->storeMidPayload($input["session_id"], json_encode($charge_data));

                if (isset($response['transaction']['redirecturl']) && !empty($response['transaction']['redirecturl'])) {
                    return redirect()->away($response['transaction']['redirecturl']);
                } elseif (isset($response['code']) && $response['code'] == '06') {
                    $input['status'] = '0';
                    $input['reason'] = $response['message'] ?? 'Something went wrong in encyption data.';
                } else {
                    $input['status'] = '0';
                    $input['reason'] = $response['message'] ?? 'Something went wrong in encyption data.';
                }
            } else {
                $input['status'] = '0';
                $input['reason'] = 'Something went wrong in encyption data.';
            }

            $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        } else {
            abort(404);
        }
    }

    public function redirect(Request $request, $session_id)
    {
        $transaction_session = DB::table('transaction_session')
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('transaction_id', $session_id)
            ->first();
        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, 1);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $get_status = $this->transactionStatus($input, $check_assign_mid);
        // \Log::info(['itexpay_callback_status' => $get_status]);

        $messages = $this->getMessages();
        if (isset($get_status['code']) && $get_status['code'] == '00') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction processed successfully.';
        } elseif (isset($get_status['code']) && array_key_exists($get_status['code'], $messages)) {
            $input['status'] = '0';
            $input['reason'] = $messages[$get_status['code']];
        } else {
            $input['status'] = '0';
            $input['reason'] = $get_status['message'] ?? 'Your transaction could not processed.';
            // \Log::info(['itexpay_callback_status_else' => $get_status]);
        }

        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        $this->updateGatewayResponseData($input, $get_status);
        return redirect($store_transaction_link);
    }

    public function callback(Request $request)
    {
        $response_data = $request->all();
        $token = request()->bearerToken();

        // request is not from itexpay
        if ($token !== self::SECRET_HASH) {
            exit();
        }
        // \Log::info(['itexpay_callback' => $response_data]);

        $this->storeMidWebhook($response_data['data']['merchantreference'], json_encode($response_data));

        $transaction_session = DB::table('transaction_session')
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('transaction_id', $response_data['data']['merchantreference'])
            ->first();
        if (empty($transaction_session)) {
            abort(404);
        }

        $input = json_decode($transaction_session->request_data, 1);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $get_status = $this->transactionStatus($input, $check_assign_mid);
        // \Log::info(['itexpay_callback_status' => $get_status]);

        $messages = $this->getMessages();
        if (isset($get_status['code']) && $get_status['code'] == '00') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction processed successfully.';
        } elseif (isset($get_status['code']) && array_key_exists($get_status['code'], $messages)) {
            $input['status'] = '0';
            $input['reason'] = $messages[$get_status['code']];
        } else {
            $input['status'] = '0';
            $input['reason'] = $get_status['message'] ?? 'Your transaction could not processed.';
            // \Log::info(['itexpay_callback_status_else' => $get_status]);
        }

        $this->storeTransaction($input);
        http_response_code(200);
        exit();
    }

    private function transactionStatus($input, $check_assign_mid)
    {
        $url = self::BASE_URL . '/transaction/status?merchantreference=' . $input['session_id'];
        $response = Http::withHeaders(["Authorization" => "Bearer " . $check_assign_mid->public_key])->get($url)->json();
        return $response;
    }

    private function encryptData($transactionData, $encryptedPublicKey)
    {
        try {
            // Generate AES key
            $secKey = openssl_random_pseudo_bytes(16); // 128 bits for AES-128
            // Convert AES key to Base64
            $SencryptKey = base64_encode($secKey);
            $keySpec = "-----BEGIN PUBLIC KEY-----
$encryptedPublicKey
-----END PUBLIC KEY-----";
            $publicKey = openssl_pkey_get_public($keySpec);

            // Encrypt AES key with RSA
            openssl_public_encrypt($SencryptKey, $encryptedKey, $publicKey, OPENSSL_PKCS1_OAEP_PADDING);
            $encryptedKey = base64_encode($encryptedKey);

            // Encrypt data with AES
            $encrypteddata = openssl_encrypt($transactionData, 'AES-128-ECB', $secKey, OPENSSL_RAW_DATA);
            $encryptedData = base64_encode($encrypteddata);

            return ['ctx' => $encryptedKey, 'data' => $encryptedData];

        } catch (\Exception $e) {
            // Handle the exception
            \Log::info(['itexpay_encryption_error' => $e->getMessage()]);
            return false;
        }
    }

    public function getFormatNumber(array $input)
    {
        if (strpos($input["phone_no"], '+') === 0) {
            return $input["phone_no"];
        } else {
            return getPhoneCode($input["country"]) . $input["phone_no"];
        }
    }
    private function getMessages()
    {
        $messages = [
            '00' => 'Approved',
            '96' => 'system malfunction',
            '09' => 'pending external',
            'Q1' => 'card authentication failed',
            '01' => 'Refer to card issuer',
            '65' => 'authentication required',
            '504' => 'transaction timeout',
            '03' => 'invalid merchant',
            '04' => 'capture card / pick-up',
            '05' => 'transaction was declined, please try again',
            '06' => 'error',
            'fail' => 'upload failed',
            '07' => 'pickup card, special condition',
            '10' => 'partial approval',
            '12' => 'invalid transaction',
            '13' => 'invalid amount',
            '14' => 'invalid card number',
            '15' => 'invalid issuer',
            '1A' => 'additional customer authentication required',
            '30' => 'format error',
            '41' => 'lost card',
            '43' => 'stolen card',
            '51' => 'insufficient funds',
            '54' => 'expired card',
            '55' => 'invalid pin',
            '57' => 'transaction not permitted to cardholder',
            '58' => 'transaction not permitted to cardholder',
            '59' => 'suspected fraud',
            '61' => 'exceeds withdrawal amount limit',
            '62' => 'restricted card',
            '63' => 'security violation',
            '75' => 'allowable number of pin tries exceeded',
            '78' => 'invalid/nonexistent account specified (general)',
            '80' => 'credit issuer unavailable',
            '85' => 'not declined (valid for all zero amount transactions)',
            '91' => 'issuer unavailable or switch inoperative',
            '92' => 'unable to route transaction',
            '93' => 'transaction cannot be completed; violation of law',
        ];

        return $messages;
    }
}
