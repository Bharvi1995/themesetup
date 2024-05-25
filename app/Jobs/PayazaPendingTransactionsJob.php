<?php

namespace App\Jobs;

use App\Traits\StoreTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use DB;
use Http;
use Log;
use App\Transaction;


class PayazaPendingTransactionsJob implements ShouldQueue
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
            $data = DB::table("transaction_session")->select("id", "request_data", "transaction_id")->where("payment_gateway_id", $this->mid->id)->where("is_completed", "0")->orderByRaw("RAND()")->limit(20)->get();
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $input = json_decode($item->request_data, true);
                    $response = $this->statusAPI($item->transaction_id);
                    if (!empty($response) && $response["response_code"] == 404) {
                        $input["status"] = "0";
                        $input["reason"] = "User cancelled the transaction process in between.";
                        array_push($txnIds, $item->id);
                        $this->transaction->storeData($input);
                    } else if (!empty($response) && $response["response_code"] == 200 && !empty($response["response_content"])) {
                        $resData = $response["response_content"];
                        if (isset($resData["transaction_status"]) && $resData["transaction_status"] == "Failed") {
                            $input["status"] = "0";
                            $input["reason"] = "Transaction could not processed.";
                        } else if (isset($resData["transaction_status"]) && $resData["transaction_status"] == "Completed") {
                            $input["status"] = "1";
                            $input["reason"] = "Transaction processed successfully!.";
                        } else if (isset($resData["transaction_status"]) && $resData["transaction_status"] == "Pending") {
                            $input["status"] = "2";
                            $input["reason"] = "Your transaction is under process . Please wait for sometime!";
                        }

                        $input["gateway_id"] = "1";
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
            }
        } catch (\Exception $err) {
            Log::error(["payaza-cron-job-error" => $err->getMessage()]);
        }


    }

    // * Hit the status api method
    public function statusAPI($txnId)
    {
        $payload = [
            "service_type" => "Account",
            "service_payload" => [
                "request_application" => "Payaza",
                "application_module" => "USER_MODULE",
                "application_version" => "1.0.0",
                "request_class" => "CheckTransactionStatusRequest",
                "transaction_reference" => $txnId
            ]
        ];

        $Key = "Payaza " . base64_encode($this->mid->api_key);
        $response = Http::withHeaders(["Authorization" => $Key])->post($this->url, $payload)->json();
        return $response;
    }
}