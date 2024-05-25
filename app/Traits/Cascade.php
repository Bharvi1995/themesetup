<?php

namespace App\Traits;

use App\User;
use App\Transaction;
use App\TransactionSession;
use Illuminate\Http\Request;

trait Cascade
{
	// ================================================
	/* method : checkGateway
	* @param  : 
	* @description : return next payment_gateway_id or false
	*/// ==============================================
	public function checkGateway($input, $response)
	{
		// get main User object
        $user = User::where('id', $input['user_id'])
            ->first();

        $input['status'] = $response['status'] ?? '0';
        $input['reason'] = $response['reason'] ?? 'Transaction declined.';

        // if transaction declined
        if ($input['status'] == '0') {

            // user has multiple mid
            if ($user['is_multi_mid'] == '1') {

                // multiple mid assign
                $mids = $user['mid_list'];

                // start first gateway from multiple mids
                if (!in_array($input['payment_gateway_id'], $mids)) {

                    // next payment_gateway_id
                    $payment_gateway_id = $mids[0];

                    return $payment_gateway_id;
                } else {

                    // get key of current payment_gateway_id
                    $mid_key = array_search($input['payment_gateway_id'], $mids);

                    // next payment_gateway_id
                    if ($mid_key != array_key_last($mids)) {
                        $next_gateway_key = $mid_key + 1;
                        
                        $payment_gateway_id = $mids[$next_gateway_key];

                        return $payment_gateway_id;
                    }
                }
            }
        }

        return false;
	}
}