<?php

namespace App\Exports;

use App\Transaction;
use DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportByMIDExportNew implements FromArray, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
    	$input = request()->all();
    	$this->transaction = new Transaction;
        $mainData = [];
        if(isset($input['payment_gateway_id'])){
            $mainData   = $this->transaction->getPayoutSummaryReportByMid($input);
        }
        return $mainData;
    }

    public function headings(): array
    {
        return [
            'Currency',
            'Success Amount',
            'Success Count',
            'Success Percentage',
            'Declined Amount',
            'Declined Count',
            'Declined Percentage',
            'Chargebacks Amount',
            'Chargebacks Count',
            'Chargebacks Percentage',
            'Refund Amount',
            'Refund Count',
            'Refund Percentage',
            'Flagged Amount',
            'Flagged Count',
            'Flagged Percentage',
            'Retrieval Amount',
            'Retrieval Count',
            'Retrieval Percentage'
        ];
    }
}

