<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class PayoutReportsChild extends Model
{
    protected $table = 'payout_report_child';
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
