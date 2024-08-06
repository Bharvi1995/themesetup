<?php

namespace App\Http\Controllers;


use File;

use Auth;
use Session;
use App\User;
use Redirect;
use App\Admin;
use Exception;
use Validator;
use App\Categories;
use App\Application;
use App\ImageUpload;
use App\TechnologyPartner;
use Illuminate\Http\Request;
use App\Events\AdminNotification;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\json_decode;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewApplicationSubmitUser;

use Illuminate\Support\Facades\Storage;
use App\Notifications\ApplicationResubmit;
use App\Notifications\NewApplicationSubmit;

class ApplicationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->moduleTitleS = 'Profile';
        $this->moduleTitleP = 'front.application';

        $this->application = new Application;

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
        $technologypartners = TechnologyPartner::latest()->pluck('name', 'id')->toArray();
        $data = Application::where('user_id', auth()->user()->id)->first();
        $data = Application::where('user_id', auth()->user()->id)->first();
        if ($data == null) {
            $inputdata['user_id'] = auth()->user()->id;
            $inputdata['status'] = '12';
            $data = $this->application->storeData($inputdata);
        }
        return view($this->moduleTitleP . '.startapplication', compact('category', 'technologypartners', 'data'));
    }

    public function startApplicationStore(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $Appdata = Application::where('user_id', auth()->user()->id)->first();
        $id = $Appdata->id;
        if (isset($input['action']) && $input['action'] == 'saveDraft') {
            $this->validate(
                $request,
                [
                    'phone_no' => 'max:14',
                    // 'passport.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    // 'company_incorporation_certificate' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    'domain_ownership' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    // 'latest_bank_account_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    // 'utility_bill.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    // 'previous_processing_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    // 'extra_document.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    // 'owner_personal_bank_statement' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    'licence_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                    // 'moa_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                ],
                [
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
            // $input['user_id'] = auth()->user()->id;
            $user = auth()->user();
            

            
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

            unset($input['action']);
            // try {
            //     $application = $this->application->updateApplication($id, $input);
            // } catch (Exception $e) {
            //     prd($e->getMessage());
            // }

            try {
                // $input['status'] = '12';
                $application = $this->application->updateApplication($id, $input);
                \Session::put("successcustom", "Your details has been stored as a Draft");
                return redirect('verification')->with('success', 'Your details has been stored as a Draft.');
            } catch (Exception $e) {
                \Session::put('error', 'Something went wrong with your request. Kindly try again');
                return redirect()->back()->withInput($request->all());
            }
        } else {
            $total_required_files = 1;
            if ($request->board_of_directors != null && $request->board_of_directors > 0) {
                $total_required_files = $request->board_of_directors;
            }
            $OldData = $Appdata->toArray();
            $ArrDataValidate = $this->GetValidateArr($OldData, $total_required_files);
            $this->validate(
                $request,
                $ArrDataValidate,
                [
                    'business_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                    'phone_no.numeric' => 'Please Enter Only Digits.',
                    'business_contact_first_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                    'business_contact_last_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                    // 'business_address1.regex' => 'Please Enter Valid Company Address.',
                    // 'residential_address.regex' => 'Please Enter Valid Residential Address.',
                    // 'passport.*.max' => 'The passport size may not be greater than 35 MB.',
                    // 'company_incorporation_certificate.max' => 'The company incorporation certificate size may not be greater than 35 MB.',
                    'domain_ownership.max' => 'The domain ownership size may not be greater than 35 MB.',
                    // 'latest_bank_account_statement.*.max' => 'The latest bank account statement size may not be greater than 35 MB.',
                    // 'utility_bill.*.max' => 'The utility bill size may not be greater than 35 MB.',
                    // 'previous_processing_statement.max' => 'The previous processing statement size may not be greater than 35 MB.',
                    // 'extra_document.*.max' => 'The additional document size may not be greater than 35 MB.',
                    'licence_document.max' => 'The Licence document size may not be greater than 35 MB.',
                    // 'moa_document.max' => 'The MOA document size may not be greater than 35 MB.',
                    // 'owner_personal_bank_statement.max' => 'The owner personal bank statement size may not be greater than 35 MB.',
                ],
            );
            $input['user_id'] = auth()->user()->id;
            $user = auth()->user();
            

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

            
            try {
                $input['status'] = '1';
                unset($input["action"]);
                // $application = $this->application->storeData($input);
                $application = $this->application->updateApplication($id, $input);
                $application = Application::where('user_id', auth()->user()->id)->first();
                \Session::put("success", "Thank you for submitting your verification information.");
                return redirect('verification')->with('success', 'Thank you for providing your verification details. Our team is currently reviewing them and will update you shortly.');
            } catch (Exception $e) {
                \Session::put('error', 'Something went wrong with your request. Kindly try again');
                return redirect()->back()->withInput($request->all());
            }
        }
    }

    public function status(Request $request)
    {
        if (\Auth::user()->main_user_id != '0')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;
        $data = $this->application->FindDataFromUser($userID);
        $isResume = 0;
        if ($data) {
            $LastData = $data->toArray();
            $LastData = array_filter($LastData);

            unset($LastData['id']);
            unset($LastData['user_id']);
            unset($LastData['monthly_volume']);
            unset($LastData['monthly_volume_currency']);
            unset($LastData['country_code']);
            unset($LastData['status']);
            unset($LastData['created_at']);
            unset($LastData['updated_at']);
            unset($LastData['name']);
            unset($LastData['email']);
            unset($LastData['agent_commission']);
            unset($LastData['accept_card']);
            if (!empty($LastData)) {
                $isResume = 1;
            }
        }
        if(isset(Auth::user()->application)  &&  (Auth::user()->application->status == 4 || Auth::user()->application->status == 5 || Auth::user()->application->status == 6 || Auth::user()->application->status == 10 || Auth::user()->application->status == 11)){
            return redirect()->route('dashboardPage');
        }else{
            return view($this->moduleTitleP . '.status', compact('data', 'isResume'));
        }
    }

    public function applicationsEdit(Request $request, $id)
    {
        $data = $this->application->findData($id);
        if ($data->user_id != \Auth::user()->id) {
            return redirect()->back();
        }
        if ($data) {
            $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
            $technologypartners = TechnologyPartner::latest()->pluck('name', 'id')->toArray();
            return view($this->moduleTitleP . '.applicationsEdit', compact('category', 'data', 'id', 'request', 'technologypartners'));
        }
        return redirect()->back();
    }

    public function applicationsUpdate(Request $request, $id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $user = User::where('id', $input['user_id'])->first();
        $application = Application::where('id', $id)->first();
        $this->validate(
            $request,
            [
                // 'business_type' => 'required',
                // 'accept_card' => 'required',
                'business_name' => 'required|regex:/^[a-z\d\-_\s\. ]+$/i',
                'website_url' => 'required|url',
                'phone_no' => 'required|numeric',
                'skype_id' => 'required|regex:/^[a-z\d\-_\s\. ]+$/i',
                'business_contact_first_name' => 'required|regex:/^[a-z\d\-_\s\. ]+$/i',
                'business_contact_last_name' => 'required|regex:/^[a-z\d\-_\s\. ]+$/i',
                // 'business_address1' => 'required|regex:/^[a-z\d\-_\s\.\,]+$/i',
                // 'residential_address' => 'required|regex:/^[a-z\d\-_\s\.\,]+$/i',
                // 'monthly_volume' => 'required',
                // 'country' => 'required',
                // 'processing_currency' => 'required',
                // 'technology_partner_id' => 'required',
                // 'processing_country' => 'required',
                'category_id' => 'required',
                'company_license' => 'required',
                // 'passport.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                // 'company_incorporation_certificate' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'domain_ownership' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                // 'latest_bank_account_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                // 'utility_bill.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                // 'previous_processing_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                // 'extra_document.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                // 'owner_personal_bank_statement' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'licence_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                // 'moa_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            ],
            [
                'business_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'phone_no.numeric' => 'Please Enter Only Digits.',
                'business_contact_first_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'business_contact_last_name.regex' => 'Please Enter Only Alphanumeric Characters.',
                // 'business_address1.regex' => 'Please Enter Valid Company Address.',
                // 'residential_address.regex' => 'Please Enter Valid Residential Address.',
                // 'passport.*.max' => 'The passport size may not be greater than 35 MB.',
                // 'company_incorporation_certificate.max' => 'The company incorporation certificate size may not be greater than 35 MB.',
                'domain_ownership.max' => 'The domain ownership size may not be greater than 35 MB.',
                // 'latest_bank_account_statement.*.max' => 'The latest bank account statement size may not be greater than 35 MB.',
                // 'utility_bill.*.max' => 'The utility bill size may not be greater than 35 MB.',
                // 'previous_processing_statement.max' => 'The previous processing statement size may not be greater than 35 MB.',
                // 'extra_document.*.max' => 'The additional document size may not be greater than 35 MB.',
                // 'owner_personal_bank_statement.max' => 'The owner personal bank statement size may not be greater than 35 MB.',
                'licence_document.max' => 'The Licence document size may not be greater than 35 MB.',
                // 'moa_document.max' => 'The MOA document size may not be greater than 35 MB.',
            ],
        );

        $filePath = storage_path() . "/uploads/" . $user->name . '-' . $user->id . '/';
        
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

       
        if ($request->hasFile('domain_ownership')) {
            Storage::disk('s3')->delete($application->domain_ownership);
            $imageNamedomainownership = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNamedomainownership = $imageNamedomainownership . '.' . $request->file('domain_ownership')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNamedomainownership;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('domain_ownership')->getRealPath()));
            $input['domain_ownership'] = $filePath;
        }
        $this->application->updateApplication($id, $input);

        if ($application->status == '2') {
            $this->application->updateApplication($id, ['status' => '1']);
        }

        try {
            if ($application->status == '2') {
                $this->application->updateApplication($id, ['reason_reassign' => '']);

                notificationMsg('success', 'Your KYC has been resubmitted successfully.');
                return redirect()->route('my-application');
            }
        } catch (\Exception $e) {
        }
        notificationMsg('success', 'Your verification has been updated successfully.');

        return redirect()->route('my-application');
    }

    public function downloadDocumentsUploade(Request $request)
    {
        addToLog('application document download', [$request->file], 'general');
        return Storage::disk('s3')->download($request->file);
    }

    public function viewAppImage(Request $request)
    {
        //{{ Config('app.aws_path').'uploads/application-'.$user->id.'/'.$value }}
        // $user = auth()->user();
        // $path = storage_path('uploads/application-'.$user->id.'/'.$request->file);
        // if (!File::exists($path)) {
        //     abort(404);
        // }
        // $file = File::get($path);
        // $type = File::mimeType($path);
        // $response = \Response::make($file, 200);
        // $response->header("Content-Type", $type);
        // return $response;

    }

    public function GetValidateArr($varData = array(), $total_required_files)
    {

        $ArrValidate = [
            // 'business_type' => 'required',
            // 'accept_card' => 'required',
            'business_name' => 'required|regex:/^[a-z\d\-_\s\. ]+$/i',
            'phone_no' => 'required|numeric',
            'skype_id' => 'required|regex:/^[a-z\d\-_\s\. ]+$/i',
            'website_url' => 'required|url',
            'business_contact_first_name' => 'required|regex:/^[a-z\d\-_\s\. ]+$/i',
            'business_contact_last_name' => 'required|regex:/^[a-z\d\-_\s\. ]+$/i',
            // 'business_address1' => 'required|regex:/^[a-z\d\-_\s\.\,]+$/i',
            // 'residential_address' => 'required|regex:/^[a-z\d\-_\s\.\,]+$/i',
            // 'monthly_volume' => 'required',
            // 'country' => 'required',
            'country_code' => 'required',
            // 'processing_currency' => 'required',
            // 'technology_partner_id' => 'required',
            // 'processing_country' => 'required',
            'category_id' => 'required',
            'company_license' => 'required',
            // 'passport' => 'required|array|min:' . $total_required_files,
            // 'passport.*' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            // 'company_incorporation_certificate' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            'domain_ownership' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            // 'latest_bank_account_statement' => 'required|array|min:' . $total_required_files,
            // 'latest_bank_account_statement.*' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            // 'utility_bill' => 'required|array|min:' . $total_required_files,
            // 'utility_bill.*' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            // 'previous_processing_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            // 'extra_document.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            // 'owner_personal_bank_statement' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            'licence_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            // 'moa_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            // 'board_of_directors' => 'required'
        ];
        if (!empty($varData)) {
            if (!empty($varData['skype_id'])) {
                unset($ArrValidate['skype_id']);
            }
            if (!empty($varData['country_code'])) {
                unset($ArrValidate['country_code']);
            }
            if (!empty($varData['category_id'])) {
                unset($ArrValidate['category_id']);
            }
            if (!empty($varData['company_license'])) {
                unset($ArrValidate['company_license']);
            }
            if (!empty($varData['domain_ownership'])) {
                unset($ArrValidate['domain_ownership']);
            }
            if (!empty($varData['licence_document'])) {
                unset($ArrValidate['licence_document']);
            }            
        }
        return $ArrValidate;
    }
}
