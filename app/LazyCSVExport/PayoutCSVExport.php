<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\PayoutReport;
use App\PayoutReportChild;
use DB;

class PayoutCSVExport 
{   
    public function exportReportById(Request $request){
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
            'Net Settlement Amount'
        ];
        /*$data = PayoutReport::select('payout_reports.invoice_no',
                'payout_reports.company_name',
                'payout_reports.date',
                'payout_reports.start_date',
                'payout_reports.end_date',
                'payout_report_children.currency',
                'payout_report_children.approved_transaction',
                'payout_report_children.total_amount_processes',
                'payout_report_children.declined_transaction',
                'payout_report_children.total_amount_declined',
                'payout_report_children.chargebacks',
                'payout_report_children.chargebacks_amount',
                //'payout_report_children.remove_past_chargebacks',
                //'payout_report_children.past_chargebacks_charge_amount',
                'payout_report_children.refunds',
                'payout_report_children.refunds_amount',
                'payout_report_children.total_flagged',
                'payout_report_children.total_flagged_amount',
                'payout_report_children.remove_past_flagged',
                'payout_report_children.past_flagged_charge_amount',
                //'payout_report_children.sub_total',
                'payout_reports.merchant_discount_rate',
                'payout_report_children.mdr',
                'payout_reports.rolling_reserve_paercentage',
                'payout_report_children.rolling_reserve',
                'payout_report_children.transaction_fee',
                //'payout_report_children.total_transaction_fee',
                'payout_report_children.declined_fee',
                //'payout_report_children.total_declined_fee',
                'payout_report_children.chargebacks_fee',
                //'payout_report_children.total_chargebacks_fee',
                'payout_report_children.refund_fee',
                //'payout_report_children.total_refund_fee',
                'payout_report_children.flagged_fee',
                //'payout_report_children.total_flagged_fee',
                //'payout_report_children.transactions_fee_total',
                'payout_report_children.net_settlement_amount',)
                ->join('payout_report_children', 'payout_report_children.report_id', 'payout_reports.id')
                ->orderBy('payout_reports.id', 'desc')->get();
                dd($data);*/
        return response()->streamDownload(function() use($columns, $input){
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);
            $data = PayoutReport::select(
                    'payout_reports.invoice_no',
                    'payout_reports.company_name',
                    'payout_reports.date',
                    'payout_reports.start_date',
                    'payout_reports.end_date',
                    'payout_report_children.currency',
                    'payout_report_children.approved_transaction',
                    'payout_report_children.total_amount_processes',
                    'payout_report_children.declined_transaction',
                    'payout_report_children.total_amount_declined',
                    'payout_report_children.chargebacks',
                    'payout_report_children.chargebacks_amount',
                    //'payout_report_children.remove_past_chargebacks',
                    //'payout_report_children.past_chargebacks_charge_amount',
                    'payout_report_children.refunds',
                    'payout_report_children.refunds_amount',
                    'payout_report_children.total_flagged',
                    'payout_report_children.total_flagged_amount',
                    'payout_report_children.remove_past_flagged',
                    'payout_report_children.past_flagged_charge_amount',
                    //'payout_report_children.sub_total',
                    'payout_reports.merchant_discount_rate',
                    'payout_report_children.mdr',
                    'payout_reports.rolling_reserve_paercentage',
                    'payout_report_children.rolling_reserve',
                    'payout_report_children.transaction_fee',
                    //'payout_report_children.total_transaction_fee',
                    'payout_report_children.declined_fee',
                    //'payout_report_children.total_declined_fee',
                    'payout_report_children.chargebacks_fee',
                    //'payout_report_children.total_chargebacks_fee',
                    'payout_report_children.refund_fee',
                    //'payout_report_children.total_refund_fee',
                    'payout_report_children.flagged_fee',
                    //'payout_report_children.total_flagged_fee',
                    //'payout_report_children.transactions_fee_total',
                    'payout_report_children.total_payout'
                )
                ->join('payout_report_children', 'payout_report_children.report_id', 'payout_reports.id')
                ->whereIn('payout_reports.id', $input['id'])
                ->orderBy('payout_reports.id', 'desc');
            $data = $data->cursor()
            ->each(function ($data) use ($file) {  
                $data = $data->makeHidden('report_total')->toArray();
                fputcsv($file, $data);  
            });
            fclose($file);
        }, 'PayoutExcel'.date('d-m-Y').'.csv');
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
        ];

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = PayoutReport::select(
                        'payout_reports.invoice_no',
                        'payout_reports.company_name',
                        'payout_reports.date',
                        'payout_reports.start_date',
                        'payout_reports.end_date',
                        'payout_report_children.currency',
                        'payout_report_children.approved_transaction',
                        'payout_report_children.total_amount_processes',
                        'payout_report_children.declined_transaction',
                        'payout_report_children.total_amount_declined',
                        'payout_report_children.chargebacks',
                        'payout_report_children.chargebacks_amount',
                        //'payout_report_children.remove_past_chargebacks',
                        //'payout_report_children.past_chargebacks_charge_amount',
                        'payout_report_children.refunds',
                        'payout_report_children.refunds_amount',
                        'payout_report_children.total_flagged',
                        'payout_report_children.total_flagged_amount',
                        'payout_report_children.remove_past_flagged',
                        'payout_report_children.past_flagged_charge_amount',
                        //'payout_report_children.sub_total',
                        'payout_reports.merchant_discount_rate',
                        'payout_report_children.mdr',
                        'payout_reports.rolling_reserve_paercentage',
                        'payout_report_children.rolling_reserve',
                        'payout_report_children.transaction_fee',
                        //'payout_report_children.total_transaction_fee',
                        'payout_report_children.declined_fee',
                        //'payout_report_children.total_declined_fee',
                        'payout_report_children.chargebacks_fee',
                        //'payout_report_children.total_chargebacks_fee',
                        'payout_report_children.refund_fee',
                        //'payout_report_children.total_refund_fee',
                        'payout_report_children.flagged_fee',
                        //'payout_report_children.total_flagged_fee',
                        //'payout_report_children.transactions_fee_total',
                        'payout_report_children.total_payout',
                    )
                    ->join('payout_report_children', 'payout_report_children.report_id', 'payout_reports.id')
                    ->orderBy('payout_reports.id', 'desc');
            $data = $data->cursor()
            ->each(function ($data) use ($file) {  
                $data = $data->makeHidden('report_total')->toArray(); 
                fputcsv($file, $data);  
            });

            fclose($file);
        }, 'AllPayoutExcelReports'.date('d-m-Y').'.csv');
    }
}
