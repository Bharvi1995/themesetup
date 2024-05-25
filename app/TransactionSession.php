<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class TransactionSession extends Model
{
    // use Cachable;
    protected $table = 'transaction_session';
    protected $guarded = array();

    protected $fillable = [
        'user_id',
        'payment_gateway_id',
        'transaction_id',
        'order_id',
        'gateway_id',
        'request_data',
        'input_details',
        'response_data',
        'amount',
        'email',
        'is_completed',
        'is_checkout',
        'is_card',
        "extra_data"
    ];

    // ================================================
    /* method : storeData
    * @param  :
    * @description : store method
    */// ==============================================
    public function storeData($input)
    {
        // mask input
        if (isset($input['card_no']) && $input['card_no'] != null) {
            $input['card_no'] = substr($input['card_no'], 0, 6) . 'XXXXXX' . substr($input['card_no'], -4);
            $input['cvvNumber'] = 'XXX';
        }

        $data['user_id'] = $input['user_id'] ?? null;
        $data['payment_gateway_id'] = $input['payment_gateway_id'] ?? null;
        $data['transaction_id'] = $input['session_id'] ?? null;
        $data['order_id'] = $input['order_id'] ?? null;
        $data['request_data'] = json_encode($input, JSON_UNESCAPED_UNICODE) ?? null;
        $data['input_details'] = json_encode($input, JSON_UNESCAPED_UNICODE) ?? null;
        $data['amount'] = $input['amount'] ?? null;
        $data['email'] = $input['email'] ?? null;
        $data['is_completed'] = '0' ?? null;

        try {
            return static::create($data);
        } catch (\Exception $e) {
            return static::create($data);
        }
    }

    // ================================================
    /* method : getTransactionSessionData
    * @param  :
    * @Description : get all transaction session data
    */// ==============================================
    public function getTransactionSessionData($input)
    {
        // $data = static::select('transaction_session.id', 'middetails.bank_name', 'applications.business_name')
        //     ->join('applications', 'applications.user_id', 'transaction_session.user_id')
        //     ->join('middetails', 'middetails.id', 'transaction_session.payment_gateway_id');

        $data = static::select('id', "request_data", "order_id", "is_completed", "payment_gateway_id", "created_at", "email", "user_id", "transaction_id");

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transaction_session.request_data', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['customer_order_id']) && $input['customer_order_id'] != '') {
            $data = $data->where('transaction_session.request_data', 'like', '%' . $input['customer_order_id'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transaction_session.request_data', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transaction_session.payment_gateway_id', $input['payment_gateway_id']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transaction_session.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transaction_session.user_id', $input['company_name']);
        }

        if (isset($input['order_id']) && $input['order_id'] != null) {
            $data = $data->where('transaction_session.order_id', $input['order_id']);
        }

        if (isset($input['is_completed']) && $input['is_completed'] != null) {
            $data = $data->where('transaction_session.is_completed', $input['is_completed']);
        }

        if (isset($input['id']) && $input['id'] != null) {
            $data = $data->where('transaction_session.id', $input['id']);
        }

        if (isset($input['gateway_id']) && $input['gateway_id'] != null) {
            $data = $data->where('transaction_session.gateway_id', $input['gateway_id']);
        }

        if (isset($input['session_id']) && $input['session_id'] != null) {
            $data = $data->where('transaction_session.transaction_id', $input['session_id']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transaction_session.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transaction_session.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transaction_session.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transaction_session.created_at)'), '<=', $end_date);
        }
        $data = $data->orderBy('id', 'desc')
            ->paginate($input['noList']);

        return $data;
    }
}