<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RpApplication extends Model
{
    use SoftDeletes;

    protected $table = 'rp_applications';

    protected $guarded = array();

    public function storeData($data)
    {
        return static::create($data);
    }

    public function agent()
    {
        return $this->belongsTo('App\Agent');
    }

    public function category()
    {
        return $this->belongsTo('App\Categories');
    }

    public function findData($id)
    {
        $data = static::select('rp_applications.*', 'agents.name', 'agents.email')
            ->join('agents', 'agents.id', 'rp_applications.agent_id')
            ->where('rp_applications.id', $id)
            ->first();
        return $data;
    }

    public function FindDataFromUser($agent_id)
    {
        $data = static::select('rp_applications.*')
            ->join('agents', 'agents.id', 'rp_applications.agent_id')
            ->where('rp_applications.agent_id', $agent_id)
            ->first();
        return $data;
    }

    public function updateApplication($id, $input)
    {
        return static::where('id', $id)->update($input);
    }

    public function destroyWithUserId($agent_id)
    {
        return static::where('agent_id', $agent_id)->delete();
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
        return static::select('rp_applications.company_name', 'rp_applications.agent_id')
            ->join('agents', 'agents.id', '=', 'rp_applications.agent_id')
            ->whereNull('agents.deleted_at')
            ->whereNull('rp_applications.deleted_at')
            ->whereIn('status', ['4', '5', '6', '10', '11'])
            ->get();
    }

    public function getTransactionData()
    {
        return \DB::table('rp_applications')
            ->join('agents','agents.id','rp_applications.agent_id')
            ->pluck('company_name','agent_id')->toArray();

    }

    public function getData($input,$noList)
    {
        return static::select('rp_applications.*')->orderBy("id", "DESC")->paginate($noList);
    }
}
