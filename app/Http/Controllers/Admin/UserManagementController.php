<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendBulkEmailToUsers;
use Hash;
use DB;
use App\User;
use App\Ticket;
use Illuminate\Support\Collection;
use Validator;
use App\MIDDetail;
use App\ImageUpload;
use App\Transaction;
use App\Categories;
use App\Http\Requests;
use App\Mail\LiveMIDMail;
use App\MidOnCountryBase;
use Illuminate\Support\Str;
use App\Exports\UserExport;
use App\Application;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Exports\SubUserExport;
use App\Mail\SendLoginDetails;
use App\Mail\SendUserMultiMail;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;
use App\Mail\userEmailChange;
use App\UserBankDetails;
use App\Rules;
use App\RulesList;
use App\MailTemplates;
use App\AdminAction;
use Log;

class UserManagementController extends AdminController
{

    public $MIDDetail, $User, $Transaction, $Application, $Ticket, $template, $categories, $moduleTitleS, $moduleTitleP;

    public function __construct()
    {
        parent::__construct();
        $this->MIDDetail = new MIDDetail;
        $this->User = new User;
        $this->Transaction = new Transaction;
        $this->Application = new Application;
        $this->Ticket = new Ticket;
        $this->template = new MailTemplates;
        $this->categories = new Categories;

        $this->moduleTitleS = 'User Management';
        $this->moduleTitleP = 'admin.userManagement';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $payment_gateway_id = DB::table('middetails')->get();

        $dataT = $this->User->getMainUserData($input, $noList);

        $companyName = $this->Application::select('user_id', 'business_name')
            ->whereHas('user', function (Builder $query) {
                $query->where('is_white_label', '0');
            })
            ->orderBy('id', 'desc')
            ->get();

        $countries = config('country');

        $categories = $this->categories::orderBy('name')->get();
        $agents = DB::table('agents')->where('main_agent_id', '0')->whereNULL('deleted_at')->get(['id', 'name', 'email']);
        $cryptoMIDData = DB::table('middetails')->where('mid_type', '3')->whereNull('deleted_at')->get();
        $bankMIDData = DB::table('middetails')->where('mid_type', '2')->whereNull('deleted_at')->get();
        $template = $this->template->getListForMail();
        return view($this->moduleTitleP . '.index', compact('payment_gateway_id', 'dataT', 'noList', 'companyName', 'agents', 'countries', 'categories', 'cryptoMIDData', 'bankMIDData', 'template'))->with('i', ($request->input('page', 1) - 1) * $noList);
    }

