<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Jobs\MekaPayPendingTxnJob;
use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Http;

class MekaPay extends Controller
{
    use StoreTransaction;

    const BASE_URL = "https://mekapaygroup.tech/api/charge";
    const STATUS_API = "https://mekapaygroup.tech/api/charge/validation";

    public function checkout($input, $midDetails)
    {
        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $cardExp = explode("20", $input["ccExpiryYear"]);
        $payload = [
            "amount" => $input["converted_amount"],
            "currency" => $input["converted_currency"],
            "cardNumber" => $input["card_no"],
            "expMonth" => $input["ccExpiryMonth"],
            "expYear" => $cardExp[1],
            "cardCVV" => $input["cvvNumber"],
            "country" => $input["country"],
            "state" => $input["state"],
            "city" => $input["city"],
            "address" => $input["address"],
            "zip_code" => $input["zip"],
            "firstname" => $input["first_name"],
            "lastname" => $input["last_name"],
            "phone" => $input["phone_no"],
            "email" => $input["email"],
            "ip" => $input["ip_address"],
            "reference" => $input["session_id"],
            "callback_url" => route('mekapay.callback', [$input["session_id"]])
        ];

        $response = Http::withHeaders(["authorization" => "Bearer " . $midDetails->secret_key, "content-type" => "application/json"])->post(self::BASE_URL, $payload)->json();


        // * Store mid payload
        $this->storeMidPayload($input["session_id"], json_encode($payload));

        $input["gateway_id"] = "1";
        $this->updateGatewayResponseData($input, $response);


        if ($response == null || empty($response)) {
            return [
                'status' => '0',
                'reason' => "We are facing temporary issue from the bank side. Please contact us for more detail.",
                'order_id' => $input['order_id'],
            ];
        }

        if (isset($response["status"]) && $response["status"] == "success" && isset($response["redirectLink"]) && $response["redirectLink"] != null) {
            return [
                'status' => '7',
                'reason' => '3DS link generated successful, please redirect.',
                'redirect_3ds_url' => $response["redirectLink"]
            ];
        } else {
            return [
                "status" => "0",
                "reason" => $response["message"] ?? "Your transaction could not processed."
            ];
        }

    }

    public function callback(Request $request, $id)
    {

        $response = $request->all();
        $transaction = DB::table("transaction_session")->select("id", "request_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404);
        }
        $callbackRes = json_decode($response["response"], true);
        $input = json_decode($transaction->request_data, true);

        // * Check if gateway id exist then hit the status API 
        if (isset($callbackRes["id"])) {
            $input["gateway_id"] = $callbackRes["id"];
            $mid = checkAssignMID($transaction->payment_gateway_id);
            $statusRes = $this->statusAPI($mid, $input["gateway_id"]);
            $input["tranStatus"] = $statusRes["data"]["tranStatus"] ?? "";
        } else {
            $input["gateway_id"] = "1";
            $input["tranStatus"] = $callbackRes["status"];
        }

        if (isset($input["tranStatus"]) && $input["tranStatus"] == "successful") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if (isset($input["tranStatus"]) && $input["tranStatus"] == "failed") {
            $input["status"] = "0";
            $input["reason"] = isset($callbackRes["vbvrespmessage"]) ? $callbackRes["vbvrespmessage"] : "Transaction could not processed!";
        } else {
            $input["status"] = "0";
            $input["reason"] = isset($callbackRes["vbvrespmessage"]) ? $callbackRes["vbvrespmessage"] : "Transaction could not processed!";
        }

        $this->updateGatewayResponseData($input, $callbackRes);
        $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);

    }

    // * Status API

    public function statusAPI($mid, $id)
    {

        $response = Http::withHeaders(["authorization" => "Bearer " . $mid->secret_key, "content-type" => "application/json"])->post(self::STATUS_API, ["reference" => $id])->json();

        return $response;
    }

    // * Update Pending txn Job
    public function pendingTxnJob(Request $request)
    {
        try {
            if ($request->get("password") != "f8d3h5883e7e4318608a90ZUGW445545e3e56489") {
                abort(404);
            }
            $mid = checkAssignMID("55");
            MekaPayPendingTxnJob::dispatch($mid, self::STATUS_API);
            return response()->json(["status" => 200, "message" => "job added successfully!"]);
        } catch (\Exception $err) {
            return response()->json(["status" => 500, "message" => $err->getMessage()]);

        }
    }
}