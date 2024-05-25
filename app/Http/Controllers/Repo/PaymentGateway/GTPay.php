<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class GTPay extends Controller
{
    use StoreTransaction;

    //const BASE_URL = 'https://ibank.gtbank.co.ug/GTBANK/AAGTPAY/GTPay/GTPay.aspx'; 
    const BASE_URL = 'http://campaign.gtbank.com/click.php/e78192749/TVHljaGVfTUMyMjAyMjUsVHljaGVfTUNfTGlzdCxodHRwczovL2d0cGF5Lmd0YmFuay5jby51Zy9ndHBheS9ndHBheS9HVFBheS5hc3B4/s3226009717';
    
    public function checkout($input, $check_assign_mid) {
        $secureSecret = $check_assign_mid->secret_key;
        $secureHashSecret = $secureSecret;
        $arr = [
            "gtp_Amount" => $input["converted_amount"],
            "gtp_CustomerCode" => "751776",
            "gtp_Currency" => $input["converted_currency"],
            "gtp_PayerName" => $input["first_name"]." " . $input["last_name"],
            "gtp_OrderId" => $input["order_id"],
            "gtp_TransDetails" => $input["session_id"]
        ];
        ksort ($arr);
        $vpcURL = self::BASE_URL;
        $postData = "";
        $hashInput = "";
        foreach($arr as $key => $value) {
            if (strlen($value) > 0) {
                $postData .= (($postData=="") ? "" : "&") . urlencode($key) . "=" . urlencode($value);
                $hashInput .= $key . "=" . $value . "&";
            }
        }
        $dt = new \DateTime();
        $dt->setTimeZone(new \DateTimeZone('UTC'));
        $dat = $dt->format('Y-m-d\TH-i-s\Z');
        $secVal = "SHA256";
        $hashInput=rtrim($hashInput,"&");
        $secureHash = strtoupper(hash_hmac('SHA256',$hashInput.$secVal, pack("H*",$secureHashSecret)));
        
        $postData .= (($postData=="") ? "" : "&") . urlencode("gtp_TransDate") . "=" . urlencode($dat);
        $postData .= (($postData=="") ? "" : "&") . urlencode("gtp_SecureHash") . "=" . urlencode($secureHash);
        $postData .= (($postData=="") ? "" : "&") . urlencode("gtp_SecureHashType") . "=" . urlencode("SHA256");
        $redirectURL = $vpcURL."?".$postData;
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect.',
            'redirect_3ds_url' => $redirectURL
        ];
    }

    public function return(Request $request){
        $response = $request->all();
        \Log::info([
            'GTPay-return' => $response
        ]);
        $data = \DB::table('transaction_session')
            ->where('order_id', $response["transaction_id"])
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        if($response["message"] == 1){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
        }else{
            $input['status'] = '0';
            $input['reason'] = $response["message_desc"] ?? 'Your transaction could not processed.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function callback(Request $request) {
        $response = $request->all();
        \Log::info([
            'GTPay-callback' => $response
        ]);
        if(isset($response["transaction_id"]) && !empty($response["transaction_id"])){
            $data = \DB::table('transaction_session')
                ->where('order_id', $response["transaction_id"])
                ->first();

            if ($data == null) {
                return abort(404);
            }
            $input = json_decode($data->request_data, 1);
            if($response["message"] == 1){
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
            }else{
                $input['status'] = '0';
                $input['reason'] = $response["message_desc"] ?? 'Your transaction could not processed.';
            }
            $transaction_response = $this->storeTransaction($input);
        }
        exit();
    }
}

