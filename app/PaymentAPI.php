<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class PaymentAPI extends Model
{
    protected $table = 'payment_apis';

    protected $guarded = [];

    // ================================================
    /* method : getAPIData
     * @param  :
     * @Description : get all transaction session data
     */// ==============================================
    public function getAPIData($input)
    {
        $data = static::select('id', 'order_id', 'session_id', 'created_at', "user_id");

        if (isset($input['customer_order_id']) && !empty($input['customer_order_id'])) {
            $data = $data->where('response', 'like', '%' . $input['customer_order_id'] . '%');
        }

        if (isset($input['email']) && !empty($input['email'])) {
            $data = $data->where('email', $input['email']);
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('user_id', $input["user_id"]);
        }

        if (isset($input['order_id']) && !empty($input['order_id'])) {
            $data = $data->where('order_id', $input['order_id']);
        }

        if (isset($input['session_id']) && !empty($input['session_id'])) {
            $data = $data->where('session_id', $input['session_id']);
        }

        if (isset($input['id']) && $input['id'] != null) {
            $data = $data->where('id', $input['id']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(created_at)'), '<=', $end_date);
        }
        $data = $data->orderBy('id', 'desc')->simplePaginate($input['noList']);

        return $data;
    }
}