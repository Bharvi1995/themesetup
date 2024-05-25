<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agent extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'agents';
    protected $guarded = array();

    protected $fillable = [
        'name',
        'email',
        'commission',
        'otp',
        'is_otp_required',
        'is_wl_merchant_allow',
        'add_buy_rate',
        'add_buy_rate_amex',
        'add_buy_rate_master',
        'add_buy_rate_discover',
        'is_active',
        'login_otp',
        'agreement_status',
        'token',
        'main_agent_id',
        'password'
    ];

    protected $hidden = [
        'password',
        'token',
        'remember_token'
    ];

    public function getData($input, $noList)
    {
        $data = static::select("agents.*", "rp_agreement_document_upload.sent_files as sent_files", "rp_agreement_document_upload.files as files")
            ->leftJoin('rp_agreement_document_upload', 'rp_agreement_document_upload.rp_id', 'agents.id')
            ->groupBy("agents.id")
            ->orderBy("agents.id", "DESC")
            ->where('agents.main_agent_id', '0');

        if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('agents.name', 'like', '%' . $input['name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('agents.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['agent_id']) && $input['agent_id'] != '') {
            $data = $data->where('agents.id', $input['agent_id']);
        }

        if (isset($input['agreement_status']) && $input['agreement_status'] != '') {
            $data = $data->where('agents.agreement_status', $input['agreement_status']);
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getAllSubAgent($input, $noList)
    {
        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }
        $data = static::select("agents.*")->where('main_agent_id', $agentId)
            ->orderBy("id", "DESC");

        if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('agents.name', 'like', '%' . $input['name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('agents.email', 'like', '%' . $input['email'] . '%');
        }

        $data = $data->paginate($noList);

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

    public function agreementDocument()
    {
        return $this->hasOne('App\RpAgreementDocumentUpload', 'rp_id');
    }

    // * Get Agents 
    public function getAgents()
    {
        return static::select("id", "name")->whereIn('agreement_status', ["1", "2"])->get();
    }
}