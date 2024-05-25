<?php

namespace App\Exports;

use App\Transaction;
use DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class AdminTransactionSummaryReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {

        $input = request()->all();

        $pg  = (array_key_exists("page", $input) ? $input['page'] : 1);
        //$mid = (int) (array_key_exists("payment_gateway_id", $input) ? $input['payment_gateway_id'] : "0");
        $grp = (array_key_exists("grp", $input)         ? $input['grp'] : "Daily");
        $merchant = (int) (array_key_exists("user_id", $input)   ? $input['user_id'] : "0");
        $start_date =  (array_key_exists("start_date", $input)   ? $input['start_date'] : "");
        $end_date =    (array_key_exists("end_date", $input)   ? $input['end_date'] : "");
        //dd($input);


        $start_date = ($start_date != "" ? " date_format(str_to_date(   '{$start_date}'  ,'%m/%d/%Y'),'%Y-%m-%d') " : "");
        $end_date   = ($end_date   != "" ? " date_format(str_to_date(   '{$end_date}'    ,'%m/%d/%Y'),'%Y-%m-%d') " : "");

        // $payment_gateway_condition  = ($mid > 0     ? " and payment_gateway_id = {$mid} " : "");
        // $grp_condition              = ($grp > 0     ? " and grp = {$grp} " : "");
        $merchant_condition         = ($merchant > 0   ? " and user_id = {$merchant} " : "");



        if ($grp == null || $grp == '') $grp = 'Daily';

        $grp_select = '';
        $group_by = " group by 1,2 ";
        $ndays = 7;

        if ($grp == 'Daily') {
            $grp_select = " min(t.created_at) grp, min(DATE_FORMAT(t.created_at , '%Y-%m-%d')) date_start, max(DATE_FORMAT(t.created_at , '%Y-%m-%d')) date_end, ";
            $ndays = 1;
            $group_by = " group by 1 ";
        }
        if ($grp == 'Weekly') {
            $grp_select = " YEARWEEK(t.created_at, 1) grp, min(DATE_FORMAT(t.created_at , '%Y-%m-%d')) date_start, max(DATE_FORMAT(t.created_at , '%Y-%m-%d')) date_end, ";
            $ndays = 7;
        }
        if ($grp == 'Monthly') {
            $grp_select = " MONTH(t.created_at) grp, min(DATE_FORMAT(t.created_at , '%Y-%m')) date_start, max(DATE_FORMAT(t.created_at , '%Y-%m')) date_end, ";
            $ndays = 30;
        }

        if ($start_date == "" && $end_date == "")
        $date_condition             = "t.created_at >= DATE_SUB( Now(), INTERVAL " . $ndays . " DAY)";
        else
        if ($start_date != "" && $end_date == "")
        $date_condition             = "t.created_at >= DATE_SUB( {$start_date} , INTERVAL " . $ndays . " DAY)";
        else
        if ($start_date == "" && $end_date != "")
        $date_condition             = "t.created_at >= DATE_SUB( {$end_date} , INTERVAL " . $ndays . " DAY)";
        else
        $date_condition             = "t.created_at >= {$start_date} and t.created_at <= {$end_date}";

        if ($grp == 'Monthly')
        $date_condition = $date_condition . ' and MONTH(t.created_at) = MONTH(CURRENT_DATE())';

        // if (isset($input['type']) && $input['type'] == 'xlsx') {
        //     return Excel::download(new AdminPayoutSummaryReportExport, 'Trx_Summary_Report_Excel_' . date('d-m-Y') . '.xlsx');
        // }




        $payment_gateway_id = \DB::table('middetails')
        ->orderBy('bank_name', 'asc')
        ->get();

        $agent_names = \DB::table('agents')
        ->orderBy('name', 'asc')
        ->get();

        $company_names = \DB::table('merchantapplications')
        ->select('merchantapplications.company_name', 'merchantapplications.user_id', 'merchantapplications.id')
        ->join('users', function ($join) use ($input) {
            $join->on('users.id', '=', 'merchantapplications.user_id')
            ->where('users.main_user_id', '0');
        })
        ->orderBy('company_name')
        ->get();

        $query1 = 'select  t.currency, ';
        $query2 = <<<SQL
    sum(VOLs) VOLs,     sum(TXs) TXs,               sum(TXs) / (sum(TXs) +  sum(TXd))  TXsP,
    sum(VOLd) VOLd,     sum(TXd) TXd,               sum(TXd) / (sum(TXs) +  sum(TXd))  TXdP,
    sum(CBV) CBV,       sum(CBTX) CBTX,             sum(CBTX)  / xDivZ( sum(TXs) ) CBTXP,
    sum(REFV) REFV,     sum(REFTX) REFTX,           sum(REFTX) / xDivZ( sum(TXs) ) REFTXP,
    sum(FLGV) FLGV,     sum(FLGTX) FLGTX,           sum(FLGTX) / xDivZ( sum(TXs) ) FLGTXP,
    sum(RETV) RETV,     sum(RETTX) RETTX,           sum(RETTX) / xDivZ( sum(TXs) ) RETTXP
from tx_payout t
where
    $date_condition
    $merchant_condition
    $group_by
order by 2 asc
SQL;

        // $query = $query1 . ' ' . $grp_select . ' ' . $query2;

        $query = $query1 . ' ' . $grp_select . ' ' . $query2;

        // echo '<pre>';
        // dd($query);
        // echo '</pre>';

        $data = \DB::select($query);
        $myArray = json_decode(json_encode($data), true);
        $collect = collect($myArray);
        $mainData = (array) $collect;
        return $mainData;
    }


    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_PERCENTAGE_00,
            'H' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'J' => NumberFormat::FORMAT_PERCENTAGE_00,
            'K' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'M' => NumberFormat::FORMAT_PERCENTAGE_00,
            'N' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'P' => NumberFormat::FORMAT_PERCENTAGE_00,
            'Q' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'S' => NumberFormat::FORMAT_PERCENTAGE_00,
            'T' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'V' => NumberFormat::FORMAT_PERCENTAGE_00
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:T1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

    public function headings(): array
    {
        return [
            'Currency',
            'Date',
            'Date Start',
            'Date End',
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
