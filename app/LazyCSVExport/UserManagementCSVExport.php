<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\User;
use DB;

class UserManagementCSVExport
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
            'Company Name',
            'Email',
            'Business Type',
            'Business Catagory',
            'Website',
            'Country',
            'Country Code',
            'Phone no',
            'Skype Id',
            'State',
            'City',
            'PostCode',
            'Referral Partner Name',
            'Referral Partner Percentage'
        ];
        // dd('Yes');

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = User::select('users.name as name', 'applications.business_name', 'users.email as email','applications.business_type','applications.business_category','applications.website_url','applications.country','applications.country_code','applications.phone_no','applications.skype_id','applications.state','applications.city','applications.postcode','agents.name as agent_name', 'users.agent_commission as agent_percentage')
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->leftJoin('agents', 'agents.id', 'users.agent_id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');
            if (!empty($input['ids'])) {
            if (is_string($input['ids'])) {
                $input['ids'] = explode(',', $input['ids']);
            }
            $data = $data->whereIn('users.id', $input['ids']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }

        if (isset($input['verify_status']) && $input['verify_status'] != '') {
            if ($input['verify_status'] == 1) {
                $data = $data->where('users.email_verified_at', '!=', null);
            } else {
                $data = $data->where('users.email_verified_at', '=', null);
            }
        }

        if (isset($input['application_status']) && $input['application_status'] != '') {
            if ($input['application_status'] == '0') {
                $data = $data->whereNull('applications.id');
            } else {
                $data = $data->where('applications.status', $input['application_status']);
            }
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('applications.country', 'like', '%' . $input['country'] . '%');
        }
        if (isset($input['company']) && $input['company'] != '') {
            $data = $data->where('applications.business_name', $input['company']);
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }
        if (isset($input['agent_id']) && $input['agent_id'] != '') {
            $data = $data->where('users.agent_id', $input['agent_id']);
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
        if (isset($input['mode']) && $input['mode'] != '') {
            if($input['mode'] == 'test'){
                $test = [1,2];
                $data = $data->whereIn('users.mid', $test);
            }
            if($input['mode'] == 'live'){
                $test = [1,2];
                $data = $data->whereNotIn('users.mid', $test);
            }
            

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

        $data = $data->orderBy('users.id', 'desc')->where('users.main_user_id', '0');

        $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                fputcsv($file, $data);
            });

            fclose($file);
        }, 'UserManagement_Excel_'.date('d-m-Y').'.csv');
    }
}
