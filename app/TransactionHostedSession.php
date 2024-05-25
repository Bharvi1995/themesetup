<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionHostedSession extends Model
{
    // use Cachable;
    protected $table = 'transaction_hosted_session';
    protected $guarded = array();

    protected $fillable = [
        'user_id',
        'payment_gateway_id',
        'transaction_id',
        'order_id',
        'gateway_id',
        'request_data',
        'response_data',
        'amount',
        'email',
        'is_completed'
    ];

    // ================================================
    /* method : storeData
    * @param  :
    * @description : store method
    */// ==============================================
    public function storeData($input)
    {
        $data['user_id'] = $input['user_id'] ?? null;
        $data['payment_gateway_id'] = $input['payment_gateway_id'] ?? null;
        $data['transaction_id'] = $input['session_id'] ?? null;
        $data['order_id'] = $input['order_id'] ?? null;
        $data['request_data'] = json_encode($input) ?? null;
        $data['amount'] = $input['amount'] ?? null;
        $data['email'] = $input['email'] ?? null;
        $data['is_completed'] = '0' ?? null;

        return static::create($data);
    }

    // ================================================
    /* method : getTransactionSessionData
    * @param  :
    * @Description : get all transaction session data
    */// ==============================================
    public function getTransactionSessionData($input)
    {
        $data = static::select('transaction_session.*', 'middetails.bank_name','applications.business_name')
                ->join('applications', 'applications.user_id', 'transaction_session.user_id')
                ->join('middetails', 'middetails.id', 'transaction_session.payment_gateway_id');

            if(isset($input['first_name']) && $input['first_name'] != '') {
                $data = $data->where('transaction_session.request_data',  'like', '%' . $input['first_name'] . '%');
            }

            if(isset($input['last_name']) && $input['last_name'] != '') {
                $data = $data->where('transaction_session.request_data',  'like', '%' . $input['last_name'] . '%');
            }

            if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                $data = $data->where('transaction_session.payment_gateway_id', $input['payment_gateway_id']);
            }

            if(isset($input['email']) && $input['email'] != '') {
                $data = $data->where('transaction_session.email',  'like', '%' . $input['email'] . '%');
            }

            if(isset($input['company_name']) && $input['company_name'] != ''){
                $data = $data->where('applications.business_name',  'like', '%' . $input['company_name'] . '%');
            }

            if(isset($input['order_id']) && $input['order_id'] != null) {
                $data = $data->where('transaction_session.order_id', $input['order_id']);
            }

            if(isset($input['is_completed']) && $input['is_completed'] != null) {
                $data = $data->where('transaction_session.is_completed', $input['is_completed']);
            }

            $data = $data->orderBy('id', 'desc')
                ->paginate($input['noList']);

        return $data;
    }
}
