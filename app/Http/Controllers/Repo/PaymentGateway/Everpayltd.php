<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Everpayltd extends Controller
{
	use StoreTransaction;

	// const BASE_URL = 'https://everpayltd.com/api/charge';
	const BASE_URL = 'https://staging.everpayltd.com/api/charge';

	// ================================================
	/* method : __construct
	* @param  :
	* @description : create new instance of the class
	*/// ===============================================
	public function __construct()
	{
		$this->transaction = new Transaction;
	}

	// ================================================
	/* method : checkout
	* @param  :
	* @description : gateway main method
	*/// ===============================================
	public function checkout($input, $check_assign_mid)
	{
		\Log::info(["input" => $input]);
		$curl = curl_init();
		 $postArr = [
		    "amount" => $input['converted_amount'], 
		   	"currency" => $input['converted_currency'], 
		   	"reference" => $input['session_id'], 
		   	"firstname" => $input['user_first_name'], 
		   	"lastname" => $input['user_last_name'], 
		   	"email" => $input['user_email'], 
		   	"phone" => $input['user_phone_no'], 
		   	"cardName" => $input['user_first_name']. " ". $input['user_last_name'], 
		   	"cardNumber" => $input['user_card_no'], 
		   	"cardCVV" => $input['user_cvv_number'], 
		   	"expMonth" => $input['user_ccexpiry_month'], 
		   	"expYear" => substr($input["user_ccexpiry_year"], -2), 
		   	"country" => $input['user_country'], 
		   	"city" => $input['user_city'], 
		   	"address" => $input['user_address'], 
		   	"ip_address" => $input['request_from_ip'], 
		   	// "ip_address" => "3.15.226.97",
		   	"zip_code" => $input['user_zip'], 
		   	"state" => $input['user_state'], 
		   	"callback_url" => route("everpayltd.callback", $input["session_id"]),
		   	// "callback_url" => "https://webhook.site/0ddd6b87-a504-430c-b674-2a3dc9460b68",
		   	// "webhook_url" => "https://webhook.site/0ddd6b87-a504-430c-b674-2a3dc9460b68",
		   	"webhook_url" => route("everpayltd.webhook", $input["session_id"]),
		]; 
		curl_setopt_array($curl, array(
		  CURLOPT_URL => self::BASE_URL,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => json_encode($postArr),
		  CURLOPT_HTTPHEADER => array(
		    'authorization: Bearer '.$check_assign_mid->secret_key,
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		\Log::info([
            'request' => $postArr,
            'Everpayltd-response' => $response,
        ]);
		curl_close($curl);
		$response_data = json_decode($response,true);
		\Log::info([
            'response_data' => $response_data
        ]);
        $input['gateway_id'] = $payment_array['data']['orderid'] ?? null;
    	$this->updateGatewayResponseData($input, $response_data);
    	if (isset($response_data['status']) && $response_data['status'] == "success") {
	        if(isset($response_data['data']['link']) && !empty($response_data['data']['link'])){
	        	return [
                    'status' => '7',
                    'reason' => '3DS link generated successfully, please redirect.',
                    'payment_link' => $response_data['data']['link'],
                ];
	        }else {
				$input['status'] = '0';
	            $input['reason'] = $response_data['message'] ?? $response_data['message'] ?? 'Transaction authorization failed.';
	        }
        } else {
        	$input['status'] = '0';
	        $input['reason'] = $response_data['message'] ?? $response_data['message'] ?? 'Transaction authorization failed.';
            return $input;
        }
	}

	public function callback(Request $request, $session_id) {
        \Log::info([
            'Everpayltd-redirect' => $request->all(),
        ]);

        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        $input['gateway_id'] = isset($request_data['order_id']) ? $request_data['order_id'] : "1";
        // $check_assign_mid = checkAssignMID($input["payment_gateway_id"]);
        if (isset($request_data['status']) && $request_data['status'] == 'approved') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
        }else {
            $input['status'] = '2';
            $input['reason'] = 'Transaction is in pending.';
        }
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    public function webhook(Request $request, $session_id) {
        \Log::info([
            'Everpayltd-callback' => $request->all(),
        ]);
        sleep(10);
        $request_data = $request->all();
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        if (isset($request_data["trxDetails"]['status']) && $request_data["trxDetails"]['status'] == 'approved') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction has been processed successfully.';
            $this->storeTransaction($input);
        } else {
            $input['status'] = '0';
            $input['reason'] = (isset($request_data["trxDetails"]['message']) ? $request_data["trxDetails"]['message'] : 'Your transaction could not processed.');
            $this->storeTransaction($input);
        }
        exit();
    }
}
