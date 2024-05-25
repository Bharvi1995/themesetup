<?php

namespace App\Jobs;

use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use DB;
use Http;
use Log;


class LeonePayPendingTxnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $mid, $url, $transaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mid, $url)
    {
        $this->mid = $mid;
        $this->url = $url;
        $this->transaction = new Transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $txnIds = [];
            $data = DB::table("transaction_session")->select("id", "request_data", "order_id", "transaction_id", "gateway_id")->where("payment_gateway_id", $this->mid->id)->where("is_completed", "0")->orderByRaw("RAND()")->limit(30)->get();
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $input = json_decode($item->request_data, true);
                    $response = $this->statusApi($item->order_id, $item->gateway_id);

                    if ($response == null || empty($response)) {
                        continue;
                    }

                    if (isset($response["success"]) && $response["success"] == false) {
                        $input["status"] = "0";
                        $input["reason"] = "User cancelled the transaction process in between.";
                    } else if (isset($response["success"]) && $response["success"] == true && isset($response["status"]) && $response["status"] == "INITIATED") {
                        $input["status"] = "0";
                        $input["reason"] = "User did not complete the transaction process.";
                    } else if (isset($response["success"]) && $response["success"] == true && isset($response["status"]) && $response["status"] == "APPROVED") {
                        $input["status"] = "1";
                        $input["reason"] = "Transaction processed successfully!";
                    } else if (isset($response["success"]) && $response["success"] == true && isset($response["status"]) && $response["status"] == "DECLINED") {
                        $input["status"] = "0";
                        $input["reason"] = isset($response["data"]["gatewayResponse"]) ? $response["data"]["gatewayResponse"] : "Transaction could not processed!";
                    } else if (isset($response["success"]) && $response["success"] == true && isset($response["status"]) && $response["status"] == "PENDING") {
                        continue;
                    } else if (isset($response["success"]) && $response["success"] == true && isset($response["status"]) && $response["status"] == "ERROR") {
                        $input["status"] = "0";
                        $input["reason"] = isset($response["data"]["gatewayResponse"]) ? $response["data"]["gatewayResponse"] : "Transaction could not processed!";
                    }

                    // * Only update those transaction who have final status
                    if ($input["status"] != "2") {
                        array_push($txnIds, $item->id);
                        updateGatewayData($input, $response, $item->transaction_id);
                        $this->transaction->storeData($input);
                    }
                }
                // * update session table record
                if (count($txnIds) > 0) {
                    DB::table("transaction_session")->whereIn("id", $txnIds)->update(["is_completed" => "1"]);
                }
            } else {
                Log::info(["Leonepay-cron-msg" => "No record found!"]);
            }
        } catch (\Exception $err) {
            Log::error(["Leonepay-cron-job-error" => $err->getMessage()]);
        }
    }

    public function statusApi($orderId, $gatewayId)
    {
        $payload = [
            "apiKey" => $this->mid->api_key,
            "orderId" => $orderId,
            "paymentId" => $gatewayId
        ];
        $response = Http::withHeaders(["Content-Type" => "application/json", "authToken" => $this->mid->auth_token])->post($this->url, $payload)->json();
        return $response;
    }
}
