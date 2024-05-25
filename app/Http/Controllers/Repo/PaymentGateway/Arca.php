<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use DB;
use App\Traits\StoreTransaction;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Log;
use Http;

include_once __DIR__ . '/arca_pg/src/EncryptHelper.php';
use arca_pg\checkout\enc\EncryptHelper;

class Arca extends Controller
{

    use StoreTransaction;

    const CREATE_ORDER_URL = "https://checkout-api.arcapg.com/checkout/order/create";
    const PAY_URL = "https://checkout-api.arcapg.com/checkout/order/pay";

    const STATUS_API = "https://checkout-api.arcapg.com/checkout/order/verify";

    public function checkout($input, $midDetails)
    {

        $input['converted_amount'] = number_format((float) $input['converted_amount'], 2, '.', '');
        $customerPayload = [
            "customer" => [
                "first_name" => $input["first_name"],
                "last_name" => $input["last_name"],
                "mobile" => $this->getFormatNumber($input),
                "country" => $input["country"],
                "email" => $input["email"]
            ],
            "order" => [
                "amount" => $input['converted_amount'],
                "reference" => $input["session_id"],
                "description" => "testpay transaction " . $input["order_id"],
                "currency" => $input["converted_currency"]
            ],
            "payment" => [
                "redirect_url" => route('arca.redirect', [$input["session_id"]])
            ]
        ];


        // * Encrypt data
        $initService = new EncryptHelper($midDetails->encryption_key);
        $encryptedData = $initService->EncryptData(json_encode($customerPayload, JSON_UNESCAPED_SLASHES));

        $createOrderRes = Http::withHeaders(["accept" => "application/json", "api-key" => $midDetails->public_key, "content-type" => "application/json"])->post(self::CREATE_ORDER_URL, ["data" => $encryptedData])->json();


        // * Store mid payload
        $this->storeMidPayload($input["session_id"], json_encode($customerPayload));
        $input["gateway_id"] = "1";
        $this->updateGatewayResponseData($input, $createOrderRes);

        if ($createOrderRes != null && !empty($createOrderRes)) {
            if (isset($createOrderRes["status"]) && $createOrderRes["status"] == "failed") {
                return [
                    "status" => "0",
                    "reason" => $createOrderRes["message"] ?? "Transaction could not processed!"
                ];
            }

            if (isset($createOrderRes["data"]["order"]["reference"])) {
                // * Update gateway id
                $input["gateway_id"] = $createOrderRes["data"]["order"]["processor_reference"] ?? "1";

                // * Create card payload 
                $ccYear = explode("20", $input["ccExpiryYear"]);
                $cardPayload = [
                    "reference" => $createOrderRes["data"]["order"]["reference"],
                    "payment_option" => "C",
                    "country" => $input["country"],
                    "card" => [
                        "cvv" => $input["cvvNumber"],
                        "card_number" => $input["card_no"],
                        "expiry_month" => $input["ccExpiryMonth"],
                        "expiry_year" => $ccYear[1]
                    ]
                ];

                // * Encrypt card payload 
                $encryptedCardData = $initService->EncryptData(json_encode($cardPayload, JSON_UNESCAPED_SLASHES));
                // Log::info(["encrypted-cc-array" => $encryptedCardData, "cc-data" => json_encode($cardPayload)]);

                $ccResponse = Http::withHeaders(["api-key" => $midDetails->public_key])->post(self::PAY_URL, ["data" => $encryptedCardData])->json();

                // * Store complete payload
                $midPayload = [];
                $midPayload[] = $customerPayload;
                $cardPayload["card"]["card_number"] = cardMasking($cardPayload["card"]["card_number"]);
                $cardPayload["card"]["cvv"] = "XXX";
                $midPayload[] = $cardPayload;

                $this->storeMidPayload($input["session_id"], json_encode($midPayload));

                if (isset($ccResponse["data"]["order_payment"]["order_payment_reference"])) {
                    $input["gateway_id"] = $ccResponse["data"]["order_payment"]["order_payment_reference"];
                }

                $this->updateGatewayResponseData($input, $ccResponse);
                // Log::info(["cc-response" => $ccResponse]);

                if ($ccResponse == null || empty($ccResponse)) {
                    return [
                        "status" => "0",
                        "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
                    ];
                } else if (isset($ccResponse["data"]["payment_detail"]["redirect_url"])) {
                    return [
                        'status' => '7',
                        'reason' => '3DS link generated successful, please redirect.',
                        'redirect_3ds_url' => $ccResponse["data"]["payment_detail"]["redirect_url"]
                    ];
                } else if (isset($ccResponse["status"]) && $ccResponse["status"] == "failed") {
                    return [
                        "status" => "0",
                        "reason" => $ccResponse["message"] ?? "Transaction could not processed."
                    ];
                } else {
                    return [
                        "status" => "0",
                        "reason" => "Transaction could not processed."
                    ];
                }
            }

        }
        return [
            "status" => "0",
            "reason" => "We are facing temporary issue from the bank side. Please contact us for more detail."
        ];

    }


    // * Redirect callback
    public function redirect(Request $request, $id)
    {
        // $response = $request->all();
        // Log::info(["arca-redirect" => $response]);

        $transaction = DB::table("transaction_session")->select("id", "request_data", "payment_gateway_id")->where("transaction_id", $id)->first();
        if ($transaction == null) {
            abort(404);
        }

        $input = json_decode($transaction->request_data, true);
        // hit the status API
        $mid = checkAssignMID($transaction->payment_gateway_id);
        $statusRes = $this->statusAPI($mid, $id);


        if ($statusRes != null && $statusRes["payment_response_code"] == "00" && $statusRes["status"] == "Successful") {
            $input["status"] = "1";
            $input["reason"] = "Transaction processed successfully!";
        } else if ($statusRes != null && $statusRes["payment_response_code"] == "05" && $statusRes["status"] == "Failed") {
            $input["status"] = "0";
            $input["reason"] = $statusRes["payment_response_message"] ?? "Transaction could not processed!";
        } else if ($statusRes != null && $statusRes["payment_response_code"] == "96") {
            $input["status"] = "0";
            $input["reason"] = $statusRes["payment_response_message"] ?? "Transaction could not processed!";
        } else if ($statusRes != null && $statusRes["payment_response_code"] == "02") {
            $input["status"] = "2";
            $input["reason"] = "Transaction is under process.please check after sometime.";
        } else {
            $input["status"] = "0";
            $input["reason"] = $statusRes["payment_response_message"] ?? "Transaction could not processed!";
        }

        $this->storeTransaction($input);
        $this->updateGatewayResponseData($input, $statusRes);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }

    // * Status API
    public function statusAPI($mid, $id)
    {

        $response = Http::withHeaders(["api-key" => $mid->secret_key])->post(self::STATUS_API, ["reference" => $id])->json();
        return isset($response["data"]) ? $response["data"] : null;
    }

    public function getFormatNumber(array $input)
    {
        if (strpos($input["phone_no"], '+') === 0) {
            return $input["phone_no"];
        } else {
            return getPhoneCode($input["country"]) . $input["phone_no"];
        }
    }

}