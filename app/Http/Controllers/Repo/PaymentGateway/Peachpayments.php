<?php
namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\Transaction;
use App\TransactionSession;
use Illuminate\Support\Facades\Hash;

class Peachpayments extends Controller {

    //const BASE_URL = 'https://test.oppwa.com/v1/'; // Test
    const BASE_URL = 'https://oppwa.com/v1/'; //Live

    use StoreTransaction;

    public function checkout($input, $check_assign_mid) {
        $data = [
            'entityId' => $check_assign_mid->entityId,
            'amount' => number_format($input["converted_amount"], 2, '.', ''),
            'currency' => $input["converted_currency"],
            'paymentType' => 'DB',
            'customer.givenName' => $input['first_name'],
            'customer.surname' => $input['last_name'],
            'customer.email' => $input['email'],
            'billing.street1' => $input['address'],
            'billing.city' => $input['city'],
            'billing.state' => $input['state'],
            'billing.country' => $input['country'],
            'billing.postcode' => $input['zip']
        ];
        $data = http_build_query($data, '&');
        $url = self::BASE_URL . 'checkouts';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization:Bearer '. $check_assign_mid->bearer_token
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $err = curl_error($curl);
        $responseData = curl_exec($curl);
        
        curl_close($curl);

        $result = json_decode($responseData, true);
        if(isset($result["id"])){
            $input['gateway_id'] = $result["id"] ?? "";
            $this->updateGatewayResponseData($input, $result);
        }
        if (isset($result['result']['code']) && ($result['result']['code'] == '000.200.100') || ($result['result']['code'] == '000.200.101') || ($result['result']['code'] == '000.200.200')) {

            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect \'redirect_3ds_url\'.',
                'redirect_3ds_url' => route('peach-form', $input['session_id']),
            ];

        } else {
            $input['status'] = '0';
            $input['reason'] = $result['result']['description'] ?? 'Your transaction could not processed.';
        }
        return $input;
    }

    public function peachPayForm(Request $request,$id)
    {
        $url = self::BASE_URL;
        $error = '';
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $transaction_response = json_decode($data->response_data,1);
        return view('gateway.peach', compact('error','transaction_response','id','url'));
        
    }

    public function callback(Request $request,$id)
    {   
        \Log::info([
            'callback_response' => $request->all(),
            'id' => $id
        ]);
        $data = \DB::table('transaction_session')
            ->where('transaction_id', $id)
            ->first();

        if ($data == null) {
            return abort(404);
        }
        $input = json_decode($data->request_data, 1);
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']); 
        $url = self::BASE_URL . 'checkouts/' . $request->id . '/payment?entityId=' . $check_assign_mid->entityId;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization:Bearer ' . $check_assign_mid->bearer_token
        ]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $result = json_decode($responseData, true);
        
        $status = preg_match('/^(000\.000\.|000\.100\.1|000\.[36])/',$result['result']['code']);
        \Log::info([
            'response' => $result,
            'status' => $status
        ]);
        if ($status == 1) {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was processed successfully.';
        }else {
            $input['status'] = '0';
            $input['reason'] = $result['result']['description'] ?? 'Your transaction could not processed.';
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }
}


