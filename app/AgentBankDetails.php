<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentBankDetails extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function walletDetail()
    {
        return $this->belongsTo('App\Wallet','wallet');
    }
}
