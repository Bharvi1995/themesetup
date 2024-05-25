<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class QartPayUPI extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://dashboard.qartpay.com/crm/jsp/paymentrequest'; // live
    // const BASE_URL = 'https://uat.qartpay.com/crm/jsp/paymentrequest'; // test
    
    public function checkout($input, $check_assign_mid)
    {
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route("qartpayupi.form", $input["session_id"]),
        ];
    }

    public function form($session_id)
    {
        $transaction_session = TransactionSession::where('transaction_id', $session_id)
            ->where('created_at', '>', \Carbon\Carbon::now()->subHour(2)->toDateTimeString())
            ->where('is_checkout', '0')
            ->where('is_completed', 0)
            ->orderBy('id', 'desc')
            ->first();
        
        if (empty($transaction_session)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'The link is expired, please try again.',
            ]);
        }

        // validate user and payment_gateway_id
        $payment_gateway_id = DB::table('users')
            ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
            ->leftJoin('middetails', 'middetails.id','users.mid')
            ->where('users.id', $transaction_session->user_id)
            ->where('users.is_active', '1')
            ->whereNotNull('users.upi_mid')
            ->where('users.upi_mid', '!=', '0')
            ->whereNull('users.deleted_at')
            ->first();

        if (empty($payment_gateway_id) || $payment_gateway_id->upi_mid == '0' || $payment_gateway_id->upi_mid == null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Your UPI MID is not approved, please contact support team.',
            ]);
        }

        $input = json_decode($transaction_session->request_data, 1);

        // payment gateway object
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        $data = [
            'AMOUNT' => ceil($input["converted_amount"])*100,
            'CURRENCY_CODE' => '356',
            'CUST_EMAIL' => $input["email"],
            'CUST_NAME' =>  $input["first_name"],
            'CUST_PHONE' => $input['phone_no'],
            'MOP_TYPE' => $check_assign_mid->mop_id,
            'ORDER_ID' => $input['order_id'],
            'PAYMENT_TYPE' => 'UP',
            'PAY_ID' => $check_assign_mid->pay_id,
            'PRODUCT_DESC' => $input["session_id"],
            'RETURN_URL' => Route("qartpayupi.callback", $input["session_id"]),
            'TXNTYPE' => 'SALE',
            'UPI' => $input['upi'],
        ];
        ksort($data);
        $explodeArray = urldecode(http_build_query($data, '', '~'));
        $explodeArray = $explodeArray .$check_assign_mid->salt;
        $hash =  hash('sha256',$explodeArray);
        $data['HASH'] = strtoupper($hash);

        return view("gateway.qartpays2s.form", compact('data'));
    }

    // ================================================
    /* method : callback
    * @param  : 
    * @description : return to response_url
    */// ===============================================
    public function callback(Request $request, $session_id)
    {
        $response = $request->all();
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

