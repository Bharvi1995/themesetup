<?php

namespace App\Exports;

use App\Transaction;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class TransactionSummaryReportExport implements FromArray, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $input = request()->all();

        $payoutSummaryReportData = Transaction::select('transactions.*','users.name as usersName','merchantapplications.company_name as company_name')
                ->join('users','users.id','transactions.user_id')
                ->join('merchantapplications','merchantapplications.user_id','transactions.user_id')
                ->where('transactions.user_id', auth()->user()->id);

        if((isset($input['start_date']) && $input['start_date'] != '') &&  (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d',strtotime($input['start_date']));
            $end_date = date('Y-m-d',strtotime($input['end_date']));

            $payoutSummaryReportData = $payoutSummaryReportData->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date.' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date.' 00:00:00');
        }

        $payoutSummaryReportData = $payoutSummaryReportData->orderBy('transactions.created_at','DESC')
        ->groupBy('transactions.user_id')
        ->get();

        $mainData = [];
        $transaction = new Transaction;
        $count = 0;
        if(!empty($payoutSummaryReportData) && $payoutSummaryReportData->count()){

            foreach($payoutSummaryReportData as $key=>$value){

                foreach($transaction->getPayoutSummaryReportByUser($value->user_id,$input) as $ukey => $uvalue){

                    $mainData[$count]['currency'] = $ukey;

                    foreach($uvalue as $amountKey => $amountValue){
                        $mainData[$count][$amountKey] = $amountValue;
                    }
                    
                    $count++;
                }
            }
        }

        // echo "<pre>";
        // print_r($mainData);
        // exit();

        return $mainData;
    }

    public function headings(): array
    {
    	return [
            'Currency',
            'Success Amount',
            'Success Count',
            'Declined Amount',
            'Declined Count',
            'Chargebacks Amount',
            'Chargebacks Count',
            'Refund Amount',
            'Refund Count',
            'Flagged Amount',
            'Flagged Count'
        ];
    }
}
