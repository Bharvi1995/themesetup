<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use AvalanchePay\Api\Amount;
use AvalanchePay\Api\Payer;
use AvalanchePay\Api\Payment;
use AvalanchePay\Api\RedirectUrls;
use AvalanchePay\Api\Transaction;

class Altercards extends Controller {
    
    use StoreTransaction;

    public function checkout($input, $check_assign_mid) {
        $aes_key = $check_assign_mid->aes_key;
        $cardid=strtoupper(bin2hex(openssl_encrypt($input["card_no"],"AES-128-ECB",$aes_key,OPENSSL_RAW_DATA)));
        $month=strtoupper(bin2hex(openssl_encrypt($input["ccExpiryMonth"],"AES-128-ECB",$aes_key,OPENSSL_RAW_DATA)));
        $year=strtoupper(bin2hex(openssl_encrypt($input["ccExpiryYear"],"AES-128-ECB",$aes_key,OPENSSL_RAW_DATA)));
        $cvv=strtoupper(bin2hex(openssl_encrypt($input["cvvNumber"],"AES-128-ECB",$aes_key,OPENSSL_RAW_DATA)));
        $amount = number_format((float)$input['converted_amount'], 2, '.', '');
        $mm_string=md5(hash("sha256",$input["order_id"].$amount.$check_assign_mid->merchantid.$check_assign_mid->siteid.$aes_key.$input['converted_currency'].$input["card_no"].$input["ccExpiryYear"].$input["ccExpiryMonth"].$input["cvvNumber"]));
        $mm_string2=md5(hash("sha256",$mm_string));
        $curlPost="cardid=".$cardid;
        $curlPost.="&month=".$month;
        $curlPost.="&year=".$year;
        $curlPost.="&cvv=".$cvv;
        $curlPost.="&Amount=".$amount;
        $curlPost.="&name=".$input["first_name"]." ".$input["last_name"];
        $curlPost.="&order_id=".$input["order_id"];
        $curlPost.="&currency_code=".$input['converted_currency'];
        $curlPost.="&merchantid=".$check_assign_mid->merchantid;
        $curlPost.="&siteid=".$check_assign_mid->siteid;
        //$curlPost.="return_url=".route('altercards.callback');
        $curlPost.="&firstname=".$input["first_name"];
        $curlPost.="&lastname=".$input["last_name"];
        $curlPost.="&address=".$input["address"];
        $curlPost.="&city=".$input["city"];
        $curlPost.="&state=".$input["state"];
        $curlPost.="&country=".$input["country"];
        $curlPost.="&postcode=".$input["zip"];
        $curlPost.="&phone=".$input["phone_no"];
        $curlPost.="&email=".$input["email"];
        $curlPost.="&customer_ip=".$input["ip_address"];
        $curlPost.="&secure_string=".$mm_string2;
        $curlPost.="&version=3.1";
        $curlPost.="&rebilling=yes";
        $curlPost.="&rebcircle=30";
        $curlPost.="&rebtimes=6";
        $curlPost.="&rebregularamount=2";
        $curlPost.="&rebstartday=7";
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,"https://payment6.altercards.com/eng6/ccgate/billing/acquirer/securepay_new.php");
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$curlPost);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $data=curl_exec($ch);
        curl_close($ch);
        
        $response_data = json_decode($data, 1);
        //echo "<pre>";print_r($response_data);exit();

        if(isset($response_data['altercards_id'])) {
            $input['gateway_id'] = $response_data['altercards_id'] ?? null;
            $this->updateGatewayResponseData($input, $response_data);
        }
        try {
            if (isset($response_data['payment_result']) && $response_data['payment_result'] == 'paymentpending') {
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }else if(isset($response_data['payment_result']) && $response_data['payment_result'] == 'paymentsuccess'){
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
            }else if(isset($response_data['payment_result']) && $response_data['payment_result'] == 'paymenterror'){
                $input['status'] = '0';
                $input['reason'] = (isset($response_data['declined_reason']) && !empty($response_data['declined_reason']) ? $response_data['declined_reason'] : 'Your transaction could not processed.');
            }else{
                $input['status'] = '0';
                $input['reason'] = 'Your transaction could not processed.';
            }
        } catch (Exception $e) {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }
        return $input; 
       
    }

    public function callBack(Request $request){
        \Log::info([
            'callBack-altercards' => $request->all()
        ]);
    }

    public function webhook(Request $request){
        \Log::info([
            'webhook-altercards' => $request->all()
        ]);
        $response = $request->all(); 
        $data = \DB::table('transaction_session')
            ->where('order_id', $response["oid"])
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        if($response['successcode'] == 'ok') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }
        $transaction_response = $this->storeTransaction($input);
        exit();
    }
}
