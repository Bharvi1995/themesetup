<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class QartPays2s extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://dashboard.qartpay.com/crm/jsp/paymentrequest'; // live
    // const BASE_URL = 'https://uat.qartpay.com/crm/jsp/paymentrequest'; // test
  
    
    public function checkout($input, $check_assign_mid) {
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route("qartpay.form",$input["session_id"]),
        ];
    }


    public function form($session_id){
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $userData = \App\User::find($input["user_id"]);
        return view("gateway.qartpays2s.qartpay",compact('input','userData'));
    }


    public function formSubmit(Request $request, $session_id){
        if($request->payment_type == "CC" || $request->payment_type == "DC" || $request->payment_type == "UP"){
            $data = \DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();

            if ($data == null) {
                return abort(404);
            }
            $input = json_decode($data->request_data, 1);
            $userData = \App\User::find($input["user_id"]);
            return view('gateway.qartpays2s.details',compact('request','input','userData'));
        }else{
            return $this->formSendData($request, $session_id);
        }
    }

    public function formSendData(Request $request,$session_id){
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $url = "https://dashboard.qartpay.com/crm/jsp/merchantpay";
        $curl = curl_init();
        $data = [
            'AMOUNT' => ceil($input["converted_amount"])*100,
            'CURRENCY_CODE' => '356',
            'CUST_EMAIL' => $input["email"],
            'CUST_NAME' =>  $input["first_name"],
            'CUST_PHONE' => $input['phone_no'],
            'MOP_TYPE' => $request->mop_id,
            'ORDER_ID' => $input['order_id'],
            'PAYMENT_TYPE' => $request->payment_type,
            'PAY_ID' => $check_assign_mid->PAY_ID,
            'PRODUCT_DESC' => $input["session_id"],
            'RETURN_URL' => Route("qartpays2s.callback",$input["session_id"]),
            'TXNTYPE' => 'SALE',
        ];
        if($request->payment_type == "CC" || $request->payment_type == "DC"){
            $data["CARD_NUMBER"] = str_replace(" ", "", $request->card_no);
            $ccExpiryMonth = substr($request->ccExpiryMonthYear, 0, 2);
            $ccExpiryYear = substr($request->ccExpiryMonthYear, -2);
            $data["CARD_EXP_DT"] = $ccExpiryMonth."".'20'.$ccExpiryYear;
            $data["CVV"] = '936';
        }
        else if($request->payment_type == "UP"){
            $data["UPI"] = $request->txtUPI;
        }
        ksort($data);
        $explodeArray = urldecode(http_build_query($data, '', '~'));
        $explodeArray = $explodeArray .$check_assign_mid->SALT;
        $hash =  hash('sha256',$explodeArray);
        $data['HASH'] = strtoupper($hash);
        \Log::info([
            'qartpay-s2s-data' => $data
        ]);
        return view("gateway.qartpays2s.form",compact('data'));
    }

    public function callbacks2s(Request $request,$session_id){
        $response = $request->all();
        \Log::info([
            'qartpay-callback' => $response,
            'session_id' => $session_id
        ]);
        if (! empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response["RESPONSE_CODE"] == "000" && ($response['STATUS'] == 'Approved' || $response['STATUS'] == 'Captured' )) {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else if($response["RESPONSE_CODE"] == "007") {
                $input['status'] = '0';
                $input['reason'] = (isset($response['RESPONSE_MESSAGE']) ? $response['RESPONSE_MESSAGE'] : 'Your transaction could not processed.');
            }else if($response["RESPONSE_CODE"] == "010"){
                $input['status'] = '0';
                $input['reason'] = (isset($response['RESPONSE_MESSAGE']) ? $response['RESPONSE_MESSAGE'] : 'Your transaction was cancelled by user.');
            }elseif ($response["RESPONSE_CODE"] == "300") {
                $input['status'] = '0';
                $input['reason'] = 'Invalid Request. Please check your data.';
            }elseif ($response["STATUS"] == "Failed") {
                $input['status'] = '0';
                $input['reason'] = (isset($response['RESPONSE_MESSAGE']) ? $response['RESPONSE_MESSAGE'] : 'Your transaction could not processed.');
            }else{
                $input['status'] = '2';
                $input['reason'] = 'Transaction is in pending';
            }
            // Update callback response
            $input['gateway_id'] = $response['TXN_ID'] ?? null;
            $this->updateGatewayResponseData($input, $response);
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }
}

