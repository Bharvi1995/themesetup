<?php

namespace App\Exports;

use DB;
use App\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubTransactionsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

    	$input = request()->all();
        // dd($input['transactionPage']);
    	if(\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1'){
            $userID = \Auth::user()->main_user_id;
    	}
        else{
            $userID = \Auth::user()->id;
        }

		$data = Transaction::select('transactions.order_id',
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
				\DB::raw("concat('XXXXXXXXXXXX',SUBSTR(transactions.card_no, -4, 4)) AS card_no"),
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
				->where('user_id',$userID)
				->where('is_reccuring_transaction_id', request()->id);

		if(isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if(isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no',  'like', '%' . $input['card_no'] . '%');
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

		$data = $data->orderBy('id', 'DESC')
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
            'Sulte APT No.',
            'Country',
            'State',
            'City',
            'Zip',
            'Birth Date',
            'Email',
            'Phone No.',
            'Card Type',
            'Amount',
            'Currency',
            'Card No.',
            'Expiry Month',
            'Expiry Year',
            'CVV No.',
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
            'Transaction Date',
            'Descriptor'
        ];
    }
}
