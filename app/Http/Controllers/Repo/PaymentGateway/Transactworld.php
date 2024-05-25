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

class Transactworld extends Controller {

    const BASE_URL = 'https://secure.transactworld.com/transactionServices/REST/v1/payments'; // Live

    use StoreTransaction;

    public function checkout($input, $check_assign_mid) {
        $token = $this->requestAuthToken($check_assign_mid);
        $arrToken = json_decode($token);
        $values= $check_assign_mid->memberId."|".$check_assign_mid->secretkey."|".$input["order_id"]."|".number_format((float)$input["converted_amount"], 2, '.', '');
        $generatedCheckSum=md5($values);
        $url = "https://secure.transactworld.com/transactionServices/REST/v1/payments";
        $data = "authentication.memberId=".$check_assign_mid->memberId.
            "&authentication.checksum=".$generatedCheckSum.
            "&authentication.terminalId=".$check_assign_mid->terminalId.
            "&merchantTransactionId=".$input["order_id"].
            "&amount=".number_format((float)$input["converted_amount"], 2, '.', '').
            "&currency=".$input["converted_currency"].
            "&shipping.country=".$input["country"].
            "&shipping.city=".$input["city"].
            "&shipping.state=".$input["state"].
            "&shipping.postcode=".$input["zip"].
            "&shipping.street1=".$input["address"].
            //"&customer.telnocc=".$input["country_code"].
            //"&customer.phone=".$input["phone_no"].
            "&customer.email=".$input["email"].
            "&customer.givenName=".$input["first_name"].
            "&customer.surname=".$input["last_name"].
            "&customer.ip=3.8.25.32".
            "&card.number=".$input["card_no"].
            "&card.expiryMonth=".$input["ccExpiryMonth"].
            "&card.expiryYear=".$input["ccExpiryYear"].
            "&card.cvv=".$input["cvvNumber"].
            "&paymentBrand=".$check_assign_mid->payment_type.
            "&paymentMode=CC".
            "&paymentType=DB".
            "&merchantRedirectUrl=".route("transactworld.callback",$input["session_id"]).
            "&notificationUrl=".route("transactworld.callback",$input["session_id"]).
            "&tmpl_amount=".$input["converted_amount"].
            "&tmpl_currency=".$input["converted_currency"].
            "&recurringType=INITIAL".
            "&createRegistration=true";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('AuthToken:'.$arrToken->AuthToken));
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData= json_decode($responseData);
        try {
            if(isset($responseData)){
                if(isset($responseData->paymentId)) {
                    $input['gateway_id'] = $responseData->paymentId ?? null;
                    $this->updateGatewayResponseData($input, $responseData);
                }
                if (isset($responseData->result->code) && $responseData->result->code == '00001' && isset($responseData->transactionStatus) && $responseData->transactionStatus == "Y") {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction was processed successfully.';
                }elseif(isset($responseData->transactionStatus) && $responseData->transactionStatus == "P"){
                    $input['status'] = '2';
                    $input['reason'] = 'Transaction is in pending';
                }elseif(isset($responseData->transactionStatus) && $responseData->transactionStatus == "C"){
                    $input['status'] = '0';
                    $input['reason'] = 'Your transaction could not processed.';
                }elseif( (isset($responseData->result->code) && $responseData->result->code == "20007") || (isset($responseData->transactionStatus) && $responseData->transactionStatus == "N")){
                    $input['status'] = '0';
                    $input['reason'] = (isset($responseData->result->description) && !empty($responseData->result->description) ? $responseData->result->description : 'Your transaction could not processed.');
                }else{
                    $input['status'] = '0';
                    $input['reason'] = 'Your transaction could not processed.';
                }
            }else{
                $input['status'] = '0';
                $input['reason'] = 'Your transaction could not processed.';
            }
            return $input;
        } catch (Exception $e) {
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    public function requestAuthToken($check_assign_mid) {
        $url = "https://secure.transactworld.com/transactionServices/REST/v1/authToken";
        $data = "authentication.partnerId=".$check_assign_mid->partnerId."&merchant.username=".$check_assign_mid->username."&authentication.sKey=".$check_assign_mid->secretkey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $responseData;
    }

    public function callback(Request $request,$id){
        $responseData = $request->all(); 
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        if ($data == null) {
            return abort(404);
        }
        sleep(5);
        $input = json_decode($data->request_data, 1);
        if(isset($responseData)){
            if (isset($responseData->result->code) && $responseData->result->code == '00001' && isset($responseData->transactionStatus) && $responseData->transactionStatus == "Y") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was processed successfully.';
            }elseif(isset($responseData->transactionStatus) && $responseData->transactionStatus == "P"){
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }elseif(isset($responseData->transactionStatus) && $responseData->transactionStatus == "C"){
                $input['status'] = '0';
                $input['reason'] = 'Your transaction could not processed.';
            }elseif( (isset($responseData->result->code) && $responseData->result->code == "20007") || (isset($responseData->transactionStatus) && $responseData->transactionStatus == "N")){
                $input['status'] = '0';
                $input['reason'] = (isset($responseData->result->description) && !empty($responseData->result->description) ? $responseData->result->description : 'Your transaction could not processed.');
            }else{
                $input['status'] = '0';
                $input['reason'] = 'Your transaction could not processed.';
            }
        }else{
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
        }
        $transaction_response = $this->storeTransaction($input);
        exit();
    }
}


