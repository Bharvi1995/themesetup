<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\AgentPayoutReport;
use App\AgentPayoutReportChild;
use DB;

class AdminAgentPayoutCSVExport 
{   
    public function download(Request $request)
    {
        $input = request()->all();
        $input['id'] = explode(',', $input['id']);
        
        $columns = [
            'Settlement No',
            'Agent Name',
            'Company Name',
            'Created Date',
            'Start Date',
            'End Date',
            'Currency',
            'Approved Count',
            'Approved Amount',
            'Commission Percentage',
            'Total Commission',
        ];

        // $data = AgentPayoutReport::select(
        //     'agent_payout_report.report_no',
        //     'agent_payout_report.agent_name',
        //     'agent_payout_report.company_name',
        //     'agent_payout_report.date',
        //     'agent_payout_report.start_date',
        //     'agent_payout_report.end_date',
        //     'agent_payout_report_child.currency',
        //     'agent_payout_report_child.success_count',
        //     'agent_payout_report_child.success_amount',
        //     'agent_payout_report_child.commission_percentage',
        //     'agent_payout_report_child.total_commission',
        // )
        // ->join('agent_payout_report_child', 'agent_payout_report_child.report_id', 'agent_payout_report.id')
        // ->whereIn('report_id', $input['id'])
        // ->orderBy('report_id', 'desc')
        // ->get();

        // dd($data);

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = AgentPayoutReport::select(
                'agent_payout_report.report_no',
                'agent_payout_report.agent_name',
                'agent_payout_report.company_name',
                'agent_payout_report.date',
                'agent_payout_report.start_date',
                'agent_payout_report.end_date',
                'agent_payout_report_child.currency',
                'agent_payout_report_child.success_count',
                'agent_payout_report_child.success_amount',
                'agent_payout_report_child.commission_percentage',
                'agent_payout_report_child.total_commission',
            )
            ->join('agent_payout_report_child', 'agent_payout_report_child.report_id', 'agent_payout_report.id')
            ->whereIn('report_id', $input['id'])
            ->orderBy('report_id', 'desc');
           
            $data = $data->cursor()
            ->each(function ($data) use ($file) {  
                $data = $data->toArray();
                fputcsv($file, $data);  
            });

            fclose($file);
        }, 'AgentPayoutExcel'.date('d-m-Y').'.csv');
    }

    public function allDownload(Request $request)
    {
        $input = request()->all();

        $columns = [
            'Settlement No',
            'Agent Name',
            'Company Name',
            'Created Date',
            'Start Date',
            'End Date',
            'Currency',
            'Approved Count',
            'Approved Amount',
            'Commission Percentage',
            'Total Commission',
        ];

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = AgentPayoutReport::select(
                'agent_payout_report.report_no',
                'agent_payout_report.agent_name',
                'agent_payout_report.company_name',
                'agent_payout_report.date',
                'agent_payout_report.start_date',
                'agent_payout_report.end_date',
                'agent_payout_report_child.currency',
                'agent_payout_report_child.success_count',
                'agent_payout_report_child.success_amount',
                'agent_payout_report_child.commission_percentage',
                'agent_payout_report_child.total_commission',
            )
            ->join('agent_payout_report_child', 'agent_payout_report_child.report_id', 'agent_payout_report.id')
            ->orderBy('report_id', 'desc');
           
            $data = $data->cursor()
            ->each(function ($data) use ($file) {  
                $data = $data->toArray();
                fputcsv($file, $data);  
            });

            fclose($file);
        }, 'AllAgentPayoutExcelReports'.date('d-m-Y').'.csv');
    }
}
