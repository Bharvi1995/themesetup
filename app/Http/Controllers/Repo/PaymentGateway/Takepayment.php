<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class Takepayment extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://gw1.tponlinepayments.com/paymentform/';
  
    public function checkout($input, $check_assign_mid) {
        
        try {

            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('takepayment-confirmation', [Crypt::encryptString($input['session_id'])]),
            ];  

        } catch(\Exception $e) {

            \Log::info([
                'takepayment-exception' => $e->getMessage(),
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

            $data = [
                'merchantID' => $check_assign_mid->merchant_id,
                'action' => 'SALE',  // PREAUTH, VERIFY
                'type' => 1,
                'countryCode' => $input['country'],
                'currencyCode' => $input['converted_currency'],
                'amount' => $input['converted_amount'] * 100,
                'orderRef' => 'Pay By ' .$input['first_name'] . ' ' . $input['last_name'],
                'transactionUnique' => $input['order_id'],
                'callbackURL' => route('takepayment-callback', $input["session_id"]),
                'redirectURL' => route('takepayment-redirect', $input["session_id"])
            ]; 

            ksort($data);

            $sign = http_build_query($data, '', '&');
            $sign = str_replace(array('%0D%0A', '%0A%0D', '%0D'), '%0A', $sign);
            $data['signature'] = hash('SHA512', $sign . $check_assign_mid->signature_key);
            $data['redirect'] = self::BASE_URL;
            
            \Log::info([
                'takepayment-input' => $data,
                'id' => $input['session_id']
            ]);

        } catch (\Exception $e) {
            
            $error = $e->getMessage();
            \Log::info([
                'takepayment-form-exception' => $e->getMessage(),
                'id' => $session_id
            ]);
            $session_id = $data = '' ;
        }

        return view('gateway.takepayment', compact('error', 'data', 'session_id'));
    }

    public function callback($id, Request $request) {
        
        $response = $request->all();
        \Log::info([
            'takepayment-callback' => $response
        ]);

        $transaction_session = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data,true);

        if ($response['responseCode'] == 0) {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
        } else {
            $input['status'] = '0';
            $input['reason'] = (isset($response['responseMessage']) ? $response['responseMessage'] : 'Your transaction could not processed.');
        }

        // Update callback response
        $input['gateway_id'] = $response['transactionID'] ?? null;
        $this->updateGatewayResponseData($input, $response);
        // store transaction
        $transaction_response = $this->storeTransaction($input);
        exit();
    }

    public function redirect($id, Request $request) {
        

        $response = $request->all();
        \Log::info([
            'takepayment-redirect' => $response,
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

            if ($response['responseCode'] == 0) {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($response['responseMessage']) ? $response['responseMessage'] : 'Your transaction could not processed.');
            }
            
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
           
            return redirect($store_transaction_link);
        }
    }

}

