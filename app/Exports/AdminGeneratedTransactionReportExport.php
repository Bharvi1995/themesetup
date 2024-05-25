<?php

namespace App\Exports;

use DB;
use App\UserGenerateReport;
use App\UserGenerateReportChild;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminGeneratedTransactionReportExport implements FromArray, WithHeadings
{
	/**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
    	$input = request()->all();

    	$id = !empty($input['id']) ? explode(',', $input['id']) : [];

        $data = UserGenerateReport::select('usergeneratereports.*','usergeneratereportschild.id as rid','usergeneratereportschild.*')
        	->leftjoin("usergeneratereportschild", "usergeneratereportschild.report_id", "=", "usergeneratereports.id")
            ->where('usergeneratereports.is_excel', '0');
            if(!empty($id)){
                $data = $data->whereIn('usergeneratereports.id',$id);
            }

            if(isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('usergeneratereports.user_id', $input['user_id']);
            }

            if(
                (isset($input['start_date']) && $input['start_date'] != '') &&
                (isset($input['end_date']) && $input['end_date'] != '')
            ) {
                $start_date = date('d/m/Y', strtotime($input['start_date']));
                $end_date = date('d/m/Y', strtotime($input['end_date']));

                $date_raw = DB::raw("STR_TO_DATE(`date`, '%d/%m/%Y')");

                $start_raw = DB::raw("STR_TO_DATE(?, '%d/%m/%Y')");
                $end_raw = DB::raw("STR_TO_DATE(?, '%d/%m/%Y')");


                $data = $data->where($date_raw, '>=', $start_raw)
                    ->where($date_raw, '<=', $end_raw);
                // check if user id, then bind user_id as first binding else only start_date & and_date
                if (isset($input['user_id']) && $input['user_id'] != '') {
                    $data = $data->setBindings(['0', $input['user_id'], $start_date, $end_date]);
                } else {
                    $data = $data->setBindings(['0',$start_date, $end_date]);
                }
            }

            $data = $data->get();

        $tmp = '';    
        $result = [];

        foreach ($data as $key => $value) {
        	if($value->invoice_no != $tmp){
        		$result[$value->rid]['date'] = $value->date;
        		$result[$value->rid]['invoice_no'] = $value->invoice_no;
        		$result[$value->rid]['company_name'] = $value->company_name;
        		$result[$value->rid]['start_date'] = $value->start_date;
        		$result[$value->rid]['end_date'] = $value->end_date;
        	}else{
        		$result[$value->rid]['date'] = '';
        		$result[$value->rid]['invoice_no'] = '';
        		$result[$value->rid]['company_name'] = '';
        		$result[$value->rid]['start_date'] = '';
        		$result[$value->rid]['end_date'] = '';
        		$result[$value->rid]['currency'] = '';
        	}

        	$MerchantDiscountRateFee = ($value->total_amount_processes*$value['merchant_discount_rate'])/100;

            $RollingReservePercentageFee = ($value->total_amount_processes*$value['rolling_reserve_paercentage'])/100;

            $TransactionAmountFee = $value->approved_transaction*$value['transaction_fee_amount'];

            $DeclinedAmountFee = $value->declined_transaction*$value['declined_fee_amount'];

            $RefundAmountFee = $value->refunds*$value['refund_fee_amount'];

            $ChargebackAmountFee = $value->chargebacks*$value['chargebacks_fee_amount'];

            $FlaagedAmountFee = $value->total_flagged*10;

            $extra  = $TransactionAmountFee+$DeclinedAmountFee+$RefundAmountFee+$ChargebackAmountFee+$FlaagedAmountFee;

            $DeclinedVol = ($value->total_amount_declined);
            $RefundVol = ($value->refunds_amount);
            $ChargebacksVol = ($value->chargebacks_amount);
            $FlaggedVol = ($value->total_flagged_amount);


            $SubTotal = $value->total_amount_processes-($RefundVol+$ChargebacksVol+$FlaggedVol);                    
            
            $GrandTotal = $SubTotal - ($extra+$RollingReservePercentageFee+$MerchantDiscountRateFee);

        	$result[$value->rid]['currency'] = $value->currency;
        	$result[$value->rid]['approved_transaction'] = $value->approved_transaction;
        	$result[$value->rid]['declined_transaction'] = $value->declined_transaction;
        	$result[$value->rid]['total_transaction'] = $value->total_transaction;
        	$result[$value->rid]['total_amount_processes'] = $value->total_amount_processes;
        	$result[$value->rid]['total_amount_declined'] = $value->total_amount_declined;
        	$result[$value->rid]['refunds'] = $value->refunds;
        	$result[$value->rid]['refunds_amount'] = $value->refunds_amount;
        	$result[$value->rid]['chargebacks'] = $value->chargebacks;
        	$result[$value->rid]['chargebacks_amount'] = $value->chargebacks_amount;
        	$result[$value->rid]['total_flagged'] = $value->total_flagged;
        	$result[$value->rid]['total_flagged_amount'] = $FlaggedVol;
        	$result[$value->rid]['mdr'] = $value->mdr;
        	$result[$value->rid]['rolling_reserve'] = $value->rolling_reserve;
        	$result[$value->rid]['refund_fee'] = $value->refund_fee;
        	$result[$value->rid]['chargebacks_fee'] = $value->chargebacks_fee;
        	$result[$value->rid]['transaction_fee'] = $value->transaction_fee;
        	$result[$value->rid]['flagged_fee'] = '10';
        	$result[$value->rid]['net_settlement_amount'] = number_format($GrandTotal, 2);

        	$tmp = $value->invoice_no;
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'Date',
			'Invoice No.',
			'Company Name',
			'Start Date',
			'End Date',
			'Currency',
			'Approved Transaction',
			'Declined Transaction',
			'Total Transaction',
			'Total Amount Processed',
			'Total Amount Declined',
			'Total Refund',
			'Total Refund Amount',
			'Total Chargebacks',
			'Total Chargebacks Amount',
			'Total Flagged',
			'Total Flagged Amount',
			'MDR',
			'Rolling Reserve',
			'Refund Fee',
			'Chargebacks Fee',
			'Transaction Fee',
			'Flagged Fee',
			'Net Settlement Amount'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return UserGenerateReport::all();
    // }
}
