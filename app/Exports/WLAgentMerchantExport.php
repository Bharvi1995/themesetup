<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WLAgentMerchantExport implements FromCollection, WithHeadings
{
	protected $id;

	 function __construct($id) {
	        $this->id = $id;
	 }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $input = request()->all();
        $data = User::select('applications.business_name', 'users.name as name', 'users.email as email')
            ->leftjoin('applications', 'applications.user_id', 'users.id')
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

        $data = $data->orderBy('users.id', 'desc')
        		->where('users.main_user_id', '0')
        		->where('users.is_white_label', '1')
        		->where('white_label_agent_id',$this->id)
        		->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Business Name',
            'User Name',
            'Email',
        ];
    }
}