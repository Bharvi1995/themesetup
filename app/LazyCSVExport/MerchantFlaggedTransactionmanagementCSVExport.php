<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\Transaction;
use DB;

class MerchantFlaggedTransactionmanagementCSVExport
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
            'Customer Order Id',
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
            'Transaction Date',
            'Suspicious',
            'Suspicious Date',
        ];

        return response()->streamDownload(function () use ($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = Transaction::select(
                'transactions.order_id',
                'transactions.customer_order_id',
                'applications.business_name',
                'transactions.first_name',
                'transactions.last_name',
                'transactions.address',
                'transactions.country',
                'transactions.state',
                'transactions.city',
                'transactions.zip',
                'transactions.email',
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
                DB::raw('(CASE
                        WHEN transactions.is_converted = "1" THEN transactions.converted_amount
                        ELSE transactions.amount END 
                    ) AS amount'),
                DB::raw('(CASE
                        WHEN transactions.is_converted = "1" THEN transactions.converted_currency
                        ELSE transactions.currency END 
                    ) AS currency'),
                'transactions.card_no',
                'transactions.ccExpiryMonth',
                'transactions.ccExpiryYear',
                \DB::raw('(CASE
                        WHEN transactions.status = "1" THEN "Success"
                        WHEN transactions.status = "2" THEN "Pending"
                        WHEN transactions.status = "3" THEN "Canceled"
                        WHEN transactions.status = "4" THEN "To Be Confirm"
                        ELSE "Declined"
                        END) AS status'),
                'transactions.reason',
                'transactions.created_at',
                \DB::raw('(CASE
                        WHEN transactions.is_flagged = "1" THEN "Yes"
                        ELSE "No"
                        END) AS Suspicious'),
                \DB::raw("(DATE_FORMAT(transactions.flagged_date, '%d-%m-%Y %h:%i:%s')) AS flagged_date"),
            )
                ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')->where('transactions.chargebacks', '0')->where('transactions.is_flagged', '1')->where('transactions.is_flagged_remove', '0')
                ->join('applications', 'applications.user_id', 'transactions.user_id');
            if (!is_null($this->id)) {
                $data = $data->whereIn('transactions.id', $this->id);
            }

            if (\Auth::guard('web')->user()->id != '') {
                if (\Auth::guard('web')->user()->main_user_id != '0') {
                    $data = $data->where('transactions.user_id', \Auth::guard('web')->user()->main_user_id);
                } else {
                    $data = $data->where('transactions.user_id', \Auth::guard('web')->user()->id);
                }
            }
            if (isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('transactions.user_id', $input['user_id']);
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
            }

            if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
                $transaction_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
                $transaction_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));

                $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $transaction_start_date . ' 00:00:00')
                    ->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $transaction_end_date . ' 00:00:00');
            } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] == '')) {
                $transaction_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
                $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $transaction_start_date . ' 00:00:00');
            } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] == '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
                $transaction_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
                $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $transaction_end_date . ' 00:00:00');
            }

            if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] != '') && (isset($input['flagged_end_date']) && $input['flagged_end_date'] != '')) {
                $flagged_start_date = date('Y-m-d', strtotime($input['flagged_start_date']));
                $flagged_end_date = date('Y-m-d', strtotime($input['flagged_end_date']));

                $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $flagged_start_date . ' 00:00:00')
                    ->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $flagged_end_date . ' 00:00:00');
            } else if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] != '') || (isset($input['flagged_end_date']) && $input['flagged_end_date'] == '')) {
                $flagged_start_date = date('Y-m-d', strtotime($input['flagged_start_date']));
                $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $flagged_start_date . ' 00:00:00');
            } else if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] == '') || (isset($input['flagged_end_date']) && $input['flagged_end_date'] != '')) {
                $flagged_end_date = date('Y-m-d', strtotime($input['flagged_end_date']));
                $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $flagged_end_date . ' 00:00:00');
            }

            $data = $data->whereNotIn('transactions.payment_gateway_id', ['1', '2'])->orderBy('transactions.id', 'DESC');
            $data = $data->cursor()
                ->each(function ($data) use ($file) {
                    $data = $data->toArray();
                    $data['created_at'] = convertDateToLocal($data['created_at'], 'd-m-Y / H:i:s');
                    $data['order_id'] = '`' . $data['order_id'];
                    $data['card_no'] = '`' . substr($data['card_no'], 0, 6) . 'XXXXXX' . substr($data['card_no'], -4);
                    fputcsv($file, $data);
                });

            fclose($file);
        }, 'Suspicious_Transaction_Excel_' . date('d-m-Y') . '.csv');
    }
}