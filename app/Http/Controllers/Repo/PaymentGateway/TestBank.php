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


class TestBank extends Controller
{
    use StoreTransaction;

    public function __construct()
    {
        $this->transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route('test-bank-transaction',$input['session_id']),
        ];       
    }

    public function testBankForm(Request $request, $session_id)
    {
        $request_data = $request->all();
        
        $transaction_session = DB::table('transaction_session')
        ->where('transaction_id', $session_id)
        ->first();
        
        if ($transaction_session == null) {
            return response()->json(['message' => 'Transaction not found.']);
        }
        
        $request_data = json_decode($transaction_session->request_data, 1);
        
        return view('gateway.testbankform', compact('session_id', 'request_data'));
    }

    public function testbankFormSubmit(Request $request, $session_id)
    {   
        $transaction_session = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->first();

        if ($transaction_session == null) {
            return response()->json(['message' => 'Transaction not found.']);
        }

        $input = json_decode($transaction_session->request_data, 1);

        $input['reason'] = $request->transaction_response;

        $input['status'] = $input['reason'] == 'Approved' ? '1' : '0';

        // store transaction
        $transaction_response = $this->storeTransaction($input);
        
        $store_transaction_link = $this->getRedirectLink($input);
        
        return redirect($store_transaction_link);
    }
}
