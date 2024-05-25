<?php

namespace App\LazyCSVExport;

use Illuminate\Http\Request;
use App\Bank;
use DB;

class BankUserCSVExport
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
            'User Name',
            'Email',
            'Industry Type',
            'Status'
        ];
        // dd('Yes');

        return response()->streamDownload(function() use($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = Bank::select("banks.bank_name","banks.email","banks.category_id",'banks.is_active');
                
        if (! empty($input)) {
            if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('bank_name',  'like', '%' . $input['name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('email',  'like', '%' . $input['email'] . '%');
        }
        }
        $data = $data->orderBy('banks.id', 'desc');
        $data = $data->cursor()
            ->each(function ($data) use ($file) {
                $data = $data->toArray();
                fputcsv($file, $data);
            });

            fclose($file);
        }, 'Bank_user_Excel_'.date('d-m-Y').'.csv');
    }
}
