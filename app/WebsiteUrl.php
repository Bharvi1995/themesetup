<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Auth;
use DB;
use Illuminate\Support\Str;

class WebsiteUrl extends Model
{
    // use Cachable;
    protected $table = 'website_url';
    protected $guarded = array();

    protected $fillable = [
        'user_id',
        'website_name',
        'ip_address',
        'is_active'
    ];

    public function getData($input, $noList)
    {
        $data = static::select("website_url.*", 'users.email', 'applications.business_name')
            ->join('users', 'users.id', 'website_url.user_id')
            ->join('applications', 'applications.user_id', 'website_url.user_id')
            ->orderBy("website_url.id", "DESC");

        if (isset($input['email']) && $input['email'] != '') {
            $email = Str::of($input['email'])->trim();
            $data = $data->where('users.email', 'like', '%' . $email . '%');
        }
        if (isset($input['business_name']) && $input['business_name'] != '') {
            $business_name = Str::of($input['business_name'])->trim();
            $data = $data->where('users.id', '=', $business_name);
        }
        if (isset($input['website_name']) && $input['website_name'] != '') {
            $website_name = Str::of($input['website_name'])->trim();
            $data = $data->where('website_url.website_name', 'like', '%' . $website_name . '%');
        }
        if (isset($input['ip_address']) && $input['ip_address'] != '') {
            $ip = Str::of($input['ip_address'])->trim();
            $data = $data->where('website_url.ip_address', 'like', '%' . $ip . '%');
        }

        if ($noList) {
            $data = $data->paginate($noList);
        }

        return $data;
    }
}