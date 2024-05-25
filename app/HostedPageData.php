<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostedPageData extends Model
{
    protected $table = 'hosted_page_client_info';

    protected $guarded = [];

    public function getHostedData($input) {
    	$data = static::select(['hosted_page_client_info.*', 'ma.company_name', 'middetails.bank_name'])
    				->join('middetails','middetails.id','hosted_page_client_info.payment_gateway_id')
    				->join('merchantapplications as ma', 'ma.user_id', 'hosted_page_client_info.user_id')
    				->orderBy('created_at','desc');

        if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('hosted_page_client_info.payment_gateway_id', $input['payment_gateway_id']);
        }

        if(isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('ma.company_name',  'like', '%' . $input['company_name'] . '%');
        }

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('hosted_page_client_info.email',  'like', '%' . $input['email'] . '%');
        }

        if(isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('hosted_page_client_info.first_name',  'like', '%' . $input['first_name'] . '%');
        }

        if(isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('hosted_page_client_info.last_name',  'like', '%' . $input['last_name'] . '%');
        }

        if((isset($input['start_date']) && $input['start_date'] != '') &&  (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d',strtotime($input['start_date']));
            $end_date = date('Y-m-d',strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(hosted_page_client_info.created_at)'), '>=', $start_date.' 00:00:00')
                ->where(DB::raw('DATE(hosted_page_client_info.created_at)'), '<=', $end_date.' 00:00:00');
        }
                    
        $data = $data->where('is_moved','0')->get();

        return $data;
    }

    public function findData($id) {
        return static::select(['hosted_page_client_info.*', 'ma.company_name', 'middetails.bank_name'])
    				->join('middetails','middetails.id','hosted_page_client_info.payment_gateway_id')
    				->join('merchantapplications as ma', 'ma.user_id', 'hosted_page_client_info.user_id')
    				->find($id);
    }
}
