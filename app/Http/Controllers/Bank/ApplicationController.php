<?php

namespace App\Http\Controllers\Bank;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Application;
use App\BankApplication;
use App\Admin;
use App\Categories;
use App\TechnologyPartner;
use App\ApplicationAssignToBank;
use App\ApplicationNoteBank;
use App\Bank;
use App\Agent;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Mail\ApplicationApprovedToBankMail;
use App\Mail\ApplicationDeclinedToBankMail;
use App\Mail\ApplicationReferredToBankMail;
use App\Mail\ApplicationNoteBankToAdminMail;
use View;
use Redirect;
use Storage;
use Hash;
use Auth;
use Mail;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use App\Exports\AllApplicationsForBankExport;
use App\Exports\ApprovedApplicationsForBankExport;
use App\Exports\DeclinedApplicationsForBankExport;
use App\Exports\PendingApplicationsForBankExport;
use App\Exports\ReferredApplicationsForBankExport;


class ApplicationController extends BankUserBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->moduleTitleP = 'bank.applications';

        $this->user = new User;
        $this->bankUser = new Bank;
        $this->Application = new Application;
        $this->ApplicationAssignToBank = new ApplicationAssignToBank;
    }

    public function list(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $categories = Categories::all();

        $data = $this->Application->getBankApplications($input,$noList);

        return view($this->moduleTitleP.'.index', compact('data','categories'));
    }

    public function listApproved(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $categories = Categories::all();

        $data = $this->Application->getBankApplicationsApproved($input,$noList);

        return view($this->moduleTitleP.'.approved', compact('data','categories'));
    }

    public function listDeclined(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $categories = Categories::all();

        $data = $this->Application->getBankApplicationsDeclined($input,$noList);

        return view($this->moduleTitleP.'.declined', compact('data','categories'));
    }

    public function listReferred(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $categories = Categories::all();

        $data = $this->Application->getBankApplicationsReferred($input,$noList);

        return view($this->moduleTitleP.'.referred', compact('data','categories'));
    }

    public function listPending(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }

        $categories = Categories::all();

        $data = $this->Application->getBankApplicationsPending($input,$noList);

        return view($this->moduleTitleP.'.pending', compact('data','categories'));
    }

    public function applicationReview(Request $request)
    {
        $data['data'] = Application::select('applications.*', 'users.name', 'users.email', 'users.agent_commission')
            ->join('users', 'users.id', 'applications.user_id')
            ->where('applications.id', $request->id)
            ->first();

        $data['bank'] = ApplicationAssignToBank::select('banks.*','application_assign_to_bank.*')
                    ->join('banks','banks.id','application_assign_to_bank.bank_user_id')
                    ->where('application_assign_to_bank.application_id',$request->id)
                    ->where('application_assign_to_bank.bank_user_id', auth()->guard('bankUser')->user()->id)
                    ->first();

        return view('bank.applications.show', $data);
    }

    public function downloadDocumentsUploade(Request $request)
    {
        return Storage::disk('s3')->download($request->file);
    }

    public function downloadPDF(Request $request, $id)
    {
        $data = $this->Application->findData($id);
        view()->share('data', $data);

        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml(view('bank.applications.application_PDF'));

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        // $dompdf->setPaper([0, 0, 1000.98, 900.85], 'landscape');
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream($data->business_name.'.pdf');
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

    public function applicationDeclined(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declined_reason' => 'required',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method', 'id', 'bank_users_id','declined_reason'));
        if ($validator->passes()) {
            if($this->ApplicationAssignToBank->applicationDeclined($request->get('applications_id'), $request->get('bank_users_id'), $request->get('declined_reason')))
            {
                \DB::beginTransaction();
                try {
                    $bank = Bank::where('id',$request->get('bank_users_id'))->first();
                    $application = Application::where('id',$request->get('applications_id'))->first();
                    Mail::to(config('notification.default_email'))->send(new ApplicationDeclinedToBankMail($bank, $application, $request->declined_reason));
                } catch(\Exception $e) {
                    \DB::rollBack();
                }
                \DB::commit();

                return response()->json(['success' => '1', 'datas' => $request->all()]);
            } else {
                return response()->json(['success' => '0', 'datas' => $request->all()]);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function applicationReferred(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referred_note' => 'required',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method', 'id', 'bank_users_id','referred_note'));
        if ($validator->passes()) {
            if($this->ApplicationAssignToBank->applicationReferred($request->get('applications_id'), $request->get('bank_users_id'), $request->get('referred_note')))
            {
                \DB::beginTransaction();
                try {
                    $bank = Bank::where('id',$request->get('bank_users_id'))->first();
                    $application = Application::where('id',$request->get('applications_id'))->first();
                    $application_assign_to_bank = $this->ApplicationAssignToBank->findData($request->get('applications_id'), $request->get('bank_users_id'));
                    Mail::to(config('notification.default_email'))->send(new ApplicationReferredToBankMail($bank, $application,$application_assign_to_bank));
                } catch(\Exception $e) {
                    \DB::rollBack();
                }
                \DB::commit();

                return response()->json(['success' => '1', 'datas' => $request->all()]);
            } else {
                return response()->json(['success' => '0', 'datas' => $request->all()]);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function applicationApproved(Request $request)
    {
        if($this->ApplicationAssignToBank->applicationApproved($request->get('applications_id'), $request->get('bank_users_id')))
        {
            \DB::beginTransaction();
            try {
                $bank = Bank::where('id',$request->get('bank_users_id'))->first();
                $application = Application::where('id',$request->get('applications_id'))->first();
                Mail::to(config('notification.default_email'))->send(new ApplicationApprovedToBankMail($bank, $application));
            } catch(\Exception $e) {
                \DB::rollBack();
            }
            \DB::commit();

            return response()->json(['success' => '1', 'data' => $request->all()]);
        } else {
            return response()->json(['success' => '0', 'data' => $request->all()]);
        }
    }

    public function exportAllApplications(Request $request)
    {
        return (new AllApplicationsForBankExport($request->ids))->download();
    }

    public function exportApprovedApplications(Request $request)
    {
        return (new ApprovedApplicationsForBankExport($request->ids))->download();
    }

    public function exportDeclinedApplications(Request $request)
    {
        return (new DeclinedApplicationsForBankExport($request->ids))->download();
    }

    public function exportReferredApplications(Request $request)
    {
        return (new ReferredApplicationsForBankExport($request->ids))->download();
        return Excel::download(new ReferredApplicationsForBankExport($request->ids), '.xlsx');
    }

    public function exportPendingApplications(Request $request)
    {
        return (new PendingApplicationsForBankExport($request->ids))->download();
    }

    public function getApplicationNoteBankToAdmin(Request $request)
    {
        $data = ApplicationNoteBank::where('application_id',$request->id)->where('bank_id',auth()->guard('bankUser')->user()->id)->latest()->get();

        $html = view('partials.application.noteBank',compact('data'))->render();

        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function storeApplicationNoteBankToAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($validator->passes()) {

            $input['application_id'] = $request->get('id');
            $input['user_id'] = auth()->guard('bankUser')->user()->id;
            $input['bank_id'] = auth()->guard('bankUser')->user()->id;
            $input['user_type'] = 'BANK';
            $input['note'] = $request->get('note');

            if (ApplicationNoteBank::create($input)) {

                try {
                    $application = Application::where('id',$input['application_id'])->first();

                    $bankApplication = BankApplication::where('bank_id',auth()->guard('bankUser')->user()->id)->first();

                    $data['note'] = $request->get('note');
                    $data['business_name'] = $application->business_name;
                    $data['bank_company_name'] = $bankApplication->company_name;

                    Mail::to(config('notification.default_email'))->send(new ApplicationNoteBankToAdminMail($data));
                } catch (\Exception $e) {

                }

                return response()->json(['success' => '1', 'id' => $request->get('id')]);
            } else {
                return response()->json(['success' => '0', 'id' => $request->get('id')]);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }
}
