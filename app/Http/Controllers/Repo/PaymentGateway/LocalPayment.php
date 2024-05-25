<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use Hamcrest\Thingy;

class LocalPayment extends Controller {
    
    use StoreTransaction;

    // const BASE_URL = 'https://api.stage.localpayment.com/api'; // test
    const BASE_URL = 'https://api.v3.localpayment.com/api'; // live  

    public function checkout($input, $check_assign_mid) {
        
        try {

            $token = $this->getAccessToken($check_assign_mid);
            echo "<pre>";print_r($token);exit();
            if (isset($token['access'])) {
                
                // Because requried in paymnt gateway
                if (empty($input['country_code'])) {
                    throw new \Exception('A country code must be required.');
                }

                $data = config('localpayment.payin_fields');
                $data['externalId'] = $input['order_id'];
                $data['amount'] = $input['converted_amount'];
                $data['currency'] = $input['converted_currency'];
                $data['payer']['name'] = $input['first_name'];
                $data['payer']['lastname'] = $input['last_name'];
                $data['payer']['email'] = $input['email'];
                $data['payer']['phone']['countryCode'] = $input['country_code'];
                $data['payer']['phone']['number'] = $input['phone_no'];
                $data['payer']['address']['street'] = $input['address'];
                $data['payer']['address']['city'] = $input['city'];
                $data['payer']['address']['state'] = $input['state'];
                $data['payer']['address']['country'] = $input['country'];
                $data['payer']['address']['zipCode'] = $input['zip'];
                $data['card']['name'] = $input['first_name'] . $input['last_name'];
                $data['card']['number'] = $input['card_no'];
                $data['card']['cvv'] = $input['cvvNumber'];
                $data['card']['expirationMonth'] = $input['ccExpiryMonth'];
                $data['card']['expirationYear'] = $input['ccExpiryYear'];
                
                // Find countryIsoCode3 from 2 character country code
                // $countryIsoCode3 = $this->countryIsoCode3($data['payer']['address']['country']);
                $countryIsoCode3 = countryReplace($data['payer']['address']['country']);
                $data['country'] = $countryIsoCode3;
                if (strlen($data['country']) != 3) {
                    throw new \Exception('This country is not supported.');
                }
                
                // Find credit/debit from first 6 digit of card
                $cardType = $this->getCardType(substr($data['card']['number'], 0, 6));
                $cardType = ucfirst(strtolower($cardType)) . 'Card';

                // Check payment method is supported or not
                if ($cardType == 'CreditCard' || $cardType == 'DebitCard') {

                    $paymentMethods = $this->getPaymentMethods($countryIsoCode3, $token);

                    foreach ($paymentMethods as $paymentMethod) {
                        if (isset($paymentMethod['payinPaymentMethods'])) {

                            foreach ($paymentMethod['payinPaymentMethods'] as $payinPaymentMethod) {
                                if (isset($payinPaymentMethod['paymentMethodType']) && $payinPaymentMethod['paymentMethodType'] == $cardType) {
                                    $data['accountNumber'] = $paymentMethod['accountNumber'];
                                    $data['paymentMethod']['code'] = $payinPaymentMethod['code'];
                                    $data['paymentMethod']['type'] = $payinPaymentMethod['paymentMethodType'];
                                    break;
                                }
                            }

                        }
                        if (! empty($data['accountNumber'])) {
                            break;
                        }
                    }
                }
                if (empty($data['accountNumber'])) {
                    throw new \Exception('This payment method is not supported.');
                }

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, self::BASE_URL . '/payin');
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token['access']
                ]);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                // $info = curl_getinfo($curl);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                $responseData = json_decode($response, 1);

                \Log::info([
                    'localpayment-input' => $data
                ]);
                \Log::info([
                    'localpayment-response' => $responseData
                ]);

                if ($err) {
                    throw new \Exception($err);
                }
                if (! empty($responseData['error'])) {
                    throw new \Exception($responseData['message']);
                }
                if (! empty($responseData['errors'])) {
                    throw new \Exception($responseData['errors'][0]['detail']);
                }
                if (empty($responseData['status'])) {
                    throw new \Exception($responseData['detail']);
                }

                $input['gateway_id'] = $responseData["externalId"] ?? null;
                $this->updateGatewayResponseData($input, $responseData);
                return [
                    'status' => '1',
                    'reason' => 'Your transaction has been processed successfully.',
                    'order_id' => $input['order_id']
                ];
            }

            // Token generation fail.
            \Log::info([
                'localpayment-token-error' => $token
            ]);
            throw new \Exception('Your transaction could not processed.');
            
        } catch (\Exception $e) {
            \Log::info([
                'localpayment-exception' => $e->getMessage()
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
        
        $data = [
            'username' => $check_assign_mid->username,
            'password' => $check_assign_mid->password
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . '/token');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, 1);
        \Log::info([
            'localpayment-token-response' => $responseData
        ]);
        return $responseData;
    }
    
    /**
     * 
     * Find countryIsoCode3 from 2 character country code
     * 
     * */
    private function countryIsoCode3($country) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://restcountries.eu/rest/v2/alpha/' . $country);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, 1);
        return $responseData['alpha3Code'];
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
        return $responseData['type'] ?? null;
    }
    
    /**
     *
     * Find countryIsoCode3 from 2 character country code
     *
     * */
    private function getPaymentMethods($countryIsoCode3, $token) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . '/resources/payment-methods?countryIsoCode3=' . $countryIsoCode3);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['access']
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, 1);
        return $responseData;
    }
}
