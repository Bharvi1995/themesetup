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

class TransactionsExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithCustomValueBinder, WithMapping
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
    public function collection()
    {
        $slave_connection = env('SLAVE_DB_CONNECTION_NAME', '');
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if (!empty($slave_connection)) {
            \DB::setDefaultConnection($slave_connection);
            $getDatabaseName = \DB::connection()->getDatabaseName();
            _WriteLogsInFile($getDatabaseName . " connection from user transactions", 'slave_connection');
        }
        $input = \Arr::except(request()->all(), array('_token', '_method'));
        $user = \Auth::user()->main_user_id ?  \Auth::user()->main_user_id : \Auth::user()->id;
        $input["user_id"] = $user;

        $data = DB::table("transactions");
        $data = $data->select("transactions.*", "middetails.bank_name","applications.business_name");
        $data = $data->join('middetails', 'middetails.id', 'transactions.payment_gateway_id');
        $data = $data->join('applications', 'applications.user_id', 'transactions.user_id');
        if (!is_null($this->id)) {
            $data = $data->whereIn('transactions.id', $this->id);
        }
        if (get_guard() != 'admin') {
            if (isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('transactions.user_id', $input['user_id']);
            }
        }
        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }
        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }
        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }
        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }
        if (isset($input['customer_order_id']) && $input['customer_order_id'] != '') {
            $data = $data->where('transactions.customer_order_id', 'like', '%' . $input['customer_order_id'] . '%');
        }
        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        } else {
            $data = $data->whereNotIn('payment_gateway_id', $payment_gateway_id);
        }
        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }
        if (isset($input['amount']) && $input['amount'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['amount']);
        }
        if (isset($input['greater_then']) && $input['greater_then'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['greater_then']);
        }
        if (isset($input['less_then']) && $input['less_then'] != '') {
            $data = $data->where('transactions.amount', '<=', $input['less_then']);
        }
        if (isset($input['session_id']) && $input['session_id'] != '') {
            $data = $data->where('transactions.session_id', 'like', '%' . $input['session_id'] . '%');
        }
        if (isset($input['gateway_id']) && $input['gateway_id'] != '') {
            $data = $data->where('transactions.gateway_id', $input['gateway_id']);
        }
        if (isset($input['reason']) && $input['reason'] != '') {
            $data = $data->where('transactions.reason', 'like', '%' . $input['reason'] . '%');
        }
        if (isset($input['is_request_from_vt']) && $input['is_request_from_vt'] != '') {
            if ($input['is_request_from_vt'] == 'iFrame') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'Pay Button') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'WEBHOOK') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'API') {
                $data = $data->where(function ($query) use ($input) {
                    $query->where('transactions.is_request_from_vt', $input['is_request_from_vt'])
                        ->orWhere('transactions.is_request_from_vt', '0');
                });
            }
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 00:00:00');
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00');
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 00:00:00');
        } if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date)
                ->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
            } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] == '')) {
                $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
                $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date);
            } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] == '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
                $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
                $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
            }   

        $data = $data->whereNotIn('transactions.payment_gateway_id',$payment_gateway_id)
            ->orderBy("transactions.id", "DESC")
            ->whereNull("transactions.deleted_at")
            ->get();

        return $data;
    }

    public function headings(): array
    {
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
            'MID',
            'Status',
            'Reason',
            'Chargebacks',
            'Chargebacks Date',
            'Refund',
            'Refund Date',
            'Suspicious',
            'Suspicious Date',
            'Transaction Date'
        ];
    }

    public function map($transaction): array
    {
        if ($transaction->status == '1') {
            $transaction->status = 'Success';
        } elseif ($transaction->status == '2') {
            $transaction->status = 'Pending';
        } elseif ($transaction->status == '3') {
            $transaction->status = 'Canceled';
        } elseif ($transaction->status == '4') {
            $transaction->status = 'To Be Confirm';
        } else {
            $transaction->status = 'Declined';
        }
        if ($transaction->card_type == '1')
            $transaction->card_type = 'Amex';
        elseif ($transaction->card_type == '2')
            $transaction->card_type = 'Visa';
        elseif ($transaction->card_type == '3')
            $transaction->card_type = 'Master Card';
        elseif ($transaction->card_type == '3')
            $transaction->card_type = 'Discover';
        else
            $transaction->card_type = '';

        if ($transaction->chargebacks == '1')
            $transaction->chargebacks = 'Yes';
        else
            $transaction->chargebacks = 'No';

        if ($transaction->refund == '1')
            $transaction->refund = 'Yes';
        else
            $transaction->refund = 'No';

        if ($transaction->is_flagged == '1')
            $transaction->is_flagged = 'Yes';
        else
            $transaction->is_flagged = 'No';

        if ($transaction->is_retrieval == '1')
            $transaction->is_retrieval = 'Yes';
        else
            $transaction->is_retrieval = 'No';

        if ($transaction->is_converted == '1')
            $transaction->amount = $transaction->amount . '-' . $transaction->converted_amount;
        elseif ($transaction->is_converted_user_currency == '1')
            $transaction->amount = $transaction->amount . '-' . $transaction->converted_user_amount;
        else
            $transaction->amount = $transaction->amount;

        if ($transaction->is_converted == '1')
            $transaction->currency = $transaction->currency . '-' . $transaction->converted_currency;
        elseif ($transaction->is_converted_user_currency == '1')
            $transaction->currency = $transaction->currency . '-' . $transaction->converted_user_currency;
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
            $transaction->bank_name,
            $transaction->status,
            $transaction->reason,
            $transaction->chargebacks,
            $transaction->chargebacks_date,
            $transaction->refund,
            $transaction->refund_date,
            $transaction->is_flagged,
            $transaction->flagged_date,
            convertDateToLocal($transaction->created_at, 'd-m-Y H:i:s'),
        ];
    }
}
