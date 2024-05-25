<?php

namespace App\Traits;

use App\User;
use App\Application;

trait BinChecker
{
	// ================================================
	/* method : binChecking
	* @param  :
	* @description : check rules and get payment_gateway_id
	*/// ==============================================
	public function binChecking($input)
    {
        $postdata = array(
            "user-id" => config('services.neutrino.user_id'),
            "api-key" => config('services.neutrino.api_key'),
            "bin-number" => substr($input['user_card_no'], 0, 6),
            "customer-ip" => $input['user_ip'] ?? \Request::ip(),
        );

        $url = 'https://neutrinoapi.net/bin-lookup';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        
        $response_body = curl_exec($ch);
        
        curl_close($ch);

        $result = json_decode($response_body, true);

        return $result;
    }
}
