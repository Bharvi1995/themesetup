<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $guarded = array();

    protected $fillable = [

        'name',
        'full_name',
        'code',
        'iso3',
        'number'
    ];
}
