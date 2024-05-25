<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailySettlementReport extends Model
{
    protected $table = 'daily_settlement_report';
    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at', 'start_date', 'end_date', 'paid_date'];
    
    public function createOrUpdateReport( $data ){

        $findReport = static::where('user_id', $data['user_id'])->where('start_date', $data['start_date'])->where('end_date', $data['end_date'])->first();
        if( !isset($findReport->id) ){
            static::create($data);
        }else{
            unset($data['user_id']);
            unset($data['start_date']);
            unset($data['end_date']);
            static::where('id', $findReport->id)->update($data);
        }

    }

    public function fetchUserReport( $user_id ){
        return static::where('user_id', $user_id)->orderBy('id','DESC')->first();
    }


}
