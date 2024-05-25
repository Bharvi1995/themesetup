<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\ReconcilationReport;
use App\ReconcilationChild;
use DB;

class ReconcilationExport 
{   
    public function exportReport(Request $request){
        $input = request()->all();
        $input['id']=array();
        if($request->id){
            $input['id'] = explode(',', $request->id);
        }
        $type="all";
        $fileName="AllReconcilationExcelReports";
        if($input['id']){
            $type="selected";
            $fileName="ReconcilationExcel";
        }
        $columns = [
            'Settlement No',
            'Company Name',
            'Created Date',
            'Start Date',
            'End Date',
            'Currency',
            'Approved Count',
            'Approved Amount',
            'Declined Count',
            'Declined Amount',
            'Chargebacks Count',
            'Chargebacks Amount',
            //'Remomve Chargebacks Count',
            //'Remomve Chargebacks Fee',
            'Refunds Count',
            'Refunds Amount',
            'Flagged Count',
            'Flagged Amount',
            'Remove Flagged Count',
            'Remove Flagged Fee',
            //'Total Settlement',
            'Merchant Discount Rate',
            'Merchant Discount Amount',
            'Rolling Reserve (180 Days)',
            'Rolling Reserve Amount',
            'Approved Transaction Fee Rate',
            //'Approved Transaction Fee Amount',
            'Declined Transaction Fee Rate',
            //'Declined Transaction Fee Amount',
            'Chargebacks Transaction Fee Rate',
            //'Chargebacks Transaction Fee Amount',
            'Refunds Transaction Fee Rate',
            //'Refunds Transaction Fee Amount',
            'Flagged Transaction Fee Rate',
            //'Flagged Transaction Fee Amount',
            //'Total Transaction Fee Amount',
            'Net Settlement Amount',
            'Old Net Settlement Amount'
        ];
        return response()->streamDownload(function() use($columns, $input,$fileName,$type){
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);
            $data = ReconcilationReport::select(
                    'reconcilation_reports.invoice_no',
                    'reconcilation_reports.company_name',
                    'reconcilation_reports.date',
                    'reconcilation_reports.start_date',
                    'reconcilation_reports.end_date',
                    'reconcilation_children.currency',
                    'reconcilation_children.approved_transaction',
                    'reconcilation_children.total_amount_processes',
                    'reconcilation_children.declined_transaction',
                    'reconcilation_children.total_amount_declined',
                    'reconcilation_children.chargebacks',
                    'reconcilation_children.chargebacks_amount',
                    //'reconcilation_children.remove_past_chargebacks',
                    //'reconcilation_children.past_chargebacks_charge_amount',
                    'reconcilation_children.refunds',
                    'reconcilation_children.refunds_amount',
                    'reconcilation_children.total_flagged',
                    'reconcilation_children.total_flagged_amount',
                    'reconcilation_children.remove_past_flagged',
                    'reconcilation_children.past_flagged_charge_amount',
                    //'reconcilation_children.sub_total',
                    'reconcilation_reports.merchant_discount_rate',
                    'reconcilation_children.mdr',
                    'reconcilation_reports.rolling_reserve_paercentage',
                    'reconcilation_children.rolling_reserve',
                    'reconcilation_children.transaction_fee',
                    //'reconcilation_children.total_transaction_fee',
                    'reconcilation_children.declined_fee',
                    //'reconcilation_children.total_declined_fee',
                    'reconcilation_children.chargebacks_fee',
                    //'reconcilation_children.total_chargebacks_fee',
                    'reconcilation_children.refund_fee',
                    //'reconcilation_children.total_refund_fee',
                    'reconcilation_children.flagged_fee',
                    //'reconcilation_children.total_flagged_fee',
                    //'reconcilation_children.transactions_fee_total',
                    'reconcilation_children.total_payout',
                    'reconcilation_children.old_total_payout'
                )
                ->join('reconcilation_children', 'reconcilation_children.report_id', 'reconcilation_reports.id')
                ->orderBy('reconcilation_reports.id', 'desc');
            if($type=="selected"){
                $data=$data->whereIn('reconcilation_reports.id', $input['id']);
            }
            $data = $data->cursor()
            ->each(function ($data) use ($file) {  
                $data = $data->makeHidden('report_total')->toArray();
                fputcsv($file, $data);  
            });
            fclose($file);
        }, $fileName.date('d-m-Y').'.csv');
    }

    public function allReportExport(Request $request)
    {
        $input = request()->all();

        $columns = [
            'Settlement No',
            'Company Name',
            'Created Date',
            'Start Date',
            'End Date',
            'Currency',
            'Approved Count',
            'Approved Amount',
            'Declined Count',
            'Declined Amount',
            'Chargebacks Count',
            'Chargebacks Amount',
            //'Remomve Chargebacks Count',
            //'Remomve Chargebacks Fee',
            'Refunds Count',
            'Refunds Amount',
            'Flagged Count',
            'Flagged Amount',
            'Remove Flagged Count',
            'Remove Flagged Fee',
            //'Total Settlement',
            'Merchant Discount Rate',
            'Merchant Discount Amount',
            'Rolling Reserve (180 Days)',
            'Rolling Reserve Amount',
            'Approved Transaction Fee Rate',
            //'Approved Transaction Fee Amount',
            'Declined Transaction Fee Rate',
            //'Declined Transaction Fee Amount',
            'Chargebacks Transaction Fee Rate',
            //'Chargebacks Transaction Fee Amount',
            'Refunds Transaction Fee Rate',
            //'Refunds Transaction Fee Amount',
            'Flagged Transaction Fee Rate',
            //'Flagged Transaction Fee Amount',
            //'Total Transaction Fee Amount',
            'Net Settlement Amount',
            'Old Settlement Amount',
        ];

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = ReconcilationReport::select(
                        'reconcilation_reports.invoice_no',
                        'reconcilation_reports.company_name',
                        'reconcilation_reports.date',
                        'reconcilation_reports.start_date',
                        'reconcilation_reports.end_date',
                        'reconcilation_children.currency',
                        'reconcilation_children.approved_transaction',
                        'reconcilation_children.total_amount_processes',
                        'reconcilation_children.declined_transaction',
                        'reconcilation_children.total_amount_declined',
                        'reconcilation_children.chargebacks',
                        'reconcilation_children.chargebacks_amount',
                        //'reconcilation_children.remove_past_chargebacks',
                        //'reconcilation_children.past_chargebacks_charge_amount',
                        'reconcilation_children.refunds',
                        'reconcilation_children.refunds_amount',
                        'reconcilation_children.total_flagged',
                        'reconcilation_children.total_flagged_amount',
                        'reconcilation_children.remove_past_flagged',
                        'reconcilation_children.past_flagged_charge_amount',
                        //'reconcilation_children.sub_total',
                        'reconcilation_reports.merchant_discount_rate',
                        'reconcilation_children.mdr',
                        'reconcilation_reports.rolling_reserve_paercentage',
                        'reconcilation_children.rolling_reserve',
                        'reconcilation_children.transaction_fee',
                        //'reconcilation_children.total_transaction_fee',
                        'reconcilation_children.declined_fee',
                        //'reconcilation_children.total_declined_fee',
                        'reconcilation_children.chargebacks_fee',
                        //'reconcilation_children.total_chargebacks_fee',
                        'reconcilation_children.refund_fee',
                        //'reconcilation_children.total_refund_fee',
                        'reconcilation_children.flagged_fee',
                        //'reconcilation_children.total_flagged_fee',
                        //'reconcilation_children.transactions_fee_total',
                        'reconcilation_children.total_payout',
                        'reconcilation_children.old_total_payout',
                    )
                    ->join('reconcilation_children', 'reconcilation_children.report_id', 'reconcilation_reports.id')
                    ->orderBy('reconcilation_reports.id', 'desc');
            $data = $data->cursor()
            ->each(function ($data) use ($file) {  
                $data = $data->makeHidden('report_total')->toArray(); 
                fputcsv($file, $data);  
            });

            fclose($file);
        }, 'AllReconcilationExcelReports'.date('d-m-Y').'.csv');
    }
}
