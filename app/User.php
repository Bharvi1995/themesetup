<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPasswordNotification;
use DB;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $table = 'users';


    protected $fillable = [
        'uuid',
        'name',
        'email',
        'email_verified_at',
        'country_code',
        'mobile_no',
        'mid',
        'crypto_mid',
        'is_test_mode',
        'amexmid',
        'visamid',
        'mastercardmid',
        'discovermid',
        'api_key',
        'callback_url',
        'visa_credit',
        'visa_debit',
        'mastercard_debit',
        'mastercard_credit',
        'transaction_fee',
        'setup_fee',
        'setup_fee_master_card',
        'refund_fee',
        'flagged_fee',
        'retrieval_fee',
        'threshold_amount',
        'pci',
        'chargeback_fee',
        'annual_fee',
        'merchant_discount_rate',
        'merchant_discount_rate_master_card',
        'rolling_reserve_paercentage',
        'fx_margin',
        'currency',
        'is_active',
        'is_desable_vt',
        'is_ip_remove',
        'make_refund',
        'is_otp_required',
        'otp',
        'one_day_card_limit',
        'one_day_email_limit',
        'one_week_card_limit',
        'one_week_email_limit',
        'one_month_card_limit',
        'one_month_email_limit',
        'daily_card_decline_limit',
        'is_multi_mid',
        'mid_list',
        'per_transaction_limit',
        'merchant_transaction_notification',
        'user_transaction_notification',
        'additional_merchant_transaction_notification',
        'additional_mail',
        'logo',
        'iframe_logo',
        'enable_product_dashboard',
        'platform',
        'website_url',
        'agent_id',
        'agent_commission',
        'agent_commission_master_card',
        'crypto_api_id',
        'category',
        'token',
        'remember_token',
        'password',
        'email_changes',
        'agreement',
        'transactions',
        'reports',
        'settings',
        'application_show',
        'main_user_id',
        'multiple_mid',
        'bank_mid',
        'is_disable_rule',
        'is_whitelable',
        'is_white_label',
        'white_label_agent_id',
        'is_login_wl_merchant',
        'is_bin_remove',
        'merchant_discount_rate_amex_card',
        'merchant_discount_rate_discover_card',
        'setup_fee_amex_card',
        'setup_fee_discover_card',
        'minimum_settlement_amount',
        'payment_frequency',
        'merchant_discount_rate_upi'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function getUser($user_id)
    {
        return static::find($user_id);
    }

    public function sendPasswordResetNotification($token)
    {
        $userName = static::where('email', request()->email)->pluck('name')->first();

        $this->notify(new CustomResetPasswordNotification($userName, $token, request()->email));
    }
    public function midDetail()
    {
        return $this->belongsTo(MIDDetail::class, 'mid');
    }

    public function merchantapplications()
    {
        return $this->hasOne('\App\Merchantapplication', '');
    }

    public function application()
    {
        return $this->hasOne('App\Application', '');
    }

    public function transactions()
    {
        return $this->hasMany('\App\Transaction');
    }

    public function user_report_childs()
    {
        return $this->hasMany('\App\UserGenerateReportChild');
    }

    public function un_reported_childs()
    {
        return $this->hasMany('\App\PayoutReportChild')->where(['is_report' => '0']);
    }

    public function remove_flagged_transactions()
    {
        return $this->hasMany('\App\RemoveFlaggedTransaction');
    }

    public function websiteUrl()
    {
        return $this->hasMany('\App\WebsiteUrl');
    }
    /*
    |=============================|
    | For a  admin porpouse       |
    |=============================|
    */
    public function agent()
    {
        return $this->belongsTo('\App\Agent');
    }

    public function getAdminData()
    {
        return static::get();
    }

    public function getData()
    {
        return static::get();
    }

    public function storeData($input)
    {
        $input['password'] = bcrypt($input['password']);

        return static::create($input);
    }

    public function storeSubData($input)
    {
        \DB::beginTransaction();
        try {
            $appData = Merchantapplication::where('user_id', \Auth::user()->id)->first();
            $last_id = static::insertGetId($input);
            $app['user_id'] = $last_id;
            $app['company_name'] = $appData->company_name;
            $app['company_number'] = $appData->company_number;
            $app['first_name'] = $appData->first_name;
            $app['last_name'] = $appData->last_name;
            $app['email'] = $input['email'];
            $app['phone_no'] = '';
            Merchantapplication::create($app);
            \DB::commit();

            return true;
        } catch (\Exception $e) {
            \DB::rollback();

            return false;
        }
    }

    public function updateSubData($input, $id)
    {
        return static::where('id', $id)->update($input);
    }

    public function findData($id)
    {
        return static::find($id);
    }

    public function findDataWithCompanyName($id)
    {
        return static::select('users.*', 'applications.business_name as company_name')
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->where('users.id', $id)->first();
    }

    public function updateData($id, $input)
    {
        return static::where('id', $id)->update($input);
    }

    public function getAllSubUsers($user_id, $input)
    {
        return static::where('main_user_id', $user_id)->get();
    }

    /*
    |=============================|
    | For A Admin Porpouse        |
    |=============================|
    */

    public function getUserData($input, $noList)
    {
        $data = static::select('applications.business_name', 'applications.company_number', 'middetails.bank_name', 'users.*')
            ->join('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }
        if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('users.name', 'like', '%' . $input['name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.company_number', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->orderBy('users.id', 'desc')
            ->paginate($noList);

        return $data;
    }
    //    public function chamgeMID($prevMid,$presentMID,$optional)
    //    {

    //     $data = static::select('applications.business_name', 'applications.company_number', 'middetails.bank_name','users.*')
    //     ->join('applications', 'applications.user_id', 'users.id')
    //     ->leftJoin('middetails', 'middetails.id', 'users.mid');
    //     // dd("hi");

    //     if($optional ===NULL){
    //         $data = $data->where('applications.business_name',  'like', '%' . $optional . '%')->update(['users.mid'=>$presentMID]);
    //     }
    //     else{
    //         $data = $data->where('users.mid', $prevMid)->update(['users.mid'=>$presentMID]);

    //     }

    // $data =  $data = $data->where('applications.business_name',  'like', '%' . $input['company_name'] . '%');

    // if(request()->getRequestUri()))
    // if(isset())

    // if($mid != '') {
    //     $data = $data->update(['users.mid',$mid]);
    // }
    //    }
    public function getMainUserData($input, $noList)
    {
        $data = static::select(
            'applications.status as appStatus',
            'applications.business_name',
            'applications.phone_no',
            'applications.business_contact_first_name',
            'applications.business_contact_last_name',
            'middetails.bank_name',
            'users.*',
            'agents.name as agent'
        )
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->leftJoin('agents', 'agents.id', 'users.agent_id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');

        if (isset($input['verify_status']) && $input['verify_status'] != '') {
            if ($input['verify_status'] == 1) {
                $data = $data->where('users.email_verified_at', '!=', null);
            } else {
                $data = $data->where('users.email_verified_at', '=', null);
            }
        }
        if (isset($input['application_status']) && $input['application_status'] != '') {
            if ($input['application_status'] == '0') {
                $data = $data->whereNull('applications.id');
            } else {
                $data = $data->where('applications.status', $input['application_status']);
            }
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['api_key']) && $input['api_key'] != '') {
            $data = $data->where('users.api_key', $input['api_key']);
        }
        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('applications.country', 'like', '%' . $input['country'] . '%');
        }
        if (isset($input['company']) && $input['company'] != '') {
            $data = $data->where('applications.business_name', $input['company']);
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }
        if (isset($input['visamid']) && $input['visamid'] != '') {
            $data = $data->where('users.visamid', $input['visamid']);
        }
        if (isset($input['mastercardmid']) && $input['mastercardmid'] != '') {
            $data = $data->where('users.mastercardmid', $input['mastercardmid']);
        }
        if (isset($input['bank_mid']) && $input['bank_mid'] != '') {
            $data = $data->where('users.bank_mid', $input['bank_mid']);
        }
        if (isset($input['crypto_mid']) && $input['crypto_mid'] != '') {
            $data = $data->where('users.crypto_mid', $input['crypto_mid']);
        }

        if (isset($input['agent_id']) && $input['agent_id'] != '') {
            $data = $data->where('users.agent_id', $input['agent_id']);
        }
        if (isset($input['category']) && $input['category'] != '') {
            $data = $data->where('applications.category_id', $input['category']);
        }
        if (isset($input['website']) && $input['website'] != '') {
            $data = $data->where('applications.website_url', 'like', '%' . $input['website'] . '%');
        }
        if (isset($input['global_rule']) && $input['global_rule'] != '') {
            $data = $data->where('users.is_disable_rule', $input['global_rule']);
        }
        if (isset($input['api_key']) && $input['api_key'] != '') {
            $data = $data->where('users.api_key', $input['api_key']);
        }
        if (isset($input['mode']) && $input['mode'] != '') {
            if ($input['mode'] == 'test') {
                $test = [1, 2];
                $data = $data->whereIn('users.mid', $test);
            }
            if ($input['mode'] == 'live') {
                $test = [1, 2];
                $data = $data->whereNotIn('users.mid', $test);
            }
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }
        $data = $data->orderBy('users.id', 'desc')
            ->where('users.main_user_id', '0')
            ->where('users.is_white_label', '0')
            ->paginate($noList);

        return $data;
    }

    public function getMainWLUserData($input, $noList)
    {
        $data = static::select(
            'applications.business_name',
            'middetails.bank_name',
            'users.*'
        )
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['company']) && $input['company'] != '') {
            $data = $data->where('applications.business_name', $input['company']);
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }
        if (isset($input['category']) && $input['category'] != '') {
            $data = $data->where('applications.category_id', $input['category']);
        }
        if (isset($input['website']) && $input['website'] != '') {
            $data = $data->where('applications.website_url', 'like', '%' . $input['website'] . '%');
        }
        if (isset($input['api_key']) && $input['api_key'] != '') {
            $data = $data->where('users.api_key', $input['api_key']);
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }
        $data = $data->orderBy('users.id', 'desc')
            ->where('users.main_user_id', '0')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->paginate($noList);

        return $data;
    }

    public function getAllWLUserDataForAdmin($input, $noList)
    {
        $data = static::select(
            'applications.business_name',
            'middetails.bank_name',
            'users.*'
        )
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['company']) && $input['company'] != '') {
            $data = $data->where('applications.business_name', $input['company']);
        }

        if (isset($input['wl_agents']) && $input['wl_agents'] != '') {
            $data = $data->where('users.white_label_agent_id', $input['wl_agents']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }
        if (isset($input['category']) && $input['category'] != '') {
            $data = $data->where('applications.category_id', $input['category']);
        }
        if (isset($input['website']) && $input['website'] != '') {
            $data = $data->where('applications.website_url', 'like', '%' . $input['website'] . '%');
        }
        if (isset($input['api_key']) && $input['api_key'] != '') {
            $data = $data->where('users.api_key', $input['api_key']);
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }
        $data = $data->orderBy('users.id', 'desc')
            ->where('users.main_user_id', '0')
            ->where('users.is_white_label', '1')
            ->paginate($noList);

        return $data;
    }

    public function getMainWLUserDataForAdmin($input, $noList, $id)
    {
        $data = static::select(
            'applications.business_name',
            'middetails.bank_name',
            'users.*'
        )
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['company']) && $input['company'] != '') {
            $data = $data->where('applications.business_name', $input['company']);
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }
        if (isset($input['category']) && $input['category'] != '') {
            $data = $data->where('applications.category_id', $input['category']);
        }
        if (isset($input['website']) && $input['website'] != '') {
            $data = $data->where('applications.website_url', 'like', '%' . $input['website'] . '%');
        }
        if (isset($input['api_key']) && $input['api_key'] != '') {
            $data = $data->where('users.api_key', $input['api_key']);
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }
        $data = $data->orderBy('users.id', 'desc')
            ->where('users.main_user_id', '0')
            ->where('users.is_white_label', '1')
            ->where('users.white_label_agent_id', $id)
            ->paginate($noList);

        return $data;
    }

    public function getActiveMerchantsData($input, $noList, $activeMerchantsArray)
    {
        $data = static::select('applications.business_name', 'applications.company_number', 'middetails.bank_name', 'users.*')
            ->join('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid')
            ->whereIn('users.id', $activeMerchantsArray);

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }
        if (isset($input['visamid']) && $input['visamid'] != '') {
            $data = $data->where('users.visamid', $input['visamid']);
        }
        if (isset($input['mastercardmid']) && $input['mastercardmid'] != '') {
            $data = $data->where('users.mastercardmid', $input['mastercardmid']);
        }
        if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('users.name', 'like', '%' . $input['name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.company_number', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->orderBy('users.id', 'desc')->where('users.main_user_id', '0')
            ->paginate($noList);

        return $data;
    }

    public function getSubUserData($input, $noList, $id)
    {
        $data = static::select('applications.business_name', 'middetails.bank_name', 'users.*')
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }
        if (isset($input['name']) && $input['name'] != '') {
            $data = $data->where('users.name', 'like', '%' . $input['name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.company_number', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->where('users.main_user_id', $id)
            ->orderBy('users.id', 'desc')
            ->paginate($noList);

        return $data;
    }

    public function destroyData($id)
    {
        \DB::beginTransaction();
        try {
            $user = \DB::table('users')->where('id', $id)->first();
            \DB::table('users')
                ->where('id', $id)
                ->delete();

            \DB::table('applications')
                ->where('user_id', $id)
                ->delete();

            \DB::commit();

            return true;
        } catch (\Exception $e) {
            \DB::rollback();

            return false;
        }
    }

    public function getAllUserDataForExcel($input)
    {
        $data = static::select('applications.business_name', 'applications.company_number', 'users.email as email')
            ->join('applications', 'applications.user_id', 'users.id');

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.company_number', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->get();

        return $data;
    }

    public function getAllMainUserDataForExcel($input)
    {
        $data = static::select('applications.business_name', 'applications.company_number', 'users.email as email')
            ->join('applications', 'applications.user_id', 'users.id');

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.company_number', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->where('users.main_user_id', '0')->get();

        return $data;
    }

    public function getAllSubUserDataForExcel($input, $id)
    {
        $data = static::select('applications.business_name', 'applications.company_number', 'users.email as email')
            ->leftjoin('applications', 'applications.user_id', 'users.id');

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('users.mid', $input['payment_gateway_id']);
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.company_number', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->where('users.main_user_id', $id)
            ->groupBy('users.mid')
            ->get();

        return $data;
    }

    public function destroyDataMultipal($id)
    {
        $user = static::where('id', $id)->first();

        return static::where('id', $id)->delete();
    }

    public function getUserArray()
    {
        return static::where('is_sub_user', '0')->pluck('email', 'id')->all();
    }

    public function getUsersUsingAgentId($agentId)
    {
        return static::select('users.*', 'applications.business_name as company_name')
            ->leftjoin('applications', 'applications.user_id', 'users.id')
            ->where('users.agent_id', $agentId)
            ->get();
    }

    public function getUserDataForAgent($input, $noList)
    {
        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'middetails.bank_name', 'users.*', 'applications.status as appStatus')
            ->leftJoin('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid_list');

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('users.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.token', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('users.otp', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->whereIn('users.id', $userIds)
            ->orderBy('users.id', 'desc')
            ->paginate($noList);

        return $data;
    }

    public function getAgentUsers()
    {
        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'middetails.bank_name', 'users.*')
            ->join('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid_list');

        $data = $data->whereIn('users.id', $userIds)
            ->orderBy('users.id', 'desc')
            ->limit(10)
            ->get();

        return $data;
    }

    public function getWLAgentUsers()
    {
        $agentId = auth()->guard('agentUserWL')->user()->id;

        $userIds = \DB::table('users')->where('white_label_agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'users.*')
            ->join('applications', 'applications.user_id', 'users.id');

        $data = $data->whereIn('users.id', $userIds)
            ->orderBy('users.id', 'desc')
            ->limit(10)
            ->get();

        return $data;
    }

    public function findUserDataForAgent($id)
    {
        return static::select('applications.business_name', 'middetails.bank_name', 'users.*')
            ->join('applications', 'applications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid')
            ->where('users.id', $id)
            ->first();
    }

    public function isIPRestricted()
    {
        if ($this->is_ip_remove == '0') {
            $getIPData = WebsiteUrl::where('user_id', $this->id)
                ->where('ip_address', request()->ip())
                ->first();

            // if IP is not added on the IP whitelist
            if (!$getIPData) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'This API key is not permitted for transactions from this IP address (' . request()->ip() . '). Please add your IP by clicking on this link : https://testpay.com',
                    'customer_order_id' => request()->customer_order_id,
                ]);
            }

            // if IP is not approved
            if ($getIPData->is_active == '0') {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Your Website URL and your IP (' . request()->ip() . ') is still under approval , Please contact ' . config("app.name") . ' Support for more information',
                    'customer_order_id' => request()->customer_order_id,
                ]);
            }

            request()->merge([
                'website_url_id' => $getIPData->id
            ]);
        }

        return false;
    }

    public function merchantCountData($agentId = null)
    {
        $users = static::select([
            DB::raw("SUM(if(users.mid = '1' or users.mid = '2' or users.mid is null, 1, 0)) as notLiveMerchant"),
            DB::raw("SUM(if(users.mid != '1' and users.mid != '2' and users.mid is not null, 1, 0)) as liveMerchant"),
            DB::raw("COUNT(id) as totalMerchant")
        ])
            ->where('users.is_active', '=', '1')
            ->where('users.main_user_id', '=', '0')
            ->whereNull('users.deleted_at');

        if ($agentId) {
            $users = $users->where('agent_id', $agentId);
        }

        $users = $users->first();
        return $users;

    }
}