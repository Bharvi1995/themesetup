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

class AdminPayoutSummaryReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    //use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {

        $input = request()->all();

        $pg  = (array_key_exists("page", $input) ? $input['page'] : 1);
        $mid = (int) (array_key_exists("payment_gateway_id", $input) ? $input['payment_gateway_id'] : "0");
        $agent = (int) (array_key_exists("agent_id",
            $input
        )         ? $input['agent_id'] : "0");
        $merchant = (int) (array_key_exists("user_id", $input)   ? $input['user_id'] : "0");
        $start_date =  (array_key_exists("start_date", $input)   ? $input['start_date'] : "");
        $end_date =    (array_key_exists("end_date", $input)   ? $input['end_date'] : "");


        $start_date = ($start_date != "" ? " date_format(str_to_date(   '{$start_date}'  ,'%m/%d/%Y'),'%Y-%m-%d') " : "");
        $end_date   = ($end_date   != "" ? " date_format(str_to_date(   '{$end_date}'    ,'%m/%d/%Y'),'%Y-%m-%d') " : "");

        $payment_gateway_condition  = ($mid > 0     ? " and payment_gateway_id = {$mid} " : "");
        $agent_condition            = ($agent > 0   ? " and agent_id = {$agent} " : "");
        $merchant_condition         = ($merchant > 0   ? " and user_id = {$merchant} " : "");

        if ($start_date == "" && $end_date == "")
        $date_condition             = "t.created_at >= DATE_SUB( Now(), INTERVAL 7 DAY)";
        else
        if ($start_date != "" && $end_date == "")
        $date_condition             = "t.created_at >= DATE_SUB( {$start_date} , INTERVAL 7 DAY)";
        else
        if ($start_date == "" && $end_date != "")
        $date_condition             = "t.created_at >= DATE_SUB( {$end_date} , INTERVAL 7 DAY)";
        else
            $date_condition             = "t.created_at >= {$start_date} and t.created_at <= {$end_date}";

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
        ->whereNull('merchantapplications.deleted_at')
        ->orderBy('company_name')
        ->get();

        $query = <<<SQL
select  t.company_name merchant_name,  t.currency,
    sum(VOLs) VOLs, 	sum(TXs) TXs, 				sum(TXs) / (sum(TXs) +  sum(TXd))  TXsP,
    sum(VOLd) VOLd,     sum(TXd) TXd, 				sum(TXd) / (sum(TXs) +  sum(TXd))  TXdP,
    sum(CBV) CBV, 		sum(CBTX) CBTX, 			(sum(CBTX)  / xDivZ( sum(TXs) ))  CBTXP,
    sum(REFV) REFV, 	sum(REFTX) REFTX, 	        (sum(REFTX) / xDivZ( sum(TXs) ))  REFTXP,
    sum(FLGV) FLGV, 	sum(FLGTX) FLGTX, 		    (sum(FLGTX) / xDivZ( sum(TXs) ))  FLGTXP,
    sum(RETV) RETV,     sum(RETTX) RETTX, 	        (sum(RETTX) / xDivZ( sum(TXs) ))  RETTXP
from tx_payout t
where
	$date_condition
    $payment_gateway_condition
    $agent_condition
    $merchant_condition
group by 1,2
order by 3 desc
SQL;

        $data = \DB::select($query);
        $myArray = json_decode(json_encode($data), true);
        $collect = collect($myArray);
        $mainData = (array) $collect;
        return $mainData;
    }


    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'E' => NumberFormat::FORMAT_PERCENTAGE_00,
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'H' => NumberFormat::FORMAT_PERCENTAGE_00,
            'I' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'K' => NumberFormat::FORMAT_PERCENTAGE_00,
            'L' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'N' => NumberFormat::FORMAT_PERCENTAGE_00,
            'O' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'Q' => NumberFormat::FORMAT_PERCENTAGE_00,
            'R' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'T' => NumberFormat::FORMAT_PERCENTAGE_00
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
