<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class Cellulant extends Controller
{
    use StoreTransaction;
  
    public function checkout($input, $check_assign_mid) {

        try {
            
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('cellulant-confirmation', [Crypt::encryptString($input['session_id'])]),
            ];  

        } catch(\Exception $e) {

            \Log::info([
                'cellulant-exception' => $e->getMessage(),
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

            $payload = [
                'merchantTransactionID' => $input['order_id'],
                'requestAmount' => $input['converted_amount'],
                'currencyCode' => $input['converted_currency'],
                'accountNumber' => $input['order_id'],
                'serviceCode' => $check_assign_mid->service_code,
                'customerFirstName' => $input['first_name'],
                'customerLastName' => $input['last_name'],
                'customerEmail' => $input['email'],
                'successRedirectUrl' => route('cellulant-success', $input['session_id']),
                'pendingRedirectUrl' =>  route('cellulant-pending', $input['session_id']),
                'failRedirectUrl' => route('cellulant-fail', $input['session_id']),
                'paymentWebhookUrl' => route('cellulant-webhook', $input['session_id']),
            ];
            $input['gateway_id'] = $input['order_id'] ?? null;
            $this->updateGatewayResponseData($input, []);
            
            $payload['country'] = $input['country'];
            $payload['encrypt'] = $this->encrypt($check_assign_mid->iv_key, $check_assign_mid->secret_key, $payload);
            $payload['access_key'] = $check_assign_mid->access_key;

            \Log::info([
                'cellulant-input' => $payload,
                'id' => $input['session_id']
            ]);

        } catch (\Exception $e) {

            $error = $e->getMessage();

            \Log::info([
                'cellulant-form-exception' => $e->getMessage(),
                'id' => $session_id
            ]);
            $session_id = $payload = '' ;
        }

        return view('gateway.cellulant', compact('error', 'session_id', 'payload'));
    }

    private function encrypt($ivKey, $secretKey, $payload = []) {

        $encrypt_method = "AES-256-CBC";        
        $key = hash('sha256', $secretKey);      
        $iv = substr(hash('sha256', $ivKey), 0, 16);        
        $encrypted = openssl_encrypt(json_encode($payload, true), $encrypt_method, $key, 0, $iv);        
        $encryptedPayload = base64_encode($encrypted);    
           
        return $encryptedPayload;    
    }

    /*
     * For successRedirectUrl
     * */
    public function success($id, Request $request) {
        
        $responseData = $request->all();

        \Log::info([
            'cellulant-success' => $responseData,
            'id' => $id
        ]);

        if (! empty($id)) {

            $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

            if ($transaction_session == null) {
                return abort(404);
            }

            $input = json_decode($transaction_session->request_data, 1);
            $this->updateGatewayResponseData($input, $responseData);
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            
            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);

            return redirect($store_transaction_link);
        }
    }

    /*
     * For failRedirectUrl 
     * */
    public function fail($id, Request $request) {
        
        $responseData = $request->all();
        \Log::info([
            'cellulant-fail' => $responseData,
            'id' => $id
        ]);
        
        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $id)
        ->first();

        if ($transaction_session == null) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data, 1);
        $this->updateGatewayResponseData($input, $responseData);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction could not processed.';
        
        // store transaction
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
       
        return redirect($store_transaction_link);
    }

    /*
     * For pendingRedirectUrl
     * */
    public function pending($id, Request $request) {
        
        $responseData = $request->all();
        \Log::info([
            'cellulant-pending' => $responseData,
            'id' => $id
        ]);
        
        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $id)
        ->first();

        if ($transaction_session == null) {

            return abort(404);
        }

        $input = json_decode($transaction_session->request_data, 1);
        $this->updateGatewayResponseData($input, $responseData);

        $transactions = DB::table('transactions')
        ->where('order_id', $transaction_session->order_id)
        ->first();
        
        if ( empty($transactions) ) {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
            $transaction_response = $this->storeTransaction($input); 
        } else {
            $input['status'] = $transactions->status ?? '0';
            $input['reason'] = $transactions->reason ?? 'Your transaction could not processed.';
        }
        
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    /*
     * For paymentWebhookUrl
     * */
    public function webhook($id, Request $request) {
        
        $responseData = $request->all();
        
        \Log::info([
            'cellulant-webhook' => $responseData,
            'id' => $id
        ]);
        
        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $id)
        ->first();

        if ($transaction_session == null) {
            return abort(404);
        }

        $input = json_decode($transaction_session->request_data, 1);
        $this->updateGatewayResponseData($input, $responseData);

        $transactions = DB::table('transactions')
        ->where('order_id', $transaction_session->order_id)
        ->first();
       
        if (empty($transactions) && isset($responseData['requestStatusCode']) && $responseData['requestStatusCode'] == '178') {
            
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $transaction_response = $this->storeTransaction($input);
        } else if (empty($transactions)) {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction could not processed.';
            $transaction_response = $this->storeTransaction($input);
        }
        exit();
    }    

}
