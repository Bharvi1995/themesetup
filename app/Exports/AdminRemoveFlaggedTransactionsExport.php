<?php

namespace App\Exports;

use DB;
use App\Transaction;
use App\RemoveFlaggedTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdminRemoveFlaggedTransactionsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$input = request()->all();
        
        $removeFlagged = new RemoveFlaggedTransaction;
        $data = $removeFlagged->getAllDataFilterExcel($input);
		return $data;            
    }

    public function headings(): array
    {
        return [
            'id',
            'Order No.',
            'Company name',
            'unflagged_date',
            'amount',
            'currency',
            'type'
        ];
    }
}

