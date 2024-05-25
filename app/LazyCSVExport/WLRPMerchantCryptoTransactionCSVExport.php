<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\Transaction;
use DB;

class WLRPMerchantCryptoTransactionCSVExport
{
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
            'Transaction Date'
        ];

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

        $slave_connection = env('SLAVE_DB_CONNECTION_NAME', '');
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if (!empty($slave_connection)) {
            \DB::setDefaultConnection($slave_connection);
            $getDatabaseName = \DB::connection()->getDatabaseName();
            _WriteLogsInFile($getDatabaseName . " connection from RP dashboard", 'slave_connection');
        }

        $agentId = auth()->guard('agentUserWL')->user()->id;

        $userIds = \DB::table('users')->where('white_label_agent_id', $agentId)->pluck('id');

        $data = Transaction::select('transactions.order_id','transactions.customer_order_id','applications.business_name','transactions.first_name',
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
                    \DB::raw('(CASE
                        WHEN transactions.status = "1" THEN "Success"
                        WHEN transactions.status = "2" THEN "Pending"
                        WHEN transactions.status = "3" THEN "Canceled"
                        WHEN transactions.status = "4" THEN "To Be Confirm"
                        ELSE "Declined"
                        END) AS status'),
                    'transactions.reason',
                    \DB::raw("(DATE_FORMAT(transactions.created_at, '%d-%m-%Y %h:%i:%s')) AS created_at_c")
                    )
                    ->join('applications', 'applications.user_id', 'transactions.user_id')
                    ->where('transactions.is_transaction_type', 'CRYPTO');

            if (isset($input['company_name']) && $input['company_name'] != '') {
                $data = $data->where('transactions.user_id', $input['company_name']);
            }
            if(isset($input['first_name']) && $input['first_name'] != '') {
                $data = $data->where('transactions.first_name',  'like', '%' . $input['first_name'] . '%');
            }
            if(isset($input['last_name']) && $input['last_name'] != '') {
                $data = $data->where('transactions.last_name',  'like', '%' . $input['last_name'] . '%');
            }
            
            if (isset($input['status']) && $input['status'] != '') {
                $data = $data->where('transactions.status', $input['status']);
            }
            if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                $start_date = date('Y-m-d', strtotime($input['start_date']));
                $end_date = date('Y-m-d', strtotime($input['end_date']));

                $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
            } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
                $start_date = date('Y-m-d', strtotime($input['start_date']));
                $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
            } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
                $end_date = date('Y-m-d', strtotime($input['end_date']));
                $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
            }
            
            if (isset($input['order_id']) && $input['order_id'] != '') {
                $data = $data->where('transactions.order_id', $input['order_id']);
            }

            if (isset($input['email']) && $input['email'] != '') {
                $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
            }

            if (isset($input['country']) && $input['country'] != '') {
                $data = $data->where('transactions.country', $input['country']);
            }

            if (isset($input['currency']) && $input['currency'] != '') {
                $data = $data->where('transactions.currency', $input['currency']);
            }
            
            $data = $data->whereIn('transactions.user_id', $userIds)
		            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
		            ->where('transactions.deleted_at', NULL)
            		->orderBy('transactions.id', 'DESC');

            $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                $data['order_id'] = '`'.$data['order_id'];
                $data['card_no'] = '`' . substr($data['card_no'], 0, 6).'XXXXXX'. substr($data['card_no'], -4);
                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Crypto_Transaction_Excel_'.date('d-m-Y').'.csv');     
    }
}
