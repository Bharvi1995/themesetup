<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Transaction;




class CentPayTransactionRestoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $mid, $url;


    protected $transaction;


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
            $txnId = [];
            $data = DB::table("transaction_session")->select("id", "gateway_id", "request_data", "transaction_id")->where("payment_gateway_id", "10")->orWhere("payment_gateway_id", "14")->where("is_completed", "0")->get();
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $input = json_decode($item->request_data, true);
                    if ($item->gateway_id == null || $item->gateway_id == "1") {
                        $input["status"] = "0";
                        $input["reason"] = "User cancelled the transaction process.";
                        $input['gateway_id'] = "1";
                        array_push($txnId, $item->id);
                        $this->transaction->storeData($input);
                    } else {
                        $response = Http::withHeaders(["api-key" => $this->mid->api_key, "api-secret" => $this->mid->secret_key])->post($this->url, ["transaction_id" => $item->gateway_id])->json();
                        // Log::info(["status-api-job-res" => json_encode($response)]);
                        if (!empty($response)) {
                            $data = $response["data"];
                            if ($data["status"] == "Failed") {
                                $input["status"] = "0";
                                $input["reason"] = $data["message"] != null && $data["message"] != "" ? $data["message"] : "Transaction got declined.";
                            } else if ($data["status"] == "Success") {
                                $input["status"] = "1";
                                $input["reason"] = $data["message"] != null && $data["message"] != "" ? $data["message"] : "Transaction processed successfully!";
                            } else if ($data["status"] == "Droped") {
                                $input['status'] = '0';
                                $input['reason'] = $data["message"] != null && $data["message"] != "" ? $data['message'] : "Your transaction got declined.";
                            } else if ($data["status"] == "In Progress") {
                                $input['status'] = '2';
                                $input['reason'] = $data["message"] != null && $data["message"] != "" ? $data["message"] : "Your transaction is under process . Please wait for sometime!";
                            } else {
                                $input["status"] = "0";
                                $input["reason"] = $data["message"] != null && $data["message"] != "" ? $data["message"] : "Your transaction could not processed.";

                            }
                            updateGatewayData($input, $response, $item->transaction_id);
                            DB::table("transaction_session")->where("id", $item->id)->update(["is_completed" => $input["status"] == "2" ? "0" : "1"]);
                            $this->transaction->storeData($input);
                        }
                    }
                }

                // * Update session transaction value for cancelled transactions
                if (count($txnId) > 0) {
                    DB::table('transaction_session')->whereIn("id", $txnId)->update(["is_completed" => "1"]);

                }
            }
        } catch (\Exception $err) {
            Log::info(["centpay-transaction-restore-error" => $err->getMessage()]);
        }
    }


}