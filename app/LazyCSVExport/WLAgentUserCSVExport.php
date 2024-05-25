<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\WLAgent;
use DB;

class WLAgentUserCSVExport
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
            'Status'
        ];

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = WLAgent::select("wl_agents.name","wl_agents.email",'wl_agents.is_active');
        if (! empty($input)){        
            if (isset($input['name']) && $input['name'] != '') {
                $data = $data->where('wl_agents.name',  'like', '%' . $input['name'] . '%');
            }
            if (isset($input['email']) && $input['email'] != '') {
                $data = $data->where('wl_agents.email',  'like', '%' . $input['email'] . '%');
            }
        }
        $data = $data->orderBy('wl_agents.id', 'desc');
        $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                if($data['is_active'] == '1'){
                    $data['is_active'] = 'Active';
                }else{
                    $data['is_active'] = 'Inactive';
                }

                fputcsv($file, $data);
            });

            fclose($file);
        }, 'WL_Agents_user_Excel_'.date('d-m-Y').'.csv');
    }
}
