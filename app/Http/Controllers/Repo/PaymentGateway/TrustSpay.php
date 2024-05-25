<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use App\Traits\CCDetails;
use Illuminate\Support\Facades\Crypt;

class TrustSpay extends Controller
{
    use StoreTransaction, CCDetails;

    //const BASE_URL = 'https://shoppingingstore.com/TestTPInterface'; // test
    const BASE_URL = 'https://shoppingingstore.com/TPInterface'; // live
    
    public function checkout($input, $check_assign_mid) {
        
        $cardDetails = $input['card_no'] . '|' . $input['ccExpiryYear'] . '|' . $input['ccExpiryMonth'] . '|' . $input['cvvNumber'];

        $cardType = $this->getCardType($input['card_no']);
        \Log::info([
            'trustspay-cardtype' => $cardType,
            'trustspay-order_id' => $input['order_id']
        ]);

        // Fetch credential based on card type
        $check_assign_mid = $this->getCredential($input['payment_gateway_id'], $cardType);

        if (empty($check_assign_mid)) {

            return [
                'status' => '0',
                'reason' => 'Your card type is not supported. Please check the card type',
                'order_id' => $input['order_id']
            ];
        }

        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route('trustspay-confirmation', [
                Crypt::encryptString($input['session_id']),
                Crypt::encryptString($cardDetails)
            ])
        ];
    }

    public function confirmation(Request $request, $session_id, $cardDetails) {
        
        $error = $input = $merNo = $gatewayNo = $signkey = '';

        try {

            $session_id = Crypt::decryptString($session_id);
            $cardDetails = Crypt::decryptString($cardDetails);

            $transaction_session = DB::table('transaction_session')->where('transaction_id', $session_id)->first();

            if ($transaction_session == null) {
                throw new \Exception('Transaction not found.');
            }

            $input = json_decode($transaction_session->request_data, 1);
            $cardDetails = explode('|', $cardDetails);
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            $cardType = $this->getCardType($cardDetails[0]);

            // Check for if set valid credential based on card
            $check_assign_mid = $this->getCredential($input['payment_gateway_id'], $cardType);
            if (! empty($check_assign_mid)) {

                // From payment gateway credential
                $merNo = $check_assign_mid[0];
                $gatewayNo = $check_assign_mid[1];
                $signkey = $check_assign_mid[2];
            }

            if (empty($merNo) || empty($gatewayNo) || empty($signkey)) {
                throw new \Exception('Your MID is deactivated, please contact Technical team.');
            }

            $input['merNo'] = $merNo;
            $input['gatewayNo'] = $gatewayNo;
            $input['signkey'] = $signkey;
            $input['orderNo'] = uniqid();
            $signInfo = $input['merNo'] . $input['gatewayNo'] . $input['orderNo'] . $input['currency'] . $input['converted_amount'] . $input['first_name'] . $input['last_name'] . $cardDetails[0] . $cardDetails[1] . $cardDetails[2] . $cardDetails[3] . $input['email'] . $input['signkey'];

            $input['signinfo'] = hash('sha256', trim($signInfo));
        } catch (\Exception $e) {

            $error = $e->getMessage();
            $cardDetails = '';

            \Log::info([
                'trustspay-payment-form-exception' => $e->getMessage()
            ]);
        }

        return view('gateway.trustspay', compact('error', 'input', 'session_id', 'cardDetails'));
    }

    /*
     * For formsubmit
     */
    public function confirmationFormSubmit(Request $request) {
        
        try {

            $data = [
                'merNo' => $request->merNo,
                'gatewayNo' => $request->gatewayNo,
                'orderNo' => $request->orderNo,
                'orderCurrency' => $request->orderCurrency,
                'orderAmount' => $request->orderAmount,
                'returnUrl' => $request->returnUrl,
                'cardNo' => $request->cardNo,
                'cardExpireMonth' => $request->cardExpireMonth,
                'cardExpireYear' => $request->cardExpireYear,
                'cardSecurityCode' => $request->cardSecurityCode,
                'csid' => $request->csid,
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'address' => $request->address,
                'zip' => $request->zip,
                'signInfo' => $request->signInfo,
                'ip' => $request->ip,
                'issuingBank' => $request->issuingBank
            ];

            $url = self::BASE_URL;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);
            $err = curl_error($curl);
            $response = curl_exec($curl);
            curl_close($curl);

            \Log::info([
                'trustspay-input' => $data
            ]);

            \Log::info([
                'trustspay-response' => $response
            ]);

            if ($err) {
                throw new \Exception('Error: ' . $err);
            }

            $xml = simplexml_load_string($response);
            $json = json_encode($xml);
            $responseData = json_decode($json, true);

            if ($responseData['orderStatus'] == 1) {

                return $this->success($request->session_id, $request);
            }
            return $this->fail($request->session_id, $request, $responseData['orderInfo']);
        } catch (\Exception $e) {

            \Log::info([
                'trustspay-exception' => $e->getMessage()
            ]);

            // Complete this transaction because of the exception
            DB::table('transaction_session')->where('transaction_id', $request->session_id)->update([
                'response_data' => $e->getMessage(),
                'is_completed' => '1'
            ]);

            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $request->order_id
            ];
        }

        return view('gateway.trustspay', compact('error', 'input', 'session_id', 'cardDetails'));
    }

    /*
     * For success transaction
     */
    public function success($id, Request $request) {
        $body = $request->all();

        \Log::info([
            'trustspay-success' => $body,
            'id' => $id
        ]);

        if (! empty($id)) {
            $transaction_session = DB::table('transaction_session')->where('transaction_id', $id)->first();
            if ($transaction_session == null) {

                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';

            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);

            return redirect($store_transaction_link);
        }
    }

    /*
     * For fail transaction
     */
    public function fail($id, Request $request, $message = '') {
        
        $body = $request->all();
        \Log::info([
            'trustspay-fail' => $body,
            'id' => $id
        ]);
        $transaction_session = DB::table('transaction_session')->where('transaction_id', $id)->first();

        if ($transaction_session == null) {

            $error = 'Transaction not found.';
        }
        $input = json_decode($transaction_session->request_data, 1);
        $input['status'] = '0';
        $input['reason'] = $message;
        // store transaction
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function getCredential($payment_gateway_id, $cardType) {
        
        $check_assign_mid = checkAssignMID($payment_gateway_id);

        return [
            $check_assign_mid->merNo,
            $check_assign_mid->gatewayNo,
            $check_assign_mid->signkey
        ];
    }
}