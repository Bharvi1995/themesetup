<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Session;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;

class Gamepay extends Controller
{
    use StoreTransaction;
    
    const BASE_URL = 'https://akwaababites.com/fatorah';

    // ================================================
    /* method : transaction
     * @param  : 
     * @Description : wonderland api call
     */// ==============================================
    public function checkout($input, $check_assign_mid)
    {
        $data = [
            "payment_method" => 6,
            "CurrencyIso" => $input['converted_currency'],
            "InvoiceAmount" => $input['converted_amount'],
            'return_url' => route('gamepay.callback',$input['session_id']),
            'error_url' => route('gamepay.callback',$input['session_id']),
            // 'return_url' => 'https://webhook.site/42e1a9b6-70ae-43e8-adf7-2050b122b2a1',
            // 'error_url' => 'https://webhook.site/42e1a9b6-70ae-43e8-adf7-2050b122b2a1',
            "CustomerName" => $input["first_name"]." ".$input["last_name"],
            "Language" => 'en',
            "MobileCountryCode" => 1,
            "CustomerMobile" => $input['phone_no'],
            "CustomerEmail" => $input["email"],
            "systemPaymentId" => "Gamepay1",
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://akwaababites.com/fatorah",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data,JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer ".$check_assign_mid->token,
                "content-type: application/json",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $response_data = json_decode($response, true);
        \Log::info([
            'gamepay-response' => $response_data,
        ]);
        if(isset($response_data["Data"]["PaymentURL"])){
            try {
                $input['gateway_id'] = $response_data['Data']['InvoiceId'] ?? null;
                $this->updateGatewayResponseData($input, $response_data);
                // redirect to Gamepay server
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $response_data['Data']['PaymentURL'],
                ];
            } catch (Exception $e) {
                \Log::info([
                    'gamepay-exception' => $e->getMessage()
                ]);
                return [
                    'status' => '0',
                    'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                    'order_id' => $input['order_id']
                ];
            }
        }
        $reason = "Your transaction could not processed.";
        if(isset($response_data["ValidationErrors"])){
            $reason = $response_data["ValidationErrors"]["0"]["Error"];
        }
        return [
            'status' => '0',
            'reason' => $reason,
            'order_id' => $input['order_id']
        ];
    }

    public function callback(Request $request,$session_id){
        $request_data = $request->all();
        $data = [
            "paymentId" => $request_data["paymentId"]
        ];
        \Log::info([
            'gamepay_callback_data' => $request_data
        ]);
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://akwaababites.com/fatorah/fatorah_response",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => http_build_query($data),
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer yilepWgZ9NjTu3kQ4X8j3H4kLpPTdvua224Kwc4jmBIJk10BcAQ3FaLkbEI3SPYoFlZRi7uOWNhfNxmrXurpzjzqrvEUI90MHAkOth3kq_vqhvBDS_d-2itda0qMOqFa1vET7D-Oqon6AQcGA24Rfpo-UGMAkFxnrs--K45zVAYpPSRSqEkKt4GSC8cWh4sLXn1jg0TGmwAeFEx8ZAn7HxFGO0mYuVLiTKWsvdzWzQZjyWzk4WoMTvKB4Y6eK1FG1gd3zjpDcxwspl22vxLTtiouwmikTeSynjosjc7dm15R96l4hKo0qCM42Gq9KtOncDunQWbKKhrXZsnZYtPPXK_tSX4CrKXSOPrRcveHcGApROaYcaRUehcyLQjGKl-deGZzLuVlM9auy2yQNuVFMaOaSytkXigh8O99bzOXPO8zPkn8J5UNsHfc7GLH34JS2Y9pn3mHFmB2T7-UPNpGHrdM8Gp4IBdKHqEoxy6Pg_K6H4L-2gBOQ8cLPewwrkbvl11GKmgms3gU0VfDjYT5JuF331o8XQDZBlFtDBzXRjJj7t_GIDj_PCRfmrpsSzXovSkUb9PwlIQm9jO-pMokyxkOo1x31CH5t_mMk2-cuoo2muQicKAeftZ4y3yH4tgfbPDY0sZckE2QDKAogj8hQst7B2E2U1-v8iSyqdG5XeW_0BqzzhOLcjxSw3YpthjudVubJQ",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);
        \Log::info([
            'gamepay_Callback_response' => $response_data
        ]);
        try {
            if (isset($response_data["Data"]['InvoiceTransactions']['0']['TransactionStatus']) && $response_data["Data"]['InvoiceTransactions']['0']['TransactionStatus'] == 'Succss') {

                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            }
            elseif (isset($response_data["Data"]['InvoiceTransactions']['0']['TransactionStatus']) && $response_data["Data"]['InvoiceTransactions']['0']['TransactionStatus'] == 'Failed') {
                $input['status'] = '0';
                $input['reason'] = 'Your transaction could not processed.';
            }else {

                $input['status'] = '0';
                $input['reason'] = 'Transaction declined.';
            }
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);

            return redirect($store_transaction_link);
        } catch (Exception $e) {
            
        }
    }
}
