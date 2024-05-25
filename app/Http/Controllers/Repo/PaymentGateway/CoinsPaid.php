<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Http;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Log;

class CoinsPaid extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://app.cryptoprocessing.com/api/v2'; // live
    // const BASE_URL = 'https://app.sandbox.cryptoprocessing.com/api/v2'; // test

    public function checkout($input, $check_assign_mid)
    {
        if ($input['amount_in_usd'] < 10) {
            return [
                'status' => '5',
                'reason' => 'The amount is too small. The minimum amount is 10.00 EUR.',
                // $e->getMessage(),
                'order_id' => $input['order_id'],
            ];
        }

        try {
            $public_key = $check_assign_mid->api_key;
            $secret_key = $check_assign_mid->secret_key;

            $payload = [
                'timer' => true,
                'title' => 'Payment for ' . $input['first_name'] . ' ' . $input['last_name'],
                'currency' => $input['currency'],
                'amount' => $input['converted_amount'],
                'foreign_id' => $input['session_id'],
                'url_success' => route('coinspaid-success', $input["session_id"]),
                'url_failed' => route('coinspaid-failed', $input["session_id"]),
                'email_user' => $input['email']
            ];
            $requestBody = json_encode($payload);
            $signature = hash_hmac('sha512', $requestBody, $secret_key);

            $response = Http::withHeaders(["Content-Type" => "application/json", "X-Processing-Key" => $public_key, "X-Processing-Signature" => $signature])->post(self::BASE_URL . '/invoices/create', $payload)->json();

            // * Store mid payload
            $this->storeMidPayload($input["session_id"], json_encode($payload));
            $input['gateway_id'] = $input["session_id"];
            $this->updateGatewayResponseData($input, $response);
            // transaction requires 3DS redirect
            if (!empty($response['data']['url'])) {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $response['data']['url'],
                ];
            }
        } catch (\Exception $e) {
            Log::info([
                'coinspaid-exception' => $e->getMessage()
            ]);
        }
        return [
            'status' => '0',
            'reason' => 'Your transaction could not processed.',
            // $e->getMessage(),
            'order_id' => $input['order_id'],
        ];
    }

    public function success(Request $request, $id)
    {

        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction was proccessed successfully.';
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function fail(Request $request, $id)
    {

        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction was Declined.';
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    /**
     * 
     * Call automatically.
     * Set Callback Url here: https://app.sandbox.cryptoprocessing.com/business/merchants/860/edit/api
     * 
     */
    public function webhook(Request $request)
    {

        $body = $request->all();
        Log::info([
            'coinspaid-webhook' => $body
        ]);
        $input_json = TransactionSession::where('transaction_id', $body["foreign_id"])
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return exit();
        }
        $input = json_decode($input_json['request_data'], true);

        // * update the webhook
        $this->storeMidWebhook($body["foreign_id"], json_encode($body));

        $input['status'] = '2';
        $input['reason'] = 'Your transaction is in Pending.';

        if (!empty($body['status']) && $body['status'] == 'confirmed') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        } else if (!empty($body['status']) && $body['status'] == 'failed') {
            $input['status'] = '0';
            $input['reason'] = isset($body["error"]) ? $body["error"] : 'Your transaction was Declined.';
        } else if (!empty($body['status']) && $body['status'] == 'not_confirmed') {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction was Declined.';
        }

        $this->storeTransaction($input);
    }
}