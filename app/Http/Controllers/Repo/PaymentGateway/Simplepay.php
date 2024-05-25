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

class Simplepay extends Controller
{
    use StoreTransaction;

    const BASE_URL = 'https://gate.simple-pay.cc/api/v1';

    // ================================================
    /* method : stripeForm
    * @param  :
    * @Description : Load stripe test form
    */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        if($input["amount_in_usd"] <= 45){
            return [
                'status' => '5',
                'reason' => 'Minimum transaction amount should be more than 45 USD',
                'order_id' => $input['order_id'],
            ];
        }
        try {
            $payment_url = self::BASE_URL . '/purchases/';
            
            $ArrPost = [
                'client' => array ( 'email' => $input['email']),
                'purchase' => array (
                    'currency' => $input['converted_currency'],
                    'products' => array (
                        array (
                                'name' => $input['first_name'] . ' ' . $input['last_name'],
                                'price' => $input['converted_amount']*100,
                            ),
                    ),
                ),
                'reference_generated' => 'true',
                'reference' => $input["session_id"],
                'brand_id' => $check_assign_mid->brand_id,
                'failure_redirect' => route('simplepay-fail', $input['session_id']),
                'success_redirect' => route('simplepay-success', $input['session_id']),
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $payment_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($ArrPost),
                CURLOPT_HTTPHEADER => array(
                    "authorization: Bearer ".$check_assign_mid->secret_key,
                    "cache-control: no-cache",
                    "content-type: application/json",
                ),
            ));

            $response   = curl_exec($curl);
            \Log::info([
                'simplepay_payload' => $ArrPost
            ]);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return [
                    'status' => '0',
                    'reason' => $err,
                    'order_id' => $input['order_id'],
                ];
            } else {
                $result = json_decode($response, true);
                \Log::info([
                    'simple_pay_response' => $result
                ]);
            }
            if(isset($result['id'])) {
                $input['gateway_id'] = $result['id'] ?? null;
                $this->updateGatewayResponseData($input, $result);
            }

            if(isset($result['checkout_url']) && !empty($result['checkout_url'])) {
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $result['checkout_url'],
                ];
            } else {
                return [
                    'status' => '0',
                    'reason' => (isset($response['errors']) ? reset($response['errors']) : 'Your transaction could not processed.'),
                    'order_id' => $input['order_id'],
                ];
            }

        } catch(\Exception $e) {
            \Log::info(['Simplepay-exception' => $e->getMessage()]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id'],
            ];
        }
    }

    public function success($id, Request $request) {
        $body = $request->all();
        \Log::info([
            'simplepay-success' => $body,
            'id' => $id
        ]);
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
    
    public function fail($id, Request $request) {
        $body = $request->all();
        \Log::info([
            'simplepay-fail' => $body,
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

}
