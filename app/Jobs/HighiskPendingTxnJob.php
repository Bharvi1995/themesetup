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


class HighiskPendingTxnJob implements ShouldQueue
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
            $data = DB::table("transaction_session")->select("id", "request_data", "transaction_id")->where("payment_gateway_id", $this->mid->id)->where("is_completed", "0")->orderByRaw("RAND()")->limit(15)->get();
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $input = json_decode($item->request_data, true);
                    $response = $this->getStatus($item->transaction_id);
                    $input["gateway_id"] = isset($input["gateway_id"]) ? $input["gateway_id"] : "1";
                    // * transaction not reached to bank side
                    if ($response == null) {
                        array_push($txnIds, $item->id);
                        $input["status"] = "0";
                        $input["reason"] = "User cancelled the transaction process in between.";
                        $this->transaction->storeData($input);

                    } else if (isset($response["replyCode"]) && $response["replyCode"] == "000") {
                        $input["status"] = "1";
                        $input["reason"] = "Transaction processed successfully!.";
                    } else if (isset($response["replyCode"]) && $response["replyCode"] == "553") {
                        // * In case of pending txn skip the current iteration
                        continue;
                    } else {
                        $input["status"] = "0";
                        $input["reason"] = $response["replyDesc"] ?? "Your transaction could not processed!.";
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
                Log::info(["highisk-cron-msg" => "No record found!"]);
            }
        } catch (\Exception $err) {
            Log::error(["highisk-cron-job-error" => $err->getMessage()]);
        }
    }

    // * The status API 
    public function getStatus($sessionId)
    {
        $strConcat = $this->mid->merchant_no . $sessionId . $this->mid->hash;
        $shaStr = hash("sha256", $strConcat);
        $signature = base64_encode($shaStr);
        $url = $this->url . "?Order=" . $sessionId . "&CompanyNum=" . $this->mid->merchant_no . "&signature=" . $signature;
        $response = Http::get($url)->json();
        return isset($response["data"]) && count($response["data"]) > 0 ? $response["data"][0] : null;
    }
}