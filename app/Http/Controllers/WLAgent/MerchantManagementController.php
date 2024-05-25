<?php

namespace App\Http\Controllers\WLAgent;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\WLAgent;
use App\Categories;
use App\Application;
use App\Mail\SendLoginDetails;
use App\User;
use App\WebsiteUrl;
use View;
use Storage;
use Redirect;
use Hash;
use Auth;
use Str;

class MerchantManagementController extends WLAgentUserBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->WLAgent = new WLAgent;
        $this->user = new User;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    	$category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();

        return view('WLAgent.merchantManagement.create',compact('category'));
    }

    public function store(Request $request)
    {
        $validation['name'] = 'required|max:50';
        $validation['email'] = 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL';
        $validation['password'] = 'required||min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/';
        $validation['mobile_no'] = 'nullable|unique:users,mobile_no,NULL,id,deleted_at,NULL';
        $validation['business_type'] = 'required';
        $validation['business_name'] = 'required';
        $validation['website_url'] = 'required';
        $validation['category_id'] = 'required';

        foreach ($request->wl_extra_document as $key => $value) {
            $validation['wl_extra_document.' . $key . '.name'] = 'required';
            $validation['wl_extra_document.' . $key . '.document'] = 'required';
        }

        foreach ($request->website as $key => $value) {
            $validation['website.' . $key] = 'required|url';
            $validation1['website.' . $key .'.required'] = 'This website field is required';
            $validation1['website.' . $key .'.url'] = 'Enter valid URL';
        }

        foreach ($request->ip as $key => $value) {
            $validation['ip.' . $key] = 'required|ip';
            $validation1['ip.' . $key . '.required'] = 'This ip field is required';
            $validation1['ip.' . $key . '.ip'] = 'Enter valid ip format.';
        }

        foreach ($request->wl_extra_document as $key => $value) {
            $validation1['wl_extra_document.' . $key . '.name.required'] = 'The name field is required.';
            $validation1['wl_extra_document.' . $key . '.document.required'] = 'The document field is required.';
        }

        $validation1['password.regex'] = 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)';

        $this->validate($request, $validation, $validation1);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        $uuid = Str::uuid()->toString();

        $input['uuid'] = $uuid;
        $input['token'] = Str::random(40) . time();
        $input['is_active'] = '1';
        $input['is_white_label'] = '1';
        $input['white_label_agent_id'] = auth()->guard('agentUserWL')->user()->id;
        unset($input['password_confirmation']);
        $input['password'] = bcrypt($input['password']);

        $user = $this->user->create($input);

        $token_api = $user->createToken(config('app.name'))->plainTextToken;

        $this->user::where('id', $user->id)->update(['email_verified_at' => date('Y-m-d H:i:s'), 'api_key' => $token_api, 'is_rate_sent' => '2', 'mid' => '1']);

        $tmp = [];
        foreach ($input['wl_extra_document'] as $key => $value) {
            $extraDoc = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $extraDoc = $extraDoc . '.' . $value['document']->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $extraDoc;
            Storage::disk('s3')->put($filePath, file_get_contents($value['document']->getRealPath()));
            $tmp[$value['name']] = $filePath;
        }
        $input['wl_extra_document'] = json_encode($tmp);

        $application = [
            'user_id' => $user->id,
            'business_name' => $input['business_name'],
            'business_type' => $input['business_type'],
            'website_url' => $input['website_url'],
            'category_id' => $input['category_id'],
            'other_industry_type' => $input['other_industry_type'],
            'wl_extra_document' => $input['wl_extra_document'],
            'status' => '6'
        ];

        Application::create($application);

        foreach ($input['website'] as $key => $value) {
            $website = $input['website'][$key];
            $ip = $input['ip'][$key];

            WebsiteUrl::create([
                'user_id' => $user->id,
                'website_name' => $website,
                'ip_address' => $ip
            ]);
        }

        \Session::put('success', 'Merchant Created Successfully!');

        return redirect()->route('wl-merchant-management');
    }

    public function edit(Request $request, $id) {

        $data = User::select('users.*','applications.business_name','applications.business_type',
                            'applications.website_url','applications.category_id',
                            'applications.other_industry_type','applications.wl_extra_document')
                    ->join('applications','applications.user_id','users.id')
                    ->where('users.main_user_id', '0')
                    ->where('users.is_white_label', '1')
                    ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
                    ->where('users.id',$id)
                    ->first();

        if(!empty($data)){
            $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
            return view('WLAgent.merchantManagement.edit',compact('data','category'));
        } else {
            \Session::put('error', 'Merchant not found!');
            return redirect()->route('wl-merchant-management');
        }
    }

    public function update(Request $request, $id) {

        $this->validate(
            $request,
            [
                'name' => 'required|max:50',
                'email' => 'required|string|email|max:255|unique:users,email,'.$id.'NULL,id,deleted_at,NULL',
                'password' => 'nullable|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'confirm_password' => 'same:password',
                'mobile_no' => 'nullable|unique:users,mobile_no,'.$id.'NULL,id,deleted_at,NULL',
                'business_type' => 'required',
                'business_name' => 'required',
                'website_url' => 'required',
                'category_id' => 'required',
                'website.*' => 'url',
                'ip.*' => 'ip'
            ],
            [
                'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)',
                'webiste.*.url' => 'Enter valid URL',
                'ip.*.ip' => 'Enter valid IP address'
            ]
        );

        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($input['password'] != '') {
            $this->validate($request, [
                'password'   => 'required|min:9|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => "same:password",
            ], ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']);

            $input['password'] = bcrypt($input['password']);
            $password          = $input['password'];
        } else {
            $password          = "";
        }

        unset($input['confirm_password']);
        if ($input['password'] != '') {
            $this->user::where('id',$id)->update(['name'=>$input['name'],'email'=>$input['email'],'country_code'=>$input['country_code'],'mobile_no'=>$input['mobile_no'],'is_login_wl_merchant'=>$input['is_login_wl_merchant'],'password'=>$password]);
        }else{
            unset($input['password']);
            $this->user::where('id',$id)->update(['name'=>$input['name'],'email'=>$input['email'],'country_code'=>$input['country_code'],'mobile_no'=>$input['mobile_no'],'is_login_wl_merchant'=>$input['is_login_wl_merchant']]);
        }

        $user = User::where('id',$id)->first();
        $tmp = [];
        foreach ($input['wl_extra_document'] as $key => $value) {
            if(isset($value['document']) && isset($value['name'])){
                $extraDoc = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $extraDoc = $extraDoc . '.' . $value['document']->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $extraDoc;
                Storage::disk('s3')->put($filePath, file_get_contents($value['document']->getRealPath()));
                $tmp[$value['name']] = $filePath;
            }
        }

        $oldDoc = Application::select('wl_extra_document')->where('user_id',$id)->first();
        $oldDoc = (array) json_decode($oldDoc->wl_extra_document);
        $doc = array_merge($oldDoc,$tmp);
        $input['wl_extra_document'] = json_encode($doc);

        Application::where('user_id',$id)->update([
                'business_name' => $input['business_name'],
                'business_type' => $input['business_type'],
                'website_url' => $input['website_url'],
                'category_id' => $input['category_id'],
                'other_industry_type' => $input['other_industry_type'],
                'wl_extra_document' => $input['wl_extra_document']
            ]);

        if (isset($input['website']) && !empty($input['website']))
        {
            foreach ($input['website'] as $key => $value) {
                $website = $input['website'][$key];
                $ip = $input['ip'][$key];
                $old = WebsiteUrl::where('website_name',$website)->where('ip_address',$ip)->first();
                if(empty($old)){
                    WebsiteUrl::create([
                        'user_id' => $user->id,
                        'website_name' => $website,
                        'ip_address' => $ip
                    ]);
                }
            }
        }
        
        
        \Session::put('success', 'Merchant update Successfully!');

        return redirect()->route('wl-merchant-management');
    }

    public function show(Request $request, $id) {

        $data = User::select(
                        'users.*','applications.business_name','applications.wl_extra_document',
                        'applications.business_type','applications.website_url',
                        'applications.category_id','applications.other_industry_type',
                        'users.country_code as countryCode','users.mobile_no as phoneNo'
                    )
                    ->join('applications','applications.user_id','users.id')
                    ->where('users.main_user_id', '0')
                    ->where('users.is_white_label', '1')
                    ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
                    ->where('users.id',$id)
                    ->first();
        if(!empty($data)){
            return view('WLAgent.merchantManagement.show',compact('data'));
        } else {
            \Session::put('error', 'Merchant not found!');
            return redirect()->route('wl-merchant-management');
        }
    }

    public function destroy($id)
    {
        $check_record = User::select(
            'applications.business_name',
            'middetails.bank_name',
            'users.*'
        )->leftjoin('applications', 'applications.user_id', 'users.id')
        ->leftJoin('middetails', 'middetails.id', 'users.mid')
        ->where('users.main_user_id', '0')
        ->where('users.is_white_label', '1')
        ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
        ->where('users.id', $id)
        ->first();
        if(!empty($check_record)){
            $this->user->destroyData($id);
            notificationMsg('success', 'User Delete Successfully!');
            return redirect()->route('wl-merchant-management');
        } else {
            notificationMsg('error', 'You have not aacess!');
            return redirect()->route('wl-merchant-management');
        }
    }

    public function downloadDocumentsUploade(Request $request)
    {
        addToLog('application document download', [$request->file], 'general');
        return Storage::disk('s3')->download($request->file);
    }
}
