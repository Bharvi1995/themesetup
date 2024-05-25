<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MIDDetail;
use Carbon\Carbon;


class SoiController extends Controller
{
    public function refund()
    {
        return view('soi.refund');
    }

    public function store(Request $request)
    {
        $transactionData = \DB::table("transactions")->where("order_id",$request->order_id)->first();
        if ($transactionData == null) {
            return abort(404);
        }
        $check_assign_mid = checkAssignMid($transactionData->payment_gateway_id);
        $data = [
            "api-key" => $check_assign_mid->api_key,
            "orderid" => $request->order_id,
            "paymentId" => $request->gateway_order_id
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://azulpay.co/api/rest/paymentRefund',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data,JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'secret:'.$check_assign_mid->secret_key
            ),
        ));

        $response = curl_exec($curl);
        \Log::info([
            'soi-hosted-refund' => $response,
        ]);
        curl_close($curl);
        $responseData = json_decode($response,true);
        if($responseData["success"] == "false"){
            notificationMsg('error', isset($responseData["message"])?$responseData["message"]:"Something went wrong .. !!");
        }else{
            notificationMsg('success', "Refund has request for order number ".$request->order_id." has been initiated");
        }
        return redirect()->route('soi-refund');
    }
}
