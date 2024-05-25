<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $table = 'log_activity';

    protected $fillable = [
        'subject',
        'query_request',
        'query_type',
        'transaction_id',
        'url',
        'method',
        'ip',
        'agent',
        'user_id'
    ];

    public function getData()
    {
        $data = static::select("log_activity.*",'merchantapplications.company_name as company_name')
            ->join('merchantapplications','merchantapplications.user_id','log_activity.user_id')
            ->orderBy("log_activity.id","DESC")
            ->get();

        return $data;
    }

    public function getDataById($id)
    {
        $data = static::select("log_activity.*",'merchantapplications.company_name as company_name')
            ->join('merchantapplications','merchantapplications.user_id','log_activity.user_id')
            ->orderBy("log_activity.id","DESC")
            ->where('log_activity.id',$id)
            ->first();

        return $data;
    }

    public function getDataByUser($id)
    {
        $data = static::select("log_activity.*",'merchantapplications.company_name as company_name')
            ->join('merchantapplications','merchantapplications.user_id','log_activity.user_id')
            ->orderBy("log_activity.id","DESC")
            ->where('log_activity.user_id',$id)
            ->get();

        return $data;
    }

    public function distroyData($date)
    {
        return static::where('created_at','<',$date)->delete();
    }
}
