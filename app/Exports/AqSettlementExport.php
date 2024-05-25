<?php

namespace App\Exports;

use App\AqSettlement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Arr;

class AqSettlementExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $aqSettlement;

    public function __construct()
    {
        $this->aqSettlement = new AQSettlement;
    }

    public function collection()
    {
        $input = Arr::except(request()->all(), ['_token', '_method']);
        $settlement = $this->aqSettlement->advacnedSearch($input)->get();
        return $settlement;
    }

    public function headings(): array
    {
        return [
            "#",
            'Acquirer Name',
            "From Date",
            'To Date',
            "Transaction Hash",
            'Paid Date',
            'Payment Receipt',
        ];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->mid->bank_name,
            $data->from_date->format('d-m-Y'),
            $data->to_date->format('d-m-Y'),
            $data->txn_hash,
            $data->paid_date ? $data->paid_date->format('d-m-Y') : $data->created_at->format('d-m-Y'),
            getS3Url($data->payment_receipt)
        ];
    }
}
