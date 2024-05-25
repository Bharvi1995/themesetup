<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use Http;
use Mail;
use Session;
use Exception;
use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Http\Controllers\Controller;
use App\Traits\StoreTransaction;
use Illuminate\Http\Request;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Log;

class Monnet extends Controller
{
    use StoreTransaction;
    //const BASE_URL = 'https://monnetpayments.com/api-payin'; // live
    const BASE_URL = 'https://cert.monnetpayments.com/api-payin'; // test

    protected $user, $Transaction;
    public function __construct()
    {
        $this->user = new User;
        $this->Transaction = new Transaction;
    }

    public function checkout($input, $check_assign_mid)
    {
        //echo "<pre>";print_r($input);exit();
        if (!isset($input['card_no'])) {
            $sessionData = $input;
            unset($sessionData['api_key']);

            DB::table('transaction_session')->insert([
                'user_id' => $input['user_id'],
                'payment_gateway_id' => $input['payment_gateway_id'],
                'transaction_id' => $input['session_id'],
                'order_id' => $input['order_id'],
                'request_data' => json_encode($sessionData),
                'amount' => $input['converted_amount'],
                'email' => $input['email'],
                'is_completed' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        return [
            'status' => '7',
            'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
            'redirect_3ds_url' => route('Monnet.transactionForm', $input['session_id'])
        ];
    }

    public function transactionForm($session_id)
    {
        $input = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->value('request_data');

        if ($input != null) {
            $input = json_decode($input, true);
        } else {
            return abort('404');
        }

        return view('gateway.monnetOld.redirect', compact('session_id'));
    }

    public function transactionResponse(Request $request, $session_id)
    {
        //echo "<pre>";print_r($request->toArray());exit();
        $this->validate($request, [
            'payinCustomerTypeDocument' => 'required',
            'payinCustomerDocument' => 'required',
        ]);

        $request_only = $request->only(['payinCustomerTypeDocument', 'payinCustomerDocument']);

        // get request data from database
        $input_json = DB::table('transaction_session')
            ->where('transaction_id', $session_id)
            ->value('request_data');

        if ($input_json == null) {
            return abort('404');
        }

        $input = json_decode($input_json, true);

        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        // put data in session to save after transaction complete
        $input['customer_order_id'] = $input['customer_order_id'] ?? null;

        // set gateway default currency
        $check_selected_currency = checkSelectedCurrency($input['payment_gateway_id'], $input['currency'], $input['converted_amount']);

        if ($check_selected_currency) {
            $currency = $check_selected_currency['currency'];
            $amount = $check_selected_currency['amount'];
            $input['is_converted'] = '1';
            $input['converted_amount'] = $check_selected_currency['amount'];
            $input['converted_currency'] = $check_selected_currency['currency'];
        } else {
            $currency = $input['currency'];
            $amount = $input['converted_amount'];
        }
        //echo $currency;exit();
        $amount = number_format((float) $amount, 2, '.', '');
        $hash_data = $check_assign_mid->merchant_id . $input['session_id'] . $amount . "PEN" . $check_assign_mid->monnet_key;
        $signature = hash('sha512', $hash_data);
        //echo $signature
        $payload = [
            'payinMerchantID' => (int) $check_assign_mid->merchant_id,
            'payinAmount' => $amount,
            'payinCurrency' => 'PEN',
            //'payinCurrency' => $currency,
            'payinMerchantOperationNumber' => $input['session_id'],
            'payinMethod' => $check_assign_mid->descriptor,
            'payinVerification' => $signature,
            //'payinTransactionOKURL' => Route('Monnet.redirect',["status"=>'success',"session"=>$input['session_id']]),
            'payinTransactionOKURL' => 'https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475',
            'payinTransactionErrorURL' => 'https://webhook.site/87bff569-b33e-4587-80b9-3180cbdc4475',
            //'payinTransactionErrorURL' => Route('Monnet.redirect',["status"=>'fail',"session"=>$input['session_id']]),
            'payinExpirationTime' => '30',
            'payinLanguage' => 'EN',
            'payinCustomerEmail' => $input['email'],
            'payinCustomerName' => $input['first_name'],
            'payinCustomerLastName' => $input['last_name'],
            'payinCustomerTypeDocument' => $request_only['payinCustomerTypeDocument'],
            'payinCustomerDocument' => $request_only['payinCustomerDocument'],
            'payinCustomerPhone' => $input['phone_no'],
            'payinCustomerAddress' => $input['address'],
            'payinCustomerCity' => $input['city'],
            'payinCustomerRegion' => $input['state'],
            'payinCustomerCountry' => $input['country'],
            'payinCustomerZipCode' => $input['zip'],
            'payinCustomerShippingName' => $input['first_name'] . ' ' . $input['last_name'],
            'payinCustomerShippingPhone' => $input['phone_no'],
            'payinCustomerShippingAddress' => $input['address'],
            'payinCustomerShippingCity' => $input['city'],
            'payinCustomerShippingRegion' => $input['state'],
            'payinCustomerShippingCountry' => $input['country'],
            'payinCustomerShippingZipCode' => $input['zip'],
            // 'payinRegularCustomer' => $input['first_name'].' '.$input['last_name'],
            // 'payinCustomerID' => $input['session_id'],
            // 'payinDiscountCoupon' => $input['session_id'],
            // 'payinFilterBy' => $input['session_id'],
            'payinProductID' => $input['session_id'],
            'payinProductDescription' => 'ECOM PAYMENT',
            'payinProductAmount' => $amount,
            'payinDateTime' => date('Y-m-d'),
            'payinProductSku' => 'ECOM PAYMENT',
            'payinProductQuantity' => '1',
            'URLMonnet' => 'https://cert.monnetpayments.com/api-payin/v3/online-payments',
            'typePost' => 'json',
            // 'payinPan' => $input['card_no'],
            // 'payinCvv' => $input['cvvNumber'],
            // 'payinExpirationYear' => $input['ccExpiryYear'],
            // 'payinExpirationMonth' => $input['ccExpiryMonth'],
        ];

        $response = Http::withHeaders(["Content-Type" => "application/json"])->post(self::BASE_URL . '/v3/online-payments', $payload)->json();
        Log::info(["monnet-response" => $response]);

        // * Store mid payload
        $this->storeMidPayload($input["session_id"], json_encode($payload));

        // * Update the gateway response
        $this->updateGatewayResponseData($input, $response);

        if (empty($response) || $response == null) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        } else if (isset($response['url']) && $response['url'] != null) {
            // redirect to acquirer server
            return redirect($response['url']);
        } else {
            return [
                "status" => "0",
                "reason" => "Transaction could not processed."
            ];
        }
    }

    // ================================================
    /* method : redirect
    * @param  : 
    * @description : redirect back after 3ds
    */// ==============================================
    public function redirect(Request $request, $status, $session_id)
    {
        \Log::info(['monnet_redirect_string' => $request->all()]);
        // get $input data
        $input_json = TransactionSession::where('transaction_id', $session_id)
            ->first();

        if ($input_json == null) {
            return abort(404);
        }

        $input = json_decode($input_json['request_data'], true);
        $input['customer_order_id'] = $input['customer_order_id'] ?? null;

        // transaction was successful...
        if ($status == 'success') {
            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';
            // if transaction declined with reason
        } elseif ($status == 'fail') {
            $input['status'] = '0';
            $input['reason'] = $request['reason'] ?? 'TRANSACTION DECLINED.';
            // if transaction status pending
        } elseif ($status == 'pending') {
            $input['status'] = '2';
            $input['reason'] = 'Transaction pending, please wait to get update from acquirer.';

        } else {
            $input['status'] = '0';
            $input['reason'] = 'TRANSACTION DECLINED.';
        }

        // * Update gateway 
        $this->updateGatewayResponseData($input, $input_json);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // ================================================
    /* method : notify
    * @param  : 
    * @description : notify after complete
    */// ==============================================
    public function notify(Request $request)
    {
        \Log::info(['monnet_notify_string' => $request->all()]);
        $request_data = $request->all();

        $session_id = $request_data['payinMerchantOperationNumber'] ?? null;

        if ($session_id != null) {
            exit();
        }

        http_response_code(200);

        // check in transactions table
        $transaction = DB::table('transaction_session')
            ->select("request_data")
            ->where('transaction_id', $session_id)
            ->first();

        if ($transaction == null) {
            exit();
        }

        $input = json_decode($transaction->request_data, true);

        if (isset($request_data['payinStateID']) && $request_data['payinStateID'] == '5') {

            $input['status'] = '1';
            $input['reason'] = 'Your transaction was proccessed successfully.';

        } elseif (isset($request_data['payinStateID']) && in_array($request_data['payinStateID'], ['1', '2', '4'])) {
            exit();
        } elseif (isset($request_data['payinStateID']) && in_array($request_data['payinStateID'], ['0', '3', '6'])) {
            $input['status'] = '0';
            $input['reason'] = $request_data['payinStatusErrorMessage'] ?? 'Transaction unknown error.';

        }
        // * Store webhook response
        $this->storeMidWebhook($input["session_id"], json_encode($request_data));

        $this->storeTransaction($input);
        exit();

    }
}