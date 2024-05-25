<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankApplication extends Model
{
    use SoftDeletes;

    protected $table = 'bank_applications';

    protected $guarded = array();

    public function storeData($data)
    {
        return static::create($data);
    }

    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }

    public function category()
    {
        return $this->belongsTo('App\Categories');
    }

    public function findData($id)
    {
        $data = static::select('bank_applications.*')
            ->join('banks', 'banks.id', 'bank_applications.bank_id')
            ->where('bank_applications.id', $id)
            ->first();
        return $data;
    }

    public function FindDataFromUser($bank_id)
    {
        $data = static::select('bank_applications.*')
            ->join('banks', 'banks.id', 'bank_applications.bank_id')
            ->where('bank_applications.bank_id', $bank_id)
            ->whereNull('bank_applications.deleted_at')
            ->first();
        return $data;
    }

    public function updateApplication($id, $input)
    {
        return static::where('id', $id)->update($input);
    }

    public function destroyWithUserId($bank_id)
    {
        return static::where('bank_id', $bank_id)->delete();
    }

    public function softDelete($id)
    {
        return static::where('id', $id)->delete();
    }
    
    public function restore($id)
    {
        return static::onlyTrashed()->where('id', $id)->restore();
    }

    public function getCompanyName()
    {
        return static::select('bank_applications.company_name', 'bank_applications.bank_id')
            ->join('banks', 'banks.id', '=', 'bank_applications.bank_id')
            ->whereNull('banks.deleted_at')
            ->whereNull('bank_applications.deleted_at')
            ->get();
    }

    public function getTransactionData()
    {
        return \DB::table('bank_applications')
            ->join('banks','banks.id','bank_applications.bank_id')
            ->pluck('company_name','bank_id')->toArray();

    }

    public function getData($input,$noList)
    {

        $data = static::select('bank_applications.*','banks.email as email')
                ->join('banks', 'banks.id', 'bank_applications.bank_id');

        if(isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name',$input['company_name']);
        }

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('email',$input['email']);
        }

        if(isset($input['website_url']) && $input['website_url'] != '') {
            $data = $data->where('website_url','LIKE','%'.$input['website_url'].'%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(bank_applications.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '<=', $end_date);
        }

        if(isset($input['status']) && $input['status'] != '') {
            $data = $data->where('status',$input['status']);
        }
         
        $data = $data->orderBy("id", "DESC")->paginate($noList);

        return $data;
    }

    public function getPendigApplications($input,$noList)
    {
        $data = static::select('bank_applications.*')
                ->join('banks', 'banks.id', 'bank_applications.bank_id')
                ->where('status','0');

        if(isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name',$input['company_name']);
        }

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('email',$input['email']);
        }

        if(isset($input['website_url']) && $input['website_url'] != '') {
            $data = $data->where('website_url','LIKE','%'.$input['website_url'].'%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(bank_applications.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '<=', $end_date);
        }

        $data = $data->orderBy("id", "DESC")->paginate($noList);
        return $data;
    }

    public function getApprovedApplications($input,$noList)
    {
        $data = static::select('bank_applications.*')
                ->join('banks', 'banks.id', 'bank_applications.bank_id')
                ->where('status','1');

        if(isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name',$input['company_name']);
        }

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('email',$input['email']);
        }

        if(isset($input['website_url']) && $input['website_url'] != '') {
            $data = $data->where('website_url','LIKE','%'.$input['website_url'].'%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(bank_applications.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '<=', $end_date);
        }

        $data = $data->orderBy("id", "DESC")->paginate($noList);
        return $data;
    }

    public function getReassignApplications($input,$noList)
    {
        $data = static::select('bank_applications.*')
                ->join('banks', 'banks.id', 'bank_applications.bank_id')
                ->where('status','3');

        if(isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name',$input['company_name']);
        }

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('email',$input['email']);
        }

        if(isset($input['website_url']) && $input['website_url'] != '') {
            $data = $data->where('website_url','LIKE','%'.$input['website_url'].'%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(bank_applications.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(bank_applications.created_at)'), '<=', $end_date);
        }

        $data = $data->orderBy("id", "DESC")->paginate($noList);
        return $data;
    }
}
