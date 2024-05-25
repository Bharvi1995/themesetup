<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use DB;
use Carbon\Carbon;
use App\Transaction;
use Log;

class DeclinedPayazaPendingTxnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $transaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
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
            $midId = "42";
            $transactions = DB::table("transaction_session")
                ->select("id", "request_data")
                ->where("created_at", '<', Carbon::now()->subDays(1))
                ->where("payment_gateway_id", $midId)
                ->where("is_completed", "0")
                // ->orderByRaw("RAND()")
                ->limit(10)
                ->get();
            $txnIds = [];
            if (count($transactions) > 0) {
                foreach ($transactions as $item) {
                    $input = json_decode($item->request_data, true);
                    $input["status"] = "0";
                    $input["reason"] = "User did not completed the transaction process. He/She cancelled it in between.";
                    array_push($txnIds, $item->id);
                    $this->transaction->storeData($input);
                }

                // * update session table record
                if (count($txnIds) > 0) {
                    DB::table("transaction_session")->whereIn("id", $txnIds)->update(["is_completed" => "1"]);
                }
            }
        } catch (\Exception $err) {
            Log::error(["payaza-declined-txn-cron-err" => $err->getMessage()]);
        }

    }
}