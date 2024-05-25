<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;

class AppsNmobile extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://payments.anmgw.com/third_party_request';

    public function checkout($input, $check_assign_mid) {
        try {
            $service_url = self::BASE_URL;
            $service_id = $check_assign_mid->service_id;
            $client_key = $check_assign_mid->client_key;
            $secret_key = $check_assign_mid->secret_key;
            $data = array(
                'nickname' => $input['first_name'],
                'amount' => $input['converted_amount'],
                'exttrid' => $input['order_id'],
                'reference' => $input['session_id'],
                'callback_url' => route('appsnmobile-callback', $input['session_id']),
                'service_id' => $service_id,
                'ts' => date('Y-m-d H:i:s'),
                'landing_page' => '',
                'payment_mode' => 'CRD',
                'currency_code' => $input['converted_currency'],
                'currency_val' => $input['converted_amount']
            );
            $data_string = json_encode($data);
            $signature =  hash_hmac ( 'sha256' , $data_string , $secret_key );
            $auth = $client_key.':'.$signature;
            //echo $auth;exit();
            $ch = curl_init($service_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");   
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: '.$auth,
                'Content-Type: application/json',
                'timeout: 180',
                'open_timeout: 180'
                )
            );

            $result = curl_exec($ch);
            $err = curl_error($curl);
            curl_close($ch);
            $responseData = json_decode($result, 1);
            \Log::info([
                'AppsNmobile-input' => $data
            ]);
            \Log::info([
                'AppsNmobile-response' => $responseData
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
                'appsnmobile-exception' => $e->getMessage()
            ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }
    
    public function callback($session_id, Request $request) {
        echo "<pre>";print_r($request->toArray());
        exit();
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data,true);
        \Log::info([
            'appsnmobile-callback' => $request->all()
        ]);
        $transactions = DB::table('transactions')
            ->where('order_id', $transaction_session->order_id)
            ->first();
            
        $input['status'] = $transactions->status;
        $input['reason'] = $transactions->reason;
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

}
