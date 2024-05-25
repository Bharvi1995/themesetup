<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\UserGenerateReport;
use App\UserGenerateReportChild;
use DB;

class AdminPayoutCSVExport 
{   
    public function download(Request $request)
    {
        $input = request()->all();
        $input['id'] = explode(',', $input['id']);

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
            // 'Total Attempts Count',
            // 'Total Attempts Amount',
            'Chargebacks Count',
            'Chargebacks Amount',
            'Remomve Chargebacks Count',
            'Remomve Chargebacks Fee',
            'Refunds Count',
            'Refunds Amount',
            'Flagged Count',
            'Flagged Amount',
            'Remove Flagged Count',
            'Remove Flagged Fee',
            'Total Settlement',
            'Merchant Discount Rate',
            'Merchant Discount Amount',
            'Rolling Reserve (180 Days)',
            'Rolling Reserve Amount',
            'Approved Transaction Fee Rate',
            'Approved Transaction Fee Amount',
            'Declined Transaction Fee Rate',
            'Declined Transaction Fee Amount',
            'Chargebacks Transaction Fee Rate',
            'Chargebacks Transaction Fee Amount',
            'Refunds Transaction Fee Rate',
            'Refunds Transaction Fee Amount',
            'Flagged Transaction Fee Rate',
            'Flagged Transaction Fee Amount',
            'Total Transaction Fee Amount',
            'Net Settlement Amount',
        ];

        /*$data = UserGenerateReport::select(
            'usergeneratereports.invoice_no',
            'usergeneratereports.company_name',
            'usergeneratereports.date',
            'usergeneratereports.start_date',
            'usergeneratereports.end_date',
            'usergeneratereportschild.currency',
            'usergeneratereportschild.approved_transaction',
            'usergeneratereportschild.total_amount_processes',
            'usergeneratereportschild.declined_transaction',
            'usergeneratereportschild.total_amount_declined',
            // \DB::raw('(SUM(usergeneratereportschild.approved_transaction + usergeneratereportschild.declined_transaction)) as total_attempts_count')
            'usergeneratereportschild.chargebacks',
            'usergeneratereportschild.chargebacks_amount',
            'usergeneratereportschild.remove_past_chargebacks',
            'usergeneratereportschild.past_chargebacks_charge_amount',
            'usergeneratereportschild.refunds',
            'usergeneratereportschild.refunds_amount',
            'usergeneratereportschild.total_flagged',
            'usergeneratereportschild.total_flagged_amount',
            'usergeneratereportschild.remove_past_flagged',
            'usergeneratereportschild.past_flagged_charge_amount',
            'usergeneratereportschild.sub_total',
            'usergeneratereports.merchant_discount_rate',
            'usergeneratereportschild.mdr',
            'usergeneratereports.rolling_reserve_paercentage',
            'usergeneratereportschild.rolling_reserve',
            'usergeneratereportschild.transaction_fee',
            'usergeneratereportschild.total_transaction_fee',
            'usergeneratereportschild.declined_fee',
            'usergeneratereportschild.total_declined_fee',
            'usergeneratereportschild.chargebacks_fee',
            'usergeneratereportschild.total_chargebacks_fee',
            'usergeneratereportschild.refund_fee',
            'usergeneratereportschild.total_refund_fee',
            'usergeneratereportschild.flagged_fee',
            'usergeneratereportschild.total_flagged_fee',
            'usergeneratereportschild.transactions_fee_total',
            'usergeneratereportschild.net_settlement_amount',
            // 'usergeneratereportschild.*'
        )
        ->join('usergeneratereportschild', 'usergeneratereportschild.report_id', 'usergeneratereports.id')
        ->whereIn('report_id', $input['id'])
        ->orderBy('report_id', 'desc')
        ->get();
        dd($data);*/
        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = UserGenerateReport::select(
                'usergeneratereports.invoice_no',
                'usergeneratereports.company_name',
                'usergeneratereports.date',
                'usergeneratereports.start_date',
                'usergeneratereports.end_date',
                'usergeneratereportschild.currency',
                'usergeneratereportschild.approved_transaction',
                'usergeneratereportschild.total_amount_processes',
                'usergeneratereportschild.declined_transaction',
                'usergeneratereportschild.total_amount_declined',
                // \DB::raw('(SUM(usergeneratereportschild.approved_transaction + usergeneratereportschild.declined_transaction)) as total_attempts_count')
                'usergeneratereportschild.chargebacks',
                'usergeneratereportschild.chargebacks_amount',
                'usergeneratereportschild.remove_past_chargebacks',
                'usergeneratereportschild.past_chargebacks_charge_amount',
                'usergeneratereportschild.refunds',
                'usergeneratereportschild.refunds_amount',
                'usergeneratereportschild.total_flagged',
                'usergeneratereportschild.total_flagged_amount',
                'usergeneratereportschild.remove_past_flagged',
                'usergeneratereportschild.past_flagged_charge_amount',
                'usergeneratereportschild.sub_total',
                'usergeneratereports.merchant_discount_rate',
                'usergeneratereportschild.mdr',
                'usergeneratereports.rolling_reserve_paercentage',
                'usergeneratereportschild.rolling_reserve',
                'usergeneratereportschild.transaction_fee',
                'usergeneratereportschild.total_transaction_fee',
                'usergeneratereportschild.declined_fee',
                'usergeneratereportschild.total_declined_fee',
                'usergeneratereportschild.chargebacks_fee',
                'usergeneratereportschild.total_chargebacks_fee',
                'usergeneratereportschild.refund_fee',
                'usergeneratereportschild.total_refund_fee',
                'usergeneratereportschild.flagged_fee',
                'usergeneratereportschild.total_flagged_fee',
                'usergeneratereportschild.transactions_fee_total',
                'usergeneratereportschild.net_settlement_amount',
            )
            ->join('usergeneratereportschild', 'usergeneratereportschild.report_id', 'usergeneratereports.id')
            ->whereIn('report_id', $input['id'])
            ->orderBy('report_id', 'desc');
           
