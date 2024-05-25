<?php

namespace App\Exports;

use Carbon\Carbon;
use DB;
use App\User;
use App\Application;
use App\ApplicationAssignToBank;


class SentToBankApplicationsExport
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }


    public function headings(): array
    {
        return [
            'Company Name',
            'Email',
            'Sent To Bank',
        ];
    }


    public function download()
    {
        $slave_connection = env('SLAVE_DB_CONNECTION_NAME', '');

        if (!empty($slave_connection)) {
            \DB::setDefaultConnection($slave_connection);
            $getDatabaseName = \DB::connection()->getDatabaseName();
            _WriteLogsInFile($getDatabaseName . " connection from admin agreement received application", 'slave_connection');
        }

        $columns = $this->headings();
        $input = request()->all();

        return response()->streamDownload(function () use ($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);


            $data = ApplicationAssignToBank::select(
                'applications.business_name','users.email', 'application_id'
            )
                ->join('applications','applications.id','application_assign_to_bank.application_id')
                ->join('users','users.id','applications.user_id');

            if(isset($input['email']) && $input['email'] != '') {
                $data = $data->where('users.email',$input['email']);
            }

            if(isset($input['website_url']) && $input['website_url'] != '') {
                $data = $data->where('applications.website_url',$input['website_url']);
            }

            if(isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('applications.user_id',$input['user_id']);
            }

            if(isset($input['bank_id']) && $input['bank_id'] != '') {
                $data = $data->where('application_assign_to_bank.bank_user_id',$input['bank_id']);
            }

            if(isset($input['category_id']) && $input['category_id'] != '') {
                $data = $data->where('applications.category_id',$input['category_id']);
            }

            $data = $data->orderBy('application_assign_to_bank.id', 'desc')
                ->groupBy('application_assign_to_bank.application_id');

            $data->cursor()
                ->each(function ($data) use ($file) {
                    $data = $data->toArray();

                    $data['business_name'] = $data->business_name;
                    $data['email'] = $data->email;
                    $data['banks'] = implode(",",getSentBank($data->application_id)->pluck("bank_name")->toArray());

                    fputcsv($file, $data);
                });

            fclose($file);
        }, 'Sent_To_Bank_Applications_Excel_' . date('d-m-Y') . '.csv');

    }


}
