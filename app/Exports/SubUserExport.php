<?php

namespace App\Exports;

use DB;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubUserExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$input = request()->all();
        $data = User::select('applications.business_name', 'users.email as email')
            ->leftjoin('applications', 'applications.user_id', 'users.id');

        if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }

        if(isset($input['business_name']) && $input['business_name'] != '') {
            $data = $data->where('applications.business_name', $input['business_name']);
        }

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', $input['email']);
        }

        $data = $data->where('users.main_user_id', $input['ids'])
                ->groupBy('users.mid')
                ->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Company Name',
            'Email'
        ];
    }
}
