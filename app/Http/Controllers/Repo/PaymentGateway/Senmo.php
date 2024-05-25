<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\TransactionSession;
use Illuminate\Support\Facades\Crypt;

class Senmo extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://introrixkenya.com/cybersource';

    public $ArrCurrency = ['EUR' => 1, 'USD' => 2, 'GBP' => 3, 'AUD' => 4, 'INR' => 5];

    // ================================================
    /* method : stripeForm
    * @param  :
    * @Description : Load stripe test form
    */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        try {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('senmo-confirmation', [Crypt::encryptString($input['session_id'])]),
            ];  
        } catch(\Exception $e) {

            \Log::info([
                'senmo-exception' => $e->getMessage(),
                'id' => $input['session_id']
            ]);
            
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.'
                'order_id' => $input['order_id'],
            ];
        } 
    }

    public function success($id, Request $request) {
        $body = $request->all();
        \Log::info([
            'senmo-success' => $body,
            'id' => $id
        ]);
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        if($request->status == "approved"){
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
        }elseif ($request->status == "declined") {
            $input['status'] = '0';
            $input['reason'] = 'Your transaction was Declined.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }
    
    public function fail($id, Request $request) {
        $body = $request->all();
        \Log::info([
            'senmo-fail' => $body,
            'id' => $id
        ]);
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '0';
        $input['reason'] = 'Your transaction was Declined.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

     public function confirmation(Request $request, $session_id) {
        $session_id = Crypt::decryptString($session_id); 
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();
        if ($transaction_session) {
            $input = json_decode($transaction_session->request_data, 1);
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
            $data = [];
            $data["transaction_id"] = $transaction_session->transaction_id;
            $data["amount"]     = $input["converted_amount"];
            $data["currency"]   = isset($this->ArrCurrency[$input["converted_currency"]])?$this->ArrCurrency[$input["converted_currency"]]:0;
            $data["email"]      = $input["email"];
            $data["first_name"] = $input["first_name"];
            $data["last_name"]  = $input["last_name"];
            $data["phoneNum"]       = $input['phone_no'];
            $data["billCountry"]    = $input['country'];
            $data["billState"]      = $input['state'];
            $data["billCity"]       = $input['city'];
            $data["billAddress"]    = $input['address'];
            $data["billZip"]        = $input['zip'];            
            //$data["ipn"]            = "SBDPG3373750780279";
            $data["ipn"]   = $check_assign_mid->ipn;
            $data["callback_url"] = route('senmo-success', $input['session_id']);
            $data["redirect_url"] = route('senmo-success', $input['session_id']);
            return view('gateway.absa.pendingBlade', compact('data', 'session_id'));
        } else {
            return abort('404');
        }

    }

}
