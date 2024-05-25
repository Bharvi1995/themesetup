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


class YeloPayTxnJob implements ShouldQueue
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
            $data = DB::table("transaction_session")->select("id", "request_data", "gateway_id", "transaction_id")->where("payment_gateway_id", $this->mid->id)->where("is_completed", "0")->orderByRaw("RAND()")->limit(30)->get();

            if (count($data) > 0) {
                foreach ($data as $item) {
                    $input = json_decode($item->request_data, true);
                    if ($item->gateway_id == "1" || $item->gateway_id == null || !isset($input["card_no"])) {
                        $input["status"] = "0";
                        $input["reason"] = "User cancelled the transaction process in between.";
                        array_push($txnIds, $item->id);
                        $this->transaction->storeData($input);
                    } else {

                        // * Hit the status API
                        $statusRes = $this->statusAPI($item->gateway_id, $input);
                        if (empty($statusRes) || $statusRes == null || (isset($statusRes["status"]) && $statusRes["status"] == "ERROR")) {
                            $input["status"] = "0";
                            $input["reason"] = "Transaction could not processed!";
                        } else if (isset($statusRes) && $statusRes["status"] == "DECLINED") {
                            $input["status"] = "0";
                            $input["reason"] = isset($statusRes["decline_reason"]) && $statusRes["decline_reason"] != "" ? $statusRes["decline_reason"] : "Transaction could not processed!";
                        } else if (isset($statusRes) && $statusRes["status"] == "3DS" || $statusRes["status"] == "PENDING") {
                            $input["status"] = "2";
                            $input["reason"] = "Your transaction in pending state.please wait for sometime";
                        } else if (isset($statusRes) && $statusRes["status"] == "SETTLED") {
                            $input["status"] = "1";
                            $input["reason"] = "Your transaction processed successfully!";
                        } else {
                            $input["status"] = "0";
                            $input["reason"] = "Transaction could not processed!";
                        }

                        // * Only update those transaction who have final status
                        if ($input["status"] != "2") {
                            array_push($txnIds, $item->id);
                            updateGatewayData($input, $statusRes, $item->transaction_id);
                            $this->transaction->storeData($input);
                        }
                    }
                }
                // * update session table record
                if (count($txnIds) > 0) {
                    DB::table("transaction_session")->whereIn("id", $txnIds)->update(["is_completed" => "1"]);
                }
            } else {
                Log::info(["yelopay-cron-msg" => "No record found!"]);
            }

        } catch (\Exception $err) {
            Log::error(["yelopay-cron-job-error" => $err->getMessage()]);

        }
    }

    public function statusAPI(string $gatewayId, array $input)
    {
        $hash = md5(strtoupper(strrev($input["email"]) . $this->mid->password . $gatewayId . strrev(substr($input["card_no"], 0, 6) . substr($input["card_no"], -4))));
        $payload = [
            "action" => "GET_TRANS_STATUS",
            "client_key" => $this->mid->merchant_key,
            "trans_id" => $gatewayId,
            "hash" => $hash
        ];

        $statusRes = Http::asForm()->post($this->url, $payload)->json();
        return $statusRes;
    }
}
