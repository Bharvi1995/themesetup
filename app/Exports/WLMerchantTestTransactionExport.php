<?php

namespace App\Exports;

use App\Transaction;

class WLMerchantTestTransactionExport
{

    public function download()
    {
        $columns = $this->headings();
        $input = request()->all();

        $slave_connection = env('SLAVE_DB_CONNECTION_NAME', '');
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        if (!empty($slave_connection)) {
            \DB::setDefaultConnection($slave_connection);
            $getDatabaseName = \DB::connection()->getDatabaseName();
            _WriteLogsInFile($getDatabaseName . " connection from RP transactions", 'slave_connection');
        }

        return response()->streamDownload(function () use ($columns, $input, $payment_gateway_id) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);


            $userIds = \DB::table('users')
                ->where('is_white_label','1')
                ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
                ->pluck('id');

            $data = Transaction::select('transactions.*')
                ->join('applications', 'applications.user_id', 'transactions.user_id')
                ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
                ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                ->whereIn('transactions.user_id', $userIds)
                ->orderBy('transactions.id', 'DESC');

            if (isset($input['first_name']) && $input['first_name'] != '') {
                $data = $data->where('transactions.first_name',  'like', '%' . $input['first_name'] . '%');
            }

            if (isset($input['last_name']) && $input['last_name'] != '') {
                $data = $data->where('transactions.last_name',  'like', '%' . $input['last_name'] . '%');
            }

            if (isset($input['status']) && $input['status'] != '') {
                $data = $data->where('transactions.status', $input['status']);
            }

            if (isset($input['order_id']) && $input['order_id'] != '') {
                $data = $data->where('transactions.order_id', $input['order_id']);
            }

            if (isset($input['company_name']) && $input['company_name'] != '') {
                $data = $data->where('transactions.user_id',  $input['company_name']);
            }

            if ((isset($input['start_date']) && $input['start_date'] != '') &&  (isset($input['end_date']) && $input['end_date'] != '')) {
                $start_date = $input['start_date'];
                $end_date = $input['end_date'];

                $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                    ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
            } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
                $start_date = $input['start_date'];
                $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
            } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
                $end_date = date('Y-m-d', strtotime($input['end_date']));
                $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
            }

            $data->cursor()
                ->each(function ($data) use ($file) {
                    $data = $data->toArray();


                    $mappedData = $this->map($data);


                    fputcsv($file, $mappedData);
                });

            fclose($file);
        }, 'Test_Transcation_Excel_' . date('d-m-Y') . '.csv');
    }

    public function map($data): array
    {
       if($data->status == '1') {
            $data->status = 'Success';
        } elseif($data->status == '2') {
            $data->status = 'Pending';
        } elseif($data->status == '3') {
            $data->status = 'Canceled';
        } elseif($data->status == '4') {
            $data->status = 'To Be Confirm';
        } else {
            $data->status = 'Declined';
        }

        if($data->is_converted == '1')
            $data->amount = $data->amount.'-'.$data->converted_amount;
        elseif($data->is_converted_user_currency == '1')
            $data->amount = $data->amount.'-'.$data->converted_user_amount;
        else
            $data->amount = $data->amount;

        if($data->is_converted == '1')
            $data->currency = $data->currency.'-'.$data->converted_currency;
        elseif($data->is_converted_user_currency == '1')
            $data->currency = $data->currency.'-'.$data->converted_user_currency;
        else
            $data->currency = $data->currency;

        return [
            $data->order_id,
            $data->first_name,
            $data->last_name,
            $data->address,
            $data->country,
            $data->state,
            $data->city,
            $data->zip,
            $data->email,
            $data->amount,
            $data->currency,
            $data->status,
            $data->created_at->format('d-m-Y H:i:s')
        ];
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
            'Amount',
            'Currency',
            'Status',
            'Transaction Date'
        ];
    }
}