    /* Sub User Managemant */
    public function subUsersMngt(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $dataT = User::select('applications.business_name', 'users.*')
            ->leftjoin('applications', 'applications.user_id', 'users.main_user_id');

        if (isset($input['email']) && $input['email'] != '') {
            $dataT = $dataT->where('email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $dataT = $dataT->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        $dataT = $dataT->where('users.main_user_id', '!=', '0')
            ->paginate($noList);

        $companyName = DB::table('applications')->select('business_name', 'user_id')
            ->join('users', function ($join) use ($input) {
                $join->on('users.id', '=', 'applications.user_id');
            })
            ->where('users.main_user_id', '0')
            ->where('users.is_white_label', '0')
            ->whereNull('applications.deleted_at')
            ->get();

        return view($this->moduleTitleP . '.subusers', compact('dataT', 'noList', 'companyName'))->with('i', ($request->input('page', 1) - 1) * $noList);
    }

    public function subUserListEdit(Request $request, $id)
    {
        $data = $this->User->findData($id);
        return view($this->moduleTitleP . '.subUserListEdit', compact('data'));
    }

    public function subUserEdit(Request $request, $id)
    {
        $data = $this->User->findData($id);
        return view($this->moduleTitleP . '.subUserEdit', compact('data'));
    }

    public function subUserUpdate(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'email' => ['required', Rule::unique('users')->ignore($id)->whereNull('deleted_at')],
                'password' => 'nullable|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        $input = \Arr::except($request->all(), array('_token', '_method', 'password_confirmation'));

        if ($input['password'] != '') {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $input['agreement'] = isset($input['agreement_permission']) ? '1' : '0';
        $input['transactions'] = isset($input['transactions_permission']) ? '1' : '0';
        $input['reports'] = isset($input['reports_permission']) ? '1' : '0';
        $input['settings'] = isset($input['settings_permission']) ? '1' : '0';

        unset($input['agreement_permission']);
        unset($input['transactions_permission']);
        unset($input['reports_permission']);
        unset($input['settings_permission']);
        $user = $this->User::select('main_user_id')->where('id', $id)->first();
        if ($this->User->updateSubData($input, $id)) {
            notificationMsg('success', 'Sub User Updpate Successfully!');
        } else {
            notificationMsg('error', 'Something went wrong, please try again.');
        }

        return redirect('admin/sub-user/' . $user->main_user_id);
    }

    public function subUserListUpdate(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'email' => ['required', Rule::unique('users')->ignore($id)->whereNull('deleted_at')],
                'password' => 'nullable|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        $input = \Arr::except($request->all(), array('_token', '_method', 'password_confirmation'));

        if ($input['password'] != '') {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $input['agreement'] = isset($input['agreement_permission']) ? '1' : '0';
        $input['transactions'] = isset($input['transactions_permission']) ? '1' : '0';
        $input['reports'] = isset($input['reports_permission']) ? '1' : '0';
        $input['settings'] = isset($input['settings_permission']) ? '1' : '0';

        unset($input['agreement_permission']);
        unset($input['transactions_permission']);
        unset($input['reports_permission']);
        unset($input['settings_permission']);
        $user = $this->User::select('main_user_id')->where('id', $id)->first();
        if ($this->User->updateSubData($input, $id)) {
            notificationMsg('success', 'Sub User Updpate Successfully!');
        } else {
            notificationMsg('error', 'Something went wrong, please try again.');
        }

        return redirect()->route('sub-users-management');
    }

    public function subUserDelete($id)
    {
        DB::beginTransaction();
        try {
            User::where('id', $id)->delete();
            DB::commit();
            notificationMsg('success', 'Sub User deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Something went wrong, please try again.');
        }
        return redirect()->route('sub-users-management');
    }

    public function moveMID(Request $request)
    {

        $url = request()->getRequestUri();

        $prevMID = $request->prevValue;
        $presentMID = $request->changeValue;
        $company_name = $request->company_name;
        $agent_id = $request->agent_id;
        $email = $request->email;

        $data = User::select('merchantapplications.company_name', 'merchantapplications.company_number', 'middetails.bank_name', 'users.*')
            ->join('merchantapplications', 'merchantapplications.user_id', 'users.id')
            ->leftJoin('middetails', 'middetails.id', 'users.mid');

        if (isset($prevMID) && $prevMID == !NULL) {
            $data = $data->where('users.mid', $prevMID)->update(['users.mid' => $presentMID]);
        }

        if (isset($company_name) && $company_name == !NULL) {
            $data = $data->where('merchantapplications.company_name', 'like', '%' . $company_name . '%')->update(['users.mid' => $presentMID]);
        }
        if (isset($agent_id) && $agent_id == !NULL) {
            $data = $data->where('users.agent_id', $agent_id)->update(['users.mid' => $presentMID]);
        }
        if (isset($email) && $email == !NULL) {
            $data = $data->where('users.email', 'like', '%' . $email . '%')->update(['users.mid' => $presentMID]);
        }

        notificationMsg('success', ' MID Updated Successfully');
        return redirect()->route('users-management');
    }

    // ================================================
    /* method : enableProductDashboard
     * @param  :
     * @Description : Product dashboard enable or disable
     */// ==============================================
    public function enableProductDashboard(Request $request)
    {
        $input = $request->except('_token');

        $this->User->where('id', '=', $input['dataId'])
            ->update(['enable_product_dashboard' => $input['status']]);

        return response()->json([
            'success' => $input['status'],
        ]);
    }

    // ================================================
    /* method : getUserManagementData
     * @param  : Request $request
     * @Description : Admin user's OTP for login enable or disable
     */// ==============================================
    public function isAdminOtpRequired(Request $request)
    {
        $input = $request->except('_token');

        $this->User->where('id', '=', $input['dataId'])
            ->update(['is_otp_required' => $input['status']]);

        return response()->json([
            'success' => $input['status'],
        ]);
    }

    public function getUserManagementData(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $data = $this->User->getUserData($input);
        return \DataTables::of($data)
            ->addColumn('Assign MID', function ($data) {
                if ($data->mid == '0' || $data->mid == '')
                    return '<p class="font-red-mint"> Not Assing </p>';
                else
                    return '<p>' . $data->bank_name . '</p>';
            })
            ->addColumn('Actions', function ($data) {
                if (auth()->guard('admin')->user()->can(['user-management-action'])) {
                    return '<a href=' . \URL::route('assign-mid', $data->id) . ' class="btn btn-info btn-outline">Assign MID</a>
                        <a href=' . \URL::route('send-password', $data->id) . ' class="btn btn-success btn-outline">Send Password</a>
                        <a data-bs-toggle="modal" href="#changePass" class="btn btn-danger btn-outline changePassBtn" data-id="' . $data->id . '">Change Password</a>';
                } else {
                    return '---';
                }
            })
            ->addColumn('multicheckmail', function ($data) {
                return '<div class="md-checkbox has-error">
                            <div class="icheck-material-white my-0">
                                <input type="checkbox" id="checkbox' . $data->id . '" name="multicheckmail[]" class="md-check multicheckmail" value="' . $data->id . '">
                                <label for="checkbox' . $data->id . '">
                                </label>
                            </div>
                        </div>';
            })
            ->addColumn('banrefund', function ($data) {
                if (auth()->guard('admin')->user()->can(['user-ban-refund'])) {
                    if ($data->make_refund == '0')
                        return '<div class="icheck-material-white my-0">
                                    <input type="checkbox" id="banrefund"  data-id="' . $data->id . '" checked>
                                    <label for="banrefund">
                                    </label>
                                </div>';
                    else
                        return '<div class="icheck-material-white my-0">
                                    <input type="checkbox" id="banrefund"  data-id="' . $data->id . '">
                                    <label for="banrefund">
                                    </label>
                                </div>';
                } else {
                    return '---';
                }
            })
            ->addColumn('activeStatus', function ($data) {
                if (auth()->guard('admin')->user()->can(['user-ban-refund'])) {
                    if ($data->is_active == '0')
                        return '<div class="icheck-material-white my-0">
                                    <input type="checkbox" id="activestatus"  data-id="' . $data->id . '" checked>
                                    <label for="activestatus">
                                    </label>
                                </div>';
                    else
                        return '<div class="icheck-material-white my-0">
                                    <input type="checkbox" id="activestatus"  data-id="' . $data->id . '">
                                    <label for="activestatus">
                                    </label>
                                </div>';
                } else {
                    return '---';
                }
            })
            ->addColumn('Deactivated', function ($data) {
                if ($data->is_active == 1) {
                    return '<div class="icheck-material-white my-0">
                                <input type="checkbox" name="is_active" id="active1" data-id="' . $data->id . '">
                                <label for="active1">
                                </label>
                            </div>';
                } else {
                    return '<div class="icheck-material-white my-0">
                                <input type="checkbox" name="is_active" id="active1" data-id="' . $data->id . '" checked>
                                <label for="active1">
                                </label>
                            </div>';
                }
            })
            ->addColumn('delete', function ($data) {
                if (auth()->guard('admin')->user()->can(['user-management-delete'])) {
                    return '<a class="btn btn-circle btn-icon-only btn-danger remove-record" data-target="#custom-width-modal" data-bs-toggle="modal" data-url="' . \URL::route('user-management-delete', $data->id) . '" data-id="' . $data->id . '">
                        <i class="icon-trash"></i>
                    </a>';
                } else {
                    return '---';
                }
            })
            ->addColumn('company_name', function ($data) {
                return '<a href="' . \URL::route('admin-log-activity-by-user', $data->id) . '" target="blank">' . $data->company_name . '</a>';
            })
            ->addColumn('ID', function ($data) {
                return '<a data-bs-toggle="modal" href="#showTransactionAmount" class="showTransactionAmount text-info" data-id="' . $data->id . '"><strong>' . $data->id . '</strong></a>';
            })
            ->addColumn('chart', function ($data) {
                return '<a class="btn btn-circle btn-icon-only btn-success" href="' . \URL::route('user-total-amount', $data->id) . '">
                        <i class="fa fa-bar-chart"></i>
                    </a>';
            })
            ->rawColumns(['Actions', 'Deactivated', 'Assign MID', 'delete', 'banrefund', 'activeStatus', 'company_name', 'ID', 'chart', 'multicheckmail'])
            ->make(true);
    }

    public function getUserTotalAmount(Request $request)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED'];

        $totalAmount = [];
        $count = 0;

        foreach ($currencyArray as $key => $value) {
            $chekTransactionInCurrency = Transaction::where('payment_gateway_id', '<>', '16')
                ->where('payment_gateway_id', '<>', '41')
                ->where('user_id', $request->get('id'))
                ->where('currency', $value)
                ->count();
            if ($chekTransactionInCurrency > 0) {
                $totalAmount[$value] = $this->Transaction->getUserTotalAmount($request->get('id'), $value, NULL);
                $count++;
            }
        }
        $html = view($this->moduleTitleP . '.totaltransactiondetails', compact('totalAmount'))->render();
        return response()->json([
            'success' => true,
            'totalAmount' => $totalAmount,
            'count' => $count,
            'html' => $html,
        ]);
    }

    public function userTotalAmount(Request $request, $id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED'];

        $totalAmount = [];
        $count = 0;

        foreach ($currencyArray as $key => $value) {
            $chekTransactionInCurrency = Transaction::where('payment_gateway_id', '<>', '16')
                ->where('payment_gateway_id', '<>', '41')
                ->where('user_id', $id)
                ->where('currency', $value)
                ->count();
            if ($chekTransactionInCurrency > 0) {
                $totalAmount[$value] = $this->Transaction->getUserTotalAmount($id, $value, $input);
                $count++;
            }
        }

        if ($count == 0) {
            notificationMsg('warning', 'No transactions found in system.');
            return redirect()->back();
        }

        return view($this->moduleTitleP . '.totaltransactionview', compact('totalAmount', 'id'));
    }

    public function getAssignCountrySpeMidsView(Request $request)
    {
        $input = $request->all();
        $addMoreLength = $input['assign_country_spe_mids_length'];
        $midList = DB::table('middetails')->where('is_provide_reccuring', '1')->pluck('bank_name', 'id');
        $suiteView = view($this->moduleTitleP . '.assignCountrySpeMidsView', compact('addMoreLength', 'midList'))->render();
        return response()->json(array('addMoreView' => $suiteView));
    }

    public function apiKeyGenerate($id)
    {
        $user = User::where('id', $id)->first();
        $token_api = \Str::random(30).time();
        // $token_api = $user->createToken(config("app.name"))->plainTextToken;
        $this->user->where('id', $id)->update(['api_key' => $token_api]);
        notificationMsg('success', 'API Key Generated Successfully!');
        return redirect()->back();
    }

    public function merchantRemoveAgent($id)
    {
        $result = User::where('id', $id)->update(['agent_id' => NULL, 'agent_commission' => 0.00]);
        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function assignMID($id)
    {
        $midData = DB::table('middetails')->where('mid_type', '1')->whereNull('deleted_at')->orWhere('mid_type', '5')->where('apm_type', '1')->get();
        $cryptoMIDData = DB::table('middetails')->where('mid_type', '3')->whereNull('deleted_at')->orWhere('mid_type', '5')->where('apm_type', '3')->get();
        $bankMIDData = DB::table('middetails')->where('mid_type', '2')->whereNull('deleted_at')->orWhere('mid_type', '5')->where('apm_type', '2')->get();
        $upiMIDData = DB::table('middetails')->where('mid_type', '4')->whereNull('deleted_at')->orWhere('mid_type', '5')->where('apm_type', '4')->get();
        $data = $this->user->findDataWithCompanyName($id);

        // mid list
        $midListInArray = MIDDetail::where('mid_type', '1')->whereNull('deleted_at')->pluck('bank_name', 'id')->all();
        // selectetd multi visa mid list
        $selectetdVisaMID = [];
        if (!empty($data->multiple_mid)) {
            $selectetdVisaMID = json_decode($data->multiple_mid);
        }
        $arrMainVisa = [];
        foreach ($selectetdVisaMID as $key => $value) {
            $arrVisa = MIDDetail::select("bank_name", "id")->where('mid_type', '1')->whereNull('deleted_at')->where("id", $value)->first();
            $arrMainVisa[$value] = $arrVisa->bank_name;
        }
        // unset value from mid list
        $midListInArrayVisa = MIDDetail::where('mid_type', '1')->whereNull('deleted_at')->whereIn("id", $selectetdVisaMID)->pluck('bank_name', 'id')->all();
        foreach ($midListInArrayVisa as $key => $value) {
            unset($midListInArray[$key]);
        }

        //Master card
        $midListInArrayAll = MIDDetail::where('mid_type', '1')->whereNull('deleted_at')->pluck('bank_name', 'id')->all();
        $selectetdMasterMID = [];
        if (!empty($data->multiple_mid_master)) {
            $selectetdMasterMID = json_decode($data->multiple_mid_master);
        }
        $arrMainMaster = [];
        foreach ($selectetdMasterMID as $kMaster => $vMaster) {
            $arrMaster = MIDDetail::select("bank_name", "id")->where('mid_type', '1')->whereNull('deleted_at')->where("id", $vMaster)->first();
            $arrMainMaster[$vMaster] = $arrMaster->bank_name;
        }

        // unset value from mid list
        $midListInArrayMaster = MIDDetail::where('mid_type', '1')->whereNull('deleted_at')->whereIn("id", $selectetdMasterMID)->pluck('bank_name', 'id')->all();

        foreach ($midListInArrayMaster as $key => $value) {
            unset($midListInArrayAll[$key]);
        }

        return view($this->moduleTitleP . '.assign_MID', compact('midData', 'data', 'cryptoMIDData', 'bankMIDData', 'upiMIDData', 'midListInArray', 'midListInArrayVisa', 'midListInArray', 'arrMainVisa', 'midListInArrayAll', 'arrMainMaster'));
    }

    public function cardEmailLimit($id)
    {
        $data = User::select('users.*')->where('users.id', $id)->first();

        return view($this->moduleTitleP . '.card_email_limit', compact('data'));
    }

    public function merchantRateFee($id)
    {
        $data = User::select('users.*')->where('users.id', $id)->first();

        return view($this->moduleTitleP . '.merchant_rate_fee', compact('data'));
    }

    public function merchantPersonalInfo($id)
    {
        $data = User::select('users.*')->where('users.id', $id)->first();

        return view($this->moduleTitleP . '.merchant_personal_info', compact('data'));
    }

    public function additionalMail($id)
    {
        $data = User::select('users.*')->where('users.id', $id)->first();

        // additional email array
        if ($data['additional_merchant_transaction_notification'] != null) {
            $additional_merchant_transaction_notification = implode(',', json_decode($data['additional_merchant_transaction_notification']));
        } else {
            $additional_merchant_transaction_notification = null;
        }

        return view($this->moduleTitleP . '.additional_mail', compact('data', 'additional_merchant_transaction_notification'));
    }

    public function assignMIDStore(Request $request)
    {
        $this->validate($request, [
            'mid' => 'required',
        ]);
        $userData = $this->user->where('id', $request->user_id)->first();
        //echo "<pre>";print_r($userData);exit();
        $input = \Arr::except($request->all(), array('_token', '_method', 'user_id'));
        // json of additional_merchant_transaction_notification
        if (isset($input['additional_merchant_transaction_notification'])) {
            if ($input['additional_merchant_transaction_notification'] != null) {
                $email_array = explode(',', $input['additional_merchant_transaction_notification']);
                $input['additional_merchant_transaction_notification'] = json_encode($email_array);
            }
        }

        if (isset($input['additional_mail'])) {
            if ($input["additional_mail"] != null) {
                $input["additional_mail"] = $input["additional_mail"];
            }
        }

        //
        $arr = [];
        if (isset($input['multiple_mid'])) {
            $input['multiple_mid'] = json_encode($input["multiple_mid"]);
        } else {
            $input['multiple_mid'] = "";
        }

        if (isset($input['multiple_mid_master'])) {
            $input['multiple_mid_master'] = json_encode($input["multiple_mid_master"]);
        } else {
            $input['multiple_mid_master'] = "";
        }
        $this->user->updateData($request->input('user_id'), $input);
        $addLog["old_data"]["user_id"] = $request->input('user_id');
        $addLog["old_data"]["mid"] = $userData["mid"];
        $addLog["old_data"]["visa_mid"] = $userData["visamid"];
        $addLog["old_data"]["mastercardmid"] = $userData["mastercardmid"];
        $addLog["old_data"]["discovermid"] = $userData["discovermid"];
        $addLog["old_data"]["amexmid"] = $userData["amexmid"];
        $addLog["old_data"]["crypto_mid"] = $userData["crypto_mid"];
        $addLog["old_data"]["bank_mid"] = $userData["bank_mid"];
        $addLog["old_data"]["multiple_mid"] = $userData["multiple_mid"];
        $addLog["old_data"]["multiple_mid_master"] = $userData["multiple_mid_master"];
        $addLog["new_data"] = $input;
        addAdminLog(AdminAction::ASSIGN_MID, "", $addLog, "MID changed successfully!");
        try {
            if ($userData->live_card_mid_id == 0) {
                // $userData->notify(new UserMIDLive($userData));
            }
        } catch (Exception $e) {
            //
        }

        \Session::put('success', 'MID changed successfully!');

        return redirect()->back();
    }

    public function assignMIDStoremerchant(Request $request)
    {
        $userData = $this->user->where('id', $request->user_id)->first();
        $input = \Arr::except($request->all(), array('_token', '_method', 'user_id'));
        $this->user->updateData($request->input('user_id'), $input);
        \Session::put('success', 'MID changed successfully!');
        return redirect()->back();
    }

    public function merchantUserExtraInfoStore(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method', 'countrySpeMids'));

        $this->user->updateData($request->get('id'), $input);

        notificationMsg('success', 'Information Update success!');

        return redirect()->back();
    }
    public function destroy($id)
    {
        if ($this->user->destroyData($id)) {
            $this->Application->destroyWithUserId($id);
            // $this->Ticket->destroyWithUserId($id);
            notificationMsg('success', 'User Delete Successfully!');
        } else {
            notificationMsg('error', 'Something went wrong, try again !');
        }

        return redirect()->route('users-management');
    }

    public function getAssignCountrySpeMidsDelete(Request $request, $id)
    {
        MidOnCountryBase::where('id', $id)->delete();

        notificationMsg('success', 'Country Specific MID Delete Successfully!');

        return redirect()->back();
    }

    public function makeRefundStatus(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($this->user->updateData($request->get('id'), $input)) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    // public function makeActiveStatus(Request $request)
    // {
    //     $input = \Arr::except($request->all(),array('_token', '_method'));

    //     if($this->user->updateData($request->get('id'), $input)) {
    //         return response()->json([
    //             'success' => true,
    //         ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //         ]);
    //     }
    // }

    public function sendPassword($id)
    {
        $data = $this->User->findData($id);

        $content = [
            'email' => $data->email,
            'password' => $data->password,
        ];

        try {
            \Mail::to($data->email)
                ->send(new SendLoginDetails($content));

            notificationMsg('success', 'Mail Send Successfully!');
            return redirect()->route('users-management');
        } catch (Exception $e) {
            notificationMsg('error', 'Problem in Mail Sending!');
            return redirect()->route('users-management');
        }
    }

    public function sendLiveMIDConfirmationMail($id)
    {
        $data = $this->User->findData($id);

        $content = [];

        try {
            \Mail::to($data->email)
                ->send(new LiveMIDMail($content));

            notificationMsg('success', 'Mail Send Successfully!');
            return redirect()->back();
        } catch (Exception $e) {
            notificationMsg('error', 'Problem in Mail Sending!');
            return redirect()->back();
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'conform_password' => 'required|same:password',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method', 'conform_password'));

        if ($validator->passes()) {
            $input['password'] = bcrypt($input['password']);

            if ($this->user->updateData($request->get('id'), $input)) {
                return response()->json([
                    'success' => true,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                ]);
            }
        }

        return response()->json(['errors' => $validator->errors()]);
    }

    public function sendMultiMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'bodycontent' => 'required',
        ]);
        try {
            $input = \Arr::except($request->all(), array('_token', '_method'));
            if ($validator->passes()) {
                $ids = explode(',', $input['id']);
                unset($input['id']);
                $users = DB::table('users')->select("email")->whereIn("id", $ids)->orderBy("id", "desc");
                $users->chunk(100, function ($data) use ($input) {
                    $userCollection = new Collection($data);
                    $userChunks = $userCollection->chunk(50);
                    foreach ($userChunks as $userChunk) {
                        SendBulkEmailToUsers::dispatch($userChunk, $input);
                    }

                });
                return response()->json([
                    'success' => true,
                ]);
            }
            return response()->json(['errors' => $validator->errors()]);
        } catch (\Exception $err) {
            Log::error(["users-bulk-email-error" => $err->getMessage()]);
        }



    }

    public function userActiveDeactive(Request $request)
    {
        $user_id = $request->id;
        $is_active = $request->is_active;

        if ($is_active == 1) {
            $user = $this->user->where('id', $user_id)->first();
            $userT = $user->Tokens()->first();
            if (empty($userT)) {
                $token_api = \Str::random(30).time();
                // $token_api = $user->createToken(config("app.name"))->plainTextToken;
                $this->user->where('id', $user_id)->update(['email_verified_at' => date('Y-m-d H:i:s'), 'api_key' => $token_api]);
            }
        }

        if ($this->user->where('id', $user_id)->update(['is_active' => $is_active])) {
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function userOTPRequired(Request $request)
    {
        $user_id = $request->id;
        $is_otp = $request->is_otp;

        if ($this->user->where('id', $user_id)->update(['is_otp_required' => $is_otp])) {
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function userIPRemove(Request $request)
    {
        $user_id = $request->id;
        $is_ip_remove = $request->is_ip_remove;

        if ($this->user->where('id', $user_id)->update(['is_ip_remove' => $is_ip_remove])) {
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function userBinRemove(Request $request)
    {
        $user_id = $request->id;
        $is_bin_remove = $request->is_bin_remove;

        if ($this->user->where('id', $user_id)->update(['is_bin_remove' => $is_bin_remove])) {
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function userDisableRule(Request $request)
    {
        $user_id = $request->id;
        $is_disable_rule = $request->is_disable_rule;

        if ($this->user->where('id', $user_id)->update(['is_disable_rule' => $is_disable_rule])) {
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function ipremove(Request $request)
    {
        $user_id = $request->id;
        $is_ip_remove = $request->is_ip_remove;

        if ($this->user->where('id', $user_id)->update(['is_ip_remove' => $is_ip_remove])) {
            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function changeUserTransactionMode(Request $request)
    {
        $is_test_mode = $request->is_test_mode;
        $user_id = $request->id;
        if (User::where('id', $user_id)->update(['is_test_mode' => $is_test_mode])) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function subUser(Request $request, $id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            return Excel::download(new SubUserExport, 'Sub_UserList_Excel_' . date('d-m-Y') . '.xlsx');
        }
        $payment_gateway_id = DB::table('middetails')->get();

        $dataT = $this->User->getSubUserData($input, $noList, $id);

        return view($this->moduleTitleP . '.subUser', compact('id', 'payment_gateway_id', 'dataT'));
    }

    public function userOtpReset($id)
    {
        User::where('id', $id)->update(['otp' => null]);

        notificationMsg('success', 'OTP Reset Successfully!');

        return redirect()->back();
    }

    // ================================================
    /*  method : showUserDetails
     * @ param  :
     * @ Description : get user details and show on model
     */// ==============================================
    public function showUserDetails(Request $request)
    {
        $data = $this->User->findData($request->input('id'));
        if ($data != null) {
            $html = view($this->moduleTitleP . '.userdetails', compact('data'))->render();
            return response()->json([
                'html' => $html,
            ]);
        } else {
            return response()->json([
                'html' => 'No user details found, please try again.',
            ]);
        }
    }

    function massremove(Request $request)
    {
        $user_id_array = $request->input('id');
        foreach ($user_id_array as $key => $value) {
            $this->User->destroyDataMultipal($value);
            $this->Application->destroyWithUserId($value);
        }
    }

    function massremoveSubUser(Request $request)
    {
        $user_id_array = $request->input('id');
        foreach ($user_id_array as $key => $value) {
            User::where('id', $value)->delete();
        }
    }

    public function setAgent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'agent_id' => 'required',
            // 'commission' => 'required',
            // 'commission_master' => 'required',
        ]);

        if ($validator->passes()) {
            if (
                $this->user->updateData($request->user_id, [
                    'agent_id' => $request->agent_id
                    // 'agent_commission' => $request->commission,
                    // "agent_commission_master_card"=>$request->commission_master
                ])
            ) {
                return response()->json([
                    'success' => true,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                ]);
            }
        }

        return response()->json(['errors' => $validator->errors()]);
    }
    // ================================================
    /*  method : merchantUserCreate
     * @ param  :
     * @ Description : load merchant create page.
     */// ==============================================
    public function merchantUserCreate(Request $request)
    {
        return view($this->moduleTitleP . '.createMerchant');
    }
    // ================================================
    /*  method : merchantUserStore
     * @ param  :
     * @ Description : store merchant user.
     */// ==============================================
    public function merchantUserStore(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|max:50|regex:/^[a-z\d\-_\s\.]+$/i',
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'mobile_no' => 'nullable|unique:users,mobile_no,NULL,id,deleted_at,NULL',
                'password' => 'required||min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)'
            ]
        );

        $input = \Arr::except($request->all(), array('_token', '_method'));

        $uuid = Str::uuid()->toString();

        unset($input['password_confirmation']);

        $input['uuid'] = $uuid;
        $input['token'] = Str::random(40) . time();
        $input['is_active'] = '1';

        $user = $this->user->storeData($input);
        $token_api = \Str::random(30).time();
        // $token_api = $user->createToken(config("app.name"))->plainTextToken;

        $this->user::where('id', $user->id)->update(['email_verified_at' => date('Y-m-d H:i:s'), 'api_key' => $token_api]);

        \Session::put('success', 'Merchant Created Successfully!');

        return redirect()->route('users-management');
    }

    public function merchantUserEdit(Request $request, $id)
    {
        $merchantUser = User::select('users.*', 'applications.business_name as business_name')->leftjoin('applications', 'applications.user_id', 'users.id')->find($id);
        return view($this->moduleTitleP . '.merchant_user_edit', compact('merchantUser'));
    }
    public function merchantUserUpdate(Request $request, $id)
    {
        $input = $request->all();
        $user = DB::table('users')->where('id', $id)->first();

        if ($user->is_whitelable == '1') {
            $this->validate(
                $request,
                [
                    'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                    'business_name' => 'required',
                    'email' => ['required', Rule::unique('users')->ignore($id)->whereNull('deleted_at')],
                    'mobile_no' => 'required|numeric',
                    'country_code' => 'required',
                ],
                [
                    'name.regex' => 'Please Enter Only Alphanumeric Characters.'
                ]
            );

            Application::where('user_id', $user->id)->update(['business_name' => $input['business_name']]);
            unset($input['business_name']);
        } else {
            $this->validate($request, [
                'name' => 'required',
                'email' => ['required', Rule::unique('users')->ignore($id)->whereNull('deleted_at')],
                'mobile_no' => 'required|numeric',
                'country_code' => 'required',
            ]);
        }

        if ($input['password'] != '') {
            $this->validate($request, [
                'password_confirmation' => "same:password",
                'password' => 'required|min:9|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ], ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']);

            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        //exit();
        unset($input['_method']);
        unset($input['_token']);
        unset($input['password_confirmation']);
        // if ($user->email != $input["email"]) {
            // $data["id"] = $user->id;
            // $data['token'] = Str::random(40) . time();
            // $input['token'] = $data['token'];
            // $input["email_changes"] = $input["email"];
            // $data["name"] = $input["name"];
            // Mail::to($user->email)->send(new userEmailChange($data));
            // unset($input["email"]);
            $this->user->updateData($id, $input);
            \Session::put('success', 'Merchant User Updated Successfully!');
        // } else {
        //     $this->user->updateData($id, $input);
        //     \Session::put('success', 'Merchant User Updated Successfully!');
        // }

        return redirect()->route('users-management');
    }

    public function export(Request $request)
    {
        return Excel::download(new UserExport, 'UserList_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function getUserBankDetails($id)
    {
        $bankDetails = UserBankDetails::where('user_id', $id)->first();
        return view('admin.userManagement.userBankDetails')->with('bankDetails', $bankDetails);
    }

    public function merchantRules($id)
    {
        $CardRules = Rules::where("rules_type", "Card")->where('user_id', $id)->whereNull("rules.deleted_at")->count();
        $CryptoRules = Rules::where("rules_type", "Crypto")->where('user_id', $id)->whereNull("rules.deleted_at")->count();
        $BankRules = Rules::where("rules_type", "Bank")->where('user_id', $id)->whereNull("rules.deleted_at")->count();
        $upiRules = Rules::where("rules_type", "UPI")->where('user_id', $id)->whereNull("rules.deleted_at")->count();
        $data = User::select('users.*')->where('users.id', $id)->first();
        return view($this->moduleTitleP . '.merchantRules', compact('data', 'CardRules', 'CryptoRules', 'BankRules', 'upiRules', 'id'));
    }

    public function getTemplateData(Request $request)
    {
        $input = $request->all();

        $template = $this->template->getTemplateForMail($input['id']);

        return response()->json($template);
    }
}