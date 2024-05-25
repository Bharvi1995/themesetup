<?php

namespace App\Http\Controllers\Agent;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Admin;
use App\Agent;
use View;
use Redirect;
use Hash;
use Str;
use Mail;
use Auth;
use Storage;
use App\Transaction;
use App\Application;
use App\Categories;
use App\TechnologyPartner;
use App\Exports\AgentsMerchantExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\AgentBankDetails;
use App\Http\Requests\UserBankDetailFormRequest;
use App\Notifications\NewApplicationSubmit;
use App\Mail\NewApplicationSubmitUser;
use App\Mail\userRegisterMail;
use App\Mail\SendLoginDetails;

class UserManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $agentUser;
    public function __construct()
    {
        view()->share('agentUserTheme', 'layouts.agent.default');

        // $this->middleware(function ($request, $next) {
        //     if(RpApplicationStatus(auth()->guard('agentUser')->user()->id) != 1){
        //         return redirect()->route('rp.my-application.create');
        //     }
        //     $userData = Agent::where('agents.id', auth()->guard('agentUser')->user()->id)
        //         ->first();

        //     view()->share('userData', $userData);
        //     return $next($request);
        // });
        $this->user = new User;
        $this->Transaction = new Transaction;
        $this->agentUser = new Agent;
        $this->Application = new Application;
    }

    public function create(Request $request)
    {
    	return view('agent.userManagement.create');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:9|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password_confirmation' => "same:password",
            'mobile_no' => 'required|numeric',
            'country_code' => 'required',
        ],['name.regex' => 'Please Enter Only Alphanumeric Characters.', 'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']);

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $input['agent_id'] = $agentId;
        $uuid = Str::uuid()->toString();
        $input['uuid'] = $uuid;
        $input['token'] = Str::random(40) . time();

        unset($input['password_confirmation']);

        $user =  $this->user->storeData($input);
        $token_api = \Str::random(30).time();
        // $token_api = $user->createToken(config("app.name"))->plainTextToken;

        if (isset($input['is_whitelable']) && $input['is_whitelable'] == '1') {
            $this->user::where('id', $user->id)->update(['email_verified_at' => date('Y-m-d H:i:s'), 'api_key' => $token_api, 'is_rate_sent' => '2', 'mid' => '1']);

            $application = [
                'user_id' => $user->id,
                'business_name' => 'Whitelable Merchant-' . $user->id,
                'status' => '6'
            ];

            Application::create($application);

            $content = [
                'email' => $user->email,
                'password' => $input['password'],
            ];

            try {
                \Mail::to($user->email)->send(new SendLoginDetails($content));
            } catch (Exception $e) {
            }
        }

        try {
            Mail::to($input['email'])->send(new userRegisterMail($input));
        } catch (\Exception $e) {
            //
        }

        notificationMsg('success','Merchant Created Successfully!');

        return redirect()->route('rp.user-management');
    }

    public function applicationCreate(Request $request, $id)
    {
        $user = User::where('users.id', $id)->first();

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        if($user->agent_id ==  $agentId){
            $category = Categories::orderBy("categories.id","ASC")->pluck('name', 'id')->toArray();
            $technologypartners = TechnologyPartner::latest()->pluck('name', 'id')->toArray();
            return view('agent.userManagement.applicationsCreate', compact('technologypartners', 'id','category'));
        }else{
            return redirect()->route('rp.dashboard');
        }
    }

    public function applicationEdit(Request $request, $id)
    {
        $data = Application::select('applications.*','users.name', 'users.email','users.agent_id as agentId')
            ->join('users', 'users.id', 'applications.user_id')
            ->where('applications.user_id',$id)
            ->first();

        $user = User::where('users.id', $id)->first();

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        if($user->agent_id ==  $agentId){
            $category = Categories::orderBy("categories.id","ASC")->pluck('name', 'id')->toArray();
            $technologypartners = TechnologyPartner::latest()->pluck('name', 'id')->toArray();
            return view('agent.userManagement.applicationsEdit', compact('technologypartners','data', 'id','category'));
        }else{
            return redirect()->route('rp.dashboard');
        }
    }

    public function applicationShow($id)
    {
        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }
        $data = $this->Application->FindDataFromUser($id);
        if($data && $data->agent_id ==  $agentId){
            return view('agent.userManagement.show',compact('data'));
        }else{
            \Session::put('error', 'Application not found!');
            return redirect()->route('rp.user-management');
        }
    }

    public function downloadDocumentsUploade(Request $request)
    {
        return Storage::disk('s3')->download($request->file);
    }

    public function applicationsStore(Request $request, $id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $oldApp = Application::where('user_id', $id)->first();
        if ($oldApp != null) {
            \Session::put('error', 'Your application is already submitted.');
            return redirect()->back()->withInput($request->all());
        }

        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $total_required_files = 1;
        if($request->board_of_directors != null && $request->board_of_directors > 0){
            $total_required_files = $request->board_of_directors;
        }

        $this->validate(
            $request,
            [
                'business_type' => 'required',
                'accept_card' => 'required',
                'business_name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'phone_no' => 'required',
                'skype_id' => 'required',
                'website_url' => 'required',
                // 'website_url' => ['required', 'regex:/^((ftp|http|https):\/\/)?(www.)?(?!.*(ftp|http|https|www.))[a-zA-Z0-9_-]+(\.[a-zA-Z]+)+((\/)[\w#]+)*(\/\w+\?[a-zA-Z0-9_]+=\w+(&[a-zA-Z0-9_]+=\w+)*)?$/'],
                'business_contact_first_name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'business_contact_last_name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'business_address1' => 'required|regex:/^[a-z\d\-_\s\.\,]+$/i',
                'residential_address' => 'required|regex:/^[a-z\d\-_\s\.\,]+$/i',
                'monthly_volume' => 'required',
                'country' => 'required',
                'country_code' => 'required',
                'processing_currency' => 'required',
                'technology_partner_id' => 'required',
                'processing_country' => 'required',
                'category_id' => 'required',
                'company_license' => 'required',
                'passport' => 'required|array|min:'.$total_required_files,
                'passport.*' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'company_incorporation_certificate' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'domain_ownership' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'latest_bank_account_statement' => 'required|array|min:'.$total_required_files,
                'latest_bank_account_statement.*' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'utility_bill' => 'required|array|min:'.$total_required_files,
                'utility_bill.*' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'previous_processing_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'extra_document.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'owner_personal_bank_statement' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'licence_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'moa_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'board_of_directors' => 'required'
            ],
            [
                'phone_no.numeric' => 'Please Enter Only Digits.',
                'business_name.regex' => 'Please Enter Only Alphanumeric Characters',
                'business_contact_first_name.regex' => 'Please Enter Only Alphanumeric Characters',
                'business_contact_last_name.regex' => 'Please Enter Only Alphanumeric Characters',
                'business_address1.required' => 'The Company Address field is required.',
                'business_address1.regex' => 'Please Enter Proper Company Address.',
                'residential_address.regex' => 'Please Enter Proper Residential Address.',
                'website_url.regex' => 'Enter Valid website URL!.',
                'passport.*.max' => 'The passport size may not be greater than 35 MB.',
                'company_incorporation_certificate.max' => 'The company incorporation certificate size may not be greater than 35 MB.',
                'domain_ownership.max' => 'The domain ownership size may not be greater than 35 MB.',
                'latest_bank_account_statement.*.max' => 'The latest bank account statement size may not be greater than 35 MB.',
                'utility_bill.*.max' => 'The utility bill size may not be greater than 35 MB.',
                'previous_processing_statement.max' => 'The previous processing statement size may not be greater than 35 MB.',
                'extra_document.*.max' => 'The additional document size may not be greater than 35 MB.',
                'licence_document.max' => 'The Licence document size may not be greater than 35 MB.',
                'moa_document.max' => 'The MOA document size may not be greater than 35 MB.',
                'owner_personal_bank_statement.max' => 'The owner personal bank statement size may not be greater than 35 MB.',
            ],

        );

        $input['user_id'] = $id;
        $user = User::where('id',$id)->first();
        $input['processing_country'] = json_encode($input['processing_country']);
        $input['processing_currency'] = json_encode($input['processing_currency']);
        $input['technology_partner_id'] = json_encode($input['technology_partner_id']);
        $input['accept_card'] = json_encode($input['accept_card']);

        if ($request->hasFile('passport')) {
            $files = $request->file('passport');
            $passportArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($passportArr, $filePath);
            }
            $input['passport'] = json_encode($passportArr);
        }
        if ($request->hasFile('utility_bill')) {
            $files = $request->file('utility_bill');
            $utilityArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($utilityArr, $filePath);
            }
            $input['utility_bill'] = json_encode($utilityArr);
        }
        if ($request->hasFile('latest_bank_account_statement')) {
            $files = $request->file('latest_bank_account_statement');
            $bankStatementArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($bankStatementArr, $filePath);
            }
            $input['latest_bank_account_statement'] = json_encode($bankStatementArr);
        }

        if ($request->hasFile('company_incorporation_certificate')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('company_incorporation_certificate')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('company_incorporation_certificate')->getRealPath()));
            $input['company_incorporation_certificate'] = $filePath;
        }

        if ($request->hasFile('domain_ownership')) {
            $imageNamedomainownership = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNamedomainownership = $imageNamedomainownership . '.' . $request->file('domain_ownership')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNamedomainownership;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('domain_ownership')->getRealPath()));
            $input['domain_ownership'] = $filePath;
        }
        if ($request->hasFile('licence_document')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('licence_document')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('licence_document')->getRealPath()));
            $input['licence_document'] = $filePath;
        }

        if ($request->hasFile('moa_document')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('moa_document')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('moa_document')->getRealPath()));
            $input['moa_document'] = $filePath;
        }

        if ($request->hasFile('previous_processing_statement')) {
            $files = $request->file('previous_processing_statement');
            foreach ($request->file('previous_processing_statement') as $key => $value) {
                $imageStatement = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageStatement = $imageStatement . '.' . $files[$key]->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageStatement;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                $input['previous_processing_statements'][] = $filePath;
            }
            $input['previous_processing_statement'] = json_encode($input['previous_processing_statements']);
            unset($input['previous_processing_statements']);
        }

        if ($request->hasFile('extra_document')) {
            $files = $request->file('extra_document');
            foreach ($request->file('extra_document') as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $files[$key]->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                $input['extra_documents'][] = $filePath;
            }

            $input['extra_document'] = json_encode($input['extra_documents']);
            unset($input['extra_documents']);
        }

        if ($request->hasFile('owner_personal_bank_statement')) {

            $imageOwnerBankStatement = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageOwnerBankStatement = $imageOwnerBankStatement . '.' . $request->file('owner_personal_bank_statement')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageOwnerBankStatement;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('owner_personal_bank_statement')->getRealPath()));
            $input['owner_personal_bank_statement'] = $filePath;
        }

        DB::beginTransaction();
        try {
            $input['status'] = '1';
            $application = $this->Application->storeData($input);

            $notification = [
                'user_id' => '1',
                'sendor_id' => $agentId,
                'type' => 'admin',
                'title' => 'Application Created',
                'body' => 'You have received a new application from RP.',
                'url' => '/admin/applications-list/view/' . $application->id,
                'is_read' => '0'
            ];

            $realNotification = addNotification($notification);

            Admin::find('1')->notify(new NewApplicationSubmit($application));
            Mail::to($user->email)->send(new NewApplicationSubmitUser($user));

            DB::commit();
            notificationMsg('success', 'Thank you for submitting your application.');
            return redirect()->route('rp.user-management');
        } catch (Exception $e) {
            DB::rollBack();
            \Session::put('error', 'Your application not submit.Try Again.');
            return redirect()->back()->withInput($request->all());
        }
    }

    public function applicationsUpdate(Request $request, $id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $user = User::where('id', $id)->first();
        $application = Application::where('id', $input['application_id'])->first();
        unset($input['application_id']);
        $this->validate(
            $request,
            [
                'business_type' => 'required',
                'accept_card' => 'required',
                'business_name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'website_url' => 'required',
                // 'website_url' => ['required', 'regex:/^((ftp|http|https):\/\/)?(www.)?(?!.*(ftp|http|https|www.))[a-zA-Z0-9_-]+(\.[a-zA-Z]+)+((\/)[\w#]+)*(\/\w+\?[a-zA-Z0-9_]+=\w+(&[a-zA-Z0-9_]+=\w+)*)?$/'],
                'phone_no' => 'required',
                'skype_id' => 'required',
                'business_contact_first_name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'business_contact_last_name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'business_address1' => 'required|regex:/^[a-z\d\-_\s\.\,]+$/i',
                'residential_address' => 'required|regex:/^[a-z\d\-_\s\.\,]+$/i',
                'monthly_volume' => 'required',
                'country' => 'required',
                'processing_currency' => 'required',
                'technology_partner_id' => 'required',
                'processing_country' => 'required',
                'category_id' => 'required',
                'company_license' => 'required',
                'passport.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'company_incorporation_certificate' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'domain_ownership' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'latest_bank_account_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'utility_bill.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'previous_processing_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'extra_document.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'owner_personal_bank_statement' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'licence_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'moa_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            ],
            [
                'phone_no.numeric' => 'Please Enter Only Digits.',
                'business_name.regex' => 'Please Enter Only Alphanumeric Characters',
                'business_contact_first_name.regex' => 'Please Enter Only Alphanumeric Characters',
                'business_contact_last_name.regex' => 'Please Enter Only Alphanumeric Characters',
                'business_address1.required' => 'The Company Address field is required.',
                'business_address1.regex' => 'Please Enter Proper Company Address.',
                'residential_address.regex' => 'Please Enter Proper Residential Address.',
                'website_url.regex' => 'Enter Valid website URL!.',
                'passport.*.max' => 'The passport size may not be greater than 35 MB.',
                'company_incorporation_certificate.max' => 'The company incorporation certificate size may not be greater than 35 MB.',
                'domain_ownership.max' => 'The domain ownership size may not be greater than 35 MB.',
                'latest_bank_account_statement.*.max' => 'The latest bank account statement size may not be greater than 35 MB.',
                'utility_bill.*.max' => 'The utility bill size may not be greater than 35 MB.',
                'previous_processing_statement.max' => 'The previous processing statement size may not be greater than 35 MB.',
                'extra_document.*.max' => 'The additional document size may not be greater than 35 MB.',
                'owner_personal_bank_statement.max' => 'The owner personal bank statement size may not be greater than 35 MB.',
                'licence_document.max' => 'The Licence document size may not be greater than 35 MB.',
                'moa_document.max' => 'The MOA document size may not be greater than 35 MB.',
            ],
        );

        $input['processing_country'] = json_encode($input['processing_country']);
        $input['processing_currency'] = json_encode($input['processing_currency']);
        $input['technology_partner_id'] = json_encode($input['technology_partner_id']);

        $filePath = storage_path() . "/uploads/" . $user->name . '-' . $user->id . '/';

        if ($request->hasFile('passport')) {
            $old_passport_documents = json_decode($application->passport);
            $files = $request->file('passport');
            $passportArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($passportArr, $filePath);
            }
            $updated_passport_documents = array_merge($old_passport_documents, $passportArr);
            $input['passport'] = json_encode($updated_passport_documents);
        }
        if ($request->hasFile('latest_bank_account_statement')) {
            $old_bank_statement = json_decode($application->latest_bank_account_statement);
            $files = $request->file('latest_bank_account_statement');
            $bankStatementArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($bankStatementArr, $filePath);
            }
            $updated_bankStatement = array_merge($old_bank_statement, $bankStatementArr);
            $input['latest_bank_account_statement'] = json_encode($updated_bankStatement);
        }

        if ($request->hasFile('utility_bill')) {
            $old_utilityBill = json_decode($application->utility_bill);
            $files = $request->file('utility_bill');
            $utilityBill = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($utilityBill, $filePath);
            }
            $utilityBill = array_merge($old_utilityBill, $utilityBill);
            $input['utility_bill'] = json_encode($utilityBill);
        }

        if ($request->hasFile('licence_document')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('licence_document')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('licence_document')->getRealPath()));
            $input['licence_document'] = $filePath;
        } else if ($request->company_license == '1' || $request->company_license == '2') {
            Storage::disk('s3')->delete($application->licence_document);
            $input['licence_document'] = null;
        }


        if ($request->hasFile('moa_document')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('moa_document')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('moa_document')->getRealPath()));
            $input['moa_document'] = $filePath;
        }

        if ($request->hasFile('company_incorporation_certificate')) {
            Storage::disk('s3')->delete($application->company_incorporation_certificate);
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('company_incorporation_certificate')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('company_incorporation_certificate')->getRealPath()));
            $input['company_incorporation_certificate'] = $filePath;
        }

        if ($request->hasFile('domain_ownership')) {
            Storage::disk('s3')->delete($application->domain_ownership);
            $imageNamedomainownership = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNamedomainownership = $imageNamedomainownership . '.' . $request->file('domain_ownership')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNamedomainownership;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('domain_ownership')->getRealPath()));
            $input['domain_ownership'] = $filePath;
        }

        if ($request->hasFile('previous_processing_statement')) {
            // delete old records.
            if ($application->previous_processing_statement != null) {
                foreach (json_decode($application->previous_processing_statement) as $key => $value) {
                    Storage::disk('s3')->delete($value);
                }
            }
            $files = $request->file('previous_processing_statement');
            foreach ($request->file('previous_processing_statement') as $key => $value) {
                $imageStatement = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageStatement = $imageStatement . '.' . $files[$key]->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageStatement;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                $input['previous_processing_statements'][] = $filePath;
            }

            $input['previous_processing_statement'] = json_encode($input['previous_processing_statements']);
            unset($input['previous_processing_statements']);
        }


        $old_extra_documents = json_decode(Application::find($application->id)->extra_document);
        if ($old_extra_documents) {
            if ($request->hasFile('extra_document')) {
                $files = $request->file('extra_document');
                foreach ($request->file('extra_document') as $key => $value) {
                    $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                    $imageDocument = $imageDocument . '.' . $files[$key]->getClientOriginalExtension();
                    $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                    Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                    $input['extra_documents'][] = $filePath;
                }
                $input['extra_document'] = json_encode($input['extra_documents']);
                $new_extra_documents = json_decode($input['extra_document']);
                $updated_extra_documents = array_merge($old_extra_documents, $new_extra_documents);
                $input['extra_document'] = json_encode($updated_extra_documents);
                unset($input['extra_documents']);
            }
        } else {
            if ($request->hasFile('extra_document')) {
                $files = $request->file('extra_document');
                foreach ($request->file('extra_document') as $key => $value) {
                    $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                    $imageDocument = $imageDocument . '.' . $files[$key]->getClientOriginalExtension();
                    $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                    Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                    $input['extra_documents'][] = $filePath;
                }
                $input['extra_document'] = json_encode($input['extra_documents']);
                unset($input['extra_documents']);
            }
        }

        if ($request->hasFile('owner_personal_bank_statement')) {
            File::delete(storage_path() . "/uploads/" . $user->name . '-' . $user->id . '/' . $application->owner_personal_bank_statement);
            $imageOwnerBankStatement = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageOwnerBankStatement = $imageOwnerBankStatement . '.' . $request->file('owner_personal_bank_statement')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageOwnerBankStatement;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('owner_personal_bank_statement')->getRealPath()));
            $input['owner_personal_bank_statement'] = $filePath;
        }

        $this->Application->updateApplication($application->id, $input);

        notificationMsg('success', 'Your application has been updated successfully.');

        return redirect()->route('rp.user-management');
    }
}
