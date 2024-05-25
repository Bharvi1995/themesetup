<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $table = 'main_gateway';
    protected $guarded = ['id'];

    protected $fillable = [
        'title',
        'credential_titles',
        'required_fields',
        'active'
    ];

    public function subgateway()
    {
    	return \DB::table('gateway_'.\Str::slug($this->title, "_"))->get();
    }
}
