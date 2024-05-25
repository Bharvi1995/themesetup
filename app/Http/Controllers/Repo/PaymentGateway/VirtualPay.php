<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\StoreTransaction;
use App\TransactionSession;
use App\Transaction;

class VirtualPay extends Controller
{
    use StoreTransaction;

    // ================================================
    /* method : __construct
    * @param  :
    * @Description : Create a new controller instance.
    */ // ==============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    public function checkout($input, $check_assign_mid)
    {
        $currency = $input["converted_currency"];
        $amount = $input["converted_amount"];
        $merchantId = $check_assign_mid->mid;
        $session_id = $input['session_id'];
        $customer = $input['first_name'] . "" . $input['last_name'];
        $email = $input['email'];
        $cardNo = $input['card_no'];
        $expiry = $input['ccExpiryMonth'] . "/" . substr($input['ccExpiryYear'], -2);
        $cvv = $input['cvvNumber'];
        $phone = $input['phone_no'];
        $country = $input['country'];
        $state = $input['state'];
        $city = $input['city'];
        $zip = $input['zip'];

        $payload = "<?xml
            version= \"1.0\"
            encoding= \"utf-8\"?>\r\n
            <message>\r\n
                <merchantID>" . $merchantId . "</merchantID>\r\n
                <requestID>" . $session_id . "</requestID>\r\n
                <date>" . date('dmy') . "</date>\r\n
                <requestTime>" . date('Y-m-d H:m:s') . "</requestTime>\r\n
                <customerName>" . $customer . "</customerName>\r\n
                <customerPhoneNumber>" . $phone . "</customerPhoneNumber>\r\n
                <cardNumber>" . $cardNo . "</cardNumber>\r\n
                <expiry>" . $expiry . "</expiry>\r\n       
                <amount>" . $amount . "</amount>\r\n
                <redirectUrl>" . route('virtualpay.redirect', $input['session_id']) . "</redirectUrl>\r\n
                <timeoutUrl>" . route('virtualpay.redirect', $input['session_id']) . "</timeoutUrl>\r\n     
                <currency>" . $currency . "</currency>\r\n
                <country>" . $country . "</country>\r\n  
                <city>" . $city . "</city>\r\n
                <cvv>" . $cvv . "</cvv>\r\n
                <postalCode>" . $zip . "</postalCode>\r\n     
                <stateCode>" . $state . "</stateCode>\r\n    
                <email>" . $email . "</email>\r\n
                <description>TEST PAYMENT1</description>\r\n
            </message>";

        \Log::info([
            'virtual-pay-request' => $payload
        ]);

        $request_url = "https://uat.evirtualpay.com:65443/api/authenticate";

        $response = $this->curlPostRequest($request_url, $payload, $check_assign_mid);
        \Log::info([
            'virtualpay-response' => $response
        ]);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $payment_response = json_decode($json, true);

        \Log::info([
            'virtualpay-response' => $response
        ]);

        // update session data
        if ($payment_response['responsecode'] == '0' && isset($payment_response['TransactionId'])) {
            $input['gateway_id'] = $payment_response['TransactionId'] ?? null;
            $this->updateGatewayResponseData($input, $payment_response);
        }

        if (isset($payment_response['responsecode']) && $payment_response['responsecode'] == '0') {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('virtualpay.pendingBlade', $input['session_id']),
            ];
        } elseif ($payment_response['responsecode'] != "0") {
            return [
                'status' => '0',
                'reason' => $payment_response['resmsg'] ?? 'Transaction authentication failed.',
                'order_id' => $input['order_id'],
            ];
        } else {
            return [
                'status' => '0',
                'reason' => $response_data['message'] ?? 'Transaction authentication failed.',
                'order_id' => $input['order_id'],
            ];
        }
    }

    public function pendingBlade($session_id)
    {
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $paymentResponse = json_decode($input_json["response_data"]);
        $input = json_decode($input_json['request_data'], true);
        $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);

        return view('gateway.virtualpay.pending', compact('paymentResponse', 'session_id', 'check_assign_mid'));
    }

    public function redirect(Request $request, $session_id)
    {
        $response = $request->all();
        \Log::info([
            'virtualpay-redirect' => $response,
            'id' => $session_id
        ]);

        if (!empty($session_id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);

            if (isset($response['result']) && $response['result'] == '0') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($response['responsedescription']) ? $response['responsedescription'] : 'Your transaction could not processed.');
            }
            unset($input["request_data"]);
            if (isset($response["id"])) {
                $input['gateway_id'] = $response["tid"] ?? "";
                $this->updateGatewayResponseData($input, $response);
            }
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function curlPostRequest($url, $data, $check_assign_mid)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: xml",
            "username: " . $check_assign_mid->username,
            "password: " . $check_assign_mid->password,
            'Authorization: Basic ' . base64_encode($check_assign_mid->username . ':' . $check_assign_mid->password)
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
