<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;


class OnlineNaira extends Controller
{
    use StoreTransaction;
    // ================================================
    /* method : checkout
     * @param  : 
     * @Description : transaction by inline js API
     */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route('onlinenaira.inputForm', [$input['session_id']]),
        ];
        
    }

    public function inputForm($session_id){
        $input = DB::table('transaction_session')
                ->where('transaction_id', $session_id)
                ->where('is_completed','0')
                ->value('request_data');
        \Log::info([
            'input' => $input,
        ]);        
        if ($input != null) {
            $input = json_decode($input, true);
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        } else {
            return abort('404');
        }
        return view('gateway.onlinenaira.input', compact('session_id','input','check_assign_mid'));
    }

    public function cancel(Request $request,$session_id){
         \Log::info([
            'onlineNairaCancel' => $request->toArray(),
            'session_id' => $session_id
        ]); 
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first(); 
        if($data) {
            $input = json_decode($data->request_data, 1);
            $input['status'] = '0';
            $input['reason'] = 'Your transaction was canceled.';
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function callback(Request $request,$session_id){
        \Log::info([
            'onlineNairaCallback' => $request->toArray(),
            'session_id' => $session_id
        ]);
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first(); 
        if($data) {
            $input = json_decode($data->request_data, 1);
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccess successfully.';
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }

    public function callbacknotify(Request $request,$session_id){
        \Log::info([
            'onlineNairaCallbackNotify' => $request->toArray(),
            'session_id' => $session_id
        ]);
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first(); 
        if($data) {
            $input = json_decode($data->request_data, 1);
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccess successfully.';
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }









    public function notify(Request $request){
        \Log::info([
            'onlineNairaNotify' => $request->toArray(),
        ]);   
    }   

    public function return(Request $request){
        \Log::info([
            'onlineNairaReturn' => $request->toArray(),
        ]); 
        echo "<pre>";print_r($request->toArray());
    }
}