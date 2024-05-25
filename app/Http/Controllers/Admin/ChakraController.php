<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MIDDetail;
use Carbon\Carbon;


class ChakraController extends Controller
{
    // const BASE_URL = 'https://webdev.mychakra.io/'; // Test
    const BASE_URL = 'https://web.mychakra.io/'; //Live
    private $page = 1;
    private $records = 100;

    public function index()
    {
        $midId = MIDDetail::where('gateway_table','gateway_Chakra')->get();

        return view('chakra.transactionlist',compact('midId'));
    }

    public function transactionlist(Request $request)
    {   
        
        $err = $responseData = $err1 = '';
        $page = $request->page ?? $this->page;
        $records = $request->records ?? $this->records;

        if (! empty($request->mid) ) {

            $check_assign_mid = checkAssignMID($request->mid); 
            $token = $this->getAccessToken($check_assign_mid);
            $midId = MIDDetail::where('gateway_table','gateway_Chakra')->get();

            if(empty($request->start_date) && empty($request->reference_id)) {
                $err1 = "Please select date or enter reference number";
            } 
            
            else {
                if (isset($token['data']['accessToken'])) {

                $token = $token['data']['accessToken'];
                if( empty($request->reference_id)) {

                    $credential = [
                        'chakra-credentials' => base64_encode($check_assign_mid->merchant_id . ':' . $check_assign_mid->api_key)
                    ];
                    $credential = http_build_query($credential);
                    $url = 'acq/transaction-list?' . $credential; 
                    $header = [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token
                    ];
                   
                    $requestData = [
                        'paymentType' => '1',
                        'startDate' => ! empty($request->start_date) ? Carbon::parse($request->start_date)->format('Y-m-d H:i:s') : Carbon::now()->format('Y-m-d H:i:s'),
                        'endDate' => ! empty($request->end_date) ? Carbon::parse($request->end_date)->format('Y-m-d 23:59:59') : Carbon::now()->format('Y-m-d 23:59:59'),
                        'pageNumber' => $page,
                        'pageSize' => $records
                    ];
                    
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL,self::BASE_URL . $url);
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER,
                       $header
                    );
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    $info = curl_getinfo($curl);
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                    
                    $responseData = json_decode($response, 1);  
                    if ($responseData['responseCode'] != '00') {
                        $err1 = isset ($responseData['responseMessage']) && ! empty($responseData['responseMessage'])?  $responseData['responseMessage'] : 'No card transaction found';
                    }
                    
                } else {

                    $credential = [
                        'transRef' => $request->reference_id,
                        'chakra-credentials' => base64_encode($check_assign_mid->merchant_id . ':' . $check_assign_mid->api_key),
                    ];
                    $credential = http_build_query($credential);

                    $url = 'acq/get-transaction-status?' . $credential; 

                    $header = [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token
                    ];
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL,self::BASE_URL . $url);
                    curl_setopt($curl, CURLOPT_HTTPHEADER,
                       $header
                    );
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    $info = curl_getinfo($curl);
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                    $responseData = json_decode($response, 1); 
                    if ($responseData['responseCode'] != '00') {
                        $err1 = isset ($responseData['responseMessage']) && ! empty($responseData['responseMessage'])?  $responseData['responseMessage'] : 'No card transaction found';
                    }
                    if (isset($responseData['data'])) {
                        $responseData['data'] =  [$responseData['data']];
                    }
                }
            }
            }
            

        } else {
            
            $err = "Please select MID";
            $midId = MIDDetail::where('gateway_table', 'gateway_Chakra')->get();
        }
        return view('chakra.list',compact('responseData', 'err', 'midId', 'page', 'records','err1'));
    }

    /*
     * For generate aceesst oken
     * */
    private function getAccessToken($check_assign_mid) {

        $err = '';

        $requestData = [
            'merchantId' => $check_assign_mid->merchant_id,
            'apiKey' => $check_assign_mid->api_key,
        ];

        $url = 'credentials/get-token';

        $header = [
            'Content-Type: application/json',
        ];

        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
           $header
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $info = curl_getinfo($curl);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        $responseData = json_decode($response, 1);
        
        return $responseData;
    }

}
