<?php

namespace App\Exports;

use DB;
use App\Transaction;
use App\Merchantapplication;
use App\RemoveFlaggedTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MostFlaggedChargebackExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$input = request()->all();
        $lastData = [];
        $lastData = collect();
        $Transaction = new Transaction;
        $finalData = $Transaction->mostFlaggedChargebacksReport($input);
        $type = $finalData['type'];
                arsort($finalData['data']);
                if(sizeof($finalData['data'])>0)
                {
                    foreach($finalData['data'] as $key=> $new_data)
                    {
                        $company = Merchantapplication::select('company_name')->where('user_id',$key)->first();
                        $lastData[] = ['company'=>$company->company_name,'count'=>$new_data,'type'=>$type];
                    }
                    
                }
        return $lastData;            
    }

    public function headings(): array
    {
        return [
            'company',
            'count',
            'type'
        ];
    }
}

