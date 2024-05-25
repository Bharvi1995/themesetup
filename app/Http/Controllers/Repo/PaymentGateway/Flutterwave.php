<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Mail;
use Session;
use Exception;
use App\User;
use App\TransactionSession;
use App\Merchantapplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;

class Flutterwave extends Controller
{
    const BASE_URL = 'https://api.flutterwave.com/v3/';

    use StoreTransaction;

    // ================================================
    /* method : transaction
    * @param  : 
    * @Description : send to payment gateway
    // */// ==============================================
    public function checkout($input, $check_assign_mid) {

        try {
            
            $data = [
                "tx_ref" => $input['order_id'],
                "amount" => $input['converted_amount'],
                "currency" => $input['converted_currency'],
                "redirect_url" => route('flutterwave-callback', $input['session_id']),
                'customer' => [
                    'email' => $input['email'],
                    'phonenumber' => $input['phone_no'],
                    'name' => $input['first_name'] . $input['last_name']
                ],
            ];
            \Log::info([
                'flutterwave_input_response' => $data,
            ]);
            $url = self::BASE_URL . "payments";
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer '. $check_assign_mid->secret_key
            ];
        
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 200);
            curl_setopt($curl, CURLOPT_TIMEOUT, 200);
            $response_body = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            $result = json_decode($response_body, true);

            \Log::info([
                'flutterwave-input' => $data
            ]);
            \Log::info([
                'flutterwave-response' => $response_body,
            ]);

            if ($err) {
                throw new \Exception($err);
            }
            
            if (isset($result['status']) && $result['status'] == 'success') {
                if ( isset($result['data']['link']) && $result['data']['link'] != ' ') {

                    $input['gateway_id'] = $input['session_id'] ?? null;
                    $this->updateGatewayResponseData($input, $result);
                    
                    // redirect to flutterwave server
                    return [
                        'status' => '7',
                        'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                        'redirect_3ds_url' => $result['data']['link'],
                    ];
                }
            }

            throw new \Exception('Your transaction could not processed.');

        } catch (Exception $e) {

            \Log::info([
                'flutterwave-exception' => $e->getMessage()
            ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        
        }      
    }

    public function callback($session_id, Request $request) {

        $response = $request->all();
        $id = $session_id;
        \Log::info([
            'flutterwave-callback' => $response,
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

            if ($response['status'] == 'successful') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else {
                $input['status'] = '0';
                $input['reason'] = (isset($response['reason']) ? $response['reason'] : 'Your transaction could not processed.');
            }

            // store transaction
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);

            return redirect($store_transaction_link);
        }
    }

}