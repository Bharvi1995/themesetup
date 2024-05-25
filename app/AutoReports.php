<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoReports extends Model
{
    use SoftDeletes;
    protected $table = 'auto_reports';
    protected $guarded = array();

    public function findData($id)
    {
        return static::find($id);
    }

    public function storeData($input)
    {
        return static::create($input);
    }
}
