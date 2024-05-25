<?php

namespace App\Http\Middleware;

use Closure;
use App\Transaction;
class CheckCard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function __construct()
    {
        $this->transaction = new Transaction;
    }
    public function handle($request, Closure $next)
    {
        
        if(isset($request['card_no']) && $request['card_no'] != null){
            $request['card_no'] = str_replace(" ", "", $request['card_no']);            
            // if not in testing mode, assign cardwise gateway and check card limit
           
            if($request['payment_gateway_id'] != '0' && $request['payment_gateway_id'] != '41') {
                // return back if test card details has entered
                
                if (in_array($request['card_no'], config('services.test_cards'))) {
                    $request['reason'] = 'Your account is in live mode, but you used test card details.';
                    $return_data = [
                        'status' => '0',
                        'order_id' => $request['order_id'],
                        'message' => $request['reason'],
                    ];

                     return response($return_data, 406);
                }
                
                $amount_convert_usd = currency_convert_into_usd($request['currency'], $request['amount']);
                
                if($amount_convert_usd == false) {
                    $request['status'] = '0';
                    $request['reason'] = 'please check your currency and amount formate.';
                      
                    $this->transaction->storeData($request);
                    
                    $return_data = [
                        'status' => '0',
                        'order_id' => $request['order_id'],
                        'message' => $request['reason'],
                    ];

                    return response($return_data, 406);
                }
            }
            
        }
             
       
        return $next($request);
    }
}
