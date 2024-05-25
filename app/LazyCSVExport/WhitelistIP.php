<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\WebsiteUrl;
use DB;

class WhitelistIP
{
    protected $id;
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function download(Request $request)
    {
        // dd(get_guard());
        $input = $request->all();
        $columns = [
            // 'Website URL',
            'IP Address',
            'Status'
        ];

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1')
                $userID = \Auth::user()->main_user_id;
            else
                $userID = \Auth::user()->id;

            $data = WebsiteUrl::select('ip_address',
                \DB::raw('(CASE
                        WHEN is_active = "0" THEN "Pending"
                        ELSE "Approved"
                        END) AS is_active')
                )->where('user_id', $userID);
        
            $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                fputcsv($file, $data);
            });

            fclose($file);
        }, 'IPS_'.date('d_m_Y').'.csv');     
    }
}
