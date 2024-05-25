<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\Agent;
use DB;

class AgentUserCSVExport
{
    
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function download(Request $request)
    {
        $input = $request->all();
        $columns = [
            'User Name',
            'Email',
            'Referral Code',
            'Agreement Status',
            'Commission',
            'Status'
        ];
        // dd('Yes');

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = Agent::select("agents.name","agents.email","agents.referral_code",\DB::raw('(CASE
                        WHEN agents.agreement_status = "0" THEN "Pending"
                        WHEN agents.agreement_status = "1" THEN "Sent"
                        WHEN agents.agreement_status = "2" THEN "Recived"
                        WHEN agents.agreement_status = "3" THEN "Re-Assign"
                        END) AS agreement_status'),'agents.commission','agents.is_active');
        if (! empty($input)){        
            if (isset($input['name']) && $input['name'] != '') {
                $data = $data->where('agents.name',  'like', '%' . $input['name'] . '%');
            }
            if (isset($input['email']) && $input['email'] != '') {
                $data = $data->where('agents.email',  'like', '%' . $input['email'] . '%');
            }
            if (isset($input['agent_id']) && $input['agent_id'] != '') {
                $data = $data->where('agents.id',$input['agent_id']);
            }
            if (isset($input['agreement_status']) && $input['agreement_status'] != '') {
                $data = $data->where('agents.agreement_status',   $input['agreement_status']);
            }
        }
        $data = $data->where('main_agent_id','0')->orderBy('agents.id', 'desc');
        $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Agents_user_Excel_'.date('d-m-Y').'.csv');
    }
}
