<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class QartPay extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://dashboard.qartpay.com/crm/jsp/paymentrequest'; // live
    // const BASE_URL = 'https://uat.qartpay.com/crm/jsp/paymentrequest'; // test
  
    public function checkout($input, $check_assign_mid) {
        
        try {
            $currency = config('qartpay.currency.' . strtoupper($input['converted_currency']));
            if (empty($currency)) {
                throw new \Exception('This currency is not supported.');                               
            }
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('qartpay-confirmation', [Crypt::encryptString($input['session_id'])]),
            ];  
        } catch(\Exception $e) {
            \Log::info([
                'qartpay-exception' => $e->getMessage(),
                'id' => $input['session_id']
            ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.'
                'order_id' => $input['order_id'],
            ];
        } 
    }

    public function confirmation(Request $request, $session_id) {
        $error = '';
        try {
            $session_id = Crypt::decryptString($session_id); 
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            $currency = config('qartpay.currency.' . strtoupper($input['converted_currency']));
            if (empty($currency)) {
                throw new \Exception('This currency is not supported.');                               
            }
            /**
             *
             * Amount Format
             * -------------
             * The amount of the transaction, expressed in the smallest currency unit.
             * The amount must not contain any decimal points, thousands separators or currency symbols.
             * This value cannot be negative or zero.
             * 
             * For example, INR 12.50 will be expressed as 1250.
             * INR 1 will be is expressed as 100.
             * 
             * Note: Transactions in currency IDR (Indonesian Rupiah) will use an exponent of 0 (zero).
             * This means an amount expressed as 1250 will be treated as IDR Rp1,
             * 250 and not IDR Rp12.50 (with exponent 2) unlike other currencies.
             * 
             */
            $amount = $input['converted_amount'] * 100;
            $data = [
                'PAY_ID' => $check_assign_mid->PAY_ID,
                'ORDER_ID' => $input['order_id'],
                'AMOUNT' => $amount,
                'TXNTYPE' =>  'SALE', // Merchant Transaction Type AUTH/SALE
                'CUST_NAME' => $input['first_name'] . ' ' . $input['last_name'],
                'CUST_STREET_ADDRESS1' => $input['address'],
                'CUST_ZIP' => $input['zip'],
                'CUST_PHONE' => $input['phone_no'],
                'CUST_EMAIL' => $input['email'],
                'PRODUCT_DESC' => 'Pay By ' . config('app.name'),
                'CURRENCY_CODE' => $currency, //356,826,840,978 
                'RETURN_URL' => route('qartpay-callback',$input['session_id'])
            ]; 
            ksort($data);
            $explodeArray = urldecode(http_build_query($data, '', '~'));
            $explodeArray = $explodeArray .$check_assign_mid->SALT;
            $url = self::BASE_URL;
            \Log::info([
                'qartpay-form-HASH' => $explodeArray,
                'id' => $input['session_id']
            ]);
            $hash =  hash('sha256',$explodeArray);
            $data['HASH'] = strtoupper($hash);
            $data['action'] = $url;
            \Log::info([
                'qartpay-input' => $data,
                'id' => $input['session_id']
            ]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            \Log::info([
                'qartpay-form-exception' => $e->getMessage(),
                'id' => $session_id
            ]);
            $session_id = $data = '' ;
        }
        return view('gateway.qartpay', compact('error', 'data', 'session_id'));
    }

    public function callback($id, Request $request) {
        $response = $request->all();
        \Log::info([
            'qartpay-callback' => $response,
            'id' => $id
        ]);
        if (! empty($id)) {
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $id)
                ->first();
            if ($transaction_session == null) {
                $error = 'Transaction not found.';
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response['STATUS'] == 'Approved' || $response['STATUS'] == 'Captured') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($response['RESPONSE_MESSAGE']) ? $response['RESPONSE_MESSAGE'] : 'Your transaction could not processed.');
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

