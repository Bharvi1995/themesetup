<?php

namespace App\Exports;

use App\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use DB;

class LastSixMonthTransactionsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $input = request()->all();
        $data = Transaction::select('users.email','merchantapplications.company_name')
        	->join('merchantapplications','merchantapplications.user_id','transactions.user_id')
        	->join('users','users.id','transactions.user_id')
        	->where("transactions.created_at",">=", Carbon::now()->subMonths(6))
        	->groupBy('transactions.user_id')
        	->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Company Name',
            'Email',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->company_name,
            $transaction->email,
        ];
    }
}
