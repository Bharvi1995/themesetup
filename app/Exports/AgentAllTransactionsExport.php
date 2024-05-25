<?php

namespace App\Exports;

use App\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;
use Auth;

class AgentAllTransactionsExport implements FromCollection, WithHeadings , WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $input = request()->all();

        $userIds = \DB::table('users')->where('agent_id',auth()->guard('agentUser')->user()->id)->pluck('id');
        $data = Transaction::select('transactions.order_id','merchantapplications.company_name',
                'transactions.first_name','transactions.last_name',
                'transactions.address','transactions.sulte_apt_no',
                'transactions.country','transactions.state',
                'transactions.city','transactions.zip',
                'transactions.birth_date','transactions.email',
                'transactions.phone_no',
                \DB::raw('(CASE
                    WHEN transactions.card_type = "1" THEN "Amex"
                    WHEN transactions.card_type = "2" THEN "Visa"
                    WHEN transactions.card_type = "3" THEN "Master Card"
                    ELSE "Discover"
                    END) AS card_type'),
                'transactions.amount','transactions.currency',
                // 'transactions.card_no',
                // \DB::raw("concat('`',transactions.card_no) AS card_no"),
                // \DB::raw("concat(SUBSTR(transactions.card_no, 0, 5),'XXXX',SUBSTR(transactions.card_no, -6, 6)) AS card_no"),
                'transactions.card_no',
                'transactions.ccExpiryMonth',
                'transactions.ccExpiryYear','transactions.cvvNumber',
                'transactions.shipping_first_name','transactions.shipping_last_name',
                'transactions.shipping_address','transactions.shipping_country',
                'transactions.shipping_state','transactions.shipping_city',
                'transactions.shipping_zip','transactions.shipping_email',
                'transactions.shipping_phone_no',
                \DB::raw('(CASE
                    WHEN transactions.status = "1" THEN "Success"
                    WHEN transactions.status = "2" THEN "Pending"
                    WHEN transactions.status = "3" THEN "Canceled"
                    WHEN transactions.status = "4" THEN "To Be Confirm"
                    ELSE "Declined"
                    END) AS status'),
                'transactions.reason',
                \DB::raw('(CASE
                    WHEN transactions.chargebacks = "1" THEN "Yes"
                    ELSE "No"
                    END) AS chargebacks'),
                \DB::raw('(CASE
                    WHEN transactions.refund = "1" THEN "Yes"
                    ELSE "No"
                    END) AS refund'),
                \DB::raw('(CASE
                    WHEN transactions.resubmit_transaction = "1" THEN "Yes"
                    ELSE "No"
                    END) AS resubmit_transaction'),
                \DB::raw("(DATE_FORMAT(transactions.created_at, '%d-%m-%Y %h:%i:%s')) AS created_at_c"),
                'transactions.descriptor')
            ->join('merchantapplications','merchantapplications.user_id','transactions.user_id')
            ->join('middetails','middetails.id','transactions.payment_gateway_id')
            ->where('transactions.is_batch_transaction', '0');

        if(isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if(isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if(isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }

        if(isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if(isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email',  'like', '%' . $input['email'] . '%');
        }

        if(isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name',  'like', '%' . $input['first_name'] . '%');
        }

        if(isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name',  'like', '%' . $input['last_name'] . '%');
        }

        if((isset($input['start_date']) && $input['start_date'] != '') &&  (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d',strtotime($input['start_date']));
            $end_date = date('Y-m-d',strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date.' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date.' 00:00:00');
        }

        if(isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use($input){
                    $query->orWhere('transactions.id', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('transactions.order_id', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('transactions.descriptor', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('merchantapplications.company_name', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('transactions.phone_no', 'like', '%'.$input['global_search'].'%')
                        ->orWhere('transactions.email', 'like', '%'.$input['global_search'].'%');
                });
        }

        $data = $data->orderBy('transactions.id', 'DESC')
            ->where('transactions.payment_gateway_id', '!=' ,'41')
            ->where('transactions.payment_gateway_id', '!=' ,'16')
            ->whereIn('transactions.user_id',$userIds)
            ->orderBy('transactions.id', 'DESC');

        $data = $data->get();
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
            'Customer Order Id',
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
            'Shipping First Name',
            'Shipping Last Name',
            'Shipping Address',
            'Shipping Country',
            'Shipping State',
            'Shipping City',
            'Shipping Zip',
            'Shipping Email',
            'Shipping Phone No.',
            'Status',
            'Reason',
            'Chargebacks',
            'Refund',
            'Resubmit Transaction',
            'Transaction Date'
        ];
    }

    public function map($transaction): array
    {
        return [
            '`' . $transaction->order_id,
            $transaction->company_name,
            $transaction->first_name,
            $transaction->last_name,
            $transaction->address,
            $transaction->customer_order_id,
            $transaction->country,
            $transaction->state,
            $transaction->city,
            $transaction->zip,
            $transaction->email,
            $transaction->phone_no,
            $transaction->card_type,
            $transaction->amount,
            $transaction->currency,
            '`' . substr($transaction->card_no, 0, 6).'XXXXXX'. substr($transaction->card_no, -4),
            $transaction->shipping_first_name,
            $transaction->shipping_last_name,
            $transaction->shipping_address,
            $transaction->shipping_country,
            $transaction->shipping_state,
            $transaction->shipping_city,
            $transaction->shipping_zip,
            $transaction->shipping_email,
            $transaction->shipping_phone_no,
            $transaction->status,
            $transaction->reason,
            $transaction->chargebacks,
            $transaction->refund,
            $transaction->resubmit_transaction,
            $transaction->created_at_c,
        ];
    }
}
