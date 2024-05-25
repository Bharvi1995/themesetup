<?php

namespace App\Http\Controllers\Repo\PaymentGateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\StoreTransaction;
use App\User;
use App\TransactionSession;

class Rogerpay extends Controller
{
    use StoreTransaction;
    
    const BASE_URL = 'https://uiservices.rogerpay.io/hosted/default.aspx?';
    
    public function checkout($input, $check_assign_mid)
    {
        try {
            $data = [];
            // parameters list
            $data['merchantID'] = $check_assign_mid->merchant_id;
            $data['url_redirect'] =  route('rogerpay-callback',$input["session_id"]);
            $data['notification_url'] = route('rogerpay-notification',$input["session_id"]);
            $data['trans_comment'] = '';
            $data['trans_refNum']  = $input['order_id'];
            $data['trans_installments'] = '';
            $data['trans_amount'] = $input['converted_amount'];
            $data['trans_currency'] = $input['currency'];
            $data['disp_paymentType'] = 'CC';
            $data['disp_payFor'] = '';
            $data['disp_recurring'] = '0';
            $data['disp_lng'] = 'en-us';
            $data['disp_mobile'] = $input['phone_no'];
            $data['PersonalHashKey'] = $check_assign_mid->hash_key;
            $retSignature = implode('', array_values($data));
            $data['signature'] = base64_encode(hash('sha256', $retSignature, true)); // mandatory
            unset($data['PersonalHashKey']);
            $redirect_3ds_url = self::BASE_URL . http_build_query($data);
            \Log::info([
                'rogerpay-input' => $data
            ]);
            \Log::info([
                'rogerpay-response' => $redirect_3ds_url
            ]);
            $input['gateway_id'] = $input["session_id"];
            $this->updateGatewayResponseData($input, $data);
            return [
                'status' => '7',
                'reason' => '3DS link generated successfully, please redirect to \'redirect_3ds_url\'.',
                'redirect_3ds_url' => $redirect_3ds_url,
            ];
        } catch(\Exception $e) {
            \Log::info([
                'rogerpay-exception' => $e->getMessage()
            ]);
        }
        return [
            'status' => '0',
            'reason' => $e->getMessage(), // 'Your transaction could not processed.',
            'order_id' => $input['order_id'],
        ];
    }
    
    public function callback($id,Request $request) {
        
        $body = $request->all();
        \Log::info([
            'rogerpay-callback' => $body
        ]);
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        $input = json_decode($input_json['request_data'], true);
        if (! empty($body['trans_refNum'])) {
            if (! empty($body['replyCode']) && $body['replyCode'] == '000') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            }else if (! empty($body['replyCode']) && $body['replyCode'] == '001') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            }else if (! empty($body['replyCode']) && $body['replyCode'] == '552') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            }else if (! empty($body['replyCode']) && $body['replyCode'] == '553') {
                $input['status'] = '1';
                $input['reason'] = 'Your transaction was proccessed successfully.';
            }else{
                $input['status'] = '0';
                $input['reason'] = 'Your transaction was Declined.';
            }
        }
        $transaction_response = $this->storeTransaction($input);
        $store_transaction_link = $this->getRedirectLink($input);
        return redirect($store_transaction_link);
    }
    
    public function notification($id,Request $request) {
        
        $body = $request->all();
        \Log::info([
            'rogerpay-notification' => $body
        ]);
        $input_json = TransactionSession::where('transaction_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if ($input_json == null) {
            return abort(404);
        }
        if (! empty($body['trans_order'])) {
            
            $input = json_decode($input_json['request_data'], true);
            if (! empty($body['trans_refNum'])) {
                if (! empty($body['replyCode']) && $body['replyCode'] == '000') {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction was proccessed successfully.';
                }else if (! empty($body['replyCode']) && $body['replyCode'] == '001') {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction was proccessed successfully.';
                }else if (! empty($body['replyCode']) && $body['replyCode'] == '552') {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction was proccessed successfully.';
                }else if (! empty($body['replyCode']) && $body['replyCode'] == '553') {
                    $input['status'] = '1';
                    $input['reason'] = 'Your transaction was proccessed successfully.';
                }else{
                    $input['status'] = '0';
                    $input['reason'] = 'Your transaction was Declined.';
                }
            }
            $transaction_response = $this->storeTransaction($input);
            $store_transaction_link = $this->getRedirectLink($input);
            return redirect($store_transaction_link);
        }
    }
}