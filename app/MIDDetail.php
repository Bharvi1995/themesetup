<?php
namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class MIDDetail extends Authenticatable
{
    // use Cachable;
    use SoftDeletes;

    protected $table = 'middetails';
    protected $guarded = array();

    protected $fillable = [
        'mid_no',
        'mid_type',
        'bank_id',
        'bank_name',
        'gateway_table',
        'main_gateway_mid_id',
        'assign_gateway_mid',
        'is_gateway_mid',
        'converted_currency',
        'blocked_country',
        'min_transaction_limit',
        'per_transaction_limit',
        'per_day_limit',
        'per_day_card',
        'per_day_email',
        'per_week_card',
        'per_week_email',
        'per_month_card',
        'per_month_email',
        'farma_mid',
        'is_provide_refund',
        'is_active',
        'is_card_required',
        'descriptor',
        'apm_mdr',
        'apm_type',
        "accepted_industries"
    ];

    public function getData()
    {
        return static::select("middetails.*")->get();
    }
    public function getMid()
    {
        $data = static::select("middetails.*")->get();
        return $data;
    }
    public function getMidForRestore()
    {
        return static::pluck('bank_name', 'id')->all();
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

    public function userBan($id)
    {
        return static::where('id', $id)->update(array('ban' => 1));
    }

    public function userRevoke($id)
    {
        return static::where('id', $id)->update(array('ban' => 0));
    }

    public function listMIDGateway()
    {
        $data = \DB::table('midgateways')->pluck('name', 'id');

        return $data;
    }

    public function setPerDayEmailAttribute($value)
    {
        $this->attributes['per_day_email'] = !empty($value) ? $value : 3;
    }

    public function setPerDayCardAttribute($value)
    {
        $this->attributes['per_day_card'] = !empty($value) ? $value : 3;
    }

    public function setPerWeekEmailAttribute($value)
    {
        $this->attributes['per_week_email'] = !empty($value) ? $value : 3;
    }

    public function setPerWeekCardAttribute($value)
    {
        $this->attributes['per_week_card'] = !empty($value) ? $value : 3;
    }

    public function setPerMonthEmailAttribute($value)
    {
        $this->attributes['per_month_email'] = !empty($value) ? $value : 3;
    }

    public function setPerMonthCardAttribute($value)
    {
        $this->attributes['per_month_card'] = !empty($value) ? $value : 3;
    }
}