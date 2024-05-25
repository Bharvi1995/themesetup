<?php

namespace App\Http\Controllers\Repo;

use App\Helpers\PaymentResponse;
use App\PaymentGateways\PaymentGatewayContract;
use App\User;
use App\Transaction;
use App\TransactionSession;
use App\Traits\Mid;
use App\Traits\RuleCheck;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestCryptoTransactionRepo extends Controller
{
    use Mid, RuleCheck, StoreTransaction;

    // ================================================
    /* method : __construct
    * @param  :
    * @Description : Create a new controller instance.
    */// ==============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transactionSession = new TransactionSession;
    }

    // ================================================
    /* method : store
    * @param  :
    * @Description : send $input details to gateway class
    */// ==============================================
    public function store($input)
    {   
        $input['session_id'] = 'XR'. strtoupper(\Str::random(4)) . time();
        $input['order_id'] = 'TRN'. strtoupper(\Str::random(4)) . time() . strtoupper(\Str::random(6));
        //add default state value 'NA' if not provided
        $input['state'] = $input['state'] ?? 'NA';

        // gateway default currency
        $check_selected_currency = $this->midDefaultCurrencyCheck($input['payment_gateway_id'], $input['currency'], $input['amount']);

        if($check_selected_currency) {
            $input['is_converted'] = '1';
            $input['converted_amount'] = $check_selected_currency['amount'];
            $input['converted_currency'] = $check_selected_currency['currency'];
        } else {
            $input['converted_amount'] = $input['amount'];
            $input['converted_currency'] = $input['currency'];
        }

        $input['amount_in_usd'] = $this->amountInUSD($input);

        // check rules and return payment_gateway_id
        // @TODO What is currency rules
        $rule_gateway_id = $this->checkCurrencyRulesForCrypto($input);
        if ($rule_gateway_id != false) {
            $input['payment_gateway_id'] = $rule_gateway_id;
        }
        
        // get main User object
        $user = User::where('id', $input['user_id'])->first();

        // saving to transaction_session
        $this->transactionSession->storeData($input);

        // @TODO ways to get payment gateway id
        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

        // return mid limit response
        $mid_limit_response = $this->getMIDLimitResponse($input, $check_assign_mid);

        if ($mid_limit_response != false) {

            $input['status'] = $mid_limit_response['status'];
            $input['reason'] = $mid_limit_response['reason'];

            // store transaction
            $store_transaction_link = $this->storeTransaction($input);
            
            $input['redirect_3ds_url'] = $store_transaction_link;

            return $input;
        }

        // gateway curl response
        $gateway_curl_response = $this->gatewayCurlResponse($input, $check_assign_mid);
        
        $input['status'] = $gateway_curl_response['status'];
        $input['reason'] = $gateway_curl_response['reason'];
        
        // store transaction
        if($gateway_curl_response['status'] != '7') {
            
            $store_transaction_link = $this->storeTransaction($input);
        }
       
        return $gateway_curl_response;
    }

    // ================================================
    /* method : getMIDLimitResponse
    * @param  :
    * @description : return data from mid
    */// ==============================================
    public function getMIDLimitResponse($input, $check_assign_mid)
    {
        // per transaction limit
        $per_transaction_limit_response = $this->perTransactionLimitCheck($input, $check_assign_mid);
        if ($per_transaction_limit_response != false) {
            return $per_transaction_limit_response;
        }

        // mid daily limit
        $mid_daily_limit = $this->perDayAmountLimitCheck($input, $check_assign_mid);
        if ($mid_daily_limit != false) {
            return $mid_daily_limit;
        }

        // if there is email
        if(isset($input['email']) && $input['email'] != null) {

            // email per-day limit
            $email_per_day_limit = $this->dailyEMailLimitCheck($input, $check_assign_mid);
            if ($email_per_day_limit != false) {
                return $email_per_day_limit;
            }

            // email per-week limit
            $email_per_week_limit = $this->weeklyEMailLimitCheck($input, $check_assign_mid);
            if ($email_per_week_limit != false) {
                return $email_per_week_limit;
            }

            // email per-month limit
            $email_per_month_limit = $this->monthlyEMailLimitCheck($input, $check_assign_mid);
            if ($email_per_month_limit != false) {
                return $email_per_month_limit;
            }
        }

        return false;
    }

    // ================================================
    /* method : gatewayCurlResponse
    * @param  :
    * @description : get first response from gateway
    */// ==============================================
    public function gatewayCurlResponse($input, $check_assign_mid)
    {
        $class_name = 'App\\Http\\Controllers\\Repo\\PaymentGateway\\TestCrypto'; 
        $gateway_class = new $class_name;
       
        $gateway_return_data = $gateway_class->checkout($input, $check_assign_mid);
        
        return $gateway_return_data;
    }
}
