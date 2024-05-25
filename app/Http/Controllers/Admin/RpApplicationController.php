<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Agent;
use Validator;
use Dompdf\Dompdf;
use App\Categories;
use Dompdf\Options;
use App\AdminAction;
use App\MailTemplates;
use App\RpApplication;
use App\TechnologyPartner;
use Illuminate\Http\Request;
use App\Jobs\SendRpAgreementJob;
use App\Mail\AgreementSentMailRP;
use App\RpAgreementDocumentUpload;
use App\Notifications\NewBankEmail;
use App\Http\Controllers\Controller;
use App\Mail\AgentApplicationRejectMail;
use App\Http\Controllers\AdminController;
use App\Mail\AgentApplicationApproveMail;
use App\Mail\AgentApplicationReassignMail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;


class RpApplicationController extends AdminController
{

    protected $rpApplication, $agentUser, $template, $moduleTitleS, $moduleTitleP;

    public function __construct()
    {
        parent::__construct();
        $this->rpApplication = new RpApplication;
        $this->agentUser = new Agent;
        $this->template = new MailTemplates;

        $this->moduleTitleS = 'RP';
        $this->moduleTitleP = 'admin.applications.rpapplication';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_name = "";
        $email = "";
        $website_url = "";
        $start_date = '';
        $end_date = '';
        $industries_reffered = "";
        $major_regious = "";
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->company_name) {
            $company_name = $request->company_name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }
        if ($request->status || $request->status == '0') {
            $where['status'] = $request->status;
        }
        if ($request->start_date) {
            $start_date = date("Y-m-d", strtotime($request->start_date));
        }
        if ($request->end_date) {
            $end_date = date("Y-m-d", strtotime($request->end_date));
        }
        if ($request->industries_reffered) {
            $industries_reffered = $request->industries_reffered;
        }
        if ($request->major_regious) {
            $major_regious = $request->major_regious;
            if ($major_regious == "US/CANADA") {
                $major_regious = "CANADA";
            }
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition  = '=';
                    break;
                case 'l':
                    $monthly_volume_condition  = '<';
                    break;
                case 'le':
                    $monthly_volume_condition  = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition  = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition  = '>=';
                    break;
                default:
                    $monthly_volume_condition  = null;
                    break;
            }
        }
        if ($request->monthly_volume) {
            $monthly_volume = $request->monthly_volume;
        }

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data['noList'] = $noList;
        $data['applications'] = RpApplication::orderBy('rp_applications.id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('rp_applications.created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->whereDate('rp_applications.created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('rp_applications.website_url', 'like', '%' . $website_url . '%');
            })
            ->when($company_name != null, function ($query) use ($company_name) {
                return $query->where('rp_applications.company_name', 'like', '%' . $company_name . '%');
            })
            ->when($industries_reffered != null, function ($query) use ($industries_reffered) {
                return $query->where('rp_applications.industries_reffered', 'like', '%' . $industries_reffered . '%');
            })
            ->when($major_regious != null, function ($query) use ($major_regious) {
                return $query->where('rp_applications.major_regious', 'like', '%' . $major_regious . '%');
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('commited_avg_volume_per_month', $monthly_volume_condition, $monthly_volume);
            })
            ->with('agent')
            ->whereHas('agent', function ($query) use ($email) {
                $query->when($email != null, function ($query) use ($email) {
                    return $query->where('email', 'LIKE', '%' . $email . '%');
                });
            })->paginate($noList);
        // dd($data);
        $data['integration_preference'] = TechnologyPartner::orderBy('name')->pluck('name', 'id');
        $data['industry_type'] = Categories::orderBy('name')->pluck('name', 'id');
        return view($this->moduleTitleP . '.all-applications', $data);
    }

    public function pending_application(Request $request)
    {
        $company_name = "";
        $email = "";
        $website_url = "";
        $start_date = '';
        $end_date = '';
        $industries_reffered = "";
        $major_regious = "";
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array('status' => '0');
        if ($request->company_name) {
            $company_name = $request->company_name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }
        if ($request->status || $request->status == '0') {
            $where['status'] = $request->status;
        }
        if ($request->start_date) {
            $start_date = date("Y-m-d", strtotime($request->start_date));
        }
        if ($request->end_date) {
            $end_date = date("Y-m-d", strtotime($request->end_date));
        }
        if ($request->industries_reffered) {
            $industries_reffered = $request->industries_reffered;
        }
        if ($request->major_regious) {
            $major_regious = $request->major_regious;
            if ($major_regious == "US/CANADA") {
                $major_regious = "CANADA";
            }
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition  = '=';
                    break;
                case 'l':
                    $monthly_volume_condition  = '<';
                    break;
                case 'le':
                    $monthly_volume_condition  = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition  = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition  = '>=';
                    break;
                default:
                    $monthly_volume_condition  = null;
                    break;
            }
        }
        if ($request->monthly_volume) {
            $monthly_volume = $request->monthly_volume;
        }
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data['noList'] = $noList;
        $data['applications'] = RpApplication::orderBy('rp_applications.id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('rp_applications.created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->whereDate('rp_applications.created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('rp_applications.website_url', 'like', '%' . $website_url . '%');
            })
            ->when($company_name != null, function ($query) use ($company_name) {
                return $query->where('rp_applications.company_name', 'like', '%' . $company_name . '%');
            })
            ->when($industries_reffered != null, function ($query) use ($industries_reffered) {
                return $query->where('rp_applications.industries_reffered', 'like', '%' . $industries_reffered . '%');
            })
            ->when($major_regious != null, function ($query) use ($major_regious) {
                return $query->where('rp_applications.major_regious', 'like', '%' . $major_regious . '%');
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('commited_avg_volume_per_month', $monthly_volume_condition, $monthly_volume);
            })
            ->with('agent')
            ->whereHas('agent', function ($query) use ($email) {
                $query->when($email != null, function ($query) use ($email) {
                    return $query->where('email', 'LIKE', '%' . $email . '%');
                });
            })->paginate($noList);
        $data['integration_preference'] = TechnologyPartner::orderBy('name')->pluck('name', 'id');
        $data['industry_type'] = Categories::orderBy('name')->pluck('name', 'id');
        return view($this->moduleTitleP . '.all-applications', $data);
    }

    public function approved_application(Request $request)
    {
        $company_name = "";
        $email = "";
        $website_url = "";
        $start_date = '';
        $end_date = '';
        $industries_reffered = "";
        $major_regious = "";
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array('status' => '1');
        if ($request->company_name) {
            $company_name = $request->company_name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }
        if ($request->status || $request->status == '0') {
            $where['status'] = $request->status;
        }
        if ($request->start_date) {
            $start_date = date("Y-m-d", strtotime($request->start_date));
        }
        if ($request->end_date) {
            $end_date = date("Y-m-d", strtotime($request->end_date));
        }
        if ($request->industries_reffered) {
            $industries_reffered = $request->industries_reffered;
        }
        if ($request->major_regious) {
            $major_regious = $request->major_regious;
            if ($major_regious == "US/CANADA") {
                $major_regious = "CANADA";
            }
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition  = '=';
                    break;
                case 'l':
                    $monthly_volume_condition  = '<';
                    break;
                case 'le':
                    $monthly_volume_condition  = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition  = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition  = '>=';
                    break;
                default:
                    $monthly_volume_condition  = null;
                    break;
            }
        }
        if ($request->monthly_volume) {
            $monthly_volume = $request->monthly_volume;
        }
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data['noList'] = $noList;
        $data['applications'] = RpApplication::orderBy('rp_applications.id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('rp_applications.created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->whereDate('rp_applications.created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('rp_applications.website_url', 'like', '%' . $website_url . '%');
            })
            ->when($company_name != null, function ($query) use ($company_name) {
                return $query->where('rp_applications.company_name', 'like', '%' . $company_name . '%');
            })
            ->when($industries_reffered != null, function ($query) use ($industries_reffered) {
                return $query->where('rp_applications.industries_reffered', 'like', '%' . $industries_reffered . '%');
            })
            ->when($major_regious != null, function ($query) use ($major_regious) {
                return $query->where('rp_applications.major_regious', 'like', '%' . $major_regious . '%');
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('commited_avg_volume_per_month', $monthly_volume_condition, $monthly_volume);
            })
            ->with('agent')
            ->whereHas('agent', function ($query) use ($email) {
                $query->when($email != null, function ($query) use ($email) {
                    return $query->where('email', 'LIKE', '%' . $email . '%');
                });
            })->paginate($noList);
        $data['integration_preference'] = TechnologyPartner::orderBy('name')->pluck('name', 'id');
        $data['industry_type'] = Categories::orderBy('name')->pluck('name', 'id');
        return view($this->moduleTitleP . '.all-applications', $data);
    }

    public function reassign_application(Request $request)
    {
        $company_name = "";
        $email = "";
        $website_url = "";
        $start_date = '';
        $end_date = '';
        $industries_reffered = "";
        $major_regious = "";
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array('status' => '3');
        if ($request->company_name) {
            $company_name = $request->company_name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }
        if ($request->status || $request->status == '0') {
            $where['status'] = $request->status;
        }
        if ($request->start_date) {
            $start_date = date("Y-m-d", strtotime($request->start_date));
        }
        if ($request->end_date) {
            $end_date = date("Y-m-d", strtotime($request->end_date));
        }
        if ($request->industries_reffered) {
            $industries_reffered = $request->industries_reffered;
        }
        if ($request->major_regious) {
            $major_regious = $request->major_regious;
            if ($major_regious == "US/CANADA") {
                $major_regious = "CANADA";
            }
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition  = '=';
                    break;
                case 'l':
                    $monthly_volume_condition  = '<';
                    break;
                case 'le':
                    $monthly_volume_condition  = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition  = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition  = '>=';
                    break;
                default:
                    $monthly_volume_condition  = null;
                    break;
            }
        }
        if ($request->monthly_volume) {
            $monthly_volume = $request->monthly_volume;
        }
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data['noList'] = $noList;
        $data['applications'] = RpApplication::orderBy('rp_applications.id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('rp_applications.created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->whereDate('rp_applications.created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('rp_applications.website_url', 'like', '%' . $website_url . '%');
            })
            ->when($company_name != null, function ($query) use ($company_name) {
                return $query->where('rp_applications.company_name', 'like', '%' . $company_name . '%');
            })
            ->when($industries_reffered != null, function ($query) use ($industries_reffered) {
                return $query->where('rp_applications.industries_reffered', 'like', '%' . $industries_reffered . '%');
            })
            ->when($major_regious != null, function ($query) use ($major_regious) {
                return $query->where('rp_applications.major_regious', 'like', '%' . $major_regious . '%');
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('commited_avg_volume_per_month', $monthly_volume_condition, $monthly_volume);
            })
            ->with('agent')
            ->whereHas('agent', function ($query) use ($email) {
                $query->when($email != null, function ($query) use ($email) {
                    return $query->where('email', 'LIKE', '%' . $email . '%');
                });
            })->paginate($noList);
        $data['integration_preference'] = TechnologyPartner::orderBy('name')->pluck('name', 'id');
        $data['industry_type'] = Categories::orderBy('name')->pluck('name', 'id');
        return view($this->moduleTitleP . '.all-applications', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agent  $bank
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $application = $this->rpApplication->findData($id);
        // dd($application->agent->agreementDocument);
        return view($this->moduleTitleP . '.detail', compact('application'));
    }

    public function downloadRpApplicationDocumentsUpload(Request $request)
    {
        return Storage::disk('s3')->download($request->file);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Agent  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $integration_preference = TechnologyPartner::orderBy('name')->pluck('name', 'id');
        $industry_type = Categories::orderBy('name')->pluck('name', 'id');
        $application = $this->rpApplication->findData($id);
        return view($this->moduleTitleP . '.edit', compact('application', 'integration_preference', 'industry_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agent  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agent $agent)
    {
        $this->validate(
            $request,
            [
                "company_name" => "required",
                "website_url" => "required",
                "company_registered_number" => "required",
                "company_registered_number_year" => "required",
                "company_address" => "required|max:300",
                "company_email" => "required|email",
                "major_regious" => "required",
                "avg_no_of_app" => "required|numeric|digits_between:0,10",
                "commited_avg_volume_per_month" => "required|numeric|digits_between:0,10",
                "payment_solutions_needed" => "required",
                "industries_reffered" => "required",
                "authorized_individual_name.*" => "required",
                "authorized_individual_phone_number.*" => "required",
                "authorized_individual_email.*" => "required",
                "generated_lead" => "max:300",
                'passport.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'utility_bill.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'company_incorporation_certificate' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'tax_id' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            ],
            [
                'company_name.required' => 'The Entity Name field is required.',
                'company_address.required' => 'The Address field is required.',
                'company_registered_number_year.required' => 'The Date Of Birth/Incorporation field is required.',
                'passport.*.max' => 'The passport size may not be greater than 25 MB.',
                'company_incorporation_certificate.max' => 'The company incorporation certificate size may not be greater than 35 MB.',
                'utility_bill.*.max' => 'The utility bill size may not be greater than 35 MB.',
                'tax_id.max' => 'The Tax Id certificate size may not be greater than 35 MB.',
            ]
        );

        $application = $this->rpApplication->findData($request->id);
        $input = \Arr::except($request->all(), ['_token', 'action', 'authorized_individual_name', 'authorized_individual_phone_number', 'authorized_individual_email', 'id']);

        foreach ($request->authorized_individual_email as $key => $email) {
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
            if ($application->passport != null) {
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
            if ($application->utility_bill != null) {
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

        DB::beginTransaction();
        try {
            $this->rpApplication->updateApplication($request->id, $input);

            DB::commit();

            notificationMsg('success', "Application updated Successfully.");

            return redirect()->route('application-rp.detail', $request->id);
        } catch (Exception $e) {
            DB::rollBack();

            notificationMsg('error', 'Something went wrong. Try Again.');

            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->bank->destroyData($id);
        notificationMsg('success', 'Bank Delete Successfully!');
        return redirect()->route('banks.index');
    }

    public function changeStatus(Request $request, $id)
    {
        $status = $request->get('status');
        $this->bank->updateData($id, ['is_active' => $status]);
        notificationMsg('success', 'Status Change Successfully!');
        return redirect()->route('banks.index');
    }


    public function applicationApprove(Request $request)
    {

        DB::beginTransaction();

        try {
            $this->rpApplication->updateApplication($request->id, ['status' => '1']);
            $applicationData = $this->rpApplication->findData($request->id);
            $this->agentUser->updateData($applicationData->agent_id, ['agreement_status' => '1']);
            $rp_id = $applicationData->agent_id;
            $rp = Agent::where('id', $rp_id)->first();
            $rp->add_buy_rate = $request->add_buy_rate;
            $rp->add_buy_rate_master = $request->add_buy_rate_master;
            $rp->add_buy_rate_amex = $request->add_buy_rate_amex;
            $rp->add_buy_rate_discover = $request->add_buy_rate_discover;
            $rp->save();
            $adminid = auth()->guard('admin')->user()->id;

            // SendRpAgreementJob::dispatch($rp, $adminid);
            $token = $rp->id . Str::random(32);
            $data['url'] = URL::to('/') . '/rp-agreement-documents-upload?rpId=' . $rp->id . '&token=' . $token;

            // $add_buy_rate = $this->rp->add_buy_rate;
            // $add_buy_rate_master = $this->rp->add_buy_rate_master;
            // $add_buy_rate_amex = $this->rp->add_buy_rate_amex;
            // $add_buy_rate_discover = $this->rp->add_buy_rate_discover;

            $options = new Options();
            $options->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($options);

            $dompdf->loadHtml(view('admin.agents.agreement_PDF'));

            $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

            $dompdf->render();

            $filePath = 'uploads/agreement_' . $rp->id . '/agreement.pdf';
            Storage::put($filePath, $dompdf->output());
            Log::info(["rp-agreement-local" => $data]);
            Storage::disk('s3')->put($filePath, $dompdf->output());
            Log::info(["rp-agreement-s3" => $data]);
            $data['file'] = getS3Url($filePath);

            Mail::to($rp->email)->queue(new AgreementSentMailRP($data));

            RpAgreementDocumentUpload::updateOrInsert(['rp_id' => $rp->id], [
                'rp_id' => $rp->id, 'token' => $token, 'sent_files' => $filePath
            ]);
            $ArrRequest = ['rp_id' => $rp->id, 'token' => $token];
            addAdminLog(AdminAction::REFERRAL_PARTNER_AGREEMENT_STATUS, $rp->id, $ArrRequest, "Agreement Sent");

            $notification = [
                'user_id' => $rp->id,
                'sendor_id' => $adminid,
                'type' => 'RP',
                'title' => 'Agreement Sent',
                'body' => 'Agreement has been sent to your email.',
                'url' => '/rp/dashboard',
                'is_read' => '0'
            ];

            addNotification($notification);
            DB::commit();
            return response()->json(['success' => '1']);
        } catch (\Exception $e) {
            // \Log::info($e);
            DB::rollBack();
            Session::put('error', 'Soemthing wrong! try Again later.');
            return response()->json(['success' => '0']);
        }
    }

    public function applicationReject(Request $request)
    {

        if ($this->rpApplication->updateApplication($request->id, ['status' => '2', 'reject_reason' => $request->reject_reason])) {

            $applicationData = $this->rpApplication->findData($request->id);
            if (!empty($applicationData)) {
                if ($applicationData->email != "") {
                    $data = array();
                    try {
                        \Mail::to($applicationData->email)->send(new AgentApplicationRejectMail($data));
                    } catch (Exception $e) {
                    }
                }
            }

            notificationMsg('success', 'Application Rejected Successfully!');
        } else {
            notificationMsg('error', 'Something went wrong. Please try again.');
        }
        return redirect()->back();
    }

    public function applicationReAssign(Request $request)
    {
        if ($this->rpApplication->updateApplication($request->id, ['status' => '3', 'reassign_reason' => $request->reassign_reason])) {

            $applicationData = $this->rpApplication->findData($request->id);
            if (!empty($applicationData)) {
                if ($applicationData->email != "") {
                    $data['reassign_reason'] = $request->reassign_reason;
                    try {
                        \Mail::to($applicationData->email)->send(new AgentApplicationReassignMail($data));
                    } catch (Exception $e) {
                    }
                }
            }

            notificationMsg('success', 'Application Reassigned Successfully!');
        } else {
            notificationMsg('error', 'Something went wrong. Please try again.');
        }
        return redirect()->back();
    }

    public function deleteAllApplication(Request $request)
    {
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $this->rpApplication->softDelete($value);
            }
            return response()->json([
                'success' => true,
            ]);
        }
    }
}
