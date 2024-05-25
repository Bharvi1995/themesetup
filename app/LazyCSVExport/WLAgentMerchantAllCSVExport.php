<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\User;
use DB;

class WLAgentMerchantAllCSVExport
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
            'White Label RP Name',
            'Business Name',
            'User Name',
            'Email',
        ];

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = User::select('wl_agents.name as rpName','applications.business_name', 'users.name as name', 'users.email as email')
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->leftjoin('wl_agents', 'wl_agents.id', 'users.white_label_agent_id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');

        if (! empty($input)){        
            if (!empty($input['ids'])) {
	            if (is_string($input['ids'])) {
	                $input['ids'] = explode(',', $input['ids']);
	            }
	            $data = $data->whereIn('users.id', $input['ids']);
	        }

	        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
	            $data = $data->where('users.mid', $input['payment_gateway_id']);
	        }

	        if (isset($input['email']) && $input['email'] != '') {
	            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
	        }
	        if (isset($input['country']) && $input['country'] != '') {
	            $data = $data->where('applications.country', 'like', '%' . $input['country'] . '%');
	        }
	        if (isset($input['wl_agents']) && $input['wl_agents'] != '') {
	            $data = $data->where('users.white_label_agent_id', $input['wl_agents']);
	        }
	        if (isset($input['company']) && $input['company'] != '') {
	            $data = $data->where('applications.business_name', $input['company']);
	        }
	        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
	            $data = $data->where('users.mid', $input['payment_gateway_id']);
	        }
	        if (isset($input['category']) && $input['category'] != '') {
	            $data = $data->where('applications.category_id', $input['category']);
	        }
	        if (isset($input['website']) && $input['website'] != '') {
	            $data = $data->where('applications.website_url', 'like', '%' . $input['website'] . '%');
	        }
	        if (isset($input['api_key']) && $input['api_key'] != '') {
	            $data = $data->where('users.api_key', $input['api_key']);
	        }
	        if (isset($input['global_search']) && $input['global_search'] != '') {
	            $data = $data->where(function ($query) use ($input) {
	                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
	                    ->orWhere('applications.phone_no', 'like', '%' . $input['global_search'] . '%')
	                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
	                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
	                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
	                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
	            });
	        }
        }

        $data = $data->where('users.main_user_id', '0')
        		->where('users.is_white_label', '1')
        		->orderBy('users.id', 'desc');

        $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Merchant_List_Of_WL_RP_Excel_'.date('d-m-Y').'.csv');
    }
}
