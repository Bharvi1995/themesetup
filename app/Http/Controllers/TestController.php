<?php

namespace App\Http\Controllers;

use Auth;
use URL;
use Input;
use File;
use View;
use Session;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function payoutmonnetpayments(Request $request)
    {

    	$url = 'https://cert.apipayout.monnetpayments.com/ms-payroll-trx/commerce/idcommerce/payroll';

        $paramsArray = [
            'name'=>'test'
        ];

        $requestBody = json_encode($paramsArray);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Accept: application/json",
    			"Content-Type: application/json"
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        dd($response);
        $responseData = json_decode($response, true);
        dd($responseData);

        dd('ok');
    }

    public function facilitapayForm(Request $request)
    {
        return view('test.facilitapay.index');
    }

    public function testWebhookUrl(Request $request)
    {
        $data = [
            'testing' => 'hello'
        ];

        $url = 'https://egpay.btloginc.com/interface/paymentresponse/notify/testpay/merchant372';

        $output = postCurlRequestBackUpTwo($url, $data);

        dd($output);
    }

    public function microtimeFormat($data,$format=null,$lng=null)
    {
        $duration = microtime(true) - $data;
        $hours = (int)($duration/60/60);
        $minutes = (int)($duration/60)-$hours*60;
        $seconds = $duration-$hours*60*60-$minutes*60;
        return number_format((float)$seconds, 2, '.', '');
    }

    public function microtimeToSecond(Request $request)
    {
        $start = microtime(TRUE);
        print_r($this->microtimeFormat($start));
        sleep(5);
        $delay = $this->microtimeFormat($start);
        dd($delay);
    }
}
