<?php

namespace App\Exports;

use DB;
use App\Transaction;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class MerchantTransactionsReportForRpMerchantExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $input = request()->all();
        $input['by_merchant'] = 1;
        $this->transaction = new Transaction;
        $transactions_summary = $this->transaction->getTransactionSummaryForRPMerchants($input);
        $companyName = \DB::table('applications')->join('users','users.id','applications.user_id')->where('agent_id', auth()->guard('agentUser')->user()->id)->pluck('business_name','user_id')->toArray();
        $data = [];
        foreach ($transactions_summary as $userid => $value) {
            foreach ($value as $k => $val) {
                $datatmp['merchant'] = ($companyName[$userid])?$companyName[$userid]:' -';
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
        return collect($data);
    }

	public function map($data): array
    {
        // $data = $data->toArray();
        $data['success_percentage'] = (string) round($data['success_percentage'], 2);
        $data['declined_percentage'] = (string) round($data['declined_percentage'], 2);
        $data['refund_percentage'] = (string) round($data['refund_percentage'], 2);
        $data['chargebacks_percentage'] = (string) round($data['chargebacks_percentage'], 2);
        $data['flagged_percentage'] = (string) round($data['flagged_percentage'], 2);
        $data['block_percentage'] = (string) round($data['block_percentage'], 2);
        return $data;
    }

    public function headings(): array
    {
        return [
            'Merchant',
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
}
