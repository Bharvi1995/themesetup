<?php

namespace App\Exports;

use App\Transaction;
use DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportByMIDExport implements FromArray, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
    	$input = request()->all();
    	if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '' || isset($input['start_date']) && $input['start_date'] != '' || isset($input['end_date']) && $input['end_date'] != ''){
	        $reportByMidData = Transaction::select('transactions.*','users.name as usersName','merchantapplications.company_name as company_name')
	                ->join('users','users.id','transactions.user_id')
	                ->join('merchantapplications','merchantapplications.user_id','transactions.user_id');
	        if((isset($input['start_date']) && $input['start_date'] != '') &&  (isset($input['end_date']) && $input['end_date'] != '')) {
	            $start_date = date('Y-m-d',strtotime($input['start_date']));
	            $end_date = date('Y-m-d',strtotime($input['end_date']));

	            $reportByMidData = $reportByMidData->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date.' 00:00:00')
	                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date.' 00:00:00');
	        }
	        if((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
	            $reportByMidData = $reportByMidData->where('payment_gateway_id',$input['payment_gateway_id']);
	        }        
	        $reportByMidData = $reportByMidData->orderBy('transactions.created_at','DESC')
	                ->groupBy('transactions.user_id')
	                ->get();
        }else{
        	$reportByMidData = [];
        }

        $mainData = [];
        $transaction = new Transaction;
        $count = 0;
        if(!empty($reportByMidData) && $reportByMidData->count()){

            foreach($reportByMidData as $key=>$value){

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

