<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\PaythroneUser;
use App\TransactionSession;
use App\Transaction;
use Illuminate\Support\Facades\Crypt;

class Paythrone extends Controller
{
    use StoreTransaction;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->Transaction = new Transaction;
        $this->paythrone_user = new PaythroneUser;
    }

    public function checkout($input, $check_assign_mid) {
        try {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('paythrone-confirmation', [Crypt::encryptString($input['session_id'])]),
            ];  

        } catch(\Exception $e) {

            \Log::info([
                'paythrone-exception' => $e->getMessage(),
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
            $paythroneUser = $this->paythrone_user->findOrCreate($input);
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            $data = [
                'project_key' => $check_assign_mid->project_public_key, //project public key
                'user' => $paythroneUser->id,
                'name' => $input['first_name'] . ' ' . $input['last_name'],
                'price' =>  $input['converted_amount'],
                'order_id' => $input['session_id'],
                'currency' => $input['converted_currency']
            ]; 
            \Log::info([
                'paythrone-input' => $data,
                'id' => $input['session_id']
            ]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            \Log::info([
                'paythrone-form-exception' => $e->getMessage(),
                'id' => $session_id
            ]);
            $session_id = $data = '' ;
        }
        return view('gateway.paythrone', compact('error', 'session_id', 'data'));
    }

    public function webhook(Request $request) {
        $response = $request->all();
        $id = isset($response['order_id']) ? $response['order_id'] : '';
        \Log::info([
            'paythrone-webhook' => $response,
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
            if ($response['status'] == 'completed') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($response['reason']) ? $response['reason'] : 'Your transaction could not processed.');
            }
            // Update callback response
            $input['gateway_id'] = $response['order_id'] ?? null;
            $this->updateGatewayResponseData($input, $response);
            unset($input['api_key']);
            unset($input['country_code']);
            unset($input['is_disable_rule']);
            unset($input['bin_country_code']);
            unset($input['request_from_type']);
                
            $this->Transaction->storeData($input);
            \DB::table('transaction_session')
                ->where('transaction_id', $input['session_id'])
                ->update(['is_completed' => '1']);
            // $transaction_response = $this->storeTransaction($input);
            exit();
        }
    }
    
    public function redirect($session_id) {
        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $session_id)
        ->first();
        if ($transaction_session == null) {
            return abort(404);
        }
        $input = json_decode($transaction_session->request_data,true);
        $transactions = DB::table('transactions')
        ->where('order_id', $transaction_session->order_id)
        ->first();
        if(empty($transactions)) {
            $input['status'] = $transactions->status ?? 0;
            $input['reason'] = $transactions->reason ?? 'Your transaction could not processed.';
            unset($input['api_key']);
            unset($input['country_code']);
            unset($input['is_disable_rule']);
            unset($input['bin_country_code']);
            unset($input['request_from_type']);
            // $transaction_response = $this->storeTransaction($input);
            $this->Transaction->storeData($input);
            \DB::table('transaction_session')
                    ->where('transaction_id', $input['session_id'])
                    ->update(['is_completed' => '1']);
        } else {
            $input['status'] = $transactions->status ?? 0;
            $input['reason'] = $transactions->reason ?? 'Your transaction could not processed.';
        }
        
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

}
