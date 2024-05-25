<?php

namespace App\Exports;

use DB;
use App\Merchantapplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerchantsApplicationsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $input = request()->all();
        $data = Merchantapplication::select("merchantapplications.company_name as company_name","merchantapplications.company_number as company_number","merchantapplications.first_name as first_name","merchantapplications.last_name as last_name","merchantapplications.email as email");

        if(isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use($input){
                    $query->orWhere('merchantapplications.company_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.company_number', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.first_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.last_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.email', 'like', '%'.$input['global_search'].'%');
                });
        }

        $data = $data->orderBy("merchantapplications.id","DESC")->get();

        return $data;
    }

    public function headings(): array
    {
        return ['company_name'=>'Company Name','company_number'=>'Company Number','first_name'=>'First Name','last_name'=>'Last Name','email'=>'Email'];
    }
}
