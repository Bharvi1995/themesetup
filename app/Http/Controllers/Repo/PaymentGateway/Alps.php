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

class Alps extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'http://payments.alps.cl/justpay/check-out/SecurePayment'; // live
    //const BASE_URL = 'http://paymentscert.alps.cl/justpay/check-out/SecurePayment'; // test
    
    public function checkout($input, $check_assign_mid)
    {
        $current_time = date('Y-m-d\TH:i:sP');
        $signInfo = $check_assign_mid->public_key.$current_time.$input['converted_amount'].$input['converted_currency'].$input['session_id'].'30'.route('alps.success',$input["session_id"]).route('alps.fail',$input['session_id']).'3'.$check_assign_mid->secret_key;
        $signature = hash('sha256', trim($signInfo));
        $shopper_information = [
            'name_shopper' => $input['first_name'],
            'last_name_Shopper' => $input['last_name'],
            'type_doc_identi' => 'RUT',
            'Num_doc_identi' => $input['session_id'],
            'email' => $input['email'],
            'country_code' => 91,
            'Phone' => $input['phone_no'],
        ];
        $request_data = [
            'public_key' => $check_assign_mid->public_key,
            'time' => $current_time,
            'channel' => 3,
            'amount' => $input['converted_amount'],
            'currency' => $input['converted_currency'],
            'trans_id' => $input['session_id'],
            'time_expired' => '30',
            'url_ok' => route('alps.success',$input['session_id']),
            'url_error' => route('alps.fail',$input['session_id']),
            'signature' => $signature,
            'shopper_information' => json_encode($shopper_information),
        ];
        \Log::info([
            'alps_response_data' => $request_data
        ]);

        $request_url = self::BASE_URL;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_body = curl_exec($ch);
        curl_close ($ch);
        try {
            $response_data = parse_url($response_body);
            parse_str($response_data['query'], $query_array);
            $input['gateway_id'] = $query_array['tokenID'] ?? null;
            $this->updateGatewayResponseData($input, $response_body);
        } catch (\Exception $e) {
            \Log::info([
                'alps_response_catch' => $e->getMessage()
            ]);
        }
        if (filter_var($response_body, FILTER_VALIDATE_URL)) {
            // redirect to acquirer server
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect.',
                'redirect_3ds_url' => $response_body
            ];
        }
        \Log::info(['alps_response' => $response_body]);

        $input['status'] = '0';
        $input['reason'] = 'Transaction authentication failed.';
        return [
            'status' => '0',
            'reason' => $input['reason'],
            'order_id' => $input['order_id'],
        ];
    }


    public function succesRedirect($id){
        http_response_code(200);
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
        http_response_code(200);
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
