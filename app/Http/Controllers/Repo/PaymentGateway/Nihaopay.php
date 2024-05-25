<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class Nihaopay extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://api.nihaopay.com/v1.2'; // live
    // const BASE_URL = 'https://apitest.nihaopay.com/v1.2'; // test
  
    public function checkout($input, $check_assign_mid) {
        
        try {

            $currency = config('nihaopay.currency');
            if (! in_array (strtoupper($input['converted_currency']) , $currency)) {
                throw new \Exception('This currency is not supported.');                               
            }

            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('nihaopay-confirmation', [Crypt::encryptString($input['session_id'])]),
            ];  

        } catch(\Exception $e) {

            \Log::info([
                'nihaopay-exception' => $e->getMessage(),
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

        try {
            $session_id = Crypt::decryptString($session_id); 
            $transaction_session = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->first();

            if ($transaction_session == null) {
                $err = 'Transaction not found.';
            }

            $input = json_decode($transaction_session->request_data, 1);
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            $currency = config('nihaopay.currency');
            if (! in_array (strtoupper($input['converted_currency']) , $currency)) {
                throw new \Exception('This currency is not supported.');                               
            }

            /**
             *
             * Amount Format
             * -------------
             * Amount as a positive integer of the minor unit of the currency
             * The amount must not contain any decimal points, thousands separators or currency symbols.
             * This value cannot be negative or zero.
             * 
             * For Example, $10.50 in USD would be 1050, ￥100 in JPY would be 100.
             */

            if (strtoupper($input['converted_currency']) == 'JPY' ) {
                $amount =  $input['converted_amount'];   
            } else {
                $amount =  $input['converted_amount'] * 100;   
            }
            
            $data = [
                'amount'        => $amount,
                'currency'      => $input['converted_currency'],
                'vendor'        => 'unionpay', // unionpay, alipay, wechatpay
                'reference'     => $input['order_id'],
                'ipn_url'       => route('nihaopay-notification', $input['session_id']),
                'callback_url'  => route('nihaopay-callback', $input['session_id']),
                'description'   => 'Pay By' . $input['first_name'] . ' ' . $input['last_name']
            ]; 

            \Log::info([
                'nihaopay-input' => $data,
                'id' => $input['session_id']
            ]);

            $url = self::BASE_URL . "/transactions/securepay";
            $token = "Bearer " . $check_assign_mid->token;
            $headers = [
                "authorization: $token",
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $err = curl_error($curl);
            $response = curl_exec($curl);
            curl_close($curl);
            
            if ($err) {
                throw new \Exception($err);
            }

        } catch (\Exception $e) {
            
            \Log::info([
                'nihaopay-exception' => $e->getMessage(),
                'id' => $session_id
            ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
        return $response;
    }

    public function notification($id, Request $request) {
        
        $response = $request->all();

        \Log::info([
            'nihaopay-notification' => $response
        ]);

        $transaction_session = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data,true);

        if ($response['status'] == 'success') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
        } else {
            $input['status'] = '0';
            $input['reason'] =  'Your transaction could not processed.';
        }

        // Update callback response
        $input['gateway_id'] = $response['id'] ?? null;
        $this->updateGatewayResponseData($input, $response);
        // store transaction
        $transaction_response = $this->storeTransaction($input);
        exit();
    }

     public function callback($id, Request $request) {
        
        $response = $request->all();

        \Log::info([
            'nihaopay-callback' => $response
        ]);

        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $id)
        ->first();

        if ($transaction_session == null) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data,true);

        $transactions = DB::table('transactions')
        ->where('order_id', $transaction_session->order_id)
        ->first();

        if ( isset($response['status']) && $response['status'] == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        } else {
            $input['status'] = $transactions->status ?? 0;
            $input['reason'] = $transactions->reason ?? 'Your transaction could not processed.';
        }

        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }
}

