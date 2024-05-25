<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayoutReportsChildRP extends Model
{
    protected $table = 'payout_reports_child_rp';
    protected $guarded = array();

    public function storeData($input)
    {
        return static::create($input);
    }

    public function findDataByReportID($id)
    {
        return static::where('payoutreport_id',$id)->get();
    }
}
