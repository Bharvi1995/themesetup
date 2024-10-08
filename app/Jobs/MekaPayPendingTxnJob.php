<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use DB;
use Http;
use Log;
use App\Transaction;

class MekaPayPendingTxnJob implements ShouldQueue
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
                    // * Id gtaeway id is 1 or null then directly update the txn
                    if ($item->gateway_id == "1" || $item->gateway_id == null) {
                        array_push($txnIds, $item->id);
                        $input["status"] = "0";
                        $input["reason"] = "User did not complete the transaction process.";
                        $this->transaction->storeData($input);
                    } else {
                        // * Hit the status API
                        $response = $this->statusAPI($item->gateway_id);

                        if (isset($response["data"]["status"]) && $response["data"]["status"] == false) {
                            $input["status"] = "0";
                            $input["reason"] = "User cancelled the transaction process in between.";
                        } else if (isset($response["data"]["status"]) && $response["data"]["status"] == true && (isset($response["tranStatus"]) && $response["tranStatus"] == "successful")) {
                            $input["status"] = "1";
                            $input["reason"] = "Transaction processed successfully!.";
                        } else if (isset($response["data"]["status"]) && $response["data"]["status"] == true && (isset($response["tranStatus"]) && $response["tranStatus"] == "failed")) {
                            $input["status"] = "0";
                            $input["reason"] = $response["data"]["message"] ?? "Transaction could not processed failed.";
                        } else {
                            $input["status"] = "0";
                            $input["reason"] = $response["data"]["message"] ?? "Transaction could not processed failed.";
                        }

                        // * Only update those transaction who have final status
                        if ($input["status"] != "2") {
                            array_push($txnIds, $item->id);
                            updateGatewayData($input, $response, $item->transaction_id);
                            $this->transaction->storeData($input);
                        }
                    }


                }
                // * update session table record
                if (count($txnIds) > 0) {
                    DB::table("transaction_session")->whereIn("id", $txnIds)->update(["is_completed" => "1"]);
                }
            } else {
                Log::info(["Mekapay-cron-msg" => "No record found!"]);
            }
        } catch (\Exception $err) {
            Log::error(["mekapay-cron-job-error" => $err->getMessage()]);
        }
    }

    public function statusAPI($gatewayId)
    {
        $payload = ["reference" => $gatewayId];
        $response = Http::withHeaders(["authorization" => "Bearer " . $this->mid->secret_key, "content-type" => "application/json"])->post($this->url, $payload)->json();
        return $response;
    }
}
