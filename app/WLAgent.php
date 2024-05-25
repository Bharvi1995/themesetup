<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WLAgent extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'wl_agents';
    protected $guarded = array();

    protected $fillable = [
        'name',
        'email',
        'commission',
        'otp',
        'is_otp_required',
        'is_active',
        'agreement_status',
        'token',
        'password',
        'discount_rate',
        'discount_rate_master_card',
        'setup_fee',
        'setup_fee_master_card',
        'rolling_reserve_paercentage',
        'transaction_fee',
        'refund_fee',
        'chargeback_fee',
        'flagged_fee',
        'retrieval_fee'
    ];

    protected $hidden = [
        'password',
        'token',
        'remember_token'
    ];

    public function getData($input, $noList)
    {
        $data = static::select("wl_agents.*")->orderBy("id", "DESC");

        if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('name',  'like', '%' . $input['name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('email',  'like', '%' . $input['email'] . '%');
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function findData($id)
    {
        return static::find($id);
    }

    public function storeData($input)
    {
        return static::create($input);
    }

    public function destroyData($id)
    {
        return static::find($id)->delete();
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }
}
