<?php

namespace App\Http\Controllers\Admin;

use App\Mail\ApmRatesEmail;
use App\MIDDetail;
use GuzzleHttp\Utils;
use Illuminate\Support\Facades\DB;
use File;

use Storage;
use App\Bank;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
//use Excel;
use App\User;
use Response;
use App\Agent;
use Validator;
use Dompdf\Dompdf;
use App\Categories;
use Dompdf\Options;
use App\AdminAction;
use App\Application;
use App\MailTemplates;
use App\ApplicationNote;
use App\AgreementContent;
use App\TechnologyPartner;
use Illuminate\Support\Arr;
use App\ApplicationNoteBank;
use Illuminate\Http\Request;
use App\Mail\AgreementSentMail;
use App\AgreementDocumentUpload;
use App\ApplicationAssignToBank;
use App\Events\UserNotification;
use League\Flysystem\Filesystem;
use App\Jobs\SendMerchantAgreement;
use App\Mail\AgreementReAssignMail;
use function GuzzleHttp\json_decode;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\ApplicationNoteBankMail;
use App\Exports\AllApplicationsExport;
use Illuminate\Support\Facades\Session;
use App\Notifications\ApplicationReject;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\AdminController;
use App\Mail\ApplicationAssignToBankMail;
use App\Notifications\ApplicationApprove;
use App\Exports\DeletedApplicationsExport;
use App\Exports\ApprovedApplicationsExport;
use App\Exports\RejectedApplicationsExport;
use App\Exports\CompletedApplicationsExport;
use App\Notifications\ApplicationReassigned;
use App\Exports\RateDeclineApplicationExport;
use App\Exports\SentToBankApplicationsExport;
use App\Exports\TerminatedApplicationsExport;
use App\Exports\RateAcceptedApplicationExport;
use App\Exports\AgreementSentApplicationsExport;

use App\Exports\NotInterestedApplicationsExport;
use App\Mail\ApplicationReferredReplyToBankMail;
use App\Exports\AgreementSignedApplicationsExport;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use App\Exports\AgreementReceivedApplicationsExport;
use App\Apm;

class ApplicationController extends AdminController
{
    protected $template, $Application, $ApplicationAssignToBank;

    public function __construct()
    {
        parent::__construct();
        $this->template = new MailTemplates;
        $this->Application = new Application;
        $this->ApplicationAssignToBank = new ApplicationAssignToBank;
    }

