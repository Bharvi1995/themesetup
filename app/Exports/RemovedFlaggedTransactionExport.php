<?php

namespace App\Exports;

use DB;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class RemovedFlaggedTransactionExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithCustomValueBinder
{
    protected $id;

    public function __construct($id)
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
    public function collection()
    {
        $slave_connection = env('SLAVE_DB_CONNECTION_NAME', '');        

        if (!empty($slave_connection)) {
            \DB::setDefaultConnection($slave_connection);
            $getDatabaseName = \DB::connection()->getDatabaseName();
            _WriteLogsInFile($getDatabaseName . " connection from admin transactions", 'slave_connection');
        }
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $input = request()->all();
        $data = \DB::table("transactions")->where('transactions.is_flagged', '1')->where('transactions.is_flagged_remove', '1');

        if (!is_null($this->id)) {
            $data = $data->whereIn('transactions.id', $this->id);
        }

        if (get_guard() != 'admin') {
            if (\Auth::user()->id != '') {
                $data = $data->where('transactions.user_id', \Auth::user()->id);
            }
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('transactions.user_id',  $input['user_id']);
        }
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }
        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }
        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        } else {
            $data = $data->whereNotIn('payment_gateway_id', $payment_gateway_id);
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }
        if (isset($input['customer_order_id']) && $input['customer_order_id'] != '') {
            $data = $data->where('transactions.customer_order_id', 'like', '%' . $input['customer_order_id'] . '%');
        }
        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', 'like', '%' . $input['order_id'] . '%');
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 00:00:00');
        }

        $data = $data->orderBy("transactions.id", "DESC")
            ->whereNull("transactions.deleted_at")
            ->get();
        return $data;
    }

    public function headings(): array
    {
        return [
            'Order ID',
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
            'Transaction Date',
            'Flagged By',
            'Flagged Transaction Date',
            'Flagged Transaction Remove Date'
        ];
    }

    public function map($transactions): array
    {
        if ($transactions->card_type == '1')
            $transactions->card_type = 'Amex';
        elseif ($transactions->card_type == '2')
            $transactions->card_type = 'Visa';
        elseif ($transactions->card_type == '3')
            $transactions->card_type = 'Master Card';
        else
            $transactions->card_type = 'Discover';

        if ($transactions->status == '1') {
            $transactions->status = 'Success';
        } elseif ($transactions->status == '2') {
            $transactions->status = 'Pending';
        } elseif ($transactions->status == '3') {
            $transactions->status = 'Canceled';
        } elseif ($transactions->status == '4') {
            $transactions->status = 'To Be Confirm';
        } else {
            $transactions->status = 'Declined';
        }

        if ($transactions->chargebacks == '1')
            $transactions->chargebacks = 'Yes';
        else
            $transactions->chargebacks = 'No';

        if ($transactions->refund == '1')
            $transactions->refund = 'Yes';
        else
            $transactions->refund = 'No';

        if ($transactions->is_converted == '1')
            $transactions->amount = $transactions->amount . '-' . $transactions->converted_amount;
        elseif ($transactions->is_converted_user_currency == '1')
            $transactions->amount = $transactions->amount . '-' . $transactions->converted_user_amount;
        else
            $transactions->amount = $transactions->amount;

        if ($transactions->is_converted == '1')
            $transactions->currency = $transactions->currency . '-' . $transactions->converted_currency;
        elseif ($transactions->is_converted_user_currency == '1')
            $transactions->currency = $transactions->currency . '-' . $transactions->converted_user_currency;
        else
            $transactions->currency = $transactions->currency;

        return [
            $transactions->order_id,
            $transactions->first_name,
            $transactions->last_name,
            $transactions->address,
            $transactions->country,
            $transactions->state,
            $transactions->city,
            $transactions->zip,
            $transactions->email,
            $transactions->phone_no,
            $transactions->card_type,
            $transactions->amount,
            $transactions->currency,
            substr($transactions->card_no, 0, 6) . 'XXXXXX' . substr($transactions->card_no, -4, 4),
            $transactions->ccExpiryMonth,
            $transactions->ccExpiryYear,
            $transactions->status,
            $transactions->reason,
            $transactions->chargebacks,
            $transactions->refund,
            date('d-m-Y H:i:s', strtotime($transactions->created_at)),
            $transactions->flagged_by,
            convertDateToLocal($transactions->flagged_date, 'd-m-Y H:i:s'),
            convertDateToLocal($transactions->flagged_remove_date, 'd-m-Y H:i:s')
        ];
    }
}
