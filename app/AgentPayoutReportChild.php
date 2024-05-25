<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentPayoutReportChild extends Model
{
    use SoftDeletes;
    protected $table = "agent_payout_report_children";
    protected $guarded = [];
}
