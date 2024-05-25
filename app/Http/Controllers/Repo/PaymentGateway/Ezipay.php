<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;
use Illuminate\Support\Facades\Hash;

class Ezipay extends Controller {

    //const BASE_URL = 'https://test-payments.ezipaysarl.com/'; // Test
	const BASE_URL = 'https://payments.ezipaysarl.com/'; //Live

    use StoreTransaction;

    public function checkout($input, $check_assign_mid) {
        $value = "MerchantId=".$check_assign_mid->MerchantId."&Amount=".ceil($input["converted_amount"])."&Customer=".$input["phone_no"]."&Description=".$input["session_id"]."&TransactionId=".$input["order_id"]."&Signature=".$check_assign_mid->secret_key."&MerchantCode=".$check_assign_mid->MerchantCode;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL . "api/requesttoken",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $value,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response);
        try{
            if(isset($responseData) && !empty($responseData->TokenId)){
                $input['gateway_id'] = $responseData->TokenId ?? null;
                $this->updateGatewayResponseData($input, $responseData);
            }
            $url = self::BASE_URL ."checkout?token=".$responseData->TokenId."&returnurl=".route("ezipay.return",$input["session_id"]);
            if( isset($url) && !empty($url) ) {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $url,
                ];
            }
        }catch (Exception $e) {
            \Log::info([
                'ezipay-exception' => $e->getMessage()
            ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function callback(Request $request)
    {
        $response = $request->all(); 
        $data = \DB::table('transaction_session')
            ->where('order_id', $response["TransactionId"])
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        if($response["StatusCode"] == 200){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        }elseif ($response["StatusCode"] == 404) {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }else{
            $input['status'] = '2';
            $input['reason'] = 'Transaction is pending in acquirer system, please check after few minutes.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input); 
        return redirect($store_transaction_link);
    }

    public function return(Request $request,$id){
    	$response = $request->all(); 
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $input['status'] = '0';
        $input['reason'] = 'Merchant cancelled the transaction.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input); 
        return redirect($store_transaction_link);
    }

}


