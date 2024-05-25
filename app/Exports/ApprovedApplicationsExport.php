<?php

namespace App\Exports;

use Carbon\Carbon;
use DB;
use App\Application;


class ApprovedApplicationsExport
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
            'Residential Address',
            'Company Address',
            'Country Of Incorporation',
            'Phone Number',
            'Contact Details',
            'Processing Currency',
            'Integration Preference',
            'Processing Country',
            'Industry Type',
            'License Status',
            'Referral Partners',
            'Percentage',
            'Monthly Volume',
            'Monthly Volume Currency',
            'Status',
            'Application Created Date'
        ];
    }

    public function download()
    {
        $slave_connection = env('SLAVE_DB_CONNECTION_NAME', '');

        if (!empty($slave_connection)) {
            \DB::setDefaultConnection($slave_connection);
            $getDatabaseName = \DB::connection()->getDatabaseName();
            _WriteLogsInFile($getDatabaseName . " connection from admin appproval application", 'slave_connection');
        }

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
                'applications.residential_address',
                'applications.business_address1',
                'applications.country',
                'applications.phone_no',
                'applications.skype_id',
                'applications.processing_currency',
                'applications.technology_partner_id',
                'applications.processing_country',
                'applications.id AS application_id',
                // Using application_id field to generate industry type  name below
                'applications.company_license',
                'agents.name as agentsName',
                'users.agent_commission',
                'applications.monthly_volume',
                'applications.monthly_volume_currency',
                'applications.status',
                'applications.created_at'
            )
                ->join('users', 'users.id', 'applications.user_id')
                ->leftjoin('agents', 'agents.id', 'users.agent_id');


            if (!is_null($this->id)) {
                $data = $data->whereIn('users.id', $this->id);
            }

            if (isset($input['country']) && $input['country'] != '') {
                $data = $data->where('applications.country', $input['country']);
            }

            if (isset($input['website_url']) && $input['website_url'] != '') {
                $data = $data->where('applications.website_url', 'like', '%' . $input['website_url'] . '%');
            }

            if (isset($input['name']) && $input['name'] != '') {
                $data = $data->where('users.name', 'like', '%' . $input['name'] . '%');
            }

            if (isset($input['email']) && $input['email'] != '') {
                $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
            }

            if (isset($input['user_id']) && $input['user_id'] != '') {
                $data = $data->where('applications.user_id', $input['user_id']);
            }

            if (isset($input['category_id']) && $input['category_id'] != '') {
                $data = $data->where('applications.category_id', $input['category_id']);
            }

            if (isset($input['technology_partner_id']) && $input['technology_partner_id'] != '') {
                $data = $data->where('applications.technology_partner_id', $input['technology_partner_id']);
            }

            if (isset($input['agent_id']) && $input['agent_id'] != '') {
                if ($input['agent_id'] == 'no-agent') {
                    $data = $data->where('users.agent_id', NULL);
                } else {
                    $data = $data->where('users.agent_id', $input['agent_id']);
                }
            }

            if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                $start_date = date('Y-m-d', strtotime($input['start_date']));
                $end_date = date('Y-m-d', strtotime($input['end_date']));

                $data = $data->where(DB::raw('DATE(applications.created_at)'), '>=', $start_date . ' 00:00:00')
                    ->where(DB::raw('DATE(applications.created_at)'), '<=', $end_date . ' 00:00:00');
            }

            $data = $data->where('applications.status', '4');


            $data->cursor()
                ->each(function ($data) use ($file) {
                    $data = $data->toArray();


                    // Converting technology_partner_id field before putting it into csv
                    $technologyPartnerIds = json_decode($data['technology_partner_id']);
                    if (is_array($technologyPartnerIds) && $technologyPartnerIds != null) {
                        $data['technology_partner_id'] = getTechnologyPartnerNames($technologyPartnerIds);
                    }

                    //mutating license status field
                    if (!empty($data['company_license'])) {
                        $data['company_license'] = getLicenseStatus($data['company_license']);
                    }

                    // mutating application status field
                    if (!empty($data['status'])) {
                        $data['status'] = getApplicationStatus($data['status']);
                    }

                    // mutating created at field
                    if (!empty($data['created_at'])) {
                        $data['created_at'] = Carbon::parse($data['created_at'])->format('d-m-Y H:i:s');
                    }

                    // mutating industry type field from application Id
                    if (!empty($data['application_id'])) {
                        $data['application_id'] = industryTypeName($data['application_id']);
                    }


                    fputcsv($file, $data);
                });

            fclose($file);
        }, 'Approved_Applications_Excel_' . date('d-m-Y') . '.csv');

    }


}
