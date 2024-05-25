<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\PayoutReportsRP;
use DB;
use Carbon\Carbon;

class AdminGenerateRPReportExport
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
            $data = PayoutReportsRP::select('payout_reports_rp.invoice_no',
                            'payout_reports_rp.processor_name',
                            "payout_reports_rp.company_name",
                            'payout_reports_rp.date',
                            'payout_reports_child_rp.currency',
                            \DB::raw('(CASE
                                WHEN payout_reports_child_rp.card_type = "Other" THEN "Visa"
                                ELSE "MasterCard"
                                END) AS CardType'),
                            //"payout_reports_child_rp.card_type",
                            "payout_reports_child_rp.approve_transaction_count",
                            "payout_reports_child_rp.approve_transaction_sum",
                            "payout_reports_child_rp.declined_transaction_count",
                            "payout_reports_child_rp.declined_transaction_sum",
                            "payout_reports_child_rp.refund_transaction_count",
                            "payout_reports_child_rp.refund_transaction_sum",
                            "payout_reports_child_rp.chargeback_transaction_count",
                            "payout_reports_child_rp.chargeback_transaction_sum",
                            "payout_reports_child_rp.flagged_transaction_count",
                            "payout_reports_child_rp.flagged_transaction_sum",
                            'payout_reports_child_rp.net_settlement_amount',
                            'payout_reports_rp.start_date',
                            'payout_reports_rp.end_date',
                            'payout_reports_rp.chargebacks_start_date',
                            'payout_reports_rp.chargebacks_end_date'
                    )
                ->join("payout_reports_child_rp","payout_reports_child_rp.payoutreport_id","payout_reports_rp.id");
            if(isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('payout_reports_rp.user_id', $input['user_id']);
            }
            if(isset($input['show_client_side']) && $input['show_client_side'] != '') {
                $data = $data->where('payout_reports_rp.show_client_side', '1');
            }
            if(isset($input['status']) && $input['status'] != '') {
                $data = $data->where('payout_reports_rp.status', $input['status']);
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
                $data = $data->whereIn("payout_reports_rp.id",$input["ids"]);
            }
            $data = $data->orderBy('payout_reports_rp.id', 'DESC');
            $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                fputcsv($file, $data);
            });
            fclose($file);
        }, 'GenerateRPReport_Excel_'.date('d-m-Y').'.csv');
    }
}
