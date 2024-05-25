<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MidOnCountryBase extends Model
{

    protected $fillable = [
        'user_id','country_code','mid'
    ];

    protected $table = 'mid_on_country_base';
}
