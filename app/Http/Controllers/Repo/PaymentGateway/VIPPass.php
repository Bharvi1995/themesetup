<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;

class VIPPass extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.vippass.eu/api/1.0';

    public function checkout($input, $check_assign_mid) {
        try {
            // VIPPass Credentials
            $api_id = $check_assign_mid->api_id;
            $api_key = $check_assign_mid->api_key;

            $data = [
                'api_id' => $api_id,
                'amount' => $input['converted_amount'],
                'currency' => $input['converted_currency'],
                'reference' => $input['order_id'],
                'hashKey' => hash('sha256', $api_id . $api_key . $input['converted_amount'] . $input['order_id']),
                'cust_name' => $input['first_name'],
                'cust_surname' => $input['last_name'],
                'cust_address' => $input['address'] ,
                'cust_phone' => $input['phone_no'],
                'cust_email' => $input['email'],
                'cust_country' => $input['country'],
                'cust_state' => $input['state'],
                'cust_city' => $input['city'],
                'cust_zip' => $input['zip'],
                'cust_ip' => $input['ip_address'],
                'notify_url' => route('vippass-webhook', $input['session_id']),
                'response_url' => route('vippass-callback', $input['session_id']),
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, self::BASE_URL . '/thpp/process');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            ob_start();
            curl_exec($curl);
            $response = ob_get_contents();
            ob_end_clean();
            $err = curl_error($curl);
            curl_close($curl);
            $responseData = json_decode($response, 1);
            \Log::info([
                'vippass-input' => $data
            ]);
            \Log::info([
                'vippass-response' => $responseData
            ]);
            if ($err) {
                throw new \Exception('Error: ' . $err);
            }
            if (isset($responseData['Status']) && $responseData['Status'] == 'THPPRedirect') {
                $input['gateway_id'] = $responseData['TrxID'] ?? null;
                $this->updateGatewayResponseData($input, $responseData);
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $responseData['THPPRedirectURL']
                ];
            }
            throw new \Exception((isset($responseData['Information']) ? $responseData['Information'] : 'Your transaction could not processed.'));
            
        } catch (\Exception $e) {

            \Log::info([
                'vippass-exception' => $e->getMessage()
            ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }
    
    public function callback($session_id, Request $request) {
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data,true);
        \Log::info([
            'vippass-callback' => $request->all()
        ]);
        $transactions = DB::table('transactions')
            ->where('order_id', $transaction_session->order_id)
            ->first();
            
        $input['status'] = $transactions->status;
        $input['reason'] = $transactions->reason;
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    /**
     * 
     * For webhook url.
     * 
     */
    public function webhook($id, Request $request) {
        $body = json_decode(request()->getContent(), true);
        \Log::info([
            'vippass-webhook' => $body,
            'id' => $id
        ]);

        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data, 1);
        if($body["Status"]=="Failed"){
            $input['status'] = '0';
            $input['reason'] = isset($body['Information']) ? $body['Information'] : 'Your transaction could not processed.';
        }elseif($body["Status"]=="Approved"){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else {
            $input['status'] = '0';
            $input['reason'] = isset($body['Information']) ? $body['Information'] : 'Your transaction could not processed.';
        }
        $transaction_response = $this->storeTransaction($input);
        exit();
    }
}
