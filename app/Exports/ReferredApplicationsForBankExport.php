<?php

namespace App\Exports;

use DB;
use App\User;
use App\Application;


class ReferredApplicationsForBankExport
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Email',
            'Business Category',
            'Accepted Payment Methods',
            'Company Name',
            'Website URL',
            'First Name',
            'Last Name',
            'Company Address',
            'Country Of Incorporation',
            'Phone Number',
            'Contact Details',
            'Processing Currency',
            'Processing Country',
            'Referred Note'
        ];
    }

    public function download()
    {
        $columns = $this->headings();
        $input = request()->all();

        return response()->streamDownload(function () use ($columns, $input) {
            $file = fopen('php://output', 'w+');
            fputcsv($file, $columns);

            $data = Application::select(
                'users.name',
                'users.email',
                'applications.business_type',
                'applications.accept_card',
                'applications.business_name',
                'applications.website_url',
                'applications.business_contact_first_name',
                'applications.business_contact_last_name',
                'applications.business_address1',
                'applications.country',
                'applications.phone_no',
                'applications.skype_id',
                'applications.processing_currency',
                'applications.processing_country',
                'application_assign_to_bank.referred_note'
            )
                ->join('users', 'users.id', 'applications.user_id')
                ->join('application_assign_to_bank', 'applications.id', '=', 'application_assign_to_bank.application_id')
                ->where('application_assign_to_bank.bank_user_id', auth()->guard('bankUser')->user()->id)
                ->where('application_assign_to_bank.status', '2');

            if (!empty($this->id)) {
                $data = $data->whereIn('users.id', $this->id);
            }

            if (isset($input['category_id']) && $input['category_id'] != '') {
                $data = $data->where('applications.category_id', $input['category_id']);
            }

            if (isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('applications.user_id', $input['user_id']);
            }

            if (isset($input['status']) && $input['status'] != '') {
                $data = $data->where('application_assign_to_bank.status', $input['status']);
            }


            $data->cursor()
                ->each(function ($data) use ($file) {
                    $data = $data->toArray();

                    fputcsv($file, $data);
                });

            fclose($file);
        }, 'Referred_Applications_Excel_' . date('d-m-Y') . '.csv');

    }


}
