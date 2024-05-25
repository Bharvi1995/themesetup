<?php

namespace App\Exports;

use DB;
use App\TxTransaction;
use App\Transaction;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class MidSummaryReportOnCountryExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $input = request()->all();
        
        $input['groupBy'] = ['transactions.country'];
        $input['SelectFields'] = ['transactions.country'];
        $this->transaction = new Transaction;
        $transactionssummary = $this->transaction->getSummaryReportData($input);
        $data = $this->transaction->PorcessSumarryData('CountrySummary', $transactionssummary);
        return collect($data);
    }

	public function map($data): array
    {
    	$data['country'] = getCountryFullName($data['country']);
        $_data = $data;
        return $_data;
    }

    public function headings(): array
    {
        return [
            'Country',
            'Success Count',
            'Success Amount',
            'Success Percentage',

            'Declined Count',
            'Declined Amount',
            'Declined Percentage',

            'Chargebacks Count',
            'Chargebacks Amount',
            'Chargebacks Percentage',

            'Refund Count',
            'Refund Amount',
            'Refund Percentage',

            'Suspicious Count',
            'Suspicious Amount',
            'Suspicious Percentage',

            'Retrieval Count',
            'Retrieval Amount',
            'Retrieval Percentage',

            'Block Count',
            'Block Amount',
            'Block Percentage'
        ];
    }
}
