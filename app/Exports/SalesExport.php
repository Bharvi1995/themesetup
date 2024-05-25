<?php

namespace App\Exports;

use App\Sales;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class SalesExport implements FromCollection, WithHeadings, WithMapping
{

    protected $sales;

    public function __construct()
    {
        $this->sales = new Sales;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $input = Arr::except(request()->all(), ['_token', '_method']);
        $sales = $this->sales->advancedSearch($input)->get();
        return $sales;
    }

    public function headings(): array
    {
        return [
            "#",
            'Name',
            "Country Code",
            'Email',
            "Rm Link",
            'Status',
            'Created At',
            'Merchants Count',

        ];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->name,
            $data->country_code,
            $data->email,
            route("register") . "?rm=" . $data->rm_code,
            $data->status == "1" ? "Active" : "InActive",
            $data->created_at->format('d-m-Y h:m:s a'),
            $data->merchants_count
        ];
    }
}
