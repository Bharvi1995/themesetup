<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use Interkassa\Helper\Signature;
use Interkassa\Request\BaseInvoiceRequest;
use Interkassa\Helper\Config;

class Interkassa extends Controller
{
    use StoreTransaction;
    private $apiConfig;
    private $signatureHelper;

   

    public function checkout($input, $check_assign_mid)
    {
        if($check_assign_mid->id == '35') {
            $ik_co_id = '613228a9d829d7110f2a5d0a';
            $key = '223hg1NNMrdWT8AA';
        } elseif($check_assign_mid->id == '20') {
            $ik_co_id = '61164989f7d3f263f729bcb3';
            $key = 'eGB1ehUeZ78zk4jL';
        } else {
            $ik_co_id = '61164989f7d3f263f729bcb3';
            $key = 'eGB1ehUeZ78zk4jL';
        }
        
        try {
            $pmNo = $this->GetUUID(random_bytes(16));
            $dataSet = array (
                "ik_co_id" => $ik_co_id,
                "ik_pm_no" => $pmNo,
                "ik_pw_via" => "visa_cpaytrz_merchant_usd",
                "ik_am" => $input['converted_amount'],
                "ik_cur" => $input['currency'],
                "ik_desc" => "test",
                "ik_pay_card_number" => $input['card_no'],
                "ik_pay_card_exp_year" => substr($input['ccExpiryYear'], -2),
                "ik_pay_card_exp_month" => $input['ccExpiryMonth'],
                "ik_pay_card_cvv" => $input['cvvNumber'],
                "ik_int" => "json",
                "ik_act" => "process",
                'ik_suc_u' => route('interkassa-success',$input["session_id"]),
                'ik_fal_u' => route('interkassa-fail',$input["session_id"]),
            );
            $key = $key;
            ksort($dataSet, SORT_STRING); 
            $signString = implode(':', $dataSet); 
            $sha256hash = hash('sha256', $signString);
            $hmac_hash = base64_encode(hash_hmac('sha256', $sha256hash, $key, true));
            $dataSet["ik_sign_hmac"] = $hmac_hash;
            \Log::info([
                'interkassa_data' => $dataSet
            ]);
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.onepaystream.com/api/v1/payment',
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
            curl_close($curl);
            // echo $response;exit();

            $responseData = json_decode($response, true);
            $input['gateway_id'] = $pmNo;
            $this->updateGatewayResponseData($input, $responseData);
            \Log::info([
                'interkassa_data_response' => $responseData
            ]);

            if(isset($responseData['resultCode']) && $responseData['resultCode'] == '0') {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'redirect_3ds_url' => $responseData['resultData']['paymentForm']['action']
                ];
            } else {                
                return [
                    'status' => '0',
                    'reason' => isset($responseData['resultMsg'])?$responseData['resultMsg']:'Transaction was declined by bank.',
                    'order_id' => $input['order_id']
                ];
            }

        } catch(\Exception $e) {
            return [
                'status' => '0',
                'reason' => $e->getMessage(),
                'order_id' => $input['order_id'],
            ];
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
        \Log::info([
            'interkassa_response_success' => $request->toArray(),
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
        \Log::info([
            'interkassa_response_fail' => $request->toArray(),
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

    public function callback($id,Request $request) {
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $body = $request->all();
        \Log::info([
            'interkassa-success' => $body
        ]);
        $input = json_decode($input_json['request_data'], true);
        if (! empty($body['ik_pm_no'])) {
            $input['status'] = '2';
            $input['reason'] = 'Your transaction is in Pending.';
            if (! empty($body['ik_inv_st']) && $body['ik_inv_st'] == 'success') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            }else if(! empty($body['ik_inv_st']) && $body['ik_inv_st'] == 'fail'){
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was Declined.';
            }
            else if(! empty($body['ik_inv_st']) && $body["ik_inv_st"] == "canceled"){
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was Canceled.';
            }
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }
}