    public function index(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }
        if ($request->status || $request->status == '0') {
            $where['status'] = $request->status;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
                    break;
            }
        }
        if ($request->monthly_volume) {
            $monthly_volume = $request->monthly_volume;
        }

        $input = Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $data['noList'] = $noList;
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->whereDate('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', 'like', '%' . $website_url . '%');
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    })->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->paginate($noList);
        return view('admin.applications.applications_list', $data);
    }

    public function is_completed(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->start_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();

        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('status', '1')
            ->paginate($noList);
        return view('admin.applications.is_completed_applications_list', $data);
    }

    public function is_approved(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('status', '4')
            ->paginate($noList);
        return view('admin.applications.approved_applications_list', $data);
    }

    public function createApplication(Request $request)
    {
        $id = $request->id;
        $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
        $technologypartners = TechnologyPartner::latest()->pluck('name', 'id')->toArray();

        $countries = DB::table("countries")->get();
        return view('admin.applications.create', compact('technologypartners', 'category', 'id','countries'));
    }

    public function startApplicationStore(Request $request,$id)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $Appdata = Application::where('user_id', $id)->first();
        $total_required_files = 1;
        $this->validate(
            $request,
            [
                'business_contact_first_name' => 'required',
                'business_contact_last_name' => 'required',
                'business_name' => 'required',
                'country' => 'required',
                'business_category' => 'required',
                'business_address1' => 'required',
                'category_id' => 'required',
                'website_url' => 'required|url',
                'phone_no' => 'required|numeric',
                'skype_id' => 'required',
                'company_license' => 'required',                
                'passport.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'company_incorporation_certificate' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'domain_ownership' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'latest_bank_account_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'utility_bill.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'owner_personal_bank_statement' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'licence_document' => 'required_if:company_license,0|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'moa_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            ],
            [
                'passport.*.max' => 'The passport size may not be greater than 25 MB.',
                'company_incorporation_certificate.max' => 'The company incorporation certificate size may not be greater than 35 MB.',
                // 'domain_ownership.required' => 'The domain ownership file is required.',
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
        $input['user_id'] = $id;
        $user = User::find($id);

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

        if ($request->hasFile('utility_bill')) {
            $files = $request->file('utility_bill');
            $utilityBillArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($utilityBillArr, $filePath);
            }
            $input['utility_bill'] = json_encode($utilityBillArr);
        }

        if ($request->hasFile('moa_document')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('moa_document')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('moa_document')->getRealPath()));
            $input['moa_document'] = $filePath;
        }

        if ($request->hasFile('owner_personal_bank_statement')) {
            File::delete(storage_path() . '/uploads/application-' . $user->id . '/' . $application->owner_personal_bank_statement);
            $imageOwnerBankStatement = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageOwnerBankStatement = $imageOwnerBankStatement . '.' . $request->file('owner_personal_bank_statement')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageOwnerBankStatement;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('owner_personal_bank_statement')->getRealPath()));
            $input['owner_personal_bank_statement'] = $filePath;
        }

        if ($request->hasFile('company_incorporation_certificate')) {
            Storage::disk('s3')->delete($application->company_incorporation_certificate);
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('company_incorporation_certificate')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('company_incorporation_certificate')->getRealPath()));
            $input['company_incorporation_certificate'] = $filePath;
        }

        if ($request->hasFile('licence_document')) {
            $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageNameCertificate = $imageNameCertificate . '.' . $request->file('licence_document')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageNameCertificate;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('licence_document')->getRealPath()));
            $input['licence_document'] = $filePath;
        }

        unset($input['action']);
        try {
            $input['status'] = '1';
            $application = $this->application->storeData($input);
            return redirect()->route('users-management')->with('success', "Application has beed uploaded Successfully.");
        } catch (Exception $e) {
            \Session::put('error', "Your application did not go through successfully. Please attempt the submission again.");
            return redirect()->back()->withInput($request->all());
        }
       
    }

    public function rateAccepted(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::select('applications.*')
            ->join('users', 'users.id', 'applications.user_id')
            ->orderBy('applications.id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('applications.created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('applications.created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('applications.website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('applications.status', '10')
            ->where('users.is_rate_sent', '2')
            ->paginate($noList);

        return view('admin.applications.rate_accepted_applications_list', $data);
    }

    public function rateDecline(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::select('applications.*')
            ->join('users', 'users.id', 'applications.user_id')
            ->orderBy('applications.id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('applications.created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('applications.created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('applications.website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('applications.status', '9')
            ->where('users.is_rate_sent', '3')
            ->paginate($noList);

        return view('admin.applications.rate_decline_applications_list', $data);
    }

    public function rateAcceptedExport(Request $request)
    {
        return (new RateAcceptedApplicationExport($request->ids))->download();
    }

    public function rateDeclineExport(Request $request)
    {
        return (new RateDeclineApplicationExport($request->ids))->download();
        //        return Excel::download(new RateDeclineApplicationExport($request->ids), 'Rate_Decline_Merchants_' . date('d-m-Y') . '.xlsx');
    }

    public function is_rejected(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
                    break;
            }
        }
        if ($request->monthly_volume) {
            $monthly_volume = $request->monthly_volume;
        }

        $input = Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $data['noList'] = $noList;
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('status', '3')
            ->paginate($noList);
        return view('admin.applications.rejected_applications_list', $data);
    }

    public function not_interested(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }

        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('status', '7')
            ->paginate($noList);
        return view('admin.applications.not_interested_applications_list', $data);
    }

    public function is_terminated(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
                    break;
            }
        }
        if ($request->monthly_volume) {
            $monthly_volume = $request->monthly_volume;
        }

        $input = Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $data['noList'] = $noList;
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            if (!is_null($input['ids'])) {
                $input['id'] = explode(',', $input['ids']);
            } else {
                $input['id'] = null;
            }
            return Excel::download(new TerminatedApplicationsExport($input['id']), 'Terminated_Applications_Excel_' . date('d-m-Y') . '.xlsx');
        }

        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('status', '8')
            ->paginate($noList);
        return view('admin.applications.terminated_applications_list', $data);
    }

    public function is_deleted(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        if (isset($input['type']) && $input['type'] == 'xlsx') {
            if (!is_null($input['ids'])) {
                $input['id'] = explode(',', $input['ids']);
            } else {
                $input['id'] = null;
            }
            return Excel::download(new DeletedApplicationsExport($input['id']), 'Deleted_Applications_Excel_' . date('d-m-Y') . '.xlsx');
        }

        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->get();
        $data['categories'] = Categories::all();
        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->onlyTrashed()
            ->paginate($noList);
        return view('admin.applications.deleted_applications_list', $data);
    }

    public function agreement_send(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('status', '5')
            ->paginate($noList);
        return view('admin.applications.agreement_send_applications_list', $data);
    }

    public function agreementSigned(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    });
            })
            ->where('status', '11')
            ->paginate($noList);
        return view('admin.applications.agreement_signed_applications_list', $data);
    }

    public function agreement_received(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $website_url = '';
        $agent_id = '';
        $name = '';
        $email = '';
        $category_id = '';
        $monthly_volume_condition = '';
        $monthly_volume = '';
        $where = array();
        if ($request->country) {
            $where['country'] = $request->country;
        }
        if ($request->technology_partner_id) {
            $where['technology_partner_id'] = $request->technology_partner_id;
        }

        if ($request->user_id) {
            $where['user_id'] = $request->user_id;
        }
        if ($request->website_url) {
            $website_url = $request->website_url;
        }

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }
        if ($request->agent_id) {
            $agent_id = $request->agent_id;
            if ($agent_id != 'no-agent') {
                $agent_id = (int) $agent_id;
            }
        }
        if ($request->name) {
            $name = $request->name;
        }
        if ($request->email) {
            $email = $request->email;
        }
        if ($request->category_id) {
            $category_id = $request->category_id;
        }
        if ($request->monthly_volume_condition) {
            $monthly_volume_condition = $request->monthly_volume_condition;
            switch ($monthly_volume_condition) {
                case 'e':
                    $monthly_volume_condition = '=';
                    break;
                case 'l':
                    $monthly_volume_condition = '<';
                    break;
                case 'le':
                    $monthly_volume_condition = '<=';
                    break;
                case 'g':
                    $monthly_volume_condition = '>';
                    break;
                case 'ge':
                    $monthly_volume_condition = '>=';
                    break;
                default:
                    $monthly_volume_condition = null;
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
        $data['agentName'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['technologyPartner'] = TechnologyPartner::all();
        $data['businessNames'] = Application::orderBy('id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->get();
        $data['categories'] = Categories::all();

        $data['template'] = $this->template->getListForMail();

        $data['applications'] = Application::orderBy('id', 'desc')
            ->when($where != null, function ($query) use ($where) {
                return $query->where($where);
            })
            ->when($start_date != null, function ($query) use ($start_date) {
                return $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date != null, function ($query) use ($end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($website_url != null, function ($query) use ($website_url) {
                return $query->where('website_url', '=', $website_url);
            })
            ->when($category_id != null, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(($monthly_volume_condition != null && $monthly_volume != null && is_numeric($monthly_volume)), function ($query) use ($monthly_volume_condition, $monthly_volume) {
                return $query->where('monthly_volume', $monthly_volume_condition, $monthly_volume);
            })
            ->with('user')
            ->whereHas('user', function ($query) use ($agent_id, $name, $email) {
                $query->when($agent_id == 'no-agent', function ($query) use ($agent_id) {
                    return $query->where('agent_id', NULL);
                })
                    ->when(is_int($agent_id), function ($query) use ($agent_id) {
                        return $query->where('agent_id', $agent_id);
                    })
                    ->when($name != null, function ($query) use ($name) {
                        return $query->where('name', 'LIKE', '%' . $name . '%');
                    })
                    ->when($email != null, function ($query) use ($email) {
                        return $query->where('email', 'LIKE', '%' . $email . '%');
                    })->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->where('status', '6')
            ->paginate($noList);
        return view('admin.applications.agreement_received_applications_list', $data);
    }

    public function getSentToBank(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $data['noList'] = $noList;
        $data['bankName'] = Bank::where('deleted_at', null)->get(['id', 'bank_name']);
        $data['businessNames'] = Application::orderBy('applications.id', 'desc')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_whitelable', '0')->where('is_white_label', '0');
            })
            ->join('application_assign_to_bank', 'application_assign_to_bank.application_id', 'applications.id')
            ->groupBy('application_assign_to_bank.application_id')
            ->get();
        $data['categories'] = Categories::all();

        $data['applications'] = ApplicationAssignToBank::select('applications.*', 'users.email as email')
            ->join('applications', 'applications.id', 'application_assign_to_bank.application_id')
            ->join('users', 'users.id', 'applications.user_id');

        if (isset($input['email']) && $input['email'] != '') {
            $data['applications'] = $data['applications']->where('users.email', $input['email']);
        }

        if (isset($input['website_url']) && $input['website_url'] != '') {
            $data['applications'] = $data['applications']->where('applications.website_url', $input['website_url']);
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data['applications'] = $data['applications']->where('applications.user_id', $input['user_id']);
        }

        if (isset($input['bank_id']) && $input['bank_id'] != '') {
            $data['applications'] = $data['applications']->where('application_assign_to_bank.bank_user_id', $input['bank_id']);
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data['applications'] = $data['applications']->where('application_assign_to_bank.status', $input['status']);
        }

        if (isset($input['category_id']) && $input['category_id'] != '') {
            $data['applications'] = $data['applications']->where('applications.category_id', $input['category_id']);
        }

        $data['applications'] = $data['applications']->orderBy('application_assign_to_bank.id', 'desc')
            ->groupBy('application_assign_to_bank.application_id')
            ->paginate($noList);

        return view('admin.applications.sent_to_bank_applications_list', $data);
    }

    public function applicationReferredReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referred_note_reply' => 'required',
        ]);
        // dd($request->all());
        $input = \Arr::except($request->all(), array('_token', '_method', 'id', 'bank_users_id', 'referred_note_reply'));
        if ($validator->passes()) {
            \DB::beginTransaction();
            try {
                $bank = Bank::where('id', $request->get('bank_users_id'))->first();
                $application_assign_to_bank = $this->ApplicationAssignToBank->findData($request->get('applications_id'), $request->get('bank_users_id'));

                $files = $request->file('extra_documents');

                $extra_documents = null;
                if ($files) {
                    $extra_documentsArr = [];
                    foreach ($files as $key => $value) {
                        $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                        $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                        $filePath = 'uploads/assign-bank-application-' . $bank->id . '/' . $imageDocument;
                        Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                        array_push($extra_documentsArr, $filePath);
                    }
                    $extra_documents = json_encode($extra_documentsArr);
                }
                $this->ApplicationAssignToBank->applicationReferredReply($request->get('applications_id'), $request->get('bank_users_id'), $request->get('referred_note_reply'), $extra_documents);

                $application = Application::where('id', $request->get('applications_id'))->first();
                $application_assign_to_bank = $this->ApplicationAssignToBank->findData($request->get('applications_id'), $request->get('bank_users_id'));
                // Mail::to($bank->email)->send(new ApplicationReferredReplyToBankMail($bank, $application, $application_assign_to_bank));
            } catch (\Exception $e) {
                dd($e);
                notificationMsg('error', 'Something went wrong');
                return redirect()->back();
                \DB::rollBack();
            }
            \DB::commit();
            notificationMsg('success', 'Reply sent to bank successfully.');
            return redirect()->route('admin.applications.sent_to_bank');
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function view(Request $request)
    {
        $id = $request->id;
        $data['technologyPartner'] = TechnologyPartner::all();

        $data['agents'] = Agent::where('main_agent_id', '0')->get(['id', 'name']);
        $data['data'] = Application::select('applications.*', 'users.name', 'users.email', 'users.agent_commission', 'users.agent_commission_master_card', 'agreement_document_upload.sent_files as agreement_send', 'agreement_document_upload.files as agreement_received', 'agreement_document_upload.reassign_reason as agreement_reassign_reason')
            ->join('users', 'users.id', 'applications.user_id')
            ->leftjoin('agreement_document_upload', 'agreement_document_upload.application_id', 'applications.id')
            ->with('user')
            ->with('category')
            ->with('technology_partner')
            ->where('applications.id', $id)
            ->first();

        $data['bank'] = ApplicationAssignToBank::select('banks.*', 'banks.id as bankId', 'application_assign_to_bank.*', 'bank_applications.company_name as bankCompanyName')
            ->join('banks', 'banks.id', 'application_assign_to_bank.bank_user_id')
            ->join('bank_applications', 'bank_applications.bank_id', 'banks.id')
            ->where('application_assign_to_bank.application_id', $id)
            ->get();

        return view('admin.applications.show', $data);
    }

    public function update_partners(Request $request)
    {
        $id = $request->id;
        $partner_id = $request->partner_id;
        $success = 0;
        $message = 'Something went wrong.Plz try again later';
        if ($id && $partner_id) {
            $update = Application::where('id', $id)->update(['technology_partner_id' => $partner_id]);
            if ($update) {
                $success = 1;
                $message = 'Partner updated successfully.';
            }
        }
        $data['success'] = $success;
        $data['message'] = $message;
        echo json_encode($data);
    }

    public function edit_application(Request $request)
    {
        $id = $request->id;
        $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
        $technologypartners = TechnologyPartner::latest()->pluck('name', 'id')->toArray();

        $data = Application::where('id', $id)->first();

        return view('admin.applications.edit', compact('data', 'technologypartners', 'category', 'id'));
    }

    public function update_application(Request $request)
    {
        $id = $request->id;
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $user = User::where('id', $input['user_id'])->first();

        $application = Application::where('id', $id)->first();

        $this->validate(
            $request,
            [
                'business_contact_first_name' => 'required',
                'business_contact_last_name' => 'required',
                'business_name' => 'required',
                'country' => 'required',
                'business_category' => 'required',
                'business_address1' => 'required',
                'category_id' => 'required',
                'website_url' => 'required|url',
                'phone_no' => 'required|numeric',
                'skype_id' => 'required',
                'company_license' => 'required',
                'passport.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'company_incorporation_certificate' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'domain_ownership' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'latest_bank_account_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'utility_bill.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'previous_processing_statement.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                // 'extra_document.*' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'owner_personal_bank_statement' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'licence_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
                'moa_document' => 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840',
            ],
            [
                'passport.*.max' => 'The passport size may not be greater than 25 MB.',
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

        $filePath = storage_path() . "/uploads/" . $user->name . '-' . $user->id . '/';

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
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($passportArr, $filePath);
            }
            $updated_passport_documents = array_merge($old_passport_documents, $passportArr);
            $input['passport'] = json_encode($updated_passport_documents);
        }

        if ($request->hasFile('latest_bank_account_statement')) {
            $old_bank_statement = [];
            if ($application->latest_bank_account_statement != null) {
                $old_bank_statement = json_decode($application->latest_bank_account_statement);
            }

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
            $old_utilityBill = [];
            if ($application->utility_bill != null) {
                $old_utilityBill = json_decode($application->utility_bill);
            }
            $files = $request->file('utility_bill');
            $utilityBillArr = [];
            foreach ($files as $key => $value) {
                $imageDocument = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageDocument = $imageDocument . '.' . $value->getClientOriginalExtension();
                $filePath = 'uploads/application-' . $user->id . '/' . $imageDocument;
                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                array_push($utilityBillArr, $filePath);
            }
            $utilityBill = array_merge($old_utilityBill, $utilityBillArr);
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

        if ($request->hasFile('owner_personal_bank_statement')) {
            File::delete(storage_path() . '/uploads/application-' . $user->id . '/' . $application->owner_personal_bank_statement);
            $imageOwnerBankStatement = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
            $imageOwnerBankStatement = $imageOwnerBankStatement . '.' . $request->file('owner_personal_bank_statement')->getClientOriginalExtension();
            $filePath = 'uploads/application-' . $user->id . '/' . $imageOwnerBankStatement;
            Storage::disk('s3')->put($filePath, file_get_contents($request->file('owner_personal_bank_statement')->getRealPath()));
            $input['owner_personal_bank_statement'] = $filePath;
        }

        try {
            $this->Application->updateApplication($id, $input);
        } catch (\Exception $e) {
        }
        notificationMsg('success', 'Your Application was updated successfully.');
        return redirect()->route('application.view', $id);
    }

    public function approved_application(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method', 'id'));

        $agent = Agent::find($request->agent);

        $error = [];
        // if ($agent) {
        //     if ($agent->add_buy_rate && $agent->add_buy_rate > $input['merchant_discount_rate']) {
        //         $error['merchant_discount_rate_error'] = 'Merchant discount rate for visa must be greater than ' . $agent->add_buy_rate . '.';
        //     }
        // }

        if (count($error)) {
            return response()->json(['success' => '0', 'error' => $error]);
        }
        if ($this->Application->updateApplication($request->get('id'), ['status' => '10', 'reason_reassign' => ''])) {
            $application = Application::find($request->get('id'));
            $user = User::where('id', $application->user_id)->first();
            $updateUser = User::where('id', $application->user_id)
                ->update([
                    'mid' => '1',
                    'agent_id' => $request->get('agent'),
                    'minimum_settlement_amount' => $request->get('minimum_settlement_amount'),
                    'payment_frequency' => $request->get('payment_frequency'),
                    'merchant_discount_rate' => $request->get('merchant_discount_rate'),
                    'rolling_reserve_paercentage' => $request->get('rolling_reserve_paercentage'),
                    'setup_fee' => $request->get('setup_fee'),
                    'settlement_fee' => $request->get('settlement_fee'),
                    'transaction_fee' => $request->get('transaction_fee'),
                    'refund_fee' => $request->get('refund_fee'),
                    'chargeback_fee' => $request->get('chargeback_fee'),
                    'flagged_fee' => $request->get('flagged_fee'),
                    'retrieval_fee' => $request->get('retrieval_fee'),
                    'agent_commission' => $request->get("agent_commission"),
                    'is_rate_sent' => '1'
                ]);
            try {
                // $application->user->notify(new ApplicationApprove($application, $application->user));
            } catch (\Exception $e) {
                \Session::put('error', 'Soemthing wrong! try Again later.');
                return response()->json(['success' => '0']);
            }
            return response()->json(['success' => '1']);
        } else {
            return response()->json(['success' => '0']);
        }
    }

    public function reject_application(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reject_reason' => 'required',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method', 'id', 'reject_reason'));
        if ($validator->passes()) {
            if ($this->Application->updateApplication($request->get('id'), ['status' => '3', 'reason_reject' => $request->get('reject_reason'), 'reason_reassign' => ''])) {
                $application = Application::find($request->get('id'));
                try {
                    $application->user->notify(new ApplicationReject($application, $application->user));
                } catch (\Exception $e) {
                    \Session::put('error', 'Soemthing wrong! try Again later.');
                    return response()->json(['success' => '0']);
                }

                $notification = [
                    'user_id' => $application->user_id,
                    'sendor_id' => auth()->guard('admin')->user()->id,
                    'type' => 'user',
                    'title' => 'Application Rejected',
                    'body' => 'Your application has been rejected.',
                    'url' => '/my-application',
                    'is_read' => '0'
                ];

                $realNotification = addNotification($notification);
                $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                event(new UserNotification($realNotification->toArray()));

                return response()->json(['success' => '1']);
            } else {
                return response()->json(['success' => '0']);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function reassign_application(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reassign_reason' => 'required',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method', 'id', 'reassign_reason'));
        if ($validator->passes()) {
            if ($this->Application->updateApplication($request->get('id'), ['status' => '2', 'reason_reassign' => $request->get('reassign_reason')])) {
                $application = Application::find($request->get('id'));

                try {
                    $application->user->notify(new ApplicationReassigned($application, $application->user));
                } catch (\Exception $e) {
                    \Session::put('error', 'Soemthing wrong! try Again later.');
                    return response()->json(['success' => '0']);
                }

                $notification = [
                    'user_id' => $application->user_id,
                    'sendor_id' => auth()->guard('admin')->user()->id,
                    'type' => 'user',
                    'title' => 'Application Reassigned',
                    'body' => 'The application has been reassigned.',
                    'url' => '/my-application',
                    'is_read' => '0'
                ];

                $realNotification = addNotification($notification);
                $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                event(new UserNotification($realNotification->toArray()));

                return response()->json(['success' => '1']);
            } else {
                return response()->json(['success' => '0']);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function changeAgent(Request $request)
    {
        $id = $request->id;
        $user_id = $request->user_id;
        $rate = $request->rate;
        $agent = $request->agent;
        $status = 0;
        $msg = 'Referral Partners not change. Please try again.';
        if ($id && $user_id) {
            $array = array('agent_commission' => $rate, 'agent_id' => $agent);
            $update = User::where('id', $user_id)->update($array);
            if ($update) {
                $status = 1;
                $msg = 'Referral Partners change successfully.';
            }
        }
        $res['status'] = $status;
        $res['msg'] = $msg;

        echo json_encode($res);
    }

    public function destroy(Request $request, $id)
    {
        $this->Application->softDelete($id);

        notificationMsg('success', 'Application Deleted Successfully!');

        return redirect()->back();
    }

    public function restore(Request $request, $id)
    {
        $this->Application->restore($id);

        notificationMsg('success', 'Application Restored Successfully!');

        return redirect()->back();
    }

    public function downloadPDF(Request $request, $id)
    {
        $data = $this->Application->findData($id);
        view()->share('data', $data);
        // return view($this->moduleTitleP.'.applicationPDF', compact('data'));s
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml(view('admin.applications.application_PDF'));

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        // $dompdf->setPaper([0, 0, 1000.98, 900.85], 'landscape');
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream($data->business_name . '.pdf');
    }

    public function applicationAgreementSent(Request $request)
    {
        //try {
            $this->Application->updateApplication($request->get('id'), ['status' => '5']);
            $user_id = $request->get('user_id');
            $user = User::select('users.*',
                'applications.business_name as business_name',
                'applications.business_contact_first_name',
                'applications.business_contact_last_name',
                'applications.country',
                'applications.business_address1',
                'applications.phone_no',
                'applications.website_url',
                'applications.business_category',
                'applications.business_address1',
                )
            ->join('applications', 'applications.user_id','users.id')->where('users.id', $user_id)->first();
            $adminId = auth()->guard('admin')->user()->id;
            $appId = $request->get('id');
            view()->share('data', $user);
            $token = $user->id . Str::random(32);
            $data['url'] = URL::to('/') . '/service_agreement/upload?userId=' . $user->id . '&token=' . $token;

            $options = new Options();
            $options->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml(view('admin.applications.agreement_PDF'));
            // $dompdf->setPaper('A4', 'landscape');
            // $dompdf->setPaper([0, 0, 1000.98, 900.85], 'landscape');
            $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
            $dompdf->render();
            $filePath = 'uploads/agreement_' . $user->id . '/agreement.pdf';

            Storage::disk('s3')->put($filePath, $dompdf->output());

            $data['name'] = $user->name;
            $data['file'] = getS3Url($filePath);

            Mail::to($user->email)->queue(new AgreementSentMail($data));
            AgreementDocumentUpload::create(['user_id' => $user->id, 'application_id' => $appId, 'token' => $token, 'sent_files' => $filePath]);
            return response()->json(['success' => '1']);
        // } catch (\Exception $err) {
        //     Session::put('error', 'Soemthing wrong! try Again later.');
        //     return response()->json(['success' => '0']);

        // }

    }

    public function reassign_agreement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reassign_agreement_reason' => 'required',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method', 'id', 'reassign_agreement_reason'));
        if ($validator->passes()) {
            if (AgreementDocumentUpload::where('application_id', $request->get('id'))->update(['files' => '', 'reassign_reason' => $request->get('reassign_agreement_reason')])) {

                $application = Application::find($request->get('id'));
                $AgreementDocumentUpload = AgreementDocumentUpload::where('application_id', $request->get('id'))->first();
                $user = User::where('id', $application->user_id)->first();

                try {
                    $data['url'] = URL::to('/') . '/agreement-documents-upload?userId=' . $application->user_id . '&token=' . $AgreementDocumentUpload->token;
                    $data['reason'] = $request->get('reassign_agreement_reason');
                    // Mail::to($user->email)->send(new AgreementReAssignMail($data));
                } catch (\Exception $e) {
                    // \Log::info($application->user_id);
                    \Session::put('error', 'Soemthing wrong! try Again later.');
                    return response()->json(['success' => '0']);
                }

                $notification = [
                    'user_id' => $application->user_id,
                    'sendor_id' => auth()->guard('admin')->user()->id,
                    'type' => 'user',
                    'title' => 'Agreement Reassigned',
                    'body' => 'The agreement has been reassigned.',
                    'url' => '/my-application',
                    'is_read' => '0'
                ];

                $realNotification = addNotification($notification);
                $realNotification->created_at_date = convertDateToLocal($realNotification->created_at, 'd/m/Y H:i:s');
                event(new UserNotification($realNotification->toArray()));

                return response()->json(['success' => '1']);
            } else {
                return response()->json(['success' => '0']);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function applicationAgreementReceived(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method', 'id'));
        if ($this->Application->updateApplication($request->get('id'), ['status' => '6'])) {
            return response()->json(['success' => '1']);
        } else {
            return response()->json(['success' => '0']);
        }
    }

    public function updateTerminate(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method', 'id'));

        if ($this->Application->updateApplication($request->get('id'), ['status' => '8'])) {
            return response()->json(['success' => '1']);
        } else {
            return response()->json(['success' => '0']);
        }
    }

    public function downloadDocumentsUploade(Request $request)
    {
        $ArrRequest = [];
        addAdminLog(AdminAction::REFERRAL_PARTNER_DOCUMNET_DOWNLOAD, null, $ArrRequest, "Agreement document download");
        return Storage::disk('s3')->download($request->file);
    }

    public function viewAppImage(Request $request)
    {
        $user = auth()->user();
        echo Config('app.aws_path') . 'uploads/application-' . $user->id . '/' . $request->file;
    }

    public function backInprogress(Request $request, $id)
    {
        $app = Application::where('id', $id)->first();
        User::where('id', $app->user_id)->update(['is_rate_sent' => '0', 'rate_decline_reason' => NULL]);
        Application::where('id', $id)->update(['status' => '1']);

        notificationMsg('success', 'Application move successfully.');

        return redirect()->route('application.view', $id);
    }

    public function downloadDOCS(Request $request, $id)
    {
        $zipData = Application::find($id);
        $users = User::where('id', $zipData->user_id)->first();

        if (!$zipData) {
            \Session::put('warning', 'Not Found Any Documents !');
            return redirect()->back();
        }
        // see laravel's config/filesystem.php for the source disk
        $file_names = Storage::disk('s3')->files('uploads/application-' . $users->id);

        $zip = new Filesystem(new ZipArchiveAdapter(public_path($users->name . '_document.zip')));

        foreach ($file_names as $file_name) {
            $file_content = Storage::disk('s3')->get($file_name);
            $zip->put($file_name, $file_content);
        }

        if ($zip->getAdapter()->getArchive()->close()) {
            return response()->download(public_path($users->name . '_document.zip'))->deleteFileAfterSend(true);
        } else {
            \Session::put('warning', 'Please try again..');
            return redirect()->back();
        }
    }

    public function applicationMoveInNotInterested(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (!isset($input['ids']) || $input['ids'] == '') {
            notificationMsg('warning', 'You must be select at list one record.');
            return redirect()->back();
        }

        foreach ($input['ids'] as $key => $value) {
            if ($value == 'on') {
                continue;
            }
            Application::where('user_id', $value)->update(['status' => '7']);
        }
        return response()->json(['success' => '1']);
    }

    public function applicationRestore(Request $request, $id)
    {
        $this->Application->updateApplication($id, ['status' => '1', 'reason_reject' => NULL]);

        notificationMsg('success', 'Application Restored Successfully!');

        return redirect()->back();
    }

    public function changeNotInterestAppStatus(Request $request, $id)
    {
        $app = Application::where('id', $id)->first();
        Application::where('id', $id)->update([
            "status" => '1'
        ]);
        User::where('id', $app->user_id)->update([
            "is_rate_sent" => '0'
        ]);
        notificationMsg('success', 'Application Not Interested Reset Successfully!');

        return redirect()->back();
    }

    public function exportAllApplications(Request $request)
    {
        return (new AllApplicationsExport($request->ids))->download();
    }

    public function exportAllCompleted(Request $request)
    {
        return (new CompletedApplicationsExport($request->ids))->download();
    }

    public function exportAllApproved(Request $request)
    {
        return (new ApprovedApplicationsExport($request->ids))->download();
    }

    public function exportAllRejected(Request $request)
    {
        return (new RejectedApplicationsExport($request->ids))->download();
    }

    public function exportAllNotInterested(Request $request)
    {
        return (new NotInterestedApplicationsExport($request->ids))->download();
    }

    public function exportAllAgreementSend(Request $request)
    {
        return (new AgreementSentApplicationsExport($request->ids))->download();
    }

    public function exportAllAgreementSigned(Request $request)
    {
        return (new AgreementSignedApplicationsExport($request->ids))->download();
    }

    public function exportAllAgreementReceived(Request $request)
    {
        return (new AgreementReceivedApplicationsExport($request->ids))->download();
    }

    public function exportAllSentToBank(Request $request)
    {
        return (new SentToBankApplicationsExport($request->ids))->download();
        //        return Excel::download(new SentToBankApplicationsExport($request->ids), 'Sent_To_Bank_Applications_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportAllTerminated(Request $request)
    {
        return (new TerminatedApplicationsExport($request->ids))->download();
    }

    public function exportAllDeleted(Request $request)
    {
        return (new DeletedApplicationsExport($request->ids))->download();
        //        return Excel::download(new DeletedApplicationsExport($request->ids), 'Deleted_Applications_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function getApplicationBanks(Request $request)
    {
        $id = $request->get('id');

        $application = Application::where('id', $id)->first();

        $data = Bank::select('banks.*', 'bank_applications.company_name as bankCompanyName', 'application_assign_to_bank.status as status', 'application_assign_to_bank.bank_user_id as bank_user_id')
            ->leftJoin('application_assign_to_bank', function ($join) use ($id) {
                $join->on('application_assign_to_bank.bank_user_id', '=', 'banks.id')
                    ->where('application_assign_to_bank.application_id', $id)
                    ->where('application_assign_to_bank.deleted_at', NULL);
            })
            ->join('bank_applications', 'bank_applications.bank_id', 'banks.id')
            ->where('bank_applications.status', '1')
            ->whereRaw("find_in_set($application->category_id,category_id)")
            ->get();
        $final = [];
        foreach ($data as $value) {
            $processing_country = $application->processing_country;
            if (array_intersect(json_decode($value->processing_country), json_decode($application->processing_country))) {
                array_push($final, $value);
            }
        }
        $data = $final;
        $html = view('partials.bank.bankList', compact('data', 'id'))->render();

        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function sendApplicationToBank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'bank' => 'required',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($validator->passes()) {
            \DB::beginTransaction();
            try {
                $banks_id = $request->input('bank');

                if ($banks_id)
                    $bank_users = Bank::whereIn('id', $banks_id)->get();
                else
                    $bank_users = [];

                ApplicationAssignToBank::where('application_id', $input['application_id'])
                    ->where('status', '0')
                    ->whereNotIn('bank_user_id', $banks_id ?? [])
                    ->delete();

                if (!empty($bank_users)) {
                    foreach ($bank_users as $key => $value) {
                        $data['application_id'] = $input['application_id'];
                        $data['bank_user_id'] = $value->id;

                        $find = ApplicationAssignToBank::where('application_id', $input['application_id'])
                            ->where('bank_user_id', $value->id)->first();

                        if (is_null($find)) {
                            $this->ApplicationAssignToBank->storeData($data);

                            $appData = Application::select('applications.*', 'users.name as userName', 'users.email as email')
                                ->join('users', 'users.id', 'applications.user_id')
                                ->where('applications.id', $input['application_id'])
                                ->first();

                            // Mail::to($value->email)->send(new ApplicationAssignToBankMail($appData, $bank_users));
                        }
                    }
                }
            } catch (\Exception $e) {
                dd($e);
                \DB::rollBack();
                return response()->json(['success' => '0']);
            }
            \DB::commit();
            return response()->json(['success' => '1']);
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function sendMultiMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_template' => 'required',
            'subject' => 'required',
            'bodycontent' => 'required',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if ($validator->passes()) {
            $ids = explode(',', $input['id']);
            unset($input['id']);

            $details = [
                'ids' => $ids,
                'input' => $input
            ];

            // send all mail in queue.
            $job = (new \App\Jobs\ApplicationQueueEmail($details))
                ->delay(now()->addSeconds(2));

            dispatch($job);

            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json(['errors' => $validator->errors()]);
    }

    public function getApplicationNote(Request $request)
    {
        $data = ApplicationNote::where('application_id', $request->id)->latest()->get();

        $html = view('partials.application.note', compact('data'))->render();

        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function storeApplicationNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($validator->passes()) {

            $input['application_id'] = $request->get('id');
            $input['user_id'] = auth()->guard('admin')->user()->id;
            $input['note'] = $request->get('note');

            if (ApplicationNote::create($input)) {
                return response()->json(['success' => '1', 'id' => $request->get('id')]);
            } else {
                return response()->json(['success' => '0', 'id' => $request->get('id')]);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function getApplicationNoteBank(Request $request)
    {
        $data = ApplicationNoteBank::where('application_id', $request->id)->latest()->get();

        $html = view('partials.application.noteBank', compact('data'))->render();

        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function searchApplicationNoteBank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name_s' => 'required',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($validator->passes()) {
            $data = ApplicationNoteBank::where('bank_id', $request->bank_name_s)->latest()->get();

            $html = view('partials.application.noteBank', compact('data'))->render();

            return response()->json([
                'success' => '1',
                'html' => $html
            ]);
        } else {
            return response()->json(['errors' => $validator->errors()]);
        }
    }

    public function storeApplicationNoteBank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required',
            'bank_name' => 'required',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($validator->passes()) {
            $input['application_id'] = $request->get('id');
            $input['bank_id'] = $request->get('bank_name');
            $input['user_id'] = auth()->guard('admin')->user()->id;
            $input['user_type'] = 'ADMIN';
            $input['note'] = $request->get('note');

            if (ApplicationNoteBank::create($input)) {

                try {
                    $bank = Bank::where('id', $input['bank_id'])->first();
                    $application = Application::where('id', $input['application_id'])->first();

                    $data['note'] = $request->get('note');
                    $data['business_name'] = $application->business_name;

                    // Mail::to($bank->email)->send(new ApplicationNoteBankMail($data));
                } catch (\Exception $e) {
                }

                return response()->json(['success' => '1', 'id' => $request->get('id')]);
            } else {
                return response()->json(['success' => '0', 'id' => $request->get('id')]);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function applicationdeletedocs(Request $request)
    {

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['id']) && !empty($input['id'])) {
            $id = $input['id'];
            $type = $input['type'];
            $file = $input['file'];
            $data = Application::select('applications.*')->where('applications.id', $id)->first();
            if (isset($data[$type]) && !empty($data[$type])) {
                $ArrRequest = [];
                $ArrRequest['app_id'] = $id;
                $ArrRequest['type'] = $type;
                $ArrRequest['file'] = $file;

                if ($type == 'licence_document') {
                    $DeleteFileName = $data['licence_document'];
                    Storage::disk('s3')->delete($DeleteFileName);
                    Application::where('id', $id)->update(['licence_document' => '']);
                }
                if ($type == 'passport') {
                    $DeleteFileName = json_decode($data['passport'], true);
                    foreach ($DeleteFileName as $key => $value) {
                        if ($value == $file) {
                            Storage::disk('s3')->delete($value);
                            unset($DeleteFileName[$key]);
                        }
                    }
                    $DeleteFileName = array_values($DeleteFileName);
                    $DeleteFileName = json_encode($DeleteFileName);
                    Application::where('id', $id)->update(['passport' => $DeleteFileName]);
                }
                if ($type == 'company_incorporation_certificate') {
                    $DeleteFileName = $data['company_incorporation_certificate'];
                    Storage::disk('s3')->delete($DeleteFileName);
                    Application::where('id', $id)->update(['company_incorporation_certificate' => '']);
                }
                if ($type == 'domain_ownership') {
                    $DeleteFileName = $data['domain_ownership'];
                    Storage::disk('s3')->delete($DeleteFileName);
                    Application::where('id', $id)->update(['domain_ownership' => '']);
                }
                if ($type == 'owner_personal_bank_statement') {
                    $DeleteFileName = $data['owner_personal_bank_statement'];
                    Storage::disk('s3')->delete($DeleteFileName);
                    Application::where('id', $id)->update(['owner_personal_bank_statement' => '']);
                }
                if ($type == 'moa_document') {
                    $DeleteFileName = $data['moa_document'];
                    Storage::disk('s3')->delete($DeleteFileName);
                    Application::where('id', $id)->update(['moa_document' => '']);
                }
                if ($type == 'latest_bank_account_statement') {
                    $DeleteFileName = json_decode($data['latest_bank_account_statement'], true);
                    foreach ($DeleteFileName as $key => $value) {
                        if ($value == $file) {
                            Storage::disk('s3')->delete($value);
                            unset($DeleteFileName[$key]);
                        }
                    }
                    $DeleteFileName = array_values($DeleteFileName);
                    $DeleteFileName = json_encode($DeleteFileName);
                    Application::where('id', $id)->update(['latest_bank_account_statement' => $DeleteFileName]);
                }
                if ($type == 'utility_bill') {
                    $DeleteFileName = json_decode($data['utility_bill'], true);
                    foreach ($DeleteFileName as $key => $value) {
                        if ($value == $file) {
                            Storage::disk('s3')->delete($value);
                            unset($DeleteFileName[$key]);
                        }
                    }
                    $DeleteFileName = array_values($DeleteFileName);
                    $DeleteFileName = json_encode($DeleteFileName);
                    Application::where('id', $id)->update(['utility_bill' => $DeleteFileName]);
                }
                if ($type == 'previous_processing_statement') {
                    $DeleteFileName = json_decode($data['previous_processing_statement'], true);
                    foreach ($DeleteFileName as $key => $value) {
                        if ($value == $file) {
                            Storage::disk('s3')->delete($value);
                            unset($DeleteFileName[$key]);
                        }
                    }
                    $DeleteFileName = array_values($DeleteFileName);
                    $DeleteFileName = json_encode($DeleteFileName);
                    Application::where('id', $id)->update(['previous_processing_statement' => $DeleteFileName]);
                }
                if ($type == 'extra_document') {
                    $DeleteFileName = json_decode($data['extra_document'], true);
                    foreach ($DeleteFileName as $key => $value) {
                        if ($value == $file) {
                            Storage::disk('s3')->delete($value);
                            unset($DeleteFileName[$key]);
                        }
                    }
                    $DeleteFileName = array_values($DeleteFileName);
                    $DeleteFileName = json_encode($DeleteFileName);
                    Application::where('id', $id)->update(['extra_document' => $DeleteFileName]);
                }
                addAdminLog(AdminAction::REFERRAL_PARTNER_DOCUMENT_DELETE, $id, $ArrRequest, "Agreement document Delete");
                return response()->json(['success' => '1']);
            }
        } else {
            return response()->json(['success' => '0', 'errors' => 'Something wrong']);
        }
    }


    public function downloadFile(Request $request)
    {
        $path = $request->path;
        $filename = $request->filename;

        return Response::download($path, $filename);
    }

    public function deleteAllApplication(Request $request)
    {
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $this->Application->softDelete($value);
            }
            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function sendToBankList(Request $request)
    {
        $id = $request->id;
        $html = view('partials.application.sentBankList', compact('id'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    // * Resend the agreement email to user
    public function resendAgreementEmail(Request $request)
    {
        $payload = $request->only(['file', 'name', 'userId', 'email', 'appId']);
        $token = $payload['userId'] . Str::random(32);
        $data['url'] = URL::to('/') . '/agreement-documents-upload?userId=' . $payload['userId'] . '&token=' . $token;
        $data['name'] = $payload['name'];
        $data['file'] = getS3Url($payload['file']);

        AgreementDocumentUpload::where('user_id', $payload['userId'])->where('application_id', $payload['appId'])->update(["token" => $token]);
        // Mail::to($payload['email'])->queue(new AgreementSentMail($data));

        return response()->json(["status" => 200, "message" => "Email Sent successfully!"]);
    }

    // * APM modal content
    public function apmRatesContent(Request $resuest, $id)
    {
        $apms = MIDDetail::select("id", "bank_name", "apm_mdr", "apm_type")->where('mid_type', '5')->orderBy("id", "desc")->get();
        $apm = User::select("apm")->where("id", $id)->first();
        $userApms = null;
        if (isset($apm->apm)) {
            $userApms = json_decode($apm->apm, true);
        }
        $html = view('partials.application.apm_rates', compact('apms', "id", "userApms"))->render();
        return response()->json(["status" => 200, "html" => $html]);
    }

    // * Store the user APM rates
    public function storeApmRates(Request $request)
    {

        try {
            $payload = $request->only(["apm_id", "apm", "rate", "user_id", "mid_type"]);
            $data = [];
            $length = isset($payload['apm']) ? count($payload['apm']) : 0;
            if ($length > 0) {
                for ($i = 0; $i < $length; $i++) {
                    array_push($data, ["bank_name" => $payload["apm"][$i], "apm_mdr" => $payload["rate"][$i], "apm_type" => $payload["mid_type"][$i], "apm_id" => $payload["apm_id"][$i]]);
                }
                $encodeData = json_encode($data);
                $user = User::select("id", "email")->where('id', $payload["user_id"])->first();
                User::where("id", $payload["user_id"])->update(["apm" => $encodeData]);
                // Mail::to($user->email)->queue(new ApmRatesEmail($data));
                return response()->json(["status" => 200, "message" => "APM added successfully!"]);

            } else {
                User::where("id", $payload["user_id"])->update(["apm" => null]);
                return response()->json(["status" => 200, "message" => "APM removed successfully!"]);

            }
        } catch (\Exception $err) {
            return response()->json(["status" => 500, "message" => "Something went wrong. Please try again!"]);

        }
    }
}