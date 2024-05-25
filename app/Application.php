<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $table = 'applications';

    protected $guarded = array();

    protected $fillable = [
        'user_id',
        'business_type',
        'business_category',
        'accept_card',
        'business_name',
        'website_url',
        'business_contact_first_name',
        'business_contact_last_name',
        'business_address1',
        'business_address2',
        'country',
        'customer_location',
        'processing_currency',
        'settlement_currency',
        'phone_no',
        'skype_id',
        'category_id',
        'technology_partner_id',
        'processing_country',
        'company_license',
        'state',
        'city',
        'postcode',
        'licence_number',
        'board_of_directors',
        'passport',
        'company_incorporation_certificate',
        'latest_bank_account_statement',
        'utility_bill',
        'previous_processing_statement',
        'owner_personal_bank_statement',
        'extra_document',
        'wl_extra_document',
        'is_completed',
        'is_reassign',
        'reason_reassign',
        'is_processing',
        'is_placed',
        'is_agreement',
        'is_not_interested',
        'is_terminated',
        'is_reject',
        'reason_reject',
        'remember_token',
        'status',
        'country_code',
        'other_processing_country',
        'other_industry_type',
        'licence_document',
        'monthly_volume',
        'monthly_volume_currency',
        'residential_address',
        'domain_ownership',
        'moa_document'
    ];

    public function storeData($data)
    {
        return static::create($data);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Categories');
    }

    public function technology_partner()
    {
        return $this->belongsTo('App\TechnologyPartner');
    }

    public function findData($id)
    {
        $data = static::select('applications.*', 'users.name', 'users.email', 'users.agent_id', 'users.agent_commission')
            ->join('users', 'users.id', 'applications.user_id')
            ->where('applications.id', $id)
            ->first();
        return $data;
    }

    public function FindDataFromUser($user_id)
    {
        $data = static::select('applications.*', 'users.name', 'users.email', 'users.agent_id', 'users.agent_commission', 'adp.sent_files as agreement_sent', 'adp.files as agreement_received')
            ->join('users', 'users.id', 'applications.user_id')
            ->leftjoin('agreement_document_upload as adp', 'adp.application_id', 'applications.id')
            ->where('applications.user_id', $user_id)
            ->first();
        return $data;
    }

    public function updateApplication($id, $input)
    {
        return static::where('id', $id)->update($input);
    }

    public function destroyWithUserId($user_id)
    {
        return static::where('user_id', $user_id)->delete();
    }

    public function softDelete($id)
    {
        return static::where('id', $id)->delete();
    }

    public function restore($id)
    {
        return static::onlyTrashed()->where('id', $id)->restore();
    }

    public function getCompanyName()
    {
        return static::select('applications.business_name', 'applications.user_id')
            ->join('users', 'users.id', '=', 'applications.user_id')
            ->whereNull('users.deleted_at')
            ->whereNull('applications.deleted_at')
            ->whereIn('status', ['4', '5', '6', '10', '11'])
            ->get();
    }

    public function getBankApplications($input, $noList)
    {
        $data = static::select(
            'users.email',
            'applications.*',
            'users.name',
            'application_assign_to_bank.status as app_status'
        )
            ->join('users', 'users.id', 'applications.user_id')
            ->join('application_assign_to_bank', 'applications.id', '=', 'application_assign_to_bank.application_id')
            ->where('application_assign_to_bank.bank_user_id', auth()->guard('bankUser')->user()->id)
            ->where('application_assign_to_bank.deleted_at', null);

        if (isset($input['category_id']) && $input['category_id'] != '') {
            $data = $data->where('applications.category_id', $input['category_id']);
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('applications.user_id', $input['user_id']);
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('application_assign_to_bank.status', $input['status']);
        }

        $data = $data->orderBy('application_assign_to_bank.created_at', 'desc')
            ->distinct()
            ->paginate($noList);

        return $data;
    }

    public function getBankApplicationsApproved($input, $noList)
    {
        $data = static::select(
            'users.email',
            'applications.*',
            'users.name',
            'application_assign_to_bank.status as app_status'
        )
            ->join('users', 'users.id', 'applications.user_id')
            ->join('application_assign_to_bank', 'applications.id', '=', 'application_assign_to_bank.application_id')
            ->where('application_assign_to_bank.bank_user_id', auth()->guard('bankUser')->user()->id)
            ->where('application_assign_to_bank.deleted_at', null)
            ->where('application_assign_to_bank.status', '1');

        if (isset($input['category_id']) && $input['category_id'] != '') {
            $data = $data->where('applications.category_id', $input['category_id']);
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('applications.user_id', $input['user_id']);
        }

        $data = $data->orderBy('application_assign_to_bank.created_at', 'desc')
            ->distinct()
            ->paginate($noList);

        return $data;
    }

    public function getBankApplicationsDeclined($input, $noList)
    {
        $data = static::select(
            'users.email',
            'applications.*',
            'users.name',
            'application_assign_to_bank.status as app_status',
            'application_assign_to_bank.declined_reason as declined_reason'
        )
            ->join('users', 'users.id', 'applications.user_id')
            ->join('application_assign_to_bank', 'applications.id', '=', 'application_assign_to_bank.application_id')
            ->where('application_assign_to_bank.bank_user_id', auth()->guard('bankUser')->user()->id)
            ->where('application_assign_to_bank.deleted_at', null)
            ->where('application_assign_to_bank.status', '2');

        if (isset($input['category_id']) && $input['category_id'] != '') {
            $data = $data->where('applications.category_id', $input['category_id']);
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('applications.user_id', $input['user_id']);
        }

        $data = $data->orderBy('application_assign_to_bank.created_at', 'desc')
            ->distinct()
            ->paginate($noList);

        return $data;
    }

    public function getBankApplicationsReferred($input, $noList)
    {
        $data = static::select(
            'users.email',
            'applications.*',
            'users.name',
            'application_assign_to_bank.status as app_status',
            'application_assign_to_bank.referred_note as referred_note',
            'application_assign_to_bank.extra_documents'
        )
            ->join('users', 'users.id', 'applications.user_id')
            ->join('application_assign_to_bank', 'applications.id', '=', 'application_assign_to_bank.application_id')
            ->where('application_assign_to_bank.bank_user_id', auth()->guard('bankUser')->user()->id)
            ->where('application_assign_to_bank.deleted_at', null)
            ->where('application_assign_to_bank.status', '3');

        if (isset($input['category_id']) && $input['category_id'] != '') {
            $data = $data->where('applications.category_id', $input['category_id']);
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('applications.user_id', $input['user_id']);
        }

        $data = $data->orderBy('application_assign_to_bank.created_at', 'desc')
            ->distinct()
            ->paginate($noList);

        return $data;
    }

    public function getBankApplicationsPending($input, $noList)
    {
        $data = static::select(
            'users.email',
            'applications.*',
            'users.name',
            'application_assign_to_bank.status as app_status'
        )
            ->join('users', 'users.id', 'applications.user_id')
            ->join('application_assign_to_bank', 'applications.id', '=', 'application_assign_to_bank.application_id')
            ->where('application_assign_to_bank.bank_user_id', auth()->guard('bankUser')->user()->id)
            ->where('application_assign_to_bank.deleted_at', null)
            ->where('application_assign_to_bank.status', '0');

        if (isset($input['category_id']) && $input['category_id'] != '') {
            $data = $data->where('applications.category_id', $input['category_id']);
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('applications.user_id', $input['user_id']);
        }

        $data = $data->orderBy('application_assign_to_bank.created_at', 'desc')
            ->distinct()
            ->paginate($noList);

        return $data;
    }

    public function getTransactionData()
    {
        return \DB::table('applications')
            ->join('users', 'users.id', 'applications.user_id')
            ->pluck('business_name', 'user_id')->toArray();
    }

    public function getBankUserMids($bankId)
    {
        $ArrData = array(
            'user_id' => array(),
            'mid' => array(),
        );
        $userIds = Application::join('users', 'users.id', 'applications.user_id')
            ->join('application_assign_to_bank', 'applications.id', '=', 'application_assign_to_bank.application_id')
            ->where('application_assign_to_bank.bank_user_id', $bankId)
            ->where('application_assign_to_bank.deleted_at', null)
            ->where('application_assign_to_bank.status', '1')
            ->orderBy('application_assign_to_bank.created_at', 'desc')
            ->distinct()->pluck('users.id')->toArray();

        if (!empty($userIds)) {
            $ArrData['user_id'] = $userIds;
            $ArrData['mid'] = array();

            $bankmids = \DB::table('middetails')
                ->where('bank_id', $bankId)
                ->where('is_active', '1')
                ->whereNull('deleted_at')
                ->pluck('id')->toArray();

            if (!empty($bankmids)) {
                $ArrData['mid'] = $bankmids;
            }
        }
        if (empty($ArrData['user_id']) || empty($ArrData['mid'])) {
            return array();
        }
        return $ArrData;
    }
}
