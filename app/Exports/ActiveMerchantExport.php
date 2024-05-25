<?php

namespace App\Exports;

use DB;
use App\User;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActiveMerchantExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$transaction = new Transaction;
    	$activeMerchantsArray = $transaction->getActiveMerchantsArray();

        $input = request()->all();
        $data = User::select('merchantapplications.company_name', 'merchantapplications.company_number', 'users.email as email')
            ->join('merchantapplications', 'merchantapplications.user_id', 'users.id')
            ->whereIn('users.id',$activeMerchantsArray);

        if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }

        if(isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use($input){
                    $query->orWhere('merchantapplications.company_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.company_number', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('users.id', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('users.email', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('users.token', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('users.otp', 'like', '%'.$input['global_search'].'%');
                });
        }

        $data = $data->where('users.main_user_id', '0')
            ->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Company Name',
            'Company Number',
            'Email'
        ];
    }
}
