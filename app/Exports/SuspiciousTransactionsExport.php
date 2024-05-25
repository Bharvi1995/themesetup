<?php

namespace App\Exports;

use DB;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Crypt;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SuspiciousTransactionsExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithCustomValueBinder, WithMapping
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
        $payment_gateway = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];
        $data = DB::table("transactions");
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));
        }
        $finalId = [];
        if(isset($input['include_email']) && $input['include_email'] == 'yes' && isset($input['nos_email']) && $input['nos_email'] > 0 && isset($input['include_card']) && $input['include_card'] == 'yes' && isset($input['nos_card']) && $input['nos_card'] > 0){
            
            $data = DB::table("transactions as t")->select('t.*','t.card_no',"t.order_id","t.amount","t.currency","t.transaction_date","m.bank_name","t.email","a.business_name", DB::raw('COUNT(t.card_no) as card_no_count'), DB::raw('COUNT(t.email) as email_count'))
                    ->leftjoin("middetails as m","m.id","=","t.payment_gateway_id")
                    ->leftjoin("applications as a","a.user_id","=","t.user_id")
                    ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
                    ->whereNotIn('t.payment_gateway_id', $payment_gateway)
                    ->where(["t.is_flagged"=>"0","t.is_flagged_remove"=>"0","t.refund"=>"0","t.refund_remove"=>"0","t.chargebacks"=>"0","t.chargebacks_remove"=>"0","t.is_retrieval"=>"0","t.is_retrieval_remove"=>"0"])
                    ->where('t.status', '1');

            if(isset($input['country']) && $input['country'] != '') {
                $data = $data->where('t.country', $input['country']);
            }
            if(isset($input['currency']) && $input['currency'] != '') {
                $data = $data->where('t.currency', $input['currency']);
            }
            if(isset($input['gateway_id']) && $input['gateway_id'] != '') {
                $data = $data->where('t.gateway_id', $input['gateway_id']);
            }
            if(isset($input['greater_then']) && $input['greater_then'] != '') {
                $data = $data->where('t.amount', '>=', $input['greater_then']);
            }
            if(isset($input['less_then']) && $input['less_then'] != '') {
                $data = $data->where('t.amount', '<=', $input['less_then']);
            }
            if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                $data = $data->where('t.payment_gateway_id', $input['payment_gateway_id']);
            }
            if(isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('t.user_id', $input['user_id']);
            }

            $data = $data->groupBy('t.card_no', 't.email')
                    ->having('card_no_count', '>=', $input['nos_card'])
                    ->having('email_count', '>=', $input['nos_email']);
            $data = $data->get();
            return $data;

        }
        else if( isset($input['include_card']) && $input['include_card'] == 'yes' && isset($input['nos_card']) && $input['nos_card'] > 0){
            $data = DB::table("transactions as t")->select('t.*','t.card_no',"t.order_id","t.amount","t.currency","t.transaction_date","m.bank_name","t.email","a.business_name", DB::raw('COUNT(t.card_no) as card_no_count'))
                    ->leftjoin("middetails as m","m.id","=","t.payment_gateway_id")
                    ->leftjoin("applications as a","a.user_id","=","t.user_id")
                    ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
                    ->whereNotIn('t.payment_gateway_id', $payment_gateway)
                    ->where(["t.is_flagged"=>"0","t.is_flagged_remove"=>"0","t.refund"=>"0","t.refund_remove"=>"0","t.chargebacks"=>"0","t.chargebacks_remove"=>"0","t.is_retrieval"=>"0","t.is_retrieval_remove"=>"0"])
                    ->where('t.status', '1');

            if(isset($input['country']) && $input['country'] != '') {
                $data = $data->where('t.country', $input['country']);
            }
            if(isset($input['currency']) && $input['currency'] != '') {
                $data = $data->where('t.currency', $input['currency']);
            }
            if(isset($input['gateway_id']) && $input['gateway_id'] != '') {
                $data = $data->where('t.gateway_id', $input['gateway_id']);
            }
            if(isset($input['greater_then']) && $input['greater_then'] != '') {
                $data = $data->where('t.amount', '>=', $input['greater_then']);
            }
            if(isset($input['less_then']) && $input['less_then'] != '') {
                $data = $data->where('t.amount', '<=', $input['less_then']);
            }
            if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                $data = $data->where('t.payment_gateway_id', $input['payment_gateway_id']);
            }
            if(isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('t.user_id', $input['user_id']);
            }

            $data = $data->groupBy('t.card_no')->having('card_no_count', '>=', $input['nos_card']);
            $data = $data->get();
            return $data;
        }
        else if( isset($input['include_email']) && $input['include_email'] == 'yes' && isset($input['nos_email']) && $input['nos_email'] > 0){
            $data = DB::table("transactions as t")->select('t.*','t.card_no',"t.order_id","t.amount","t.currency","t.transaction_date","m.bank_name","t.email","a.business_name", DB::raw('COUNT(t.email) as email_count'))
                    ->leftjoin("middetails as m","m.id","=","t.payment_gateway_id")
                    ->leftjoin("applications as a","a.user_id","=","t.user_id")
                    ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
                    ->whereNotIn('t.payment_gateway_id', $payment_gateway)
                    ->where(["t.is_flagged"=>"0","t.is_flagged_remove"=>"0","t.refund"=>"0","t.refund_remove"=>"0","t.chargebacks"=>"0","t.chargebacks_remove"=>"0","t.is_retrieval"=>"0","t.is_retrieval_remove"=>"0"])
                    ->where('t.status', '1');

            if(isset($input['country']) && $input['country'] != '') {
                $data = $data->where('t.country', $input['country']);
            }
            if(isset($input['currency']) && $input['currency'] != '') {
                $data = $data->where('t.currency', $input['currency']);
            }
            if(isset($input['gateway_id']) && $input['gateway_id'] != '') {
                $data = $data->where('t.gateway_id', $input['gateway_id']);
            }
            if(isset($input['greater_then']) && $input['greater_then'] != '') {
                $data = $data->where('t.amount', '>=', $input['greater_then']);
            }
            if(isset($input['less_then']) && $input['less_then'] != '') {
                $data = $data->where('t.amount', '<=', $input['less_then']);
            }
            if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                $data = $data->where('t.payment_gateway_id', $input['payment_gateway_id']);
            }
            if(isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('t.user_id', $input['user_id']);
            }

            $data = $data->groupBy('t.email')->having('email_count', '>=', $input['nos_email']);
            $data = $data->get();
            return $data;
        }
        else{
                $data = DB::table("transactions as t")->select('t.*','t.card_no',"t.order_id","t.amount","t.currency","t.transaction_date","m.bank_name","t.email","a.business_name")
                    ->leftjoin("middetails as m","m.id","=","t.payment_gateway_id")
                    ->leftjoin("applications as a","a.user_id","=","t.user_id")
                    ->where(DB::raw('DATE(t.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(t.created_at)'), '<=', $end_date)
                    ->whereNotIn('t.payment_gateway_id', $payment_gateway)
                    ->where(["t.is_flagged"=>"0","t.is_flagged_remove"=>"0","t.refund"=>"0","t.refund_remove"=>"0","t.chargebacks"=>"0","t.chargebacks_remove"=>"0","t.is_retrieval"=>"0","t.is_retrieval_remove"=>"0"])
                    ->where('t.status', '1');
                if(isset($input['country']) && $input['country'] != '') {
                    $data = $data->where('t.country', $input['country']);
                }
                if(isset($input['currency']) && $input['currency'] != '') {
                    $data = $data->where('t.currency', $input['currency']);
                }
                if(isset($input['gateway_id']) && $input['gateway_id'] != '') {
                    $data = $data->where('t.gateway_id', $input['gateway_id']);
                }
                if(isset($input['greater_then']) && $input['greater_then'] != '') {
                    $data = $data->where('t.amount', '>=', $input['greater_then']);
                }
                if(isset($input['less_then']) && $input['less_then'] != '') {
                    $data = $data->where('t.amount', '<=', $input['less_then']);
                }
                if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $data = $data->where('t.payment_gateway_id', $input['payment_gateway_id']);
                }
                if(isset($input['user_id']) && $input['user_id'] != '') {
                    $data = $data->where('t.user_id', $input['user_id']);
                }
                $data = $data->get();
                return $data;
            }
    }

    public function headings(): array{
        return [
            'Order ID',
            'Company Name',
            'First Name',
            'Last Name',
            'Address',
            'Country',
            'State',
            'City',
            'Zip',
            'Email',
            'Phone No.',
            'Card Type',
            'Amount',
            'Currency',
            'Card No.',
            'Expiry Month',
            'Expiry Year',
            'Status',
            'Reason',
            'Chargebacks',
            'Refund',
            'Transaction Date'
        ];
    }

    public function map($transaction): array{
        if($transaction->status == '1') {
            $transaction->status = 'Success';
        } elseif($transaction->status == '2') {
            $transaction->status = 'Pending';
        } elseif($transaction->status == '3') {
            $transaction->status = 'Canceled';
        } elseif($transaction->status == '4') {
            $transaction->status = 'To Be Confirm';
        } else {
            $transaction->status = 'Declined';
        }
        if($transaction->card_type == '1')
            $transaction->card_type = 'Amex';
        elseif($transaction->card_type == '2')
            $transaction->card_type = 'Visa';
        elseif($transaction->card_type == '3')
            $transaction->card_type = 'Master Card';
        elseif($transaction->card_type == '3')
            $transaction->card_type = 'Discover';
        else
            $transaction->card_type = '';

        if($transaction->chargebacks == '1')
            $transaction->chargebacks = 'Yes';
        else
            $transaction->chargebacks = 'No';

        if($transaction->refund == '1')
            $transaction->refund = 'Yes';
        else
            $transaction->refund = 'No';

        if($transaction->is_converted == '1')
            $transaction->amount = $transaction->amount.'-'.$transaction->converted_amount;
        elseif($transaction->is_converted_user_currency == '1')
            $transaction->amount = $transaction->amount.'-'.$transaction->converted_user_amount;
        else
            $transaction->amount = $transaction->amount;

        if($transaction->is_converted == '1')
            $transaction->currency = $transaction->currency.'-'.$transaction->converted_currency;
        elseif($transaction->is_converted_user_currency == '1')
            $transaction->currency = $transaction->currency.'-'.$transaction->converted_user_currency;
        else
            $transaction->currency = $transaction->currency;

        if (strlen($transaction->card_no) > 4)
            $transaction->card_no = substr($transaction->card_no, 0, 6) . 'XXXXXX' . substr($transaction->card_no, -4, 4);
        else
            $transaction->card_no = $transaction->card_no;
        return [
            $transaction->order_id,
            $transaction->business_name,
            $transaction->first_name,
            $transaction->last_name,
            $transaction->address,
            $transaction->country,
            $transaction->state,
            $transaction->city,
            $transaction->zip,
            $transaction->email,
            $transaction->phone_no,
            $transaction->card_type,
            $transaction->amount,
            $transaction->currency,
            $transaction->card_no,
            $transaction->ccExpiryMonth,
            $transaction->ccExpiryYear,
            $transaction->status,
            $transaction->reason,
            $transaction->chargebacks,
            $transaction->refund,
            convertDateToLocal($transaction->created_at, 'd-m-Y H:i:s'),
        ];
    }
}
