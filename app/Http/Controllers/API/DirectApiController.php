<?php

namespace App\Http\Controllers\API;

use App\Mail\AdminRefundNotification;
use App\WebsiteUrl;
use App\Transaction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Repo\TransactionRepo;
use App\Mail\RefundTransactionMail;
use App\Transformers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\User;

class DirectApiController extends Controller
{
    protected $transaction, $transaction_repo;

    // ================================================
    /* method : __construct
     * @param  :
     * @Description : Create a new controller instance.
     */// ==============================================
    public function __construct()
    {
        $this->transaction = new Transaction;
        $this->transaction_repo = new TransactionRepo;
    }

    // ================================================
    /* method : store
     * @param  :
     * @Description : create transaction API $request
     */// ==============================================
    public function store(Request $request)
    {
        // only accept parameters that are available
        $request_only = config('required_field.fields');

        $input = $request->only($request_only);
        $api_key = $request->bearerToken();

        // if api_key is not included in request
        if (empty($api_key)) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, please pass API Key in Header';
            return ApiResponse::unauthorised($input);
        }

        // validate api_key
        $user = DB::table('users')
            ->where('api_key', $api_key)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        // if api_key is not valid or user deleted
        if (empty($user)) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, Invalid API Key or merchant deleted';
            return ApiResponse::unauthorised($input);
        }

        // user IP and domain and request from API
        $input['payment_type'] = 'card';
        $input['request_from_ip'] = $request->ip();
        $input['request_origin'] = $_SERVER['HTTP_HOST'];
        $input['is_request_from_vt'] = 'API';
        $input['user_id'] = $user->id;
        $input['payment_gateway_id'] = $user->mid;

        // if merchant on test mode
        if (in_array($user->mid, [1, 2])) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, Only test mode available.';
            return ApiResponse::unauthorised($input);
        }

        // gateway object
        $check_assign_mid = checkAssignMID($user->mid);

        $validator = Validator::make($input, [
            'first_name' => 'required|min:3|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|min:2|max:100|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required|min:2|max:250',
            'country' => 'required|max:2|min:2|regex:(\b[A-Z]+\b)',
            'state' => 'required|min:2|max:250',
            'city' => 'required|min:2|max:250',
            'zip' => 'required|min:2|max:250',
            'ip_address' => 'required|ip',
            'email' => 'required|email',
            'phone_no' => 'required|min:5|max:20',
            'amount' => 'required|regex:/^\d+(\.\d{1,9})?$/',
            'currency' => 'required|max:3|min:3|regex:(\b[A-Z]+\b)',
            'card_no' => 'required|min:12|max:24',
            'ccExpiryMonth' => 'required|numeric|min:1|max:12',
            'ccExpiryYear' => 'required|numeric|min:2023|max:2045',
            'cvvNumber' => 'required|numeric|min:0|max:9999',
            'response_url' => 'required|url',
            'webhook_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            $input['status'] = '6';
            $input['reason'] = $errors[0] ?? 'Unauthorised request, please check your request payload.';
            return ApiResponse::unauthorised($input);
        }

        // check ip_restriction
        if ($user->is_ip_remove == '0') {
            $getIPData = WebsiteUrl::where('user_id', $user->id)
                ->where('ip_address', $this->getClientIP())
                ->first();

            // if IP is not added on the IP whitelist
            if (empty($getIPData)) {
                $input['status'] = '6';
                $input['reason'] = 'Unauthorised request, please whitelist this IP address(' . $this->getClientIP() . ') in your dashboard.';
                return ApiResponse::unauthorised($input);
            }

            // if IP is not approved
            if ($getIPData->is_active == '0') {
                $input['status'] = '6';
                $input['reason'] = 'Unauthorised request, IP address(' . $this->getClientIP() . ') approval pending.';
                return ApiResponse::unauthorised($input);
            }
            $input["website_url_id"] = $getIPData->id;
        }

        // send request to transaction repo class
        $return_input = $this->transaction_repo->store($input, $user, $check_assign_mid);

        // if return_input is null
        if (empty($return_input)) {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, request validation failed.';
            return ApiResponse::unauthorised($input);
        }

        $input = array_merge($input, $return_input);

        // transaction requires 3ds redirect
        if ($return_input['status'] == '7') {
            return ApiResponse::redirect($input);
            // transaction success
        } elseif ($return_input['status'] == '1') {
            return ApiResponse::success($input);
            // transaction pending
        } elseif ($return_input['status'] == '2') {
            return ApiResponse::pending($input);
            // transaction fail
        } elseif ($return_input['status'] == '0') {
            return ApiResponse::fail($input);
            // transaction blocked
        } elseif ($return_input['status'] == '5') {
            return ApiResponse::unauthorised($return_input);
            // no response
        } else {
            $input['status'] = '6';
            $input['reason'] = 'Unauthorised request, request validation failed.';
            return ApiResponse::unauthorised($input);
        }
    }

    private function validateBasicAuth(Request $request)
    {
        $authorization = $request->header('Authorization');
        if (!$authorization || !str_starts_with($authorization, 'Basic ')) {
            $input['status'] = '6';
            $input['reason'] = 'The request lacks valid authentication credentials. Please check the provided header parameters.';
            abort(ApiResponse::unauthorised($input));
        }
        $credentials = base64_decode(substr($authorization, 6));
        $arrCredentials = explode(':', $credentials);
        if(count($arrCredentials) != 2){
            $input['status'] = '6';
            $input['reason'] = 'The request lacks valid authentication credentials. Please check the provided header parameters.';
            abort(ApiResponse::unauthorised($input));
        }
        list($username, $password) = explode(':', $credentials, 2);
        $user = User::where('email', $username)->where("api_key",$password)->first();
        if (!$user) {
            $input['status'] = '6';
            $input['reason'] = 'The request lacks valid authentication credentials. Please check the provided header parameters.';
            abort(ApiResponse::unauthorised($input));
        }
        $request->merge(['user' => $user]);
    }

    // ================================================
    /* method  : getTransaction
     * @ param  :
     * @ Description : get transaction details api
     */// ==============================================
    public function getTransaction(Request $request)
    {
        $this->validateBasicAuth($request);
        // only accept parameters that are available
        $input = $request->only(['order_id','user']);
        // dd($input);
        // gateway object
        $check_assign_mid = checkAssignMID($input["user"]->mid);

        if ($check_assign_mid == false) {
            $input['status'] = 6;
            $input['reason'] = 'Your account has been deactivated. Please contact the administrator for further assistance.';
            return ApiResponse::notFound($input);
        }

        $validator = Validator::make($input, [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $input['status'] = '6';
            $input['reason'] = $errors[0] ?? 'Kindly review your request payload to ensure all required fields are provided.';
            return ApiResponse::unauthorised($input);
        }

        $transaction = $transaction = Transaction::where('user_id', $input["user"]->id);
        if ((isset($input['order_id']) && $input['order_id'] != null)) {
            $transaction = $transaction->where('order_id', $input['order_id']);
        }
        $transaction = $transaction->orderBy('id', 'desc')
            ->first();

        if (empty($transaction)) {
            $input['status'] = 6;
            $input['reason'] = 'Transaction not located.';
            return ApiResponse::notFound($input);
        } else {
            return ApiResponse::statusTransactions($transaction);
        }
    }

    // ================================================
    /* method : getTransactionDetails
     * @param  :
     * @Description : get-transaction-details for transaction pop-up
     */// ==============================================
    public function getTransactionDetails(Request $request)
    {
        $data = Transaction::select(
            'transactions.order_id',
            'merchantapplications.company_name',
            'transactions.first_name',
            'transactions.last_name',
            'transactions.address',
            'transactions.customer_order_id',
            'transactions.country',
            'transactions.state',
            'transactions.city',
            'transactions.zip',
            'transactions.ip_address',
            'transactions.birth_date',
            'transactions.email',
            'transactions.phone_no',
            'transactions.reason',
            'transactions.status',
            'transactions.created_at as transaction_date',
            'transactions.card_type',
            'transactions.amount',
            'transactions.currency',
            'transactions.card_no',
            'transactions.ccExpiryMonth',
            'transactions.ccExpiryYear',
            'transactions.cvvNumber',
            'transactions.shipping_first_name',
            'transactions.shipping_last_name',
            'transactions.shipping_address',
            'transactions.shipping_country',
            'transactions.shipping_state',
            'transactions.shipping_city',
            'transactions.shipping_zip',
            'transactions.shipping_email',
            'transactions.shipping_phone_no',
            'transactions.is_flagged',
            'transactions.flagged_date',
            'transactions.chargebacks',
            'transactions.changebanks_date',
            'transactions.changebanks_reason',
            'transactions.refund',
            'transactions.refund_date',
            'transactions.refund_reason',
            'transactions.is_retrieval',
            'transactions.retrieval_date'
        )
            ->join('merchantapplications', 'merchantapplications.user_id', 'transactions.user_id')
            ->where('order_id', $request['order_id'])
            ->first();

        if (isset($data['card_no'])) {
            $data['card_no'] = 'XXXXXXXXXXXX' . substr($data['card_no'], -4);
        }
        if (isset($data['ccExpiryYear'])) {
            $data['ccExpiryYear'] = 'XXXX';
        }
        if (isset($data['ccExpiryMonth'])) {
            $data['ccExpiryMonth'] = 'XX';
        }
        if (isset($data['cvvNumber'])) {
            $data['cvvNumber'] = 'XXX';
        }

        if (!isset($data)) {
            $data = [];
        }
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // ================================================
    /* method : getClientIP
     * @param  : 
     * @description : get client ip address perfectly
     */// ===============================================
    public function getClientIP()
    {
        $ip_address = '';

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip_address = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip_address = 'UNKNOWN';
        }

        return $ip_address;
    }

    public function refund(Request $request)
    {
        if ($request->api_key == null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'api_key field is required',
            ]);
        } elseif ($request->order_id == null && $request->customer_order_id == null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'order_id or customer_order_id field is required',
            ]);
        }
        $input = $request->only(['api_key', 'order_id', 'customer_order_id']);
        $user = DB::table('users')
            ->where('api_key', $input['api_key'])
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->first();
        if ($user == null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'api_key is not valid.',
            ]);
        }
        $transaction = Transaction::select(
            'transactions.order_id as order_id',
            'transactions.id',
            'transactions.order_id',
            'transactions.card_type',
            'users.id as user_id',
            'users.email as user_email',
            'transactions.first_name',
            'transactions.last_name',
            'transactions.card_type',
            'transactions.card_no',
            'transactions.amount',
            'transactions.created_at',
            'transactions.currency',
            'transactions.email',
            'transactions.order_id',
            'transactions.refund',
            'transactions.chargebacks',
            'transactions.is_flagged',
            'transactions.is_retrieval',
            'transactions.status'
        )
            ->join('users', 'users.id', 'transactions.user_id');
        if (!empty($request->order_id)) {
            $transaction = $transaction->where('transactions.order_id', $request->order_id);
        } elseif (!empty($request->customer_order_id)) {
            $transaction = $transaction->where('transactions.customer_order_id', $request->customer_order_id);
        }
        $transaction = $transaction->first();
        if ($transaction != null) {
            if ($transaction->refund != '0') {
                $response_array = [];
                $response_array['status'] = 'fail';
                $response_array['message'] = 'This transaction is already marked for refund.';
                return response()->json($response_array);
            }
            if ($transaction->chargebacks != '0') {
                $response_array = [];
                $response_array['status'] = 'fail';
                $response_array['message'] = 'This transaction can not be marked for refund as it is marked for chargeback.';
                return response()->json($response_array);
            }
            if ($transaction->is_flagged != '0') {
                $response_array = [];
                $response_array['status'] = 'fail';
                $response_array['message'] = 'This transaction can not be marked for refund as it is marked for suspicious.';
                return response()->json($response_array);
            }
            if ($transaction->is_flagged != '0') {
                $response_array = [];
                $response_array['status'] = 'fail';
                $response_array['message'] = 'This transaction can not be marked for refund as it is marked for retrieval.';
                return response()->json($response_array);
            }


            if ($transaction->refund == '0' && $transaction->chargebacks == '0' && $transaction->is_flagged == '0' && $transaction->is_retrieval == '0' && $transaction->status == '1') {
                $updateData1['refund'] = '1';
                $updateData1['refund_date'] = date('Y-m-d H:i:s');
                $updateData1['transaction_date'] = date('Y-m-d H:i:s');
                $this->transaction->updateData($transaction->id, $updateData1);
                $input['title'] = 'Transaction Refund';
                $input['body'] = 'Dear merchant , your transaction <strong>Order No : ' . $transaction->order_id . '</strong> has been refunded. You can check the details of the transaction in your Dashboard.';
                $input['first_name'] = $transaction->first_name;
                $input['last_name'] = $transaction->last_name;
                $input['card_type'] = $transaction->card_type;
                $input['card_no'] = substr($transaction->card_no, 0, 6) . 'XXXXXX' . substr($transaction->card_no, -4);
                $input['user_id'] = $transaction->user_id;
                $input['amount'] = $transaction->amount;
                $input['created_at'] = $transaction->created_at;
                $input['currency'] = $transaction->currency;
                $input['order_id'] = $transaction->order_id;
                $input['refund_date'] = date('Y-m-d H:i:s');
                $input['email'] = $transaction->email;
                try {
                    Mail::to($transaction->user_email)->queue(new RefundTransactionMail($input));


                } catch (\Exception $e) {
                    $response_array = [];
                    $response_array['status'] = 'fail';
                    $response_array['message'] = 'Something went wrong';
                }
                $response_array = [];
                $response_array['status'] = 'success';
                $response_array['message'] = 'Refund Updated Successfully!';
            } else {
                $response_array = [];
                $response_array['status'] = 'fail';
                $response_array['message'] = 'Something went wrong';
            }
        } else {
            $response_array = [];
            $response_array['status'] = 'fail';
            $response_array['message'] = 'Transaction not found.';
        }

        return response()->json($response_array);
    }
}