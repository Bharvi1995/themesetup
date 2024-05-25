<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use SoftDeletes;

    protected $table   = 'invoices';
    protected $guarded = array();

    protected $fillable = [
        'admin_id',
        'company_id',
        'invoice_no',
        'amount_deducted_value',
        'usdt_erc',
        'usdt_trc',
        'btc_value',
        'total_amount',
        'request_data',
        'invoice_url',
        'is_paid',
        'transaction_hash',
        'agent_name',
        'deleted_at',
    ];
    public function getData($input = [], $noList = 0)
    {
        $data = static::select("invoices.*", "applications.business_name")
            ->leftjoin("applications", "applications.id", "invoices.company_id")
            //->where("admin_id",auth()->guard('admin')->user()->id)
            ->orderBy("id", "DESC");

        if (isset($input['company_id']) && $input['company_id'] != '') {
            $data = $data->where('company_id', $input['company_id']);
        }
        if (isset($input['invoice_no']) && $input['invoice_no'] != '') {
            $data = $data->where('invoice_no', 'like', '%' . $input['invoice_no'] . '%');
        }
        if (isset($input['is_paid']) && $input['is_paid'] != '') {
            $data = $data->where('is_paid', $input['is_paid']);
        }
        if (isset($input['transaction_hash']) && $input['transaction_hash'] != '') {
            $data = $data->where('transaction_hash', 'like', '%' . $input['transaction_hash'] . '%');
        }
        if (isset($input['agent_name']) && $input['agent_name'] != '') {
            $data = $data->where('agent_name', 'like', '%' . $input['agent_name'] . '%');
        }
        if ($noList > 0) {
            return $data->paginate($noList);
        }
        return $data->get();
    }

    public function destroyData($id)
    {
        return static::find($id)->delete();
    }
}
