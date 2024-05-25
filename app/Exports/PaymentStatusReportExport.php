<?php

namespace App\Exports;

use DB;
use App\Transaction;


class PaymentStatusReportExport
{
    public $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function headings(): array
    {
        $input = request()->all();

        if ($this->status == 1) {
            $column = [
                'Currency',
                'Business Name',
                'Success Count',
                'Success Amount',
                'Success Percentage'
            ];
        } else if ($this->status == 2) {
            $column = [
                'Currency',
                'Business Name',
                'Declined Count',
                'Declined Amount',
                'Declined Percentage',
            ];
        } else if ($this->status == 3) {
            $column = [
                'Currency',
                'Business Name',
                'Chargebacks Count',
                'Chargebacks Amount',
                'Chargebacks Percentage'
            ];

        } else if ($this->status == 4) {
            $column = [
                'Currency',
                'Business Name',
                'Refund Count',
                'Refund Amount',
                'Refund Percentage'
            ];
        } else if ($this->status == 5) {
            $column = [
                'Currency',
                'Business Name',
                'Suspicious Count',
                'Suspicious Amount',
                'Suspicious Percentage',
            ];
        } else if ($this->status == 7) {
            $column = [
                'Currency',
                'Business Name',
                'Block Count',
                'Block Amount',
                'Block Percentage',
            ];
        }
        return $column;
    }


    public function download()
    {
        $columns = $this->headings();
        $input = request()->all();
        $input['groupBy'] = ['transactions.user_id', 'transactions.currency'];
        $input['SelectFields'] = ['transactions.user_id', 'applications.business_name'];
        $input['JoinTable'] = [
            'table' => 'applications',
            'condition' => 'applications.user_id',
            'conditionjoin' => 'transactions.user_id'
        ];
        $this->transaction = new Transaction;
        $transactionssummary = $this->transaction->getSummaryReportData($input);
        $data = $this->transaction->PorcessSumarryData('PaymentSsummary', $transactionssummary);
        $data = $this->transaction->PorcessSumarryData('midSummaryForExcel', $data);
        $data = collect($data);

        return response()->streamDownload(function () use ($columns, $input, $data) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);


            $data->each(function ($data) use ($file) {
                $data = $data->toArray();


                $_data['currency'] = $data['currency'];
                $_data['business_name'] = $data['business_name'];

                if ($this->status == 1) {
                    $_data['success_count'] = $data['success_count'];
                    $_data['success_amount'] = $data['success_amount'];
                    $_data['success_percentage'] = $data['success_percentage'];

                } else if ($this->status == 2) {
                    $_data['declined_count'] = $data['declined_count'];
                    $_data['declined_amount'] = $data['declined_amount'];
                    $_data['declined_percentage'] = $data['declined_percentage'];
                } else if ($this->status == 3) {
                    $_data['chargebacks_count'] = $data['chargebacks_count'];
                    $_data['chargebacks_amount'] = $data['chargebacks_amount'];
                    $_data['chargebacks_percentage'] = $data['chargebacks_percentage'];
                } else if ($this->status == 4) {
                    $_data['refund_count'] = $data['refund_count'];
                    $_data['refund_amount'] = $data['refund_amount'];
                    $_data['refund_percentage'] = $data['refund_percentage'];
                } else if ($this->status == 5) {
                    $_data['flagged_count'] = $data['flagged_count'];
                    $_data['flagged_amount'] = $data['flagged_amount'];
                    $_data['flagged_percentage'] = $data['flagged_percentage'];
                } else if ($this->status == 7) {
                    $_data['block_count'] = $data['block_count'];
                    $_data['block_amount'] = $data['block_amount'];
                    $_data['block_percentage'] = $data['block_percentage'];
                }

                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Payment_Status_Report_Excel_' . date('d-m-Y') . '.csv');

    }

}
