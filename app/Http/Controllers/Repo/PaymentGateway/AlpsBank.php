<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\TransactionSession;
use Carbon\Carbon;

class AlpsBank extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'http://payments.alps.cl/justpay/check-out/SecurePayment'; // live
    //const BASE_URL = 'http://paymentscert.alps.cl/justpay/check-out/SecurePayment'; // test
    
    public function checkout($input, $check_assign_mid)
    {
        $form_params = [
            'public_key' => $check_assign_mid->public_key,
            'time' => Carbon::now()->format('Y-m-d H:i:s'),
            'amount' => $input['converted_amount'],
            'currency' => $input['converted_currency'],
            'trans_id' => $input['order_id'],
            'time_expired' => 90,
            'url_ok' => route('alpsbank.success',$input['session_id']),
            'url_error' => route('alpsbank.fail',$input['session_id']),
            'channel' => 1,
            'signature' => $check_assign_mid->secret_key,
        ];
        $signature = hash('sha256', implode('', array_values($form_params)));
        $form_params['signature'] = $signature;
        $request_url = self::BASE_URL;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $form_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);        
        curl_close ($ch);
        $response_data = parse_url($response_body);
        try {
            parse_str($response_data['query'], $query_array);
            $input['gateway_id'] = $query_array['tokenID'] ?? null;
            $this->updateGatewayResponseData($input, $response_body);
        } catch (\Exception $e) {
            \Log::info([
                'alpsbank_response_catch' => $e->getMessage()
            ]);
        }
        if (filter_var($response_body, FILTER_VALIDATE_URL)) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect.',
                'redirect_3ds_url' => $response_body
            ];
        }
        \Log::info(['alpsbank_response' => $response_body]);
        $input['status'] = '0';
        $input['reason'] = 'Transaction authentication failed.';
        $transaction_response = $this->storeTransaction($input);
        return [
            'status' => '0',
            'reason' => $input['reason'],
            'order_id' => $input['order_id'],
        ];
    }

    public function succesRedirect($id){
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['status'] = '1';
        $input['reason'] = 'Your transaction was proccessed successfully.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function failRedirect($id){
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
}
