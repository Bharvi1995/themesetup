<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaythroneUser extends Model
{
    protected $table = 'paythrone_users';
    protected $guarded = array();

    public function findOrCreate($input)
    {   
        return static::firstOrCreate([
            'email' =>  $input['email']
        ]);
    }

}
