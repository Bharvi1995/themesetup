<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\Transaction;
use DB;

class AdminAllRetrivalTransactionsCSVExport
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
        // dd('Yes');

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = Transaction::select('transactions.order_id','applications.business_name','transactions.first_name',
                    'transactions.last_name','transactions.address',
                    'transactions.country','transactions.state','transactions.city','transactions.zip','transactions.email',
                    'transactions.phone_no',
                    \DB::raw('(CASE
                        WHEN transactions.card_type = "1" THEN "Amex"
                        WHEN transactions.card_type = "2" THEN "Visa"
                        WHEN transactions.card_type = "3" THEN "Master Card"
                        WHEN transactions.card_type = "4" THEN "DISCOVER"
                        WHEN transactions.card_type = "5" THEN "JCB"
                        WHEN transactions.card_type = "6" THEN "MESTRO"
                        WHEN transactions.card_type = "7" THEN "SWITCH"
                        WHEN transactions.card_type = "8" THEN "SOLO"
                        ELSE "Discover"
                        END) AS card_type'),
                    'transactions.amount',
                    'transactions.currency',
                    'transactions.card_no',
                    'transactions.ccExpiryMonth','transactions.ccExpiryYear',
                    'middetails.bank_name',
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
                    \DB::raw("(DATE_FORMAT(transactions.chargebacks_date, '%d-%m-%Y %h:%i:%s')) AS chargebacks_date"),
                    \DB::raw('(CASE
                        WHEN transactions.refund = "1" THEN "Yes"
                        ELSE "No"
                        END) AS refund'),
                    \DB::raw("(DATE_FORMAT(transactions.refund_date, '%d-%m-%Y %h:%i:%s')) AS refund_date"),
                    \DB::raw('(CASE
                        WHEN transactions.is_flagged = "1" THEN "Yes"
                        ELSE "No"
                        END) AS Suspicious'),
                    \DB::raw("(DATE_FORMAT(transactions.flagged_date, '%d-%m-%Y %h:%i:%s')) AS flagged_date"),
                    \DB::raw("(DATE_FORMAT(transactions.created_at, '%d-%m-%Y %h:%i:%s')) AS created_at_c")
                    )
                    ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
                    ->join('applications', 'applications.user_id', 'transactions.user_id')
                    ->where('transactions.chargebacks', '0')
                    ->where('transactions.is_retrieval', '1');
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
        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }
        if (isset($input['customer_order_id']) && $input['customer_order_id'] != '') {
            $data = $data->where('transactions.customer_order_id', 'like', '%' . $input['customer_order_id'] . '%');
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }
        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = $input['end_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        //refund date filter
        if ((isset($input['refund_start_date']) && $input['refund_start_date'] != '') && (isset($input['refund_end_date']) && $input['refund_end_date'] != '')) {
            $start_date = $input['refund_start_date'];
            $end_date = $input['refund_end_date'];

            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date);
        } else if ((isset($input['refund_start_date']) && $input['refund_start_date'] != '') || (isset($input['refund_end_date']) && $input['refund_end_date'] == '')) {
            $start_date = $input['refund_start_date'];
            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date);
        } else if ((isset($input['refund_start_date']) && $input['refund_start_date'] == '') || (isset($input['refund_end_date']) && $input['refund_end_date'] != '')) {
            $end_date = $input['refund_end_date'];
            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date);
        }
        //chargebacks date filter
        if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] != '') && (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] != '')) {
            $start_date = $input['chargebacks_start_date'];
            $end_date = $input['chargebacks_end_date'];

            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date);
        } else if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] != '') || (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] == '')) {
            $start_date = $input['chargebacks_start_date'];
            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date);
        } else if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] == '') || (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] != '')) {
            $end_date = $input['chargebacks_end_date'];
            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date);
        }
        //retrieval date filter
        if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] != '') && (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] != '')) {
            $start_date = $input['retrieval_start_date'];
            $end_date = $input['retrieval_end_date'];

            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.retrieval_date)'), '<=', $end_date);
        } else if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] != '') || (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] == '')) {
            $start_date = $input['retrieval_start_date'];
            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '>=', $start_date);
        } else if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] == '') || (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] != '')) {
            $end_date = $input['retrieval_end_date'];
            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '<=', $end_date);
        }
        //flagged date filter
        if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] != '') && (isset($input['flagged_end_date']) && $input['flagged_end_date'] != '')) {
            $start_date = $input['flagged_start_date'];
            $end_date = $input['flagged_end_date'];

            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $end_date);
        } else if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] != '') || (isset($input['flagged_end_date']) && $input['flagged_end_date'] == '')) {
            $start_date = $input['flagged_start_date'];
            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $start_date);
        } else if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] == '') || (isset($input['flagged_end_date']) && $input['flagged_end_date'] != '')) {
            $end_date = $input['flagged_end_date'];
            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $end_date);
        }
        if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
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
        $data = $data->whereNotIn('transactions.payment_gateway_id', ['1', '2'])->orderBy('transactions.id', 'DESC');
        $data = $data->cursor()
        ->each(function ($data) use ($file) {
            $data = $data->toArray();
            $data['order_id'] = '`'.$data['order_id'];
            $data['card_no'] = '`' . substr($data['card_no'], 0, 6).'XXXXXX'. substr($data['card_no'], -4);
            fputcsv($file, $data);
        });

            fclose($file);
        }, 'Retrival_Transaction_Excel_'.date('d-m-Y').'.csv');
    }
}
