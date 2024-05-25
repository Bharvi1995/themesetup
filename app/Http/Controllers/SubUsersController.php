<?php
namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Hash;
use Validator;
use App\ImageUpload;
use App\User;
use Excel;

class SubUsersController extends HomeController
{

    public function __construct()
    {
        parent::__construct();
        $this->User = new User;
        $this->moduleTitleS = 'Users';
        $this->moduleTitleP = 'front.subusers';

        view()->share('moduleTitleP',$this->moduleTitleP);
        view()->share('moduleTitleS',$this->moduleTitleS);
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(),array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->User->getSubUserData($input, $noList, \Auth::user()->id);

        return view($this->moduleTitleP.'.index',compact('data'));
    }

    public function create(Request $request)
    {
        return view($this->moduleTitleP.'.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'country_code' => 'required',
            'mobile_no' => 'max:14|nullable|unique:users,mobile_no,NULL,id,deleted_at,NULL',
            'password' => 'required||min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
        ],
        ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']);

        $input = \Arr::except($request->all(),array('_token', '_method', 'password_confirmation'));
        $uuid = Str::uuid()->toString();
        $input['uuid'] = $uuid;
        $input['main_user_id'] = \Auth::user()->id;
        $input['agreement'] = isset($input['agreement_permission']) ? '1' : '0';
        $input['transactions'] = isset($input['transactions_permission']) ? '1' : '0';
        $input['reports'] = isset($input['reports_permission']) ? '1' : '0';
        $input['settings'] = isset($input['settings_permission']) ? '1' : '0';
        $input['application_show'] = isset($input['application_permission']) ? '1' : '0';

        unset($input['agreement_permission']);
        unset($input['transactions_permission']);
        unset($input['reports_permission']);
        unset($input['settings_permission']);
        unset($input['application_permission']);

        if($this->User->storeData($input)) {
            notificationMsg('success','User Created Successfully!');
        } else {
            notificationMsg('error','Something went wrong, please try again.');
        }

        return redirect()->route('user-management');
    }

    public function edit(Request $request, $id)
    {
        $data = $this->User->findData($id);
        if($data->main_user_id != \Auth::user()->id){
            return redirect()->back();
        }
        return view($this->moduleTitleP.'.edit', compact('data'));
    }

    public  function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($id)->whereNull('deleted_at')],
            'password' => 'nullable||min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']);

        $input = \Arr::except($request->all(),array('_token', '_method', 'password_confirmation'));

        if ($input['password'] != '') {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $input['agreement'] = isset($input['agreement_permission']) ? '1' : '0';
        $input['transactions'] = isset($input['transactions_permission']) ? '1' : '0';
        $input['reports'] = isset($input['reports_permission']) ? '1' : '0';
        $input['settings'] = isset($input['settings_permission']) ? '1' : '0';
        $input['application_show'] = isset($input['application_permission']) ? '1' : '0';

        unset($input['agreement_permission']);
        unset($input['transactions_permission']);
        unset($input['reports_permission']);
        unset($input['settings_permission']);
        unset($input['application_permission']);

        if($this->User->updateSubData($input, $id)) {
            notificationMsg('success','User Updpated Successfully!');
        } else {
            notificationMsg('error','Something went wrong, please try again.');
        }

        return redirect()->route('user-management');
    }

    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if($user->user_id != \Auth::user()->id){
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
            User::where('id', $id)->delete();
            \DB::commit();
            notificationMsg('success','User deleted successfully!');
            return redirect()->back();
        } catch (\Exception $e) {
            \DB::rollback();
            notificationMsg('error','Something went wrong, please try again.');
            return redirect()->back();
        }
    }


}
