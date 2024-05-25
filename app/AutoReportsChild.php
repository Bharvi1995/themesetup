<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoReportsChild extends Model
{
    protected $table = 'auto_reports_child';
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
