<?php

namespace App\Exports;

use DB;
use App\Transaction;


class MerchantTransactionsReportExport
{

    public function headings(): array
    {
        return [
            'Merchant',
            "Email",
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
            'Block Percentage',
        ];
    }

    public function download()
    {
        $columns = $this->headings();
        $input = request()->all();
        $input['by_merchant'] = 1;

        $this->transaction = new Transaction;
        $transactions_summary = $this->transaction->getTransactionSummaryRP($input);
        // $companyName = \DB::table('applications')->join('users', 'users.id', 'applications.user_id')->pluck('business_name', "user_id")->toArray();
        $companyName = \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck(\DB::raw("CONCAT(applications.business_name, '-', users.email) as value"), "user_id")
            ->toArray();
        $data = [];
        foreach ($transactions_summary as $userid => $value) {

            foreach ($value as $k => $val) {
                $datatmp['merchant'] = isset($companyName[$userid]) ? explode("-", $companyName[$userid])[0] : ' -';
                $datatmp['email'] = isset($companyName[$userid]) ? explode("-", $companyName[$userid])[1] : ' -';
                $datatmp['currency'] = $val['currency'];
                $datatmp['success_count'] = $val['success_count'];
                $datatmp['success_amount'] = $val['success_amount'];
                $datatmp['success_percentage'] = $val['success_percentage'];
                $datatmp['declined_count'] = $val['declined_count'];
                $datatmp['declined_amount'] = $val['declined_amount'];
                $datatmp['declined_percentage'] = $val['declined_percentage'];
                $datatmp['chargebacks_count'] = $val['chargebacks_count'];
                $datatmp['chargebacks_amount'] = $val['chargebacks_amount'];
                $datatmp['chargebacks_percentage'] = $val['chargebacks_percentage'];
                $datatmp['refund_count'] = $val['refund_count'];
                $datatmp['refund_amount'] = $val['refund_amount'];
                $datatmp['refund_percentage'] = $val['refund_percentage'];
                $datatmp['flagged_count'] = $val['flagged_count'];
                $datatmp['flagged_amount'] = $val['flagged_amount'];
                $datatmp['flagged_percentage'] = $val['flagged_percentage'];
                $datatmp['block_count'] = $val['block_count'];
                $datatmp['block_amount'] = $val['block_amount'];
                $datatmp['block_percentage'] = $val['block_percentage'];
                $data[] = $datatmp;
            }
        }
        $data = collect($data);

        return response()->streamDownload(function () use ($columns, $input, $data) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);
            $data->each(function ($data) use ($file) {
                $data['success_percentage'] = (string) round($data['success_percentage'], 2);
                $data['declined_percentage'] = (string) round($data['declined_percentage'], 2);
                $data['refund_percentage'] = (string) round($data['refund_percentage'], 2);
                $data['chargebacks_percentage'] = (string) round($data['chargebacks_percentage'], 2);
                $data['flagged_percentage'] = (string) round($data['flagged_percentage'], 2);
                $data['block_percentage'] = (string) round($data['block_percentage'], 2);

                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Merchant_Transaction_Report_Excel_' . date('d-m-Y') . '.csv');

    }

}