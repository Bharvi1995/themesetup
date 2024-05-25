<?php

namespace App\Exports;

use DB;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class GenerateReportExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithCustomValueBinder, WithMapping
{
    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * Value binding
     * https://docs.laravel-excel.com/3.1/exports/column-formatting.html#value-binders
     */
    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() == 'A') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        if ($cell->getColumn() == 'J') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){

        $input = request()->all();
        $data = DB::table("payout_reports");
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

        
        // if(isset($input['start_date']) && $input['start_date'] != '') {
        //     $from_date = Carbon::parse($input['start_date']);
        //     $newFfromDate = $from_date->format('d/m/Y');
        //     $data = $data->where('payout_reports.start_date', $newFfromDate);
        // }
        // if(isset($input['end_date']) && $input['end_date'] != '') {
        //     $from_date = Carbon::parse($input['end_date']);
        //     $newToDate = $from_date->format('d/m/Y');
        //     $data = $data->where('payout_reports.end_date', $newToDate);
        // }
        if(isset($input["ids"]) && !empty($input["ids"])){
            $data = $data->whereIn("payout_reports.id",$input["ids"]);
        }   
        $data = $data->orderBy("payout_reports.id","DESC")->get();
        return $data;
    }

    public function headings(): array{
        return [
            'Invoice No',
            'Processor Name',
            'Company Name',
            'Address',
            'Phone No',
            'Date',
            'Merchant Discount rate',
            'Rolling Reserve Percentage',
            'Transaction Fee',
            'Refund Fee',
            'Chargeback Fee',
            'Flagged Fee',
            'Wire Fee',
            'Start Date',
            'End Date',
            'Chargeback Start Date',
            'Chargeback End Date'
        ];
    }

    public function map($transaction): array{
        return [
            $transaction->invoice_no,
            $transaction->processor_name,
            $transaction->company_name,
            $transaction->address,
            $transaction->phone_no,
            $transaction->date,
            $transaction->merchant_discount_rate,
            $transaction->rolling_reserve_paercentage,
            $transaction->transaction_fee_paercentage,
            $transaction->refund_fee_paercentage,
            $transaction->chargebacks_fee_paercentage,
            $transaction->flagged_fee_paercentage,
            $transaction->wire_fee,
            $transaction->start_date,
            $transaction->end_date,
            $transaction->chargebacks_start_date,
            $transaction->chargebacks_end_date
        ];
    }
}
