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

class InterkassaCrypto extends Controller
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
                "ik_am" => $input['converted_amount'],
                "ik_cur" => $input['currency'],
                "ik_desc" => "Payment Description",
                'ik_suc_u' => route('interkassa-success',$input["session_id"]),
                'ik_fal_u' => route('interkassa-fail',$input["session_id"]),
                //'ik_pnd_u' => "https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475",
            );
            $key = $key;
            // ksort($dataSet, SORT_STRING); // sort by keys alphabetically the elements of the array
            // $signString = implode(':', $dataSet); // concatenate values through the ":"
            // $sha256hash = hash('sha256', $signString);
            // $sign = base64_encode(hash_hmac('sha256', $sha256hash, $key, true));
            ksort($dataSet, SORT_STRING);
            array_push($dataSet, $key);
            $signString = implode(':', $dataSet);
            $sign = base64_encode(hash('sha256', $signString, true));
            $success_url = route('interkassa-success',$input["session_id"]);
            $fail_url = route('interkassa-fail',$input["session_id"]);
            
            //$pending_url = "https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475";
            \Log::info([
                'interkassa_request' => $dataSet,
                'sign'=>$sign
            ]);
            $url = "https://sci.interkassa.com/?ik_co_id=".$ik_co_id."&ik_pm_no=".$pmNo."&ik_am=".$input['converted_amount']."&ik_cur=".$input['currency']."&ik_desc=Payment+Description&ik_sign=".$sign."&ik_suc_u=".$success_url."&ik_fal_u=".$fail_url."#/paysystemList";
            //"&ik_pnd_u=".$pending_url."

            $input['gateway_id'] = $pmNo;
            $this->updateGatewayResponseData($input, $dataSet);
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect.',
                'redirect_3ds_url' => $url
            ];
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
        \Log::info(['interkassa_response_success' => $request->toArray(),
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
        \Log::info(['interkassa_response_fail' => $request->toArray(),
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

    public function pending($id,Request $request){
        \Log::info(['interkassa_response_pending' => $request->toArray(),
            'id'=>$id
        ]);
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '2';
        $input['reason'] = 'Your transaction is in Pending.';
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