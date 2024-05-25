<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MIDDetail;
use Carbon\Carbon;


class AIGlobalController extends Controller
{
    public function refund()
    {
        return view('aiglobal.refund');
    }

    public function store(Request $request)
    {
        $transactionData = \DB::table("transactions")->where("order_id", $request->order_id)->first();
        if ($transactionData == null) {
            return abort(404);
        }
        $check_assign_mid = checkAssignMid($transactionData->payment_gateway_id);
        $signSrc = $check_assign_mid->mid . $check_assign_mid->gateway . $transactionData->gateway_id . $check_assign_mid->key;
        $signInfo = hash('sha256', trim($signSrc));

        $payload = [
            'merNo' => $check_assign_mid->mid,
            'gatewayNo' => $check_assign_mid->gateway,
            'signInfo' => $signInfo,
            'refundOrders' => array(
                array(
                    "tradeNo" => $transactionData->gateway_id,
                    "orderNo" => $transactionData->session_id,
                    "currency" => $transactionData->currency,
                    "tradeAmount" => $transactionData->amount_in_usd,
                    "refundAmount" => $transactionData->amount_in_usd,
                    "refundReason" => $transactionData->reason
                )
            )
        ];

        foreach ($payload as $k => $a) {
            $payload[$k] = json_decode(json_encode($a));
        }

        $request_url = 'https://merchant.aiglobalpay.com/RefundInterface';

        $responseData = $this->curlPostRequest($request_url, $payload);

        \Log::info([
            'aiglobalpay-refund-response' => $responseData
        ]);

        if ($responseData->errorStatus == "0") {
            notificationMsg('error', isset($responseData->errorInfo) ? $responseData->errorInfo : "Something went wrong .. !!");
        } else {
            if ($responseData->refundOrders && $responseData->refundOrders[0]->refundStatus == "1") {
                notificationMsg('success', "Refund has request for order number " . $request->order_id . " has been initiated");
            } else {
                notificationMsg('error', isset($responseData->refundOrders) ? $responseData->refundOrders[0]->refundInfo : "Something went wrong .. !!");
            }
        }
        return redirect()->route('aiglobal-refund');
    }

    public function curlPostRequest($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}
