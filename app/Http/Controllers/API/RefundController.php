<?php

namespace App\Http\Controllers\API;

use DB;
use Validator;
use App\Transaction;
use App\Merchantapplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundController extends Controller
{

    protected $Transaction;
    // ================================================
    /* method : __construct
	* @param  :
	* @description : create new instance of the class
	*/ // ==============================================
    public function __construct()
    {
        $this->Transaction = new Transaction;
    }
    // ================================================
    /* method : refund
	* @param  :
	* @description : refund request API
	*/ // ==============================================
    public function refund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refund_reason' => 'required',
            'api_key' => 'required',
            'order_id' => 'required',
        ]);

        $input = $request->only(['api_key', 'order_id', 'refund_reason']);

        // if api_key is not included in request
        if (empty($input['api_key']) || $input['api_key'] == null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'api_key parameter is required.'
            ]);
        }

        $getUser = DB::table('users')
            ->where('api_key', $input['api_key'])
            ->where('is_active', '1')
            ->whereNull('deleted_at')
            ->first();

        if ($getUser == null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'api_key is not valid.',
            ]);
        }

        $input['refund'] = '1';
        $input['refund_date'] = date('Y-m-d H:i:s');
        unset($input['api_key']);
        if ($validator->passes()) {

            $getTransaction = DB::table('transactions')
                ->where('user_id', $getUser->id)
                ->where('order_id', $input['order_id'])
                ->first();

            if ($getTransaction == null) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'transaction order id is not valid',
                ]);
            }

            if ($getTransaction->refund == '1') {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'this transaction already refunded',
                ]);
            }

            if ($getTransaction->status != '1') {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'only success transaction will be refunded',
                ]);
            }

            if ($this->Transaction->updateData($getTransaction->id, $input)) {

                $company_name = Merchantapplication::where('user_id', $getUser->id)
                    ->value('company_name');

                // send admin push notification
                // this array key should be included
                $primary_array = [
                    'user_id' => config('notification.default_admin_id'),
                    'sendor_id' => $getUser->id,
                    'type' => 'admin', //or admin
                    'title' => 'Refund request',
                    'body' => $company_name . ' requested for refund.',
                ];

                // this array adds more details send over firebase
                $secondary_array = [
                    'click_action' => config('notification.refund_request.url'),
                ];
                // trigger push notication
                sendFirebaseNotification($primary_array, $secondary_array);

                // save to firebase database
                saveToFirebaseDatabase($primary_array, $secondary_array);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Refund request submitted successfully.'
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'your transaction not refunded. please contact in the support.'
                ]);
            }
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'Some parameters are missing or invalid request data.',
            'errors' => $validator->errors()
        ]);
    }
}
