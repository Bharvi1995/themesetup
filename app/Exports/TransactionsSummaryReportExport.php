<?php

namespace App\Exports;

use App\Application;
use Carbon\Carbon;
use DB;
use App\Transaction;

class TransactionsSummaryReportExport
{

    public function headings(): array
    {
        return [
            'Currency',
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
            'Block Count',
            'Block Amount',
            'Block Percentage'
        ];
    }

    public function download()
    {
        $columns = $this->headings();
        $input = request()->all();

        if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
            $input['start_date'] = $_GET['start_date'];
        } else {
            $_GET['start_date'] = '';
        }
        if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
            $input['end_date'] = $_GET['end_date'];
        } else {
            $_GET['end_date'] = "";
        }
        if (isset($_GET['currency']) && !empty($_GET['currency'])) {
            $input['currency'] = $_GET['currency'];
        }
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $input['user_id'] = $_GET['user_id'];
        }
        $this->Transaction = new Transaction;
        $transactions_summary = $this->Transaction->getTransactionSummaryRP($input);
        $data = [];
        $key = 0;

        foreach ($transactions_summary as $k => $transaction) {
            $data[$key]['currency'] = $transaction['currency'];
            $data[$key]['success_count'] = $transaction['success_count'];
            $data[$key]['success_amount'] = $transaction['success_amount'];
            $data[$key]['success_percentage'] = round($transaction['success_percentage'], 2);
            $data[$key]['declined_count'] = $transaction['declined_count'];
            $data[$key]['declined_amount'] = number_format($transaction['declined_amount'], 2, ".", ",");
            $data[$key]['declined_percentage'] = round($transaction['declined_percentage'], 2);
            $data[$key]['chargebacks_count'] = $transaction['chargebacks_count'];
            $data[$key]['chargebacks_amount'] = number_format($transaction['chargebacks_amount'], 2, ".", ",");
            $data[$key]['chargebacks_percentage'] = round($transaction['chargebacks_percentage'], 2);
            $data[$key]['refund_count'] = $transaction['refund_count'];
            $data[$key]['refund_amount'] = number_format($transaction['refund_amount'], 2, ".", ",");
            $data[$key]['refund_percentage'] = round($transaction['refund_percentage'], 2);
            $data[$key]['flagged_count'] = $transaction['flagged_count'];
            $data[$key]['flagged_amount'] = number_format($transaction['flagged_amount'], 2, ".", ",");
            $data[$key]['flagged_percentage'] = round($transaction['flagged_percentage'], 2);
            $data[$key]['block_count'] = $transaction['block_count'];
            $data[$key]['block_amount'] = number_format($transaction['block_amount'], 2, ".", ",");
            $data[$key]['block_percentage'] = round($transaction['block_percentage'], 2);
            $key++;
        }

        $data = collect($data);

        return response()->streamDownload(function () use ($columns, $input, $data) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);


            $data->each(function ($data) use ($file) {
                $data = $data;


                $data['success_percentage'] = (string)round($data['success_percentage'], 2);
                $data['declined_percentage'] = (string)round($data['declined_percentage'], 2);
                $data['refund_percentage'] = (string)round($data['refund_percentage'], 2);
                $data['chargebacks_percentage'] = (string)round($data['chargebacks_percentage'], 2);
                $data['flagged_percentage'] = (string)round($data['flagged_percentage'], 2);
                $data['block_percentage'] = (string)round($data['block_percentage'], 2);

                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Transaction_Summary_Report_Excel_' . date('d-m-Y') . '.csv');
    }
}
