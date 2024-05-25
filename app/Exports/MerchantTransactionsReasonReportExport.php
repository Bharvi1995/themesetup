<?php

namespace App\Exports;

use DB;
use App\Transaction;


class MerchantTransactionsReasonReportExport
{
    public function headings(): array
    {
        return [
            'Merchant Name',
            'Reason',
            'Transaction Count',
        ];
    }


    public function download()
    {
        $columns = $this->headings();
        $input = request()->all();

        $transaction = new Transaction;

        $slave_connection = env('SLAVE_DB_CONNECTION_NAME', '');

        if(!empty($slave_connection))
        {
            \DB::setDefaultConnection($slave_connection);
            _WriteLogsInFile('Start slave connection', 'slave_connection');
        }

        $input['is_export'] = '1';

        $data = $transaction->getMerchantReasonReportData($input);

        $data = collect($data);

        return response()->streamDownload(function () use ($columns, $input, $data) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);


            $data->each(function ($data) use ($file) {
                $data = $data->toArray();

                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Merchant_Transactions_Reason_Report_Excel_' . date('d-m-Y') . '.csv');

    }

	public function map($data): array
    {
        return $data->toArray();
    }


}
