<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class QikPayUPI extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://secure.qikpay.co.in/pgui/jsp/merchantPaymentInit'; // live
    //const BASE_URL = 'https://sandbox.qikpay.co.in/pgui/jsp/merchantPaymentInit'; // test
    
    public function checkout($input, $check_assign_mid)
    {
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect.',
            'redirect_3ds_url' => route('qikpayupi.form', $input['session_id'])
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
        $pay_id = $check_assign_mid->pay_id;
        
        $data = [
            'PAY_ID' => $check_assign_mid->pay_id,
            'ORDER_ID' => $input['order_id'],
            'PAYMENT_TYPE' => 'UP',
            'MOP_TYPE' => $check_assign_mid->mop_id,
            'AMOUNT' => ceil($input["converted_amount"])*100,
            'CURRENCY_CODE' => '356',
            'CUST_EMAIL' => $input["email"],
            'CUST_NAME' =>  $input["first_name"],
            'CUST_PHONE' => $input['phone_no'],
            'PRODUCT_DESC' => $input["session_id"],
            'PAYER_ADDRESS' => $input["upi"],
            'RETURN_URL' => Route("qikpayupi.callback", $input["session_id"])
        ];

        foreach ($data as $key => $value) {
            $requestParamsJoined[] = "$key=$value";
        }

        $CryptoKey= "E517DEF0C34A3EDAA567263A27EE65CC";
        $iv = substr($CryptoKey, 0, 16); 
        $method = "AES-256-CBC";
        $salt = $check_assign_mid->secret_key;
        $requestString= $this->requestString($requestParamsJoined, $salt);
        $ciphertext = openssl_encrypt($requestString, $method, $CryptoKey, OPENSSL_RAW_DATA, $iv);
        $encdata = base64_encode($ciphertext);
        $url = self::BASE_URL;

        return view("gateway.qikpay.form",compact('encdata','pay_id','url'));
    }

    public function callback($id, Request $request)
    {
        $response = $request->all();

        if (!empty($id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $id)
                ->first();
            if (empty($transaction_session)) {
                $error = 'Transaction not found.';
            }

            $input = json_decode($transaction_session->request_data, 1);
            if(isset($response["RESPONSE_CODE"]) && $response["RESPONSE_CODE"] == "000") {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($response['PG_TXN_MESSAGE']) ? $response['PG_TXN_MESSAGE'] : 'Your transaction could not processed.');
            }
            unset($input["reqest_data"]);

            // Update callback response
            $input['gateway_id'] = $response["HASH"] ?? null;
            $this->updateGatewayResponseData($input, $response);

            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);

            return redirect($store_transaction_link);
        }
    }

    public function requestString($array, $salt_key)
    {
        sort($array);
        $merchant_data_string = implode('~', $array);
        $format_Data_string = $merchant_data_string . $salt_key;
        $hashData_uf = hash('sha256', $format_Data_string);
        $hashData = strtoupper($hashData_uf);
        $hashValue='~HASH='.$hashData;
        $finalString = $merchant_data_string.$hashValue;
        return $finalString;
    }
}
