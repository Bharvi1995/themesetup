<?php

namespace App\Exports;

use App\Transaction;
use DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportByTransactionTypeExport implements FromArray, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
    	$input = request()->all();
    	// if(isset($input['company_name']) && $input['status'] != '' || isset($input['start_date']) && $input['start_date'] != '' || isset($input['end_date']) && $input['end_date'] != ''){
	        $reportByTransactionTypeData = Transaction::select('transactions.*','users.name as usersName','merchantapplications.company_name as company_name')
	                ->join('users','users.id','transactions.user_id')
	                ->join('merchantapplications','merchantapplications.user_id','transactions.user_id');
	        if((isset($input['start_date']) && $input['start_date'] != '') &&  (isset($input['end_date']) && $input['end_date'] != '')) {
	            $start_date = date('Y-m-d',strtotime($input['start_date']));
	            $end_date = date('Y-m-d',strtotime($input['end_date']));

	            $reportByTransactionTypeData = $reportByTransactionTypeData->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date.' 00:00:00')
	                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date.' 00:00:00');
	        }
	        if(isset($input['company_name']) && $input['company_name'] != '') {
                $reportByTransactionTypeData = $reportByTransactionTypeData->where('company_name',  'like', '%' . $input['company_name'] . '%');
            }
            if(isset($input['status']) && $input['status'] != '') {
                $reportByTransactionTypeData = $reportByTransactionTypeData->where('transactions.status', $input['status']);
            }        
	        $reportByTransactionTypeData = $reportByTransactionTypeData->where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->orderBy('transactions.created_at','DESC')
	                ->groupBy('transactions.user_id')
	                ->get();
        // }else{
        // 	$reportByTransactionTypeData = [];
        // }

        $mainData = [];
        $transaction = new Transaction;
        $count = 0;
        if(!empty($reportByTransactionTypeData) && $reportByTransactionTypeData->count()){

            foreach($reportByTransactionTypeData as $key=>$value){

                foreach($transaction->getPayoutSummaryReportByUser($value->user_id,$input) as $ukey => $uvalue){

                    $mainData[$count]['merchant_name'] = $value->company_name;
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
            'Merchant Name',
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

