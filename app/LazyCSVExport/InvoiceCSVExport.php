<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\Invoices;


class InvoiceCSVExport
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
            'Company Name',
            'Agent Name',
            'Invoice No',
            'Total Amount',
            'Paid Status',
            'Transaction Hash',
        ];

        return response()->streamDownload(function () use ($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = Invoices::select(
                "applications.business_name",
                "invoices.agent_name",
                "invoices.invoice_no",
                "invoices.total_amount",
                "invoices.is_paid",
                "invoices.transaction_hash",
                "invoices.transaction_hash"
            )
                ->leftjoin("applications", "applications.id", "invoices.company_id")
                ->where("invoices.admin_id", auth()->guard('admin')->user()->id);

            if (!empty($input)) {
                if (isset($input['company_id']) && $input['company_id'] != '') {
                    $data = $data->where('company_id', $input['company_id']);
                }
                if (isset($input['invoice_no']) && $input['invoice_no'] != '') {
                    $data = $data->where('invoice_no', 'like', '%' . $input['invoice_no'] . '%');
                }
                if (isset($input['is_paid']) && $input['is_paid'] != '') {
                    $data = $data->where('is_paid', $input['is_paid']);
                }
                if (isset($input['transaction_hash']) && $input['transaction_hash'] != '') {
                    $data = $data->where('transaction_hash', 'like', '%' . $input['transaction_hash'] . '%');
                }
                if (isset($input['agent_name']) && $input['agent_name'] != '') {
                    $data = $data->where('agent_name', 'like', '%' . $input['agent_name'] . '%');
                }
            }
            $data = $data->orderBy('invoices.id', 'DESC');
            $data = $data->cursor()
                ->each(function ($data) use ($file) {
                    $data = $data->toArray();
                    $data['is_paid'] = $data['is_paid'] == 1 ? 'Paid' : 'Unpaid';
                    fputcsv($file, $data);
                });

            fclose($file);
        }, 'Invoice_Excel_' . date('d-m-Y') . '.csv');
    }
}
