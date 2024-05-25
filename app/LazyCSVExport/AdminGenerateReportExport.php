<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\PayoutReports;
use DB;
use Carbon\Carbon;

class AdminGenerateReportExport
{
    
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function download(Request $request)
    {
        $input = $request->all();
        
        $columns = [
            'Invoice No',
            'Processor Name',
            'Company Name',
            'Date',
            'Currency',
            'Card Type',
            'Successful transaction Count',
            'Successful transaction Total',
            'Declined transaction Count',
            'Declined transaction Total',
            'Refund transaction Count',
            'Refund transaction Total',
            'Chargeback transaction Count',
            'Chargeback transaction Total',
            'Suspicious transaction Count',
            'Suspicious transaction Total',
            'Total Payout',
            'Start Date',
            'End Date',
            'Chargeback start Date',
            'Chargeback end Date'
        ];
        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);
            $data = PayoutReports::select('payout_reports.invoice_no',
                            'payout_reports.processor_name',
                            "payout_reports.company_name",
                            'payout_reports.date',
                            'payout_report_child.currency',
                            \DB::raw('(CASE
                                WHEN payout_report_child.card_type = "Other" THEN "Visa"
                                ELSE "MasterCard"
                                END) AS CardType'),
                            //"payout_report_child.card_type",
                            "payout_report_child.approve_transaction_count",
                            "payout_report_child.approve_transaction_sum",
                            "payout_report_child.declined_transaction_count",
                            "payout_report_child.declined_transaction_sum",
                            "payout_report_child.refund_transaction_count",
                            "payout_report_child.refund_transaction_sum",
                            "payout_report_child.chargeback_transaction_count",
                            "payout_report_child.chargeback_transaction_sum",
                            "payout_report_child.flagged_transaction_count",
                            "payout_report_child.flagged_transaction_sum",
                            'payout_report_child.net_settlement_amount',
                            'payout_reports.start_date',
                            'payout_reports.end_date',
                            'payout_reports.chargebacks_start_date',
                            'payout_reports.chargebacks_end_date'
                    )
                ->join("payout_report_child","payout_report_child.payoutreport_id","payout_reports.id");
            if(isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('payout_reports.user_id', $input['user_id']);
            }
            if(isset($input['show_client_side']) && $input['show_client_side'] != '') {
                $data = $data->where('payout_reports.show_client_side', '1');
            }
            if(isset($input['status']) && $input['status'] != '') {
                $data = $data->where('payout_reports.status', $input['status']);
            }
            if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                $end_date = Carbon::parse($input['end_date']);
                $newToDate = $end_date->format('Y-m-d');
                $from_date = Carbon::parse($input['start_date']);
                $newfromDate = $from_date->format('Y-m-d');
                $data = $data->where(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), '>=', $newfromDate)->where(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), '<=', $newToDate);
            } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
                $from_date = Carbon::parse($input['start_date']);
                $newfromDate = $from_date->format('Y-m-d');
                $data = $data->where(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), '>=', $newfromDate);
            } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
                $end_date = Carbon::parse($input['end_date']);
                $newToDate = $end_date->format('Y-m-d');
                $data = $data->where(DB::raw("STR_TO_DATE(date, '%d/%m/%Y')"), '<=', $newToDate);
            }
            if(isset($input["ids"]) && !empty($input["ids"])){
                $data = $data->whereIn("payout_reports.id",$input["ids"]);
            }
            $data = $data->orderBy('payout_reports.id', 'DESC');
            $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                fputcsv($file, $data);
            });
            fclose($file);
        }, 'GenerateReport_Excel_'.date('d-m-Y').'.csv');
    }
}
