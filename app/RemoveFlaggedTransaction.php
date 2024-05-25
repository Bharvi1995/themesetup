<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RemoveFlaggedTransaction extends Model
{
    use SoftDeletes;
    
    protected $table = 'remove_flagged_transactions';
    protected $guarded = array();

    public function getData()
    {
        $data = static::select("remove_flagged_transactions.*")->orderBy("remove_flagged_transactions.id","DESC")->get();

        return $data; 
    }
    public function getAllDataFilter($input)
    {
        $data = static::select('remove_flagged_transactions.*', 'merchantapplications.company_name','transactions.order_id')
        ->join('merchantapplications', 'merchantapplications.user_id', 'remove_flagged_transactions.user_id')
        ->join('transactions','transactions.id','remove_flagged_transactions.transaction_id');
            if(isset($input['start_date']) && $input['start_date'] !== ''){
                $data = $data->whereDate('remove_flagged_transactions.un_flagged_date','>=',$input['start_date']);
            }
            if(isset($input['end_date']) && $input['end_date'] !== ''){
                $data = $data->whereDate('remove_flagged_transactions.un_flagged_date','<=',$input['end_date']);
            }
            if(isset($input['user_id']) && $input['user_id'] !== ''){
                $data = $data->where('remove_flagged_transactions.user_id',$input['user_id']);
            }
            if(isset($input['currency']) && $input['currency'] !== ''){
                $data = $data->where('remove_flagged_transactions.currency',$input['currency']);
            }
            $data = $data->orderBy("remove_flagged_transactions.id","DESC")->paginate($input['paginate']);        
        return $data;
    }
    public function getAllDataFilterExcel($input)
    {
        $data = static::select('remove_flagged_transactions.*', 'merchantapplications.company_name','transactions.order_id')
        ->join('merchantapplications', 'merchantapplications.user_id', 'remove_flagged_transactions.user_id')
        ->join('transactions','transactions.id','remove_flagged_transactions.transaction_id');
            if(isset($input['start_date']) && $input['start_date'] !== ''){
                $data = $data->whereDate('remove_flagged_transactions.un_flagged_date','>=',$input['start_date']);
            }
            if(isset($input['end_date']) && $input['end_date'] !== ''){
                $data = $data->whereDate('remove_flagged_transactions.un_flagged_date','<=',$input['end_date']);
            }
            if(isset($input['user_id']) && $input['user_id'] !== ''){
                $data = $data->where('remove_flagged_transactions.user_id',$input['user_id']);
            }
            if(isset($input['currency']) && $input['currency'] !== ''){
                $data = $data->where('remove_flagged_transactions.currency',$input['currency']);
            }
            $data = $data->orderBy("remove_flagged_transactions.id","DESC")->select('remove_flagged_transactions.id','transactions.order_id','merchantapplications.company_name','remove_flagged_transactions.un_flagged_date','remove_flagged_transactions.amount','remove_flagged_transactions.currency','remove_flagged_transactions.type')->get();        

        return $data;
    }
    public function findData($id)
    {
        return static::find($id);
    }

    public function storeData($input)
    {
        return static::create($input);
    }

    public function destroyData($id)
    {
        return static::find($id)->delete();
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }
}
