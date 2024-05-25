<?php

namespace App\Http\Controllers\Repo;

use DB;
use URL;
use Mail;
use App\BlockData;
use App\Transaction;
use App\TransactionSession;
use App\Traits\Mid;
use App\Traits\RuleCheck;
use App\Traits\BinChecker;
use App\Traits\StoreTransaction;
use App\Transformers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\UserNotification;
use App\Mail\FlaggedTransactionMail;
use App\PayoutReports;
use Carbon\Carbon;

class TransactionRepo extends Controller
{
    use Mid, RuleCheck, StoreTransaction, BinChecker;

    protected $transaction, $transactionSession;

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
    public function store($input, $user, $check_assign_mid)
    {
        $input['session_id'] = $input['session_id'] ?? 'XR' . strtoupper(\Str::random(4)) . time();
        $input['order_id'] = $input['order_id'] ?? 'TRN' . strtoupper(\Str::random(4)) . time() . strtoupper(\Str::random(6));
        $input['state'] = $input['state'] ?? 'NA';
        $input['amount_in_usd'] = $this->amountInUSD($input);
        $input['phone_no'] = preg_replace('/[^0-9.]+/', '', $input['phone_no']);
        if (strlen($input['phone_no']) > 10) {
            $input['phone_no'] = substr($input['phone_no'], -10);
        }

        $block_data = BlockData::pluck('field_value')->toArray();
        if (!empty($block_data)) {
            if (in_array($input['email'], $block_data)) {
                $input['status'] = '5';
                $input['reason'] = 'This email address(' . $input['email'] . ') is blocked for transaction.';

                $this->transactionSession->storeData($input);
                $this->storeTransaction($input);
                return $input;
            }
        }

        $input = $this->secureCardInputs($input);
        $input['payment_type'] = 'card';

        // if card_no is included into request
        if (isset($input['card_no']) && !empty($input['card_no'])) {
            $card_no = substr($input['card_no'], 0, 6);
            $card_no .= 'XXXXXX';
            $card_no .= substr($input['card_no'], -4);

            // * check if card already WL or not and set flag
            $isWLCard = DB::table('transactions')
                ->where("card_no", $card_no)
                ->exists();
            if ($isWLCard) {
                $input["is_white_label"] = 1;
            } else {
                $input["is_white_label"] = 0;
            }

            if (!empty($block_data)) {
                if (in_array($card_no, $block_data)) {
                    $input['status'] = '5';
                    $input['reason'] = 'This card(' . $card_no . ') is blocked for transaction.';

                    $this->transactionSession->storeData($input);
                    $this->storeTransaction($input);
                    return $input;
                }
            }

            // card decline daily limit check
            $daily_card_decline_check = $this->getUserCardDeclineLimit($card_no, $user);
            if ($daily_card_decline_check >= $user->daily_card_decline_limit) {
                $input['status'] = '5';
                $input['reason'] = 'Per day card declined limit exceeded.';

                $this->transactionSession->storeData($input);
                $this->storeTransaction($input);
                return $input;
            }

            $cardType = $this->getCardType($input['card_no']);
            if ($cardType == 0) {
                $input['status'] = '5';
                $input['reason'] = 'Card type not supported.';

                $this->transactionSession->storeData($input);
                $this->storeTransaction($input);
                return $input;
            }
            $input['card_type'] = $cardType;

            // check if card is expired
            $expires = strtotime($input['ccExpiryYear'] . '-' . $input['ccExpiryMonth']);
            $now = strtotime(date('Y-m'));
            if ($expires < $now) {
                $input['status'] = '5';
                $input['reason'] = 'This card(' . $card_no . ') is Expired.';

                $this->transactionSession->storeData($input);
                $this->storeTransaction($input);
                return $input;
            }

            // specific card type blocked
            $mid_response = $this->cardTypeMIDBlocked($input, $user);
            if ($mid_response != false) {
                $input = array_merge($input, $mid_response);
                $this->transactionSession->storeData($input);
                $this->storeTransaction($input);
                return $input;
            }

            // bin checker
            try {
                $bin_response = $this->binChecking($input);
                $input['bin_country_code'] = $bin_response['country-code'] ?? '';
                $input['bin_details'] = json_encode($bin_response);
            } catch (\Exception $e) {
                $bin_response = false;
                \Log::info(['bin_api_error' => $e->getMessage()]);
            }
            // checking bin checker
            if (isset($user->is_bin_remove) && $user->is_bin_remove == '0') {
                if ($bin_response != false && isset($bin_response['country-code'])) {
                    if ($input['country'] == 'UK') {
                        $input['country'] = 'GB';
                    }
                    if ($bin_response['country-code'] !== $input['country']) {
                        $input['status'] = '5';
                        $input['reason'] = 'The card issuing country is different than the country selected.';

                        $this->transactionSession->storeData($input);
                        $this->storeTransaction($input);
                        return $input;
                    }
                } else {
                    $input['status'] = '5';
                    $input['reason'] = 'The card issuing country is different than the country selected.';

                    $this->transactionSession->storeData($input);
                    $this->storeTransaction($input);
                    return $input;
                }
            }
        }

        // mid routing
        $mid_resume = true;
        $mid_validations = false;

        if (isset($input['is_request_from_vt']) && $input['is_request_from_vt'] == 'IFRAMEAV1') {
            $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

            if ($check_assign_mid !== false) {
                $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);
            }
        } else {
            // check general card rules and user card rules
            if (isset($user->is_disable_rule) && $user->is_disable_rule == '0') {
                $user_rule_gateway_id = $this->userCardRulesCheck($input, $user);
                if ($user_rule_gateway_id != false) {
                    $mid_resume = false;
                    $input['payment_gateway_id'] = $user_rule_gateway_id;
                } else {
                    $rule_gateway_id = $this->cardRulesCheck($input, $user);
                    if ($rule_gateway_id != false) {
                        $mid_resume = false;
                        $input['payment_gateway_id'] = $rule_gateway_id;
                    }
                }
            }

            // user specific card_type mid for 1/2/3/4 only
            if ($mid_resume == true) {
                $user_specific_mid = $this->userCardTypeMID($input, $user);

                if ($user_specific_mid != false) {
                    $input['payment_gateway_id'] = $user_specific_mid;

                    // payment gateway object
                    $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                    if ($check_assign_mid !== false) {
                        $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                        if ($mid_validations == false) {
                            $mid_resume = false;
                        }
                    }
                }
            }

            // user default mid
            if ($mid_resume == true) {
                $input['payment_gateway_id'] = $user->mid;

                // payment gateway object
                $check_assign_mid = checkAssignMID($input['payment_gateway_id']);

                if ($check_assign_mid !== false) {
                    $mid_validations = $this->getMIDLimitResponse($input, $check_assign_mid, $user);

                    if ($mid_validations == false) {
                        $mid_resume = false;
                    }
                }
            }

            // if user has visa and merchant has multiple visa mid
            if (
                isset($input['card_type']) && $input['card_type'] == '2' &&
                !empty($user->multiple_mid) && $mid_resume == true
            ) {
                $visa_mid = $this->multipleVisa($input, $user);

                if ($visa_mid) {
                    $input['payment_gateway_id'] = $visa_mid;

                    $mid_resume = false;
                    $mid_validations = false;
                }
            }

            // if user has mastercard and merchant has multiple master mid
            if (
                isset($input['card_type']) && $input['card_type'] == '3' &&
                !empty($user->multiple_mid_master) && $mid_resume == true
            ) {
                $master_mid = $this->multipleMaster($input, $user);

                if ($master_mid) {
                    $input['payment_gateway_id'] = $master_mid;

                    $mid_resume = false;
                    $mid_validations = false;
                }
            }
        }

