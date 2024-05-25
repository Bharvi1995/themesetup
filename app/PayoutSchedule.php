<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'payout_schedule';

    protected $fillable = [
        'id','from_date','to_date','issue_date','sequence_number',
    ];

    public function getData()
    {
        $data = static::select("payout_schedule.*")
            ->get();

        return $data;
    }

    public function autoGeneratePayoutSchedule()
    {
        $start_date = date('d-M-Y', strtotime('first fri of last month'));
        $end_date = date('d-M-Y', strtotime($start_date. ' + 6 days'));
        $issue_date = date('d-M-Y', strtotime($end_date. ' + 14 days'));
        
        $data = [];
        $i = 0; 
        while(strtotime($start_date) <= date(strtotime('+ 1 year'))){
            $data[$i] = [
                'from_date' => $start_date,
                'to_date' => $end_date,
                'issue_date' => $issue_date
            ];
            $start_date = date('d-M-Y', strtotime($start_date.' + 7 days'));
            $end_date = date('d-M-Y', strtotime($end_date.' + 7 days'));
            $issue_date = date('d-M-Y', strtotime($issue_date.' + 7 days'));
            $i++;
        }

        return $data;
    }

    public function findData($id)
    {
        return static::find($id);
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }

    public function getPayoutScheduleList()
    {
        $data = static::select("payout_schedule.*")
            ->orderBy('sequence_number','ASC')
            ->get();

        return $data;
    }

    public function storeData($input)
    {
        return static::create($input);
    }

    public function softDelete($id)
    {
        return static::where('id',$id)->delete();
    }

}
