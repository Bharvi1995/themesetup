<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Log;
use Exception;
use DB;
use Http;
use App\Transformers\ApiResponse;

class UpdateCentpayPendingTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $mid = checkAssignMID('10');
            $transactions = DB::table('transactions as t')->select("t.id", "t.session_id", "t.gateway_id", 'ts.request_data', 'ts.transaction_id')
                ->join('transaction_session as ts', 'ts.transaction_id', '=', 't.session_id')
                ->where('t.status', '2')->where('t.payment_gateway_id', '10')->orWhere('t.payment_gateway_id', '14')->orderBy('t.id', 'desc')->get();

            if (count($transactions) > 0) {
                foreach ($transactions as $item) {
                    // if ($item->gateway_id == null || $item->gateway_id == "1") {
                    //     DB::table('transactions')->where('id', $item->id)->update(["status" => "0", "reason" => "User cancelled the transaction process.", "updated_at" => date('Y-m-d H:i:s')]);
                    // }
                    $input = json_decode($item->request_data, true);
                    $response = Http::withHeaders(["api-key" => $mid->api_key, "api-secret" => $mid->secret_key])->post("https://centpays.com/v2/get_transaction", ["transaction_id" => $item->gateway_id])->json();
                    // Log::info(["centpay-status-api" => $response]);
                    if (!empty($response) && $response["code"] == "202") {
                        $data = $response["data"];
                        $updateInput = [];
                        if ($data["status"] == "Failed") {
                            $updateInput["status"] = "0";
                            $updateInput["reason"] = $data["message"] != null && $data["message"] != "" ? $data["message"] : "Transaction got declined.";
                        } else if ($data["status"] == "Success") {
                            $updateInput["status"] = "1";
                            $updateInput["reason"] = $data["message"] != null && $data["message"] != "" ? $data["message"] : "Transaction processed successfully!";
                        } else if ($data["status"] == "Droped") {
                            $updateInput['status'] = '0';
                            $updateInput['reason'] = $data["message"] != null && $data["message"] != "" ? $data['message'] : "Your transaction got declined.";
                        } else if ($data["status"] == "In Progress") {
                            $updateInput['status'] = '2';
                            $updateInput['reason'] = $data["message"] != null && $data["message"] != "" ? $data["message"] : "Your transaction is under process . Please wait for sometime!";
                        } else {
                            $updateInput["status"] = "0";
                            $updateInput["reason"] = $data["message"] != null && $data["message"] != "" ? $data["message"] : "User didn't completed the transaction process.";
                        }
                        $updateInput["updated_at"] = date('Y-m-d H:i:s');
                        $input["status"] = $updateInput["status"];
                        $input["reason"] = $updateInput["reason"];
                        DB::table('transactions')->where('id', $item->id)->update($updateInput);

                        // * send the webhook
                        if (isset($input['webhook_url']) && !empty($input['webhook_url']) && !in_array($input['status'], ['2', '7'])) {
                            $request_data = ApiResponse::webhook($input);
                            try {
                                $http_response = postCurlRequest($input['webhook_url'], $request_data);
                            } catch (Exception $e) {
                                Log::info(['webhook_' . $input['order_id'] => $e->getMessage()]);
                            }
                        }

                    }
                }
            }
        } catch (Exception $err) {
            return response()->json(["status" => "500", "msg" => "Something went werong", "err" => $err->__toString()]);

        }
    }
}