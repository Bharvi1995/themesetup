<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class TrustPayment extends Controller
{
    use StoreTransaction;

    public function checkout($input, $check_assign_mid) {

        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route('trust-confirmation', Crypt::encryptString($input['session_id'])),
        ];   
    }

    public function confirmation(Request $request, $session_id) {

        $error = $input = $sitereference = '';

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
           
            // Pass in the form
            $sitereference = $check_assign_mid->Sitereferences; /* From payment gateway credential */
            

        } catch (\Exception $e) {
            
            $error = $e->getMessage();

            \Log::info([
                'trustpayment-form-exception' => $e->getMessage()
            ]);
        }
        
        return view('gateway.trust', compact('error', 'input', 'sitereference', 'session_id'));
        
    }

    /*
     * For successfulurlredirect
     * */
    public function success($id, Request $request) {
        
        $body = $request->all();

        \Log::info([
            'trustpayment-success' => $body,
            'id' => $id
        ]);

        if (! empty($id)) {

            DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->update([
                'response_data' => $body,
                'is_completed' => '1'
            ]);

            $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

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
     * For errorurlredirect 
     * */
    public function fail($id, Request $request) {
        
        $body = $request->all();
        \Log::info([
            'trustpayment-fail' => $body,
            'id' => $id
        ]);
        
        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $id)
        ->first();

        if ($transaction_session == null) {

            $error = 'Transaction not found.';
        }

        $input = json_decode($transaction_session->request_data, 1);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction could not processed.';
        
        // store transaction
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
       
        return redirect($store_transaction_link);
    }

    /*
     * For declinedurlredirect
     * */
    public function decline($id, Request $request) {
        
        $body = $request->all();
        \Log::info([
            'trustpayment-decline' => $body,
            'id' => $id
        ]);
        
        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $id)
        ->first();

        if ($transaction_session == null) {

            $error = 'Transaction not found.';
        }

        $input = json_decode($transaction_session->request_data, 1);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction could not processed.';
        
        // store transaction
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }

    /*
     * For webhook notification
     * */
    public function notification($id, Request $request) {
        
        $body = $request->all();
        \Log::info([
            'trust-callback' => $body,
            'id' => $id
        ]);
        
        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $id)
        ->first();

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