<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;

class Paybypago extends Controller
{
    use StoreTransaction;
    
    const BASE_URL = 'https://uiservices.paybypago.com/hosted/default.aspx?';
    
    public function checkout($input, $check_assign_mid)
    {
        try {

            $data = [];
            // parameters list
            $data['merchantID'] = $check_assign_mid->merchant_id;
            $data['url_redirect'] =  route('paybypago-callback', $input["session_id"]);
            $data['notification_url'] = route('paybypago-notification', $input["session_id"]);
            $data['trans_comment'] = '';
            $data['trans_refNum']  = $input['order_id'];
            $data['trans_installments'] = '';
            $data['trans_amount'] = $input['converted_amount'];
            $data['trans_currency'] = $input['converted_currency'];
            $data['disp_paymentType'] = 'CC';
            $data['disp_payFor'] = '';
            $data['disp_recurring'] = '0';
            $data['disp_lng'] = 'en-us';
            $data['disp_mobile'] = $input['phone_no'];
            $data['PersonalHashKey'] = $check_assign_mid->hash_key;
            $retSignature = implode('', array_values($data));
            $data['signature'] = base64_encode(hash('sha256', $retSignature, true)); // mandatory
            unset($data['PersonalHashKey']);

            $redirect_3ds_url = self::BASE_URL . http_build_query($data);

            \Log::info([
                'paybypago-input' => $data
            ]);
            \Log::info([
                'paybypago-response' => $redirect_3ds_url
            ]);
            
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $redirect_3ds_url,
            ];

        } catch(\Exception $e) {

            \Log::info([
                'paybypago-exception' => $e->getMessage()
            ]);

            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id'],
            ];
        }
    }
    
    public function callback($id, Request $request) {
        
        $response = $request->all();
        \Log::info([
            'paybypago-callback' => $response
        ]);

        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $id)
        ->first();

        if ($transaction_session == null) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data,true);

        $transactions = DB::table('transactions')
        ->where('order_id', $transaction_session->order_id)
        ->first();
        
        $input['status'] = $transactions->status ?? 0;
        $input['reason'] = $transactions->reason ?? 'Your transaction could not processed.';
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }
    
    public function notification($id, Request $request) {
        
        $response = $request->all();
        \Log::info([
            'paybypago-notification' => $response
        ]);

        $transaction_session = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data,true);
        if ($response['reply_code'] == '000') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
        } else {
            $input['status'] = '0';
            $input['reason'] = (isset($response['reply_desc']) ? $response['reply_desc'] : 'Your transaction could not processed.');
        }

        // Update callback response
        $input['gateway_id'] = $response['trans_id'] ?? null;
        $this->updateGatewayResponseData($input, $response);
        // store transaction
        $transaction_response = $this->storeTransaction($input);
        exit();
    }
}
