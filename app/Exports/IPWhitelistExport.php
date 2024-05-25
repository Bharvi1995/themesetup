<?php

namespace App\Exports;

use DB;
use App\WebsiteUrl;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class IPWhitelistExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $input = request()->all();

        $slave_connection = env('SLAVE_DB_CONNECTION_NAME', '');

        if (!empty($slave_connection)) {
            \DB::setDefaultConnection($slave_connection);
            $getDatabaseName = \DB::connection()->getDatabaseName();
            _WriteLogsInFile($getDatabaseName . " connection from admin card summary report excel", 'slave_connection');
        }
        $WebsiteUrl = new WebsiteUrl;
        $data = $WebsiteUrl->getData($input, 0)->get();
        // dd($data);
        return $data;
    }

    public function headings(): array
    {
        return [
            'Comapny Name',
            'User Email',
            'Website URL',
            'IP Address',
            'Status'
        ];
    }

    public function map($data): array{

        
        $_data = $data->only('business_name','email','website_name','ip_address','is_active');
        if($_data['is_active'] == 0){
            $_data['is_active'] = "Pending";
        } else {
            $_data['is_active'] = "Approved";
        }
        return $_data;
    }
}
