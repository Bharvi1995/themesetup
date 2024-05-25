<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    protected $table = 'ticket_reply';
    protected $guarded = [];

    protected $fillable = [
        'ticket_id',
        'user_id',
        'user_type',
        'body',
        'files'
    ];

    public function storeData($input)
    {
    	return static::create($input);
    }

    public function user()
    {
    	if($this->user_type == 'admin'){
    		return $this->hasOne('App\Admin','id','user_id');
    	}elseif ($this->user_type == 'user'){
    		return $this->hasOne('App\User','id','user_id');
    	}
    }
}
