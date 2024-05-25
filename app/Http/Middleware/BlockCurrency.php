<?php

namespace App\Http\Middleware;

use Closure;

class BlockCurrency
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
        if (in_array($request['currency'], ['TRL', 'ARA', 'TRY', 'SEK'])) {

            $request['reason'] = 'currency not supported.ss';
           
            $return_data = [
                'status' => '0',
                'order_id' => null,
                'message' => $request['reason'],
            ];
            return response($return_data, 406);
        }
        return $next($request);
    }
}
