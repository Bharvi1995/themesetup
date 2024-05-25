<?php
namespace App\Http\Controllers\Bank;
use App\Events\AdminNotification;
use DB;
use URL;
use Auth;
use File;
use View;
use Mail;
use Input;
use Session;
use Redirect;
use Exception;
use Validator;
use App\Bank;
use App\Admin;
use App\BankApplication;
use App\ImageUpload;
use App\Mail\BankApplicationSubmited;
use App\Mail\BankApplicationReSubmited;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

use function GuzzleHttp\json_decode;

class MyApplicationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->moduleTitleS = 'Profile';
        $this->moduleTitleP = 'bank.myapplication';

        $this->application = new BankApplication;

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function create()
    {
        $application = $this->application->FindDataFromUser(auth()->guard('bankUser')->user()->id);
        if(!$application)
            return view($this->moduleTitleP . '.my-application');
        else
            return redirect()->route('bank.my-application.detail');
    }

    public function store(Request $request)
    {
        $this->validate(
            $request, 
            [
                "company_name" => "required|regex:/^[a-z\d\-_\s\.]+$/i",
                "website_url" => "required",
                "company_registered_number_year" => "required|numeric|digits_between:0,4",
                "company_address" => "required|max:300|regex:/^[a-z\d\-_\s\.\,]+$/i",
                "settlement_method_for_crypto" => "required",
                "settlement_method_for_fiat" => "required",
                "mcc_codes" => "required",
                "descriptors" => "required",            
                "authorized_individual_name.*" => "required|regex:/^[a-z\d\-_\s\.]+$/i",
                "authorized_individual_phone_number.*" => "required|numeric",
                "authorized_individual_email.*" => "required",
                "license_image" => "required_with:is_license_applied"
            ],
            [
                'company_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'company_address.regex' => 'Please Enter Only Alphanumeric Characters.',
                'authorized_individual_name.*.regex' => 'Please Enter Only Alphanumeric Characters.'
            ]
        );

        $input = \Arr::except($request->all(),['_token','action','authorized_individual_name','authorized_individual_phone_number','authorized_individual_email','license_image']);

        foreach($request->authorized_individual_email as $key => $email){
            $authorized_individual[$key]['name'] = $request->authorized_individual_name[$key];
            $authorized_individual[$key]['email'] = $request->authorized_individual_email[$key];
            $authorized_individual[$key]['phone_number'] = $request->authorized_individual_phone_number[$key];
        }

        if ($request->hasFile('license_image')) {
            $license_image = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $license_image = $license_image . '.' . $request->file('license_image')->getClientOriginalExtension();
            $filePath = 'uploads/bank-application-' . auth()->guard('bankUser')->user()->id . '/' . $license_image;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('license_image')->getRealPath()));
            $input['license_image'] = $filePath;
        }
        
        $input['authorised_individual'] = json_encode($authorized_individual);
        $input['bank_id'] = auth()->guard('bankUser')->user()->id;
        
        DB::beginTransaction();
        try {
            $this->application->storeData($input);
            
            DB::commit();

            $data['company_name'] = $input['company_name'];
            $data['company_address'] = $input['company_address'];
            $data['phone_number'] = $authorized_individual[0]['phone_number'];
            $data['email'] = $authorized_individual[0]['email'];

            Mail::to(config('notification.default_email'))->send(new BankApplicationSubmited($data));
            
            notificationMsg('success', "Application submitted Successfully.");
            
            return redirect()->route('bank.my-application.detail');
        } catch (Exception $e) {
            DB::rollBack();
            
            notificationMsg('error', 'Something went wrong. Try Again.');
            
            return redirect()->back()->withInput($request->all());
        }
    }

    public function detail(Request $request)
    {
        $data = $this->application->FindDataFromUser(auth()->guard('bankUser')->user()->id);
        return view($this->moduleTitleP . '.my-application-view',compact('data'));
    }

    public function edit(Request $request)
    {
        $application = $this->application->FindDataFromUser(auth()->guard('bankUser')->user()->id);

        //alow edit only if the application is pending or reassigned.
        if($application->status == '0' || $application->status == '3'){
            return view($this->moduleTitleP . '.edit',compact('application'));
        }else{
            return redirect()->route('bank.my-application.detail');
        }
    }

    public function update(Request $request)
    {
        $this->validate(
            $request, 
            [
                "company_name" => "required|regex:/^[a-z\d\-_\s\.]+$/i",
                "website_url" => "required",
                "company_registered_number_year" => "required|numeric|digits_between:0,4",
                "company_address" => "required|max:300|regex:/^[a-z\d\-_\s\.\,]+$/i",
                "settlement_method_for_crypto" => "required",
                "settlement_method_for_fiat" => "required",
                "mcc_codes" => "required",
                "descriptors" => "required",            
                "authorized_individual_name.*" => "required|regex:/^[a-z\d\-_\s\.]+$/i",
                "authorized_individual_phone_number.*" => "required|numeric",
                "authorized_individual_email.*" => "required"
            ],
            [
                'company_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'company_address.regex' => 'Please Enter Only Alphanumeric Characters.',
                'authorized_individual_name.*.regex' => 'Please Enter Only Alphanumeric Characters.'
            ]
        );

        $input = \Arr::except($request->all(),['_token','action','authorized_individual_name','authorized_individual_phone_number','authorized_individual_email','license_image']);
        $application = $this->application->findData($request->id);

        foreach($request->authorized_individual_email as $key => $email){
            $authorized_individual[$key]['name'] = $request->authorized_individual_name[$key];
            $authorized_individual[$key]['email'] = $request->authorized_individual_email[$key];
            $authorized_individual[$key]['phone_number'] = $request->authorized_individual_phone_number[$key];
        }

        if ($request->hasFile('license_image')) {
            $license_image = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $license_image = $license_image . '.' . $request->file('license_image')->getClientOriginalExtension();
            $filePath = 'uploads/bank-application-' . auth()->guard('bankUser')->user()->id . '/' . $license_image;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('license_image')->getRealPath()));
            $input['license_image'] = $filePath;
        }
        
        $input['authorised_individual'] = json_encode($authorized_individual);
        $input['status'] = '0';
        $application = $this->application->findData($request->id);

        DB::beginTransaction();
        try {
            
            if($application->status == '3'){

                $data['company_name'] = $input['company_name'];
                $data['company_address'] = $input['company_address'];
                $data['phone_number'] = $authorized_individual[0]['phone_number'];
                $data['email'] = $authorized_individual[0]['email'];

                Mail::to(config('notification.default_email'))->send(new BankApplicationReSubmited($data));
            }
            $application->updateApplication($request->id,$input);
            
            DB::commit();
            
            notificationMsg('success', "Application updated Successfully.");
            
            return redirect()->route('bank.my-application.detail');
        } catch (Exception $e) {
            
            DB::rollBack();
            
            notificationMsg('error', 'Something went wrong. Try Again.');
            
            return redirect()->back()->withInput($request->all());
        }
    }
}