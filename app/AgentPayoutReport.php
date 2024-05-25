<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentPayoutReport extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $dates = ['start_date', 'end_date'];

    public function childData()
    {
        return $this->hasMany('App\AgentPayoutReportChild', 'report_id', 'id');
    }
}