            $data = $data->cursor()
            ->each(function ($data) use ($file) {  
                $data = $data->toArray();
                fputcsv($file, $data);  
            });

            fclose($file);
        }, 'PayoutExcel'.date('d-m-Y').'.csv');
    }

    public function allDownload(Request $request)
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
            // 'Total Attempts Count',
            // 'Total Attempts Amount',
            'Chargebacks Count',
            'Chargebacks Amount',
            'Remomve Chargebacks Count',
            'Remomve Chargebacks Fee',
            'Refunds Count',
            'Refunds Amount',
            'Flagged Count',
            'Flagged Amount',
            'Remove Flagged Count',
            'Remove Flagged Fee',
            'Total Settlement',
            'Merchant Discount Rate',
            'Merchant Discount Amount',
            'Rolling Reserve (180 Days)',
            'Rolling Reserve Amount',
            'Approved Transaction Fee Rate',
            'Approved Transaction Fee Amount',
            'Declined Transaction Fee Rate',
            'Declined Transaction Fee Amount',
            'Chargebacks Transaction Fee Rate',
            'Chargebacks Transaction Fee Amount',
            'Refunds Transaction Fee Rate',
            'Refunds Transaction Fee Amount',
            'Flagged Transaction Fee Rate',
            'Flagged Transaction Fee Amount',
            'Total Transaction Fee Amount',
            'Net Settlement Amount',
        ];

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = UserGenerateReport::select(
                'usergeneratereports.invoice_no',
                'usergeneratereports.company_name',
                'usergeneratereports.date',
                'usergeneratereports.start_date',
                'usergeneratereports.end_date',
                'usergeneratereportschild.currency',
                'usergeneratereportschild.approved_transaction',
                'usergeneratereportschild.total_amount_processes',
                'usergeneratereportschild.declined_transaction',
                'usergeneratereportschild.total_amount_declined',
                // \DB::raw('(SUM(usergeneratereportschild.approved_transaction + usergeneratereportschild.declined_transaction)) as total_attempts_count')
                'usergeneratereportschild.chargebacks',
                'usergeneratereportschild.chargebacks_amount',
                'usergeneratereportschild.remove_past_chargebacks',
                'usergeneratereportschild.past_chargebacks_charge_amount',
                'usergeneratereportschild.refunds',
                'usergeneratereportschild.refunds_amount',
                'usergeneratereportschild.total_flagged',
                'usergeneratereportschild.total_flagged_amount',
                'usergeneratereportschild.remove_past_flagged',
                'usergeneratereportschild.past_flagged_charge_amount',
                'usergeneratereportschild.sub_total',
                'usergeneratereports.merchant_discount_rate',
                'usergeneratereportschild.mdr',
                'usergeneratereports.rolling_reserve_paercentage',
                'usergeneratereportschild.rolling_reserve',
                'usergeneratereportschild.transaction_fee',
                'usergeneratereportschild.total_transaction_fee',
                'usergeneratereportschild.declined_fee',
                'usergeneratereportschild.total_declined_fee',
                'usergeneratereportschild.chargebacks_fee',
                'usergeneratereportschild.total_chargebacks_fee',
                'usergeneratereportschild.refund_fee',
                'usergeneratereportschild.total_refund_fee',
                'usergeneratereportschild.flagged_fee',
                'usergeneratereportschild.total_flagged_fee',
                'usergeneratereportschild.transactions_fee_total',
                'usergeneratereportschild.net_settlement_amount',
            )
            ->join('usergeneratereportschild', 'usergeneratereportschild.report_id', 'usergeneratereports.id')
            ->orderBy('report_id', 'desc');
           
            $data = $data->cursor()
            ->each(function ($data) use ($file) {  
                $data = $data->toArray();
                fputcsv($file, $data);  
            });

            fclose($file);
        }, 'AllPayoutExcelReports'.date('d-m-Y').'.csv');
    }
}
