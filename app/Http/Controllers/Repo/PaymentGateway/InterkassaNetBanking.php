<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use Interkassa\Helper\Config;
use Illuminate\Support\Facades\Crypt;

class InterkassaNetBanking extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.onepaystream.com/api/v1/payment';

    public function checkout($input, $check_assign_mid)
    {
       
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route('interkassa-net-banking-confirmation', Crypt::encryptString($input['session_id'])),
        ];
    }
    
    public function confirmation(Request $request, $sessionId)
    {   
        
        $error = $orderId = $bankList = '';

        try {
            
            $sessionId = Crypt::decryptString($sessionId); 
            $bankList = config('interkassa.bank');
            
            $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $sessionId)
            ->first();
            
            if ($transaction_session == null) {
                throw new \Exception('Transaction not found.');
            }

            $input = json_decode($transaction_session->request_data, 1);
            $orderId = $input['order_id'];

        } catch (\Exception $e) {
            
            $error = $e->getMessage();
        
            \Log::info([
                'interkassa-net-banking-payment-form-exception' =>  $error 
            ]);
        }
        
        return view('gateway.interkassaNetBanking', compact('error','sessionId','orderId','bankList'));
        
    }

    public function confirmationFormSubmit(Request $request)
    {   
        $request->validate([
            'bank' => 'required',
            
        ]);
        $session_id = $request->session_id;

        try {
            
            $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
            
            if ($transaction_session == null) {
                throw new \Exception('Transaction not found.');
            }

            $input = json_decode($transaction_session->request_data, 1);
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            $key = $check_assign_mid->key;
            $ik_co_id = $check_assign_mid->user_id;
            
            $pmNo = $this->GetUUID(random_bytes(16));

            $dataSet = array (
                "ik_co_id" => $ik_co_id,
                "ik_pm_no" => $pmNo,
                "ik_pw_via" => "inps_cpaytrz_merchantNetBanking_inr",
                "ik_am" => $input['converted_amount'],
                "ik_cur" => $input['converted_currency'],
                "ik_desc" => "Payment for" . config('app.name'),
                "ik_x_mop_type" => $request->bank,
                "ik_x_customer_phone" => $input['phone_no'],
                "ik_x_customer_email" => $input['email'],
                "ik_int" => "json",
                "ik_act" => "process",
                'ik_suc_u' => route('interkassa-net-banking-success', $input["session_id"]),
                'ik_fal_u' => route('interkassa-net-banking-fail', $input["session_id"]),
            );

            ksort ($dataSet, SORT_STRING); // sort the array elements by keys in alphabetical order
            $signString = implode (':', $dataSet); // concatenate the values ​​using the": "
            $sha256hash = hash ('sha256', $signString);
            $hmac_hash = base64_encode (hash_hmac ('sha256', $sha256hash, $key, true));

            \Log::info([
                'interkassa_net_banking_request' => $dataSet,
                'sign'=>$hmac_hash
            ]);

            $dataSet['ik_sign_hmac'] = $hmac_hash;
            $url = self::BASE_URL;

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($dataSet),
                CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            $response = json_decode($response, true);

            \Log::info([
                'interkassa-net-banking-response' => $response
            ]);

            if ($err) {
                throw new \Exception('Error: ' . $err);
            }

            if($response['resultCode'] == 0) {

                return redirect($response['resultData']['paymentForm']['action'] . '?' . $response['resultData']['paymentForm']['parameters']['x-request-id']);
            }
            
            return redirect()->back()->with('danger', $response['resultMsg']);   
            

        } catch (\Exception $e) {

            return redirect()->back()->with('danger', $e->getMessage());
        }

       
    }
    public function GetUUID($data)
    {
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data) , 4));
    }

    public function success($id,Request $request){
        
        \Log::info(['interkassa_net_banking_response_success' => $request->toArray(),
            'id'=>$id
        ]);

        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction was proccessed successfully.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    public function fail($id,Request $request){

        \Log::info(['interkassa_net_banking_response_fail' => $request->toArray(),
            'id'=>$id
        ]);

        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
            
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction was Declined.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

}
