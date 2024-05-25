<?php

namespace App\Http\Controllers\Agent;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Admin;
use App\Agent;
use App\Categories;
use App\TechnologyPartner;
use View;
use Redirect;
use Hash;
use Auth;
use Storage;
use App\Transaction;
use App\RpApplication;
use App\Exports\AgentsMerchantExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\AgentBankDetails;
use App\Http\Requests\UserBankDetailFormRequest;
use App\Mail\AgentApplicationCreateMail;
use App\Mail\AgentApplicationResubmitMail;

class ApplicationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $agentUser;
    public function __construct()
    {
        $this->moduleTitleS = 'Profile';
        $this->moduleTitleP = 'agent.application';

        $this->middleware(function ($request, $next) {
            $userData = Agent::where('agents.id', auth()->guard('agentUser')->user()->id)
                ->first();

            view()->share('userData', $userData);
            return $next($request);
        });
        $this->user = new User;
        $this->Transaction = new Transaction;
        $this->agentUser = new Agent;
        $this->application = new RpApplication;
        
        view()->share('agentUserTheme', 'layouts.agent.default');
        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function create()
    {
        $integration_preference = TechnologyPartner::orderBy('name')->pluck('name','id');
        $industry_type = Categories::orderBy('name')->pluck('name','id');
        return view($this->moduleTitleP . '.my-application',compact('integration_preference','industry_type'));
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                "company_name" => "required|regex:/^[a-z\d\-_\s\.]+$/i",
                "website_url" => "required",
                "company_registered_number" => "required|regex:/^[a-z\d\-_\s\.\/]+$/i",
                "company_registered_number_year" => "required",
                "company_address" => "required|max:300|regex:/^[a-z\d\-_\s\.\,]+$/i",
                "company_email" => "required|email",
                "major_regious" => "required",
                "avg_no_of_app" => "required|numeric|digits_between:0,10",
                "commited_avg_volume_per_month" => "required|numeric|digits_between:0,10",
                "payment_solutions_needed" => "required",
                "industries_reffered" => "required",
                "authorized_individual_name.*" => "required|regex:/^[a-z\d\-_\s\.]+$/i",
                "authorized_individual_phone_number.*" => "required|numeric",
                "authorized_individual_email.*" => "required",
                "generated_lead" => "max:300|regex:/^[a-z\d\-_\s\.\,]+$/i",
                'passport.*' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'company_incorporation_certificate' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'utility_bill.*' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'tax_id' => 'nullable|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            ],
            [
                'company_name.required' => 'The Entity Name field is required.',
                'company_address.required' => 'The Address field is required.',
                'company_registered_number_year.required' => 'The Date Of Birth/Incorporation field is required.',
                'company_registered_number.regex' => 'Please Enter Valid Tax Id.',
                'company_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'company_address.regex' => 'Please Enter Only Alphanumeric Characters.',
                'generated_lead' => 'Please Enter Only Alphanumeric Characters.',
                'website_url' => 'Enter Valid website URL!.',
                'passport.*.max' => 'The passport size may not be greater than 35 MB.',
                'company_incorporation_certificate.max' => 'The company incorporation certificate size may not be greater than 35 MB.',
                'utility_bill.*.max' => 'The utility bill size may not be greater than 35 MB.',
                'tax_id' => 'The Tax Id size may not be greater than 35 MB.'
            ]
        );

        $input = \Arr::except($request->all(),['_token','action','authorized_individual_name','authorized_individual_phone_number','authorized_individual_email']);

        foreach($request->authorized_individual_email as $key => $email){
            $authorized_individual[$key]['name'] = $request->authorized_individual_name[$key];
            $authorized_individual[$key]['email'] = $request->authorized_individual_email[$key];
            $authorized_individual[$key]['phone_number'] = $request->authorized_individual_phone_number[$key];
        }
        
        $input['authorised_individual'] = json_encode($authorized_individual);
        $input['major_regious'] = json_encode($input['major_regious']);
        $input['industries_reffered'] = json_encode($input['industries_reffered']);
        $input['payment_solutions_needed'] = json_encode($input['payment_solutions_needed']);
        $input['agent_id'] = auth()->guard('agentUser')->user()->id;

        if ($request->hasFile('passport')) {
            $files = $request->file('passport');
            $passportArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/rp-application-' . auth()->guard('agentUser')->user()->id . '/' . $imageDocument;
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
                $filePath = 'uploads/rp-application-' . auth()->guard('agentUser')->user()->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($utilityArr, $filePath);
            }
            $input['utility_bill'] = json_encode($utilityArr);
        }
        if ($request->hasFile('company_incorporation_certificate')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('company_incorporation_certificate')->getClientOriginalExtension();
            $filePath = 'uploads/rp-application-' . auth()->guard('agentUser')->user()->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('company_incorporation_certificate')->getRealPath()));
            $input['company_incorporation_certificate'] = $filePath;
        }
        if ($request->hasFile('tax_id')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('tax_id')->getClientOriginalExtension();
            $filePath = 'uploads/rp-application-' . auth()->guard('agentUser')->user()->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('tax_id')->getRealPath()));
            $input['tax_id'] = $filePath;
        }
        
        DB::beginTransaction();
        
        try {
            $this->application->storeData($input);
            
            if(isset(auth()->guard('agentUser')->user()->email)){
            
                $data = array();
                \Mail::to(auth()->guard('agentUser')->user()->email)->send(new AgentApplicationCreateMail($data));
            }

            DB::commit();
            
            notificationMsg('success', "Application submitted Successfully.");
            
            return redirect()->route('rp.my-application.detail');
        } catch (Exception $e) {
            DB::rollBack();
            
            notificationMsg('error', 'Something went wrong. Try Again.');
            
            return redirect()->back()->withInput($request->all());
        }
    }
    
    public function detail(Request $request)
    {
        $data = $this->application->FindDataFromUser(auth()->guard('agentUser')->user()->id);
        return view($this->moduleTitleP . '.my-application-view',compact('data'));
    }

    public function downloadDocumentsUploadRpApplication(Request $request)
    {
        return Storage::disk('s3')->download($request->file);
    }

    public function edit(Request $request)
    {
        $application = $this->application->FindDataFromUser(auth()->guard('agentUser')->user()->id);
        if($application){

            $industry_type = Categories::orderBy('name')->pluck('name','id');
            $integration_preference = TechnologyPartner::orderBy('name')->pluck('name','id');

            return view($this->moduleTitleP . '.my-application-edit',compact('application', 'integration_preference', 'industry_type'));
        }
        return redirect()->route('rp.my-application.create')->with('error','No application found for this user. Please submit the application.');
    }

    public function update(Request $request, Agent $agent)
    {
        $this->validate(
            $request, 
            [
                "id" => "required",
                "company_name" => "required|regex:/^[a-z\d\-_\s\.]+$/i",
                "website_url" => "required",
                "company_registered_number" => "required|regex:/^[a-z\d\-_\s\.\/]+$/i",
                "company_registered_number_year" => "required",
                "company_address" => "required|max:300|regex:/^[a-z\d\-_\s\.\,]+$/i",
                "company_email" => "required|email",
                "major_regious" => "required",
                "avg_no_of_app" => "required|numeric|digits_between:0,10",
                "commited_avg_volume_per_month" => "required|numeric|digits_between:0,10",
                "payment_solutions_needed" => "required",
                "industries_reffered" => "required",
                "authorized_individual_name.*" => "required|regex:/^[a-z\d\-_\s\.]+$/i",
                "authorized_individual_phone_number.*" => "required|numeric",
                "authorized_individual_email.*" => "required",
                "generated_lead" => "max:300|regex:/^[a-z\d\-_\s\.\,]+$/i",
                'passport.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'utility_bill.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'company_incorporation_certificate' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'tax_id' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            ],
            [
                'company_name.required' => 'The Entity Name field is required.',
                'company_address.required' => 'The Address field is required.',
                'company_registered_number_year.required' => 'The Date Of Birth/Incorporation field is required.',
                'company_registered_number.regex' => 'Please Enter Valid Tax Id.',
                'company_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'company_address.regex' => 'Please Enter Only Alphanumeric Characters.',
                'generated_lead' => 'Please Enter Only Alphanumeric Characters.',
                'passport.*.max' => 'The passport size may not be greater than 25 MB.',
                'company_incorporation_certificate.max' => 'The company incorporation certificate size may not be greater than 35 MB.',
                'utility_bill.*.max' => 'The utility bill size may not be greater than 35 MB.',
                'tax_id.max' => 'The Tax Id certificate size may not be greater than 35 MB.',
            ]
        );
        
        $application = $this->application->findData($request->id);
        $input = \Arr::except($request->all(),['_token','action','authorized_individual_name','authorized_individual_phone_number','authorized_individual_email','id']);
        

        foreach($request->authorized_individual_email as $key => $email){
            $authorized_individual[$key]['name'] = $request->authorized_individual_name[$key];
            $authorized_individual[$key]['email'] = $request->authorized_individual_email[$key];
            $authorized_individual[$key]['phone_number'] = $request->authorized_individual_phone_number[$key];
        }
        
        $input['authorised_individual'] = json_encode($authorized_individual);
        $input['major_regious'] = json_encode($input['major_regious']);
        $input['industries_reffered'] = json_encode($input['industries_reffered']);
        $input['payment_solutions_needed'] = json_encode($input['payment_solutions_needed']);

        if ($request->hasFile('passport')) {
            $old_passport_documents = [];
            if($application->passport != null) {
                $old_passport_documents = json_decode($application->passport);
            }
            $files = $request->file('passport');
            $passportArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/rp-application-' . $application->agent_id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($passportArr, $filePath);
            }
            $updated_passport_documents = array_merge($old_passport_documents, $passportArr);
            $input['passport'] = json_encode($updated_passport_documents);
        }

        if ($request->hasFile('utility_bill')) {
            $old_utilityBill = [];
            if($application->utility_bill != null) {
                $old_utilityBill = json_decode($application->utility_bill);
            }
            $files = $request->file('utility_bill');
            $utilityBillArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/rp-application-' . $application->agent_id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($utilityBillArr, $filePath);
            }
            $utilityBill = array_merge($old_utilityBill, $utilityBillArr);
            $input['utility_bill'] = json_encode($utilityBill);
        }

        if ($request->hasFile('company_incorporation_certificate')) {
            Storage::disk('s3')->delete($application->company_incorporation_certificate);
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('company_incorporation_certificate')->getClientOriginalExtension();
            $filePath = 'uploads/rp-application-' . $application->agent_id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('company_incorporation_certificate')->getRealPath()));
            $input['company_incorporation_certificate'] = $filePath;
        }

        if ($request->hasFile('tax_id')) {
            Storage::disk('s3')->delete($application->tax_id);
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('tax_id')->getClientOriginalExtension();
            $filePath = 'uploads/rp-application-' . $application->agent_id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('tax_id')->getRealPath()));
            $input['tax_id'] = $filePath;
        }
        
        if($application->status == '3'){
            $input['status'] = '0';
        }
        DB::beginTransaction();
        try {
            $this->application->updateApplication($request->id,$input);
            
            DB::commit();
            if($application->status == '3'){

                $AgentData = Agent::where('agents.id', auth()->guard('agentUser')->user()->id)
                ->first();
                $agent_name = "";
                $agent_email = "";
                if(!empty($AgentData)){
                    $agent_name = $AgentData->email;
                    $agent_email = $AgentData->name;
                    $adminemail = config('notification.default_email');
                    if($adminemail != ""){
                        $data['id'] = $application->id;
                        $data['agent_name'] = $agent_name;
                        $data['agent_email'] = $agent_email;
                        $data['company_name'] = $input['company_name'];
                        \Mail::to($adminemail)->send(new AgentApplicationResubmitMail($data));
                    }
                }


                notificationMsg('success', "Your application has been resubmitted successfully.");
            } else {
                notificationMsg('success', "Application updated Successfully.");
            }
            
            return redirect()->route('rp.my-application.detail');
        } catch (Exception $e) {
            DB::rollBack();
            
            notificationMsg('error', 'Something went wrong. Try Again.');
            
            return redirect()->back()->withInput($request->all());
        }
    }

}
