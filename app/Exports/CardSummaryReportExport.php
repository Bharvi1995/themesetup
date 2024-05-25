<?php

namespace App\Exports;

use DB;
use App\TxTransaction;
use App\Transaction;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class CardSummaryReportExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $input = request()->all();
        $this->transaction = new Transaction;
        $input['groupBy'] = 'card_type';
        $input['SelectFields'] = ['card_type'];
        $transactionssummary = $this->transaction->getSummaryReportData($input);
        $data = $this->transaction->PorcessSumarryData('CardTypeSumamry', $transactionssummary);
        return collect($data);
    }

	public function map($data): array
    {
        $card_type = config('card.type');
        $_data = $data;
        if($_data['card_type'] > 0){
            $_data['card_type'] = $card_type[$_data['card_type']];
        } else {
            $_data['card_type'] = "N/A";
        }
        return $_data;
    }

    public function headings(): array
    {
        return [
            'Card',
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
            'Retrieval Percentage'
        ];
    }
}
