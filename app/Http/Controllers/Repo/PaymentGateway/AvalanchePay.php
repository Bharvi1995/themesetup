<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;
use AvalanchePay\Api\Amount;
use AvalanchePay\Api\Payer;
use AvalanchePay\Api\Payment;
use AvalanchePay\Api\RedirectUrls;
use AvalanchePay\Api\Transaction;

!defined('BASE_URL') ? define('BASE_URL', 'https://avalanchepay.com/') : false;

class AvalanchePay extends Controller {
    
    use StoreTransaction;

    public function checkout($input, $check_assign_mid) {
       
        try {
            
            $data = [
                'email' => $input['email'],
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'converted_amount' => $input['converted_amount'],
                'converted_currency' => $input['converted_currency'],
                'order_id' => $input["order_id"],
                'session_id' => $input["session_id"],
                'client_id' => $check_assign_mid->client_id,
                'client_secret' => $check_assign_mid->client_secret,
                'hash' => \Hash::make($input['order_id'] . ':' .  $input['session_id'])
            ];
            
            $url = route('avalanchepay.api');
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $link = curl_exec($curl);
            curl_close($curl);
            
            // Remove HTML from content to get link only 
            $link = strip_tags($link);

            // \Log::info([
            //     'avalanchepay-input' => $data
            // ]);
            \Log::info([
                'avalanchepay-response' => $link
            ]);
            
            if (! empty($link) && filter_var($link, FILTER_VALIDATE_URL)) {
                
                // redirect link for checkout page
                return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                    'redirect_3ds_url' => $link
                ];
            }
            
            throw new \Exception($link ?? 'Your transaction could not processed.');

        } catch (\Exception $e) {
            
            \Log::info([
                'avalanchepay-exception' => $e->getMessage()
            ]);
            
            return [
                'status' => '0',
                'reason' => $e->getMessage(), // 'Your transaction could not processed.',
                'order_id' => $input['order_id']
            ];
        
        }     
       
    }

    public function success($id, Request $request) {

        $response = json_encode($_GET);
        $responseData = json_decode(base64_decode($response), TRUE);

        // \Log::info([
        //     'avalanchepay-success' => $responseData,
        //     'id' => $id
        // ]);

        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        if(isset($responseData["transaction_id"])){
            $input['gateway_id'] = $responseData["transaction_id"] ?? "";
            $this->updateGatewayResponseData($input, $responseData);
        }
        $input['status'] = '1';
        $input['reason'] = 'Your transaction was proccessed successfully.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);

    }

    public function cancel($id) {

        $response = json_encode($_GET);
        $responseData = json_decode(base64_decode($response), TRUE);

        \Log::info([
            'avalanchepay-cancel' => $responseData,
            'id' => $id
        ]);

        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);
        // if(isset($responseData["order_id"])){
        //     $this->updateGatewayResponseData($input, $responseData);
        // }
        $input['status'] = '0';
        $input['reason'] = $responseData['error'] ?? 'Your transaction could not processed.';
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);

        return redirect($store_transaction_link);
    }
    
    public function api(Request $request) {
        
        $input = $request->toArray();
        
        try {
            
            if (! \Hash::check(($input['order_id'] . ':' .  $input['session_id']), $input['hash'])) {
                throw new \Exception('Invalid request.');
            }
            
            // Payer Object
            $payer = new Payer();
            $payer->setPaymentMethod('Default')
            ->setAutoLogin('Enabled')
            ->setUserEmail($input['email'])
            ->setFirstName($input['first_name'])
            ->setLastName($input['last_name']);
            
            // Amount Object
            $amountIns = new Amount();
            $amountIns->setTotal($input['converted_amount'])
            ->setCurrency($input['converted_currency']); // must give a valid currency code and must exist in merchant wallet list
            
            // Transaction Object
            $trans = new Transaction();
            $trans->setAmount($amountIns)
            ->setOrderId($input['order_id']);
            
            // RedirectUrls Object
            $urls = new RedirectUrls();
            $urls->setSuccessUrl(route('avalanchepay.success', $input['session_id'])) // success url
            ->setCancelUrl(route('avalanchepay.cancel', $input['session_id'] )); // cancel url
            
            //Payment Object
            $payment = new Payment();
            $payment->setCredentials([
                'client_id'     => $input['client_id'],
                'client_secret' => $input['client_secret']
            ])
            ->setRedirectUrls($urls)
            ->setPayer($payer)
            ->setTransaction($trans);
            
            //create payment
            $payment->create();
            
            $link = $payment->getApprovedUrl();
            if (! empty($link)) {
                echo $link;
            }
            exit();
            
        } catch (\Exception $e) {
            exit($e->getMessage());
        } finally {
            exit();
        }
    }
}
