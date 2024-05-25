<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Hamcrest\Thingy;

class FacilitaPay extends Controller {
    
    use StoreTransaction;

    const BASE_URL = 'https://sandbox-api.facilitapay.com/api/v1/'; // test

    public function checkout($input, $check_assign_mid) {

        try {

            $token = $this->getAccessToken($check_assign_mid);

            if (isset($token['jwt'])) {
                $cardDetails = json_decode($input['bin_details']);
                \Log::info([
                    'cardDetails-value' => $cardDetails,
                ]);
                //$cardType = "CREDIT";
                $cardType = "card-type";
                $cardType = $cardDetails->$cardType;
                //$cardBrand = $cardDetails["card-brand"]
                // $cardType = $this->getCardType(substr($input['card_no'], 0, 6));
                $cardBrand = $this->getCardBrand($input['card_no']);
                \Log::info([
                    'cardType-value' => $cardType,
                    'cardBrand-value' => $cardBrand,
                ]);
                if (empty($cardType) || ($cardType != 'CREDIT') || empty($cardBrand) || ($cardBrand == false )) {
                    throw new \Exception('we are only supporting credit card or your card is not supoorted.');
                }

                // enable notification
                $this->enableNotification($token['jwt']);

                $requestData = [
                    'transaction' => [
                        'currency' => $input['currency'],
                        'exchange_currency' => $input['converted_currency'],
                        'value' => $input['converted_amount'],
                        'from_credit_card' => [
                            'card_number' => $input['card_no'],
                            'card_expiration_date' => $input['ccExpiryMonth'] . '/' . $input['ccExpiryYear'],
                            'card_security_code' => $input['cvvNumber'],
                            'card_brand' => $cardBrand,
                            'fullname' => $input['first_name'] . $input['last_name'],
                            'document_type' => 'cnpj', // 'tax_id'
                            'document_number' => $input['order_id'],
                            'phone_country_code' => $input['country_code'],
                            'phone_area_code' => $input['country_code'],
                            'phone_number' => $input['phone_no']
                        ],
                        "to_bank_account_id" => $check_assign_mid->to_bank_account_id,
                        "subject_id" => $check_assign_mid->subject_id
                    ],
                ];
              
                $header = [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token['jwt']
                ];
                $responseData = $this->curlRequest('transactions', $header, $requestData);

                $input['gateway_id'] = $responseData["data"]["id"] ?? $input['session_id'];
                $this->updateGatewayResponseData($input, $responseData);

                if(isset ($responseData['errors']) && ! empty($responseData['errors'])) {
                   $error = $this->getArrayValuesRecursively($responseData['errors']);
                   throw new \Exception($error[0] ?? 'Your transaction could not processed.');
                }
                if ( isset ($responseData['data']['status']) && ($responseData['data']['status'] == 'canceled')) {
                    throw new \Exception($responseData['data']['canceled_reason'] ?? 'Your transaction could not processed.');
                }

                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id']
                ];
            }

            // Token generation fail.
            \Log::info([
                'facilitaPay-token-error' => $token
            ]);
            throw new \Exception('Your transaction could not processed.');
            
        } catch (\Exception $e) {

            \Log::info([
                'facilitaPay-exception' => $e->getMessage()
            ]);
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        }
    }

    /*
     * For generate aceesst oken
     */
    private function getAccessToken($check_assign_mid) {
        
        $requestData = [
            'user' => [
                'username' => $check_assign_mid->username,
                'password' => $check_assign_mid->password
            ]
        ];
        
        $header = [
                'Content-Type: application/json',
        ];

        $responseData = $this->curlRequest('sign_in', $header, $requestData);
        
        return $responseData;
    }

    /*
     * For enable notification 
     */
    private function enableNotification($token) {
        
        $requestData = [
            'url' => route('facilitapay-webhook')
        ];

        $header = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
        ];

        $responseData = $this->curlRequest('enable_webhooks', $header, $requestData);

        return $responseData;
    }

    /*
     * For curl request
     * */
    private function curlRequest($url, $header, $requestData) {

        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
           $header
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $info = curl_getinfo($curl);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        \Log::info([
            'facilitaPay-input-' . $url => $requestData
        ]);

        \Log::info([
            'facilitaPay-response-' . $url => $response
        ]);

        if ($err) {
            throw new \Exception($err);
        }

        $responseData = json_decode($response, 1);
        
        return $responseData;
        
    }

     /*
     * For webhook 
     */
    public function webhook(Request $request) {

        $response = $request->all();
        $id = $response['notification']['transaction_id'] ?? null;

        \Log::info([
            'facilitaPay-webhook' => $response,
        ]);
        if (! empty($id) && ($response['notification']['type'] == 'payment_approved') || ($response['notification']['type'] == 'payment_failed') ) {

            $transaction_session = DB::table('transaction_session')
                ->where('gateway_id', $id)
                ->first();

            if ($transaction_session == null) {
                return abort(404);
            }
            $input = json_decode($transaction_session->request_data, 1);
            if ($response['notification']['type'] != 'payment_failed') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction has been processed successfully.';
            } else  {
                $input['status'] = '0';
                $input['reason'] =  'Your transaction could not processed.';
            }

            // store transaction
            $transaction_response = $this->storeTransaction($input);
            exit();
        }
    }

    /**
     *
     * Find card brand
     *
     * */
    private function getCardBrand($card_no) {

        if (empty($card_no)) {
            return false;
        }
        $cardtype = array(
            "visa" => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            "mastercard" => "/^5[1-5][0-9]{14}$/",
            "amex" => "/^3[47]\d{13,14}$/",
            "jcb" => "/^(?:2131|1800|35\d{3})\d{11}$/",
            "solo" => "/^(6334|6767)[0-9]{12}|(6334|6767)[0-9]{14}|(6334|6767)[0-9]{15}$/",
            "maestro" => "/^(5018|5020|5038|6304|6759|6761|6763)[0-9]{8,15}$/",
            "discover" => "/^65[4-9][0-9]{13}|64[4-9][0-9]{13}|6011[0-9]{12}|(622(?:12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|9[01][0-9]|92[0-5])[0-9]{10})$/",
            "switch" => "/^(4903|4905|4911|4936|6333|6759)[0-9]{12}|(4903|4905|4911|4936|6333|6759)[0-9]{14}|(4903|4905|4911|4936|6333|6759)[0-9]{15}|564182[0-9]{10}|564182[0-9]{12}|564182[0-9]{13}|633110[0-9]{10}|633110[0-9]{12}|633110[0-9]{13}$/",
            );

        if (preg_match($cardtype['visa'], $card_no)) {
            return 'visa';
        } else if (preg_match($cardtype['mastercard'], $card_no)) {
            return 'mastercard';
        } else if (preg_match($cardtype['amex'], $card_no)) {
            return 'amex';
        } else if (preg_match($cardtype['discover'], $card_no)) {
            return 'discover';
        } else if (preg_match($cardtype['jcb'], $card_no)) {
            return 'jcb';
        } else if (preg_match($cardtype['maestro'], $card_no)) {
            return 'maestro';
        } else if (preg_match($cardtype['switch'], $card_no)) {
            return 'switch';
        } else if (preg_match($cardtype['solo'], $card_no)) {
            return 'solo';
        } else {
            return false;
        }
    }

     /**
     *
     * Find credit/debit from first 6 digit of card
     *
     * */
    private function getCardType($bin) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://lookup.binlist.net/' . $bin);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, 1);
        \Log::info([
            'cardtype-response' => $responseData
        ]);
        return $responseData['type'] ?? null;
    }

    private function getArrayValuesRecursively($array)
    {
        $values = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                $values = array_merge($values, $this->getArrayValuesRecursively($value));
            } else {
                $values[] = $value;
            }
        }
        
        return $values;
    }
}
    