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


class MilkyPayTxnJob implements ShouldQueue
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
            $data = DB::table("transaction_session")->select("id", "request_data", "order_id", "transaction_id")->where("payment_gateway_id", $this->mid->id)->where("is_completed", "0")->orderByRaw("RAND()")->limit(30)->get();
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $input = json_decode($item->request_data, true);
                    array_push($txnIds, $item->id);
                    $input["status"] = "0";
                    $input["reason"] = "User did not complete the transaction process.";
                    $this->transaction->storeData($input);
                }
                // * update session table record
                if (count($txnIds) > 0) {
                    DB::table("transaction_session")->whereIn("id", $txnIds)->update(["is_completed" => "1"]);
                }
            } else {
                Log::info(["Milkypay-cron-msg" => "No record found!"]);
            }
        } catch (\Exception $err) {
            Log::error(["Milkypay-cron-job-error" => $err->getMessage()]);
        }
    }

    public function statusApi($gatewayId)
    {
        $url = $this->url . $gatewayId;
        $response = Http::withBasicAuth($this->mid->account_id, $this->mid->password)->get($url)->json();
        return $response;
    }
}
