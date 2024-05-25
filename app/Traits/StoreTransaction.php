<?php

namespace App\Traits;

use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Transformers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;

trait StoreTransaction
{
    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    // ================================================
    /* method : storeTransaction
     * @param  :
     * @description : store transaction and return response
     */// ==============================================
    public function storeTransaction($input)
    {
        $transactionReturn = $this->transaction->storeData($input);

        if (!in_array($input['status'], ['2', '7'])) {
            \DB::table('transaction_session')
                ->where('transaction_id', $input['session_id'])
                ->update(['is_completed' => '1']);
        }

        return $transactionReturn;
    }

    // ================================================
    /* method : getRedirectLink
     * @param  :
     * @description : get redirect link for gateway
     */// ==============================================
    public function getRedirectLink($input)
    {
        return ApiResponse::returnUrl($input);
    }

    public function getRedirectLinkTransactions($input)
    {
        return ApiResponse::returnUrlTransaction($input);
    }

    // ================================================
    /* method : storeTransactionAPIVTwo
     * @param  :
     * @description : store transaction and return response
     */// ==============================================
    public function storeTransactionAPIVTwo($input)
    {
        $transactionReturn = $this->transaction->storeData($input);

        // update transaction_session record if not pending
        if (!in_array($input['status'], ['2', '7'])) {
            \DB::table('transaction_session')
                ->where('transaction_id', $input['session_id'])
                ->update(['is_completed' => '1']);
        }

        $redirect_link = $this->getRedirectLink($input);
        return $redirect_link;
    }

    // ================================================
    /* method : updateGatewayResponseData
     * @param  :
     * @description : update session response data
     */// ==============================================
    public function updateGatewayResponseData($input, $response_data, $payload = null)
    {
        try {
            // update transaction_session record
            $session_update_data = TransactionSession::where('transaction_id', $input['session_id'])
                ->first();

            $session_request_data = json_decode($session_update_data->request_data, 1);

            $session_request_data['gateway_id'] = $input['gateway_id'] ?? "1";

            $session_update_data->update([
                'request_data' => json_encode($session_request_data),
                'gateway_id' => $input['gateway_id'],
                'mid_payload' => $payload,
                'response_data' => json_encode($response_data)
            ]);
            $session_update_data->save();
        } catch (\Exception $e) {
            \Log::info([
                'session_update_error' => $e->getMessage()
            ]);

            return true;
        }

        return true;
    }

    // ================================================
    /* method : getStatus
     * @param  :
     * @description : status response by status code
     */// ==============================================
    public function getStatus($code)
    {
        $status = [
            '0' => 'fail',
            '1' => 'success',
            '2' => 'pending',
            '3' => 'fail',
            '5' => 'blocked',
            '7' => '3d_redirect',
        ];

        if (array_key_exists($code, $status)) {
            return $status[$code];
        } else {
            return '0';
        }
    }

    // * Store the mid request payload in table
    public function storeMidPayload(string $sessionId, string $payload): void
    {
        DB::table('transaction_session')->where("transaction_id", $sessionId)->update(["mid_payload" => $payload]);
    }

    // * Store the mid webhook response
    public function storeMidWebhook(string $sessionId, string $payload): void
    {
        DB::table('transaction_session')->where("transaction_id", $sessionId)->update(["webhook_response" => $payload]);

    }
}