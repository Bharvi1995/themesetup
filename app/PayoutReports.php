<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutReports extends Model
{
    use SoftDeletes;
    protected $table = 'payout_reports';
    protected $guarded = array();

    public function findData($id)
    {
        return static::find($id);
    }

    public function storeData($input)
    {
        return static::create($input);
    }

    public function getAllReportData($noList, $input)
    {
        $data = static::select("payout_reports.*");
        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('payout_reports.user_id', $input['user_id']);
        }
        if (isset($input['show_client_side']) && $input['show_client_side'] != '') {
            $data = $data->where('payout_reports.show_client_side', '1');
        }
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('payout_reports.status', $input['status']);
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = Carbon::parse($input['end_date']);
            $newToDate = $end_date->format('Y-m-d');
            $from_date = Carbon::parse($input['start_date']);
            $newfromDate = $from_date->format('Y-m-d');
            $data = $data->where(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), '>=', $newfromDate)->where(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), '<=', $newToDate);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $from_date = Carbon::parse($input['start_date']);
            $newfromDate = $from_date->format('Y-m-d');
            $data = $data->where(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), '>=', $newfromDate);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = Carbon::parse($input['end_date']);
            $newToDate = $end_date->format('Y-m-d');
            $data = $data->where(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), '<=', $newToDate);
        }
        //echo $data->toSql();exit();
        $data = $data->orderBy("payout_reports.id", "DESC")->paginate($noList);
        return $data;
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }

    public function destroyData($id)
    {
        return static::find($id)->delete();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->select("id", "settlement_fee");
    }
}