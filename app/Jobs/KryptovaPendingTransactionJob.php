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

class KryptovaPendingTransactionJob implements ShouldQueue
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
            $data = DB::table("transaction_session")->select("id", "request_data", "order_id", "transaction_id")->where("payment_gateway_id", $this->mid->id)->where("is_completed", "0")->orderByRaw("RAND()")->limit(15)->get();
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $input = json_decode($item->request_data, true);
                    $response = $this->statusAPI($item->order_id);

                    if (isset($response["status"]) && $response["status"] == "fail") {
                        array_push($txnIds, $item->id);
                        $input["status"] = "0";
                        $input["reason"] = "User cancelled the transaction process in between.";
                        $this->transaction->storeData($input);
                    } else if (isset($response["status"]) && $response["status"] == "success" && isset($response["transaction"]["transaction_status"]) && $response["transaction"]["transaction_status"] == "success") {
                        $input["status"] = "1";
                        $input["reason"] = "Transaction processed successfully!.";
                    } else if (isset($response["status"]) && $response["status"] == "success" && isset($response["transaction"]["transaction_status"]) && $response["transaction"]["transaction_status"] == "declined") {
                        $input["status"] = "0";
                        $input["reason"] = $response["transaction"]["reason"] ?? "Transaction failed.";
                    } else if (isset($response["status"]) && $response["status"] == "success" && isset($response["transaction"]["transaction_status"]) && $response["transaction"]["transaction_status"] == "pending") {
                        continue;
                    } else if (isset($response["status"]) && $response["status"] == "success" && isset($response["transaction"]["transaction_status"]) && $response["transaction"]["transaction_status"] == "blocked") {
                        $input["status"] = "0";
                        $input["reason"] = $response["transaction"]["reason"] ?? "Transaction failed.";
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
                Log::info(["kryptova-cron-msg" => "No record found!"]);
            }
        } catch (\Exception $err) {
            Log::error(["kryptova-cron-job-error" => $err->getMessage()]);
        }
    }

    public function statusAPI($orderId)
    {
        $payload = [
            "api_key" => $this->mid->api_key,
            "customer_order_id" => $orderId
        ];
        $response = Http::post($this->url, $payload)->json();
        return $response;
    }
}
