<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\Transaction;
use App\TransactionSession;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Http;

class Paymob extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://uae.paymob.com';

    public function checkout($input, $check_assign_mid)
    {
        $converted_amount = (int) intval($input['converted_amount'] * 100, FILTER_SANITIZE_NUMBER_INT);
        $int_url = self::BASE_URL . '/v1/intention/';
        $int_headers = [
            'Authorization: Token ' . $check_assign_mid->secret_key,
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        $int_data = [
            'amount' => $converted_amount,
            'currency' => $input['converted_currency'],
            'payment_methods' => [
                0 => (int) $check_assign_mid->payment_id
            ],
            'items' => [
                0 => [
                    'name' => 'Item ID: ' . $input['session_id'],
                    'amount' => $converted_amount,
                    'description' => $input['session_id'],
                    'quantity' => '1',
                ]
            ],
            'billing_data' => [
                'apartment' => $input['address'],
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'street' => $input['address'],
                'building' => $input['address'],
                'phone_number' => $input['phone_no'],
                'city' => $input['city'],
                'country' => $input['country'],
                'email' => $input['email'],
                'floor' => rand(2, 20),
                'state' => $input['state'],
            ],
            'extras' => [
                'billing_data' => [
                    'apartment' => $input['address'],
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'street' => $input['address'],
                    'building' => $input['address'],
                    'phone_number' => $input['phone_no'],
                    'city' => $input['city'],
                    'country' => $input['country'],
                    'email' => $input['email'],
                    'floor' => rand(2, 20),
                    'state' => $input['state'],
                ],
            ]
        ];
        $int_json = json_encode($int_data);

        // * Store mid payload
        $this->storeMidPayload($input["session_id"], json_encode($int_data));

        $int_response = $this->curlPost($int_url, $int_headers, $int_json);

        $input['gateway_id'] = '1';
        $this->updateGatewayResponseData($input, $int_response);
        // \Log::info(['$int_url' => $int_url, '$int_headers' => $int_headers, '$int_json' => $int_json, '$int_response' => json_encode($int_response)]);

        if (isset($int_response['client_secret']) && !empty($int_response['client_secret'])) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => self::BASE_URL . '/unifiedcheckout/?publicKey=' . $check_assign_mid->public_key . '&clientSecret=' . $int_response['client_secret']
            ];
        } elseif (isset($int_response['detail']) && !empty($int_response['detail'])) {
            return [
                'status' => '0',
                'reason' => $int_response['detail'],
                'order_id' => $input['order_id']
            ];
        } else {
            \Log::info(['paymob_status_else' => $int_response]);
            return [
                'status' => '0',
                'reason' => 'Could not create the payment.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function redirect(Request $request)
    {
        $request_data = $request->all();

        if (isset($request_data['id']) && !empty($request_data['id'])) {
            $gateway_id = $request_data['id'];

            $transaction_session = DB::table("transaction_session")
                ->whereIn('payment_gateway_id', ['8'])
                ->where('gateway_id', $gateway_id)
                ->orderBy('id', 'desc')
                ->first();

            if (empty($transaction_session)) {
                return abort(404);
            }

            $session_input = json_decode($transaction_session->request_data, true);

            $input = Transaction::where('gateway_id', $gateway_id)
                ->whereIn('payment_gateway_id', ['8'])
                ->orderBy('id', 'desc')
                ->first();

            $store_transaction_link = $this->getRedirectLink($input);

            return redirect($store_transaction_link);
        } else {
            abort(404);
        }
    }

    public function webhook(Request $request)
    {
        $request_data = $request->all();

        if (isset($request_data['intention']['intention_detail']['items'][0]['description']) && !empty($request_data['intention']['intention_detail']['items'][0]['description'])) {
            $id = $request_data['intention']['intention_detail']['items'][0]['description'];

            $transaction_session = DB::table("transaction_session")
                ->where('transaction_id', $id)
                ->orderBy('id', 'desc')
                ->first();

            if (empty($transaction_session)) {
                return abort(404);
            }

            $input = json_decode($transaction_session->request_data, true);

            if (isset($request_data["transaction"]['success']) && $request_data["transaction"]['success'] == "1") {
                $input['status'] = '1';
                $input['reason'] = "Your transaction has been processed successfully.";
            } elseif (isset($request_data["transaction"]['pending']) && $request_data["transaction"]['pending'] == "1") {
                $input['status'] = '2';
                $input['reason'] = "Your transaction is pending in acquirer, please check after sometime.";
            } elseif (isset($request_data["transaction"]['success']) && $request_data["transaction"]['success'] == "0") {
                $input['status'] = '0';
                $input['reason'] = $request_data["responseMessage"] ?? "Your transaction could not processed successfully.";
            } else {
                \Log::info(['paymob_status_else' => $request_data]);
                $input['status'] = '0';
                $input['reason'] = $request_data["responseMessage"] ?? "Your transaction could not processed successfully.";
            }

            $input['gateway_id'] = $request_data['transaction']['id'] ?? '1';

            $this->updateGatewayResponseData($input, $request_data);

            $this->storeTransaction($input);

            http_response_code(200);
            exit();
        }
    }





    private function curlPost($url, $headers = [], $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, 1);
        return $response_data;
    }
}
