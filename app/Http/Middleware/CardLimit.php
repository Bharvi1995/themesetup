<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use App\User;
class CardLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

          // validate API key
          $payment_gateway_id = DB::table('users')
          ->select('middetails.id as midid', 'middetails.gateway_table', 'users.*')
          ->leftJoin('middetails', 'middetails.id','users.mid')
          ->where('users.api_key', $request['api_key'])
          ->where('users.is_active', '1')
          ->whereNull('users.deleted_at')
          ->first();

            // if api_key is not valid or user deleted
            if(!$payment_gateway_id) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'please check your API key',
                    'sulte_apt_no' => $sulte_apt_no,
                    'api_key' => $request['api_key'],
                ]);
            }
             // user_id
             $request['user_id'] = $payment_gateway_id->id;
             // set payment_gateway_id
             $request['payment_gateway_id'] = $payment_gateway_id->mid;
             $request['is_ip_remove'] = $payment_gateway_id->is_ip_remove;
             $request['payment_gateway_user_id'] = $payment_gateway_id->id;
            $user = User::where('id', $request['user_id'])
            ->first();
            if(isset($request['card_no']) && $request['card_no'] != null){
                $request['card_no'] = str_replace(" ", "", $request['card_no']);
                $amount_convert_usd = currency_convert_into_usd($request['currency'], $request['amount']);
                // if not in testing mode, assign cardwise gateway and check card limit
                if($request['payment_gateway_id'] != '0' && $request['payment_gateway_id'] != '41') {
                     // per card one day limit
                    $user_one_day_card_limit = getUserCardOneDayOldAPI($request['card_no'], $request['user_id']);
                    if($user_one_day_card_limit >= $user->one_day_card_limit) {

                        $request['reason'] = 'Transaction was declined due to charge a single card more than your one day limit.';

                        $this->transaction->storeData($request);

                        DB::table('transaction_session')
                            ->where('transaction_id', $transaction_id)
                            ->update(['is_completed' => '1']);

                        $return_data = [
                            'status' => '0',
                            'order_id' => $request['order_id'],
                            'message' => $request['reason'],
                        ];
                        return $return_data;
                    }

                    // per card one week limit
                    $user_one_week_card_limit = getUserCardOneWeekOldAPI($request['card_no'], $request['user_id']);
                    if($user_one_week_card_limit >= $user->one_week_card_limit) {

                        $request['reason'] = 'Transaction was declined due to charge a single card more than your one week limit.';

                        $this->transaction->storeData($request);

                        DB::table('transaction_session')
                            ->where('transaction_id', $transaction_id)
                            ->update(['is_completed' => '1']);

                        $return_data = [
                            'status' => '0',
                            'order_id' => $request['order_id'],
                            'message' => $request['reason'],
                        ];

                        return $return_data;
                    }

                    // per card one month limit
                    $user_one_month_card_limit = getUserCardOneMonthOldAPI($request['card_no'], $request['user_id']);
                    if($user_one_month_card_limit >= $user->one_month_card_limit) {

                        $request['reason'] = 'Transaction was declined due to charge a single card more than your one month limit.';

                        $this->transaction->storeData($request);

                        DB::table('transaction_session')
                            ->where('transaction_id', $transaction_id)
                            ->update(['is_completed' => '1']);

                        $return_data = [
                            'status' => '0',
                            'order_id' => $request['order_id'],
                            'message' => $request['reason'],
                        ];
                        return $return_data;
                    }


                    if($amount_convert_usd == false) {
                        $request['status'] = '0';
                        $request['reason'] = 'please check your currency and amount formate.';

                        $this->transaction->storeData($request);

                        $return_data = [
                            'status' => '0',
                            'order_id' => $request['order_id'],
                            'message' => $request['reason'],
                        ];

                        return $return_data;
                    }

                    // check per transaction amount limit
                    if($amount_convert_usd > $user->per_transaction_limit) {
                    // if($request['amount'] > $user->per_transaction_limit) {

                        $request['status'] = '0';
                        $request['reason'] = 'per transaction amount exceeded.';

                        $this->transaction->storeData($request);

                        $return_data = [
                            'status' => '0',
                            'order_id' => $request['order_id'],
                            'message' => $request['reason'],
                        ];

                        return $return_data;
                    }

                    // per email one day limit
                    $user_one_day_email_limit = getUserEmailOneDayOldAPI($request['email'], $request['user_id']);
                    if($user_one_day_email_limit >= $user->one_day_email_limit) {

                        $request['status'] = '0';
                        $request['reason'] = 'Transaction was declined due to used a single email more than your one day limit.';

                        $this->transaction->storeData($request);

                        $return_data = [
                            'status' => '0',
                            'order_id' => $request['order_id'],
                            'message' => $request['reason'],
                        ];

                        return $return_data;
                    }

                    // per email one week limit
                    $user_one_week_email_limit = getUserEmailOneWeekOldAPI($request['email'], $request['user_id']);
                    if($user_one_week_email_limit >= $user->one_week_email_limit) {

                        $request['status'] = '0';
                        $request['reason'] = 'Transaction was declined due to used a single email more than your one week limit.';

                        $this->transaction->storeData($request);

                        $return_data = [
                            'status' => '0',
                            'order_id' => $request['order_id'],
                            'message' => $request['reason'],
                        ];
                        return $return_data;
                    }

                    // per email one month limit
                    $user_one_month_email_limit = getUserEmailOneMonthOldAPI($request['email'], $request['user_id']);
                    if($user_one_month_email_limit >= $user->one_month_email_limit) {

                        $request['status'] = '0';
                        $request['reason'] = 'Transaction was declined due to used a single email more than your one month limit.';

                        $this->transaction->storeData($request);

                        $return_data = [
                            'status' => '0',
                            'order_id' => $request['order_id'],
                            'message' => $request['reason'],
                        ];

                        return $return_data;
                    }
                }
            }



        return $next($request);
    }
}
