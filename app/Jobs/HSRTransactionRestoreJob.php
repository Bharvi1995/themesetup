<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Transaction;
use DB;
use Http;
use Log;

class HSRTransactionRestoreJob implements ShouldQueue
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
        // DB::beginTransaction();
        $ids = [];
        try {
            $data = DB::table("transaction_session")->select("id", "gateway_id", "request_data", "transaction_id")->where("payment_gateway_id", $this->mid->id)->where("is_completed", "0")->get();
            if (count($data) > 0) {
                foreach ($data as $item) {
                    $input = json_decode($item->request_data, true);
                    if ($item->gateway_id == null || $item->gateway_id == "1") {
                        $input["status"] = "0";
                        $input["reason"] = "User cancelled the transaction process.";
                        $input['gateway_id'] = "1";
                        $this->transaction->storeData($input);
                        array_push($ids, $item->id);
                    } else {
                        // * Genrate the Signature
                        $sigString = $item->gateway_id;
                        $sig256 = hash('sha256', $sigString . $this->mid->secret);
                        $signature = hash_hmac('sha512', $sig256, $this->mid->key);
                        $response = Http::withHeaders(["TPS-KEY" => $this->mid->key, "TPS-SIGNATURE" => $signature, "Content-Type" => 'application/json'])->post($this->url, ["txid" => $item->gateway_id])->json();
                        if (!empty($response)) {
                            if (isset($response['status']) && $response['status'] == 'success') {
                                if (isset($response['transaction']['tran_status']) && $response['transaction']['tran_status'] == "APPROVED") {
                                    $input['status'] = '1';
                                    $input['reason'] = "Your transaction has been processed successfully.";
                                } elseif (isset($response['transaction']['tran_status']) && $response['transaction']['tran_status'] == "DECLINED") {
                                    $input['status'] = '0';
                                    $input['reason'] = (isset($response['transaction']['tran_message']) && !empty($response['transaction']['tran_message'])) ? $response['transaction']['tran_message'] : "Your transaction got declined.";
                                } elseif (isset($response['transaction']['tran_status']) && $response['transaction']['tran_status'] == "PENDING") {
                                    $input['status'] = '2';
                                    $input['reason'] = $response['transaction']['tran_message'] ?? "Your transaction is pending.";
                                } else {
                                    $input['status'] = '0';
                                    $input['reason'] = isset($response['transaction']['tran_message']) ? $response['transaction']['tran_message'] : "Your transaction got declined.";
                                }
                            } else {
                                $input['status'] = '0';
                                $input['reason'] = (isset($response['message']) && !empty($response['message'])) ? $response['message'] : "Your transaction got declined.";
                            }

                            updateGatewayData($input, $response, $item->transaction_id);
                            DB::table("transaction_session")->where("id", $item->id)->update(["is_completed" => $input["status"] == "2" ? "0" : "1"]);
                            $this->transaction->storeData($input);
                        }
                    }
                }

                // * Update not completed txn at once 
                if (count($ids) > 0) {
                    DB::table("transaction_session")->whereIn("id", $ids)->update(["is_completed" => "1"]);
                }
            }
            // DB::commit();
        } catch (\Exception $err) {
            // DB::rollBack();
            Log::info(["hsr-transaction-restore-error" => $err->getMessage()]);
        }
    }
}