        // if all validation fails
        if (isset($mid_validations) && $mid_validations != false) {
            $input['status'] = $mid_validations['status'];
            $input['reason'] = $mid_validations['reason'];
        }

        // gateway default currency
        $check_selected_currency = $this->midDefaultCurrencyCheck($input['payment_gateway_id'], $input['currency'], $input['amount']);
        if ($check_selected_currency) {
            $input['is_converted'] = '1';
            $input['converted_amount'] = $check_selected_currency['amount'];
            $input['converted_currency'] = $check_selected_currency['currency'];
        } else {
            $input['converted_amount'] = $input['amount'];
            $input['converted_currency'] = $input['currency'];
        }

        $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
        $input['mid_type'] = $check_assign_mid->mid_type;

        $this->transactionSession->storeData($input);

        if (isset($input['status']) && $input['status'] == '5') {
            $this->storeTransaction($input);
            return $input;
        }

        // gateway curl response
        $gateway_curl_response = $this->gatewayCurlResponse($input, $check_assign_mid);

        $input = array_merge($input, $gateway_curl_response);

        // store transaction
        if ($input['status'] != '7') {
            $this->storeTransaction($input);
        }
        return $input;
    }

    // ================================================
    /* method : gatewayCurlResponse
     * @param  :
     * @description : get first response from gateway
     */// ==============================================
    public function gatewayCurlResponse($input, $check_assign_mid)
    {
        try {
            $class_name = 'App\\Http\\Controllers\\Repo\\PaymentGateway\\' . $check_assign_mid->title;
            if (class_exists($class_name)) {
                $gateway_class = new $class_name;
                $gateway_return_data = $gateway_class->checkout($input, $check_assign_mid);
            } else {
                $gateway_return_data['status'] = '0';
                $gateway_return_data['reason'] = 'Payment gateway not available.';
            }
        } catch (\Exception $exception) {
            \Log::info([
                'CardPaymentException' => $exception->getMessage()
            ]);
            $gateway_return_data['status'] = '0';
            $gateway_return_data['reason'] = 'Problem with your transaction data or may be transaction timeout from the bank.';
        }


        return $gateway_return_data;
    }

    // ================================================
    /* method : getUserCardDeclineLimit
     * @param  :
     * @description : daily card declined limit check
     */// ==============================================
    public function getUserCardDeclineLimit($card_no, $user)
    {
        return \DB::table('transactions')
            ->whereNull('deleted_at')
            ->where('status', '0')
            ->where('user_id', $user->id)
            ->where('card_no', $card_no)
            ->whereNotIn('payment_gateway_id', [0, 1, 2])
            ->whereBetween('created_at', [Carbon::now()->subMinutes(30)->toDateTimeString(), Carbon::now()->toDateTimeString()])
            ->count();
    }

    public function getMIDLimitResponse($input, $check_assign_mid, $user)
    {
        // per transaction maximum and minimum amount limit
        $per_transaction_limit_response = $this->perTransactionLimitCheck($input, $check_assign_mid, $user);
        if ($per_transaction_limit_response != false) {
            return $per_transaction_limit_response;
        }

        // mid daily limit
        $mid_daily_limit = $this->perDayAmountLimitCheck($input, $check_assign_mid, $user);
        if ($mid_daily_limit != false) {
            return $mid_daily_limit;
        }

        $transactions_check = \DB::table('transactions')
            ->whereNull('deleted_at')
            ->where('status', '<>', '5')
            ->where('user_id', $input['user_id'])
            ->where('payment_gateway_id', $input['payment_gateway_id']);

        // if there is card_no
        if (isset($input['card_no']) && $input['card_no'] != null) {

            $card_transactions_check = clone $transactions_check;
            $card_transactions_check = $card_transactions_check->where('card_no', substr($input['card_no'], 0, 6) . 'XXXXXX' . substr($input['card_no'], -4));

            // daily card limit check
            $card_daily_transactions = clone $card_transactions_check;
            $card_daily_transactions = $card_daily_transactions->whereBetween('created_at', [Carbon::now()->subDays(1)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($card_daily_transactions >= $check_assign_mid->per_day_card && $card_daily_transactions >= $user->one_day_card_limit) {
                return [
                    'status' => '5',
                    'reason' => 'Per day transactions by card limit exceeded.'
                ];
            }

            // card per-week limit
            $card_weekly_transactions = clone $card_transactions_check;
            $card_weekly_transactions = $card_weekly_transactions->whereBetween('created_at', [Carbon::now()->subDays(7)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($card_weekly_transactions >= $check_assign_mid->per_week_card && $card_weekly_transactions >= $user->one_week_card_limit) {
                return [
                    'status' => '5',
                    'reason' => 'Per week transactions by card limit exceeded.'
                ];
            }

            // card per-month limit
            $card_monthly_transactions = clone $card_transactions_check;
            $card_monthly_transactions = $card_monthly_transactions->whereBetween('created_at', [Carbon::now()->subDays(30)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($card_monthly_transactions >= $check_assign_mid->per_month_card && $card_monthly_transactions >= $user->one_month_card_limit) {
                return [
                    'status' => '5',
                    'reason' => 'Per month transactions by card limit exceeded.'
                ];
            }
        }

        // if there is email
        if (isset($input['email']) && $input['email'] != null) {

            $email_transactions_check = clone $transactions_check;
            $email_transactions_check = $email_transactions_check->where('email', $input['email']);

            // email per-day limit
            $email_daily_transactions = clone $email_transactions_check;
            $email_daily_transactions = $email_daily_transactions->whereBetween('created_at', [Carbon::now()->subDays(1)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($email_daily_transactions >= $check_assign_mid->per_day_email && $email_daily_transactions >= $user->one_day_email_limit) {
                return [
                    'status' => '5',
                    'reason' => 'Per day transactions by email limit exceeded.'
                ];
            }

            // email per-week limit
            $email_weekly_transactions = clone $email_transactions_check;
            $email_weekly_transactions = $email_weekly_transactions->whereBetween('created_at', [Carbon::now()->subDays(7)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($email_weekly_transactions >= $check_assign_mid->per_week_email && $email_weekly_transactions >= $user->one_week_email_limit) {
                return [
                    'status' => '5',
                    'reason' => 'Per week transactions by email limit exceeded.'
                ];
            }

            // email per-month limit
            $email_monthly_transactions = clone $email_transactions_check;
            $email_monthly_transactions = $email_monthly_transactions->whereBetween('created_at', [Carbon::now()->subDays(30)->toDateTimeString(), Carbon::now()->toDateTimeString()])
                ->count();
            if ($email_monthly_transactions >= $check_assign_mid->per_month_card && $email_monthly_transactions >= $user->one_month_email_limit) {
                return [
                    'status' => '5',
                    'reason' => 'Per month transactions by email limit exceeded.'
                ];
            }
        }

        // blocked country validation
        if (isset($input['country']) && $input['country'] != null) {
            $blocked_country_response = $this->validateBlockedCountry($input, $check_assign_mid);
            if ($blocked_country_response != false) {
                return $blocked_country_response;
            }
        }

        return false;
    }

    // ================================================
    /* method : multipleVisa
     * @param  : 
     * @description : multiple visa
     */// ===============================================
    public function multipleVisa($input, $user)
    {
        if ($input['card_type'] == '2' && !empty($user->multiple_mid)) {
            $multiple_mid = json_decode($user->multiple_mid);

            foreach ($multiple_mid as $value) {
                $input['payment_gateway_id'] = $value;
                $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
                if ($check_assign_mid == false) {
                    continue;
                }

                // mid validation
                $mid_limit_response = $this->getMIDLimitResponse($input, $check_assign_mid, $user);
                if ($mid_limit_response != false) {
                    continue;
                }

                return $value;
            }
        }
        return false;
    }

    // ================================================
    /* method : multipleMaster
     * @param  : 
     * @description : multiple master mid
     */// ===============================================
    public function multipleMaster($input, $user)
    {
        if ($input['card_type'] == '3' && !empty($user->multiple_mid_master)) {
            $multiple_mid = json_decode($user->multiple_mid_master);

            foreach ($multiple_mid as $value) {
                $input['payment_gateway_id'] = $value;
                $check_assign_mid = checkAssignMID($input['payment_gateway_id']);
                if ($check_assign_mid == false) {
                    continue;
                }

                // mid validation
                $mid_limit_response = $this->getMIDLimitResponse($input, $check_assign_mid, $user);
                if ($mid_limit_response != false) {
                    continue;
                }

                return $value;
            }
        }
        return false;
    }

    // ================================================
    /* method : secureCardInputs
     * @param  : 
     * @description : functions on credit card
     */// ===============================================
    private function secureCardInputs($input)
    {
        // change expiry year to 4 digit
        if (!empty($input['ccExpiryYear'])) {
            $input['ccExpiryYear'] = trim($input['ccExpiryYear']);
            if (strlen($input['ccExpiryYear']) == '2') {
                $input['ccExpiryYear'] = '20' . $input['ccExpiryYear'];
            }
        }

        // change expiry month to 2 digit
        if (!empty($input['ccExpiryMonth'])) {
            $input['ccExpiryMonth'] = trim($input['ccExpiryMonth']);
            if (strlen($input['ccExpiryMonth']) == '1') {
                $input['ccExpiryMonth'] = '0' . $input['ccExpiryMonth'];
            }
        }

        if (!empty($input['cvvNumber'])) {
            $input['cvvNumber'] = trim($input['cvvNumber']);
        }

        if (!empty($input['card_no'])) {
            $input['card_no'] = str_replace(' ', '', $input['card_no']);
        }

        return $input;
    }

    // ================================================
    /* method : getCardType
     * @param  : 
     * @description : get card type
     */// ===============================================
    private function getCardType($card_no)
    {
        if (empty($card_no)) {
            return false;
        }
        $cardtype = array(
            "visa" => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            "mastercard" => "/^5[1-5][0-9]{14}$/",
            "amex" => "/^3[47]\d{13,14}$/",
            "jcb" => "/^(?:2131|1800|35\d{3})\d{11}$/",
            "solo" => "/^(6334|6767)[0-9]{12}|(6334|6767)[0-9]{14}|(6334|6767)[0-9]{15}$/",
            "maestro" => "/^(5018|5020|5038|6304|6759|6761|6763|6768)[0-9]{8,15}$/",
            "discover" => "/^65[4-9][0-9]{13}|64[4-9][0-9]{13}|6011[0-9]{12}|(622(?:12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|9[01][0-9]|92[0-5])[0-9]{10})$/",
            "switch" => "/^(4903|4905|4911|4936|6333|6759)[0-9]{12}|(4903|4905|4911|4936|6333|6759)[0-9]{14}|(4903|4905|4911|4936|6333|6759)[0-9]{15}|564182[0-9]{10}|564182[0-9]{12}|564182[0-9]{13}|633110[0-9]{10}|633110[0-9]{12}|633110[0-9]{13}$/",
            "unionpay" => "/^(62[0-9]{14,17})$/",
        );

        if (preg_match($cardtype['visa'], $card_no)) {
            return '2';
        } else if (preg_match($cardtype['mastercard'], $card_no)) {
            return '3';
        } else if (preg_match($cardtype['amex'], $card_no)) {
            return '1';
        } else if (preg_match($cardtype['discover'], $card_no)) {
            return '4';
        } else if (preg_match($cardtype['jcb'], $card_no)) {
            return '5';
        } else if (preg_match($cardtype['maestro'], $card_no)) {
            return '6';
        } else if (preg_match($cardtype['switch'], $card_no)) {
            return '7';
        } else if (preg_match($cardtype['solo'], $card_no)) {
            return '8';
        } else if (preg_match($cardtype['unionpay'], $card_no)) {
            return '9';
        } else {
            // return false;
            return '0';
        }
    }

    public function markFlagged($id, $flagged_by, $status, $is_bulk = false)
    {
        $isFlaggedTransaction = Transaction::where('id', $id)->first();
        if ($isFlaggedTransaction->is_flagged == '0' && $isFlaggedTransaction->flagged_date == null && $isFlaggedTransaction->refund == '0' && $isFlaggedTransaction->chargebacks == '0' && $isFlaggedTransaction->is_retrieval == '0' && $isFlaggedTransaction->status == '1') {
            if ($this->transaction->updateData($id, ['is_flagged' => $status, 'flagged_date' => date('Y-m-d H:i:s'), 'flagged_by' => $flagged_by, 'transaction_date' => date('Y-m-d H:i:s'), "is_flagged_remove" => '0', "flagged_remove_date" => NULL])) {
                $transaction = Transaction::select('transactions.card_type as card_type', 'transactions.id as id', 'transactions.card_no as card_no', 'transactions.flagged_date as flagged_date', 'transactions.amount as amount', 'transactions.currency as currency', 'transactions.order_id', 'transactions.first_name', 'transactions.last_name', 'transactions.email', 'transactions.created_at', 'users.id as user_id', 'users.email as user_email')
                    ->join('users', 'users.id', 'transactions.user_id')
                    ->where('transactions.id', $id)
                    ->first();
                if ($transaction->card_type == '1') {
                    $input['card_type'] = 'Amex';
                } else if ($transaction->card_type == '2') {
                    $input['card_type'] = 'Visa';
                } else if ($transaction->card_type == '3') {
                    $input['card_type'] = 'Mastercard';
                } else if ($transaction->card_type == '4') {
                    $input['card_type'] = 'Discover';
                }
                if (strlen($transaction->card_no) > 4) {
                    $transaction->card_no = 'XXXXXXXXXXXX' . substr($transaction->card_no, -4);
                } else {
                    $transaction->card_no = $transaction->card_no;
                }
                $input['order_id'] = $transaction->order_id;
                $input['card_no'] = $transaction->card_no;
                $input['flagged_date'] = $transaction->flagged_date;
                $input['amount'] = $transaction->amount;
                $input['currency'] = $transaction->currency;
                $input['email'] = $transaction->email;
                $input['created_at'] = $transaction->created_at;
                $input['card_type'] = $transaction->card_type;
                $input['first_name'] = $transaction->first_name;
                $input['last_name'] = $transaction->last_name;
                $token = $transaction->id . \Str::random(32);
                $input['url'] = URL::to('/') . '/transaction-documents-upload?transactionId=' . $transaction->id . '&uploadFor=flagged&token=' . $token;
                Transaction::where('id', $transaction->id)->update(['transactions_token' => $token]);
                $mail_data['subject'] = 'Flagged transaction mail.';
                $mail_data['title'] = 'You have new flag transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.';
                if ($is_bulk) {
                    $UserArr = array(
                        'user_email' => $transaction->user_email,
                        'user_id' => $transaction->user_id,
                    );
                    $bulkSuspiciousQueueEmail = (new \App\Jobs\BulkTransactionSuspiciousQueueEmail($UserArr, $input))->delay(now()->addSeconds(2));
                    dispatch($bulkSuspiciousQueueEmail);
                } else {
                    try {
                        Mail::to($transaction->user_email)->queue(new FlaggedTransactionMail($input));
                        $user_additional_mail = getAdditionalFlaggedEmail($transaction->user_id);
                        if ($user_additional_mail != null) {
                            Mail::to($user_additional_mail)->queue(new FlaggedTransactionMail($input));
                        }
                    } catch (\Exception $e) {
                        \Log::info([
                            'error_type' => 'Flag transaction error',
                            'body' => $e->getMessage()
                        ]);
                    }
                }
                $notification = [
                    'user_id' => $transaction->user_id,
                    'sendor_id' => auth()->guard('admin')->user()->id,
                    'type' => 'user',
                    'title' => 'Transaction Flagged',
                    'body' => 'You have new suspicious transaction with order number ' . $input['order_id'] . ' in ' . config("app.name") . '. Please login to ' . config("app.name") . ' and upload the concernced document for the same.',
                    'url' => '/suspicious',
                    'is_read' => '0'
                ];
                $realNotification = addNotification($notification);
                $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                event(new UserNotification($realNotification->toArray()));
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function removeFlagged($id)
    {
        $transactionData = Transaction::find($id);
        $payout_report = PayoutReports::where('user_id', $transactionData->user_id)
            ->whereDate('chargebacks_start_date', '<=', date('Y-m-d', strtotime($transactionData->flagged_date)))
            ->whereDate('chargebacks_end_date', '>=', date('Y-m-d', strtotime($transactionData->flagged_date)))
            ->orderBy('id', 'DESC')
            ->count();

        try {
            $date = Carbon::now()->toDateTimeString();
            if ($payout_report == '0') {
                $arr = [];
            } else {
                $arr = ["is_flagged_remove" => "1", 'flagged_remove_date' => $date];
            }
            $arr["is_flagged"] = "0";

            Transaction::where('id', $id)->update($arr);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}