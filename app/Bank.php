<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Bank extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'banks';
    protected $guarded = array();

    protected $fillable = [
        'referral_code',
        'bank_name',
        'email',
        'password',
        'country',
        'category_id',
        'extra_email',
        'processing_country',
        'is_active',
        'token',
        'is_otp_required',
        'otp'
    ];

    protected $hidden = [
        'password',
        'token',
        'remember_token'
    ];

    public function getData($input, $noList)
    {
        $data = static::select("banks.*")
            ->orderBy("id", "DESC");

        if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('bank_name',  'like', '%' . $input['name'] . '%');
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

    public function application()
    {
        return $this->hasOne('App\BankApplication');
    }
}
