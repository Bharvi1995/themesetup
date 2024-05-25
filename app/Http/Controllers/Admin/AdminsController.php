<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\AdminController;
use Hash;
use DB;
use Validator;
use App\ImageUpload;
use App\Admin;
use App\Application;
use App\AgreementDocumentUpload;
use App\RpAgreementDocumentUpload;
use App\Role;
use App\User;
use App\Agent;
use App\AdminAction;
use File;
use Storage;
use Dompdf\Dompdf;
use Dompdf\Options;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AdminUsersImport;

class AdminsController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->admin = new Admin;

        $this->moduleTitleS = 'Admin User';
        $this->moduleTitleP = 'admin.adminuser';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->admin->getData($input, $noList);

        return view($this->moduleTitleP . '.index', compact('data', 'noList'))
            ->with('i', 0);
    }

    public function technical(Request $request)
    {
        return view('admin.technical');
    }

    public function create(Request $request)
    {
        $roles = Role::select('name', 'id')->get();

        return view($this->moduleTitleP . '.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'email' => 'required|string|email|max:255|unique:admins',
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                // 'is_superadmin' => 'required',
                'is_active' => 'required',
                'roles' => 'required',
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)'
            ]
        );

        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));

        $input['password'] = bcrypt($input['password']);

        $roles = $request->input('roles');
        unset($input['roles']);
        DB::beginTransaction();
        try {
            $admin = $this->admin->storeData($input);
            $admin->assignRole($roles);
            $ArrRequest = ['name' => $request->name, 'email' => $request->email, 'password' => $request->password, 'is_active' => $request->is_active, 'roles' => $roles];
            addAdminLog(AdminAction::CREATE_ADMIN, $admin->id, $ArrRequest, "Admin Created Successfully!");
            DB::commit();
            notificationMsg('success', 'Admin Create Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Admin not created!');
        }
        return redirect()->route('admin-user.index');
    }

    public function show($id)
    {
        $data = $this->user->findData($id);

        return view($this->moduleTitleP . '.show', compact('data'));
    }

    public function edit($id)
    {
        $data = $this->admin->findData($id);
        $roles = Role::select('name', 'id')->get();
        $userRole = $data->roles->pluck('id')->toArray();
        return view($this->moduleTitleP . '.edit', compact('data', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'email' => 'required|email|unique:admins,email,' . $id,
                // 'is_superadmin' => 'required',
                'password' => 'nullable|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'confirm_password' => 'same:password',
                'is_active' => 'required',
                'roles' => 'required',
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)'
            ]
        );

        $input = \Arr::except($request->all(), array('_token', '_method'));

        // apply password validation only if entered while update
        if ($input['password'] != '') {
            $this->validate($request, [
                'password' => 'required|min:9|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => "same:password",
            ], ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']);

            $input['password'] = Hash::make($input['password']);
            $password = $input['password'];
        } else {
            $password = "";
            unset($input['password']);
        }

        $roles = $request->input('roles');
        unset($input['roles']);
        unset($input['confirm_password']);
        $admin = $this->admin->findData($id);
        DB::beginTransaction();
        try {
            $this->admin->updateData($id, $input);
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $admin->assignRole($roles);
            $ArrRequest = ['name' => $request->name, 'email' => $request->email, 'password' => $password, 'is_active' => $request->is_active, 'roles' => $roles];
            addAdminLog(AdminAction::UPDATE_ADMIN, $id, $ArrRequest, "Admin Updated Successfully!");
            DB::commit();
            notificationMsg('success', 'Admin Update Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Admin not updated!');
        }
        return redirect()->route('admin-user.index');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->user->destroyData($id);
            $ArrRequest = [];
            addAdminLog(AdminAction::DELETE_ADMIN, $id, $ArrRequest, "Admin Deleted Successfully!");
            DB::commit();
            notificationMsg('success', 'Admin Delete Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            notificationMsg('error', 'Admin not deleted!');
        }
        return redirect()->route('admin-user.index');
    }

    public function deleteUser(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $this->admin->destroyData($value);
            }
            return response()->json([
                'success' => true,
            ]);
        }
        if ($this->admin->destroyData($request->get('id'))) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $status = $request->get('status');
        $update = ['is_active' => $status];
        $this->admin->updateData($id, $update);
        $ArrRequest = ['is_active' => $status];
        addAdminLog(AdminAction::CHANGE_ADMIN_STATUS, $id, $ArrRequest, "Status Change Successfully");
        notificationMsg('success', 'Status Change Successfully!');
        return redirect()->route('admin-user.index');
    }

    public function passwordExpired($id)
    {
        try {
            $password = $this->randomPassword(8);
            $input['password'] = Hash::make($password);
            $input['is_password_expire'] = '1';
            $this->admin->updateData($id, $input);
            $ArrRequest = [];
            addAdminLog(AdminAction::EXPIRED_ADMIN_PASSWORD, $id, $ArrRequest, "Admin User Password Expired Successfully");
            notificationMsg('success', 'Admin User Password Expired Successfully!');
            return redirect()->route('admin-user.index');
        } catch (Exception $e) {
            notificationMsg('error', 'Something went Wrong!');
            return redirect()->route('admin-user.index');
        }
    }

    public function randomPassword($len)
    {
        //enforce min length 8
        if ($len < 8)
            $len = 8;
        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets = array();
        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = '23456789';
        $sets[] = '~!@#$%^&*(){}[],./?';
        $password = '';
        //append a character from each set - gets first 4 characters
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }
        //use all characters to fill up to $len
        while (strlen($password) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];
            //add a random char from the random set
            $password .= $randomSet[array_rand(str_split($randomSet))];
        }
        //shuffle the password string before returning!
        return str_shuffle($password);
    }

    public function agreementGenerate(Request $request)
    {
        $companyName = Application::select('user_id', 'business_name')
            ->orderBy('id', 'desc')->get();

        return view('admin.agreementGenerate', compact('companyName'));
    }

    public function agreementGenerateStore(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token'));

        $user_id = $request->get('user_id');
        $application = Application::where('user_id', $user_id)->first();

        $user = User::where('id', $user_id)->first();
        view()->share('data', $user);
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.applications.agreement_PDF'));

        // $dompdf->setPaper('A4', 'landscape');
        // $dompdf->setPaper([0, 0, 1000.98, 900.85], 'landscape');
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

        $dompdf->render();

        $filePath = 'uploads/agreement_' . $user_id . '/agreement.pdf';

        Storage::disk('s3')->put($filePath, $dompdf->output());

        AgreementDocumentUpload::where('user_id', $user_id)->where('application_id', $application->id)->update(['sent_files' => $filePath]);

        notificationMsg('success', 'Generated Successfully!');

        return redirect()->back();
    }

    public function agreementUpload(Request $request)
    {
        $companyName = Application::select('applications.user_id', 'applications.business_name')
            ->join('users', 'users.id', 'applications.user_id')
            ->where('users.is_whitelable', '0')
            ->where('users.is_white_label', '0')
            ->orderBy('users.id', 'desc')->get();

        $rpName = Agent::select('id', 'name')
            ->where('main_agent_id', '0')
            ->orderBy('id', 'desc')->get();

        return view('admin.agreementUpload', compact('companyName', 'rpName'));
    }

    public function agreementUploadStore(Request $request)
    {
        $this->validate(
            $request,
            [
                'business_name' => 'required',
                'files' => 'required|mimes:jpg,jpeg,png,pdf,zip|max:35840'
            ]
        );
        $input = \Arr::except($request->all(), array('_token', '_method'));

        try {
            if ($request->hasFile('files')) {
                $exists = AgreementDocumentUpload::where('user_id', $input['business_name'])->first();

                if (!empty($exists->sent_files)) {
                    if ($request->hasFile('files')) {
                        $files = $request->file('files');
                        $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                        $imageName = $imageName . '.' . $files->getClientOriginalExtension();
                        $filePath = 'uploads/agreementDocumentsUpload/' . $imageName;
                        Storage::disk('s3')->put($filePath, file_get_contents($files->getRealPath()));
                        $input['files'] = $filePath;
                    }

                    AgreementDocumentUpload::where('user_id', $input['business_name'])
                        ->update(['files' => $input['files']]);

                    \Session::put('success', 'Agreement uploaded successfully.');
                } else {
                    \Session::put('error', 'Agreement Not Sent.');
                }
            } else {
                \Session::put('error', 'Please select the document');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            \Log::info(["err" => $e->getMessage()]);
            \Session::put('error', 'Something went wrong.');
            return redirect()->back();
        }
    }

    public function agreementUploadStoreRP(Request $request)
    {
        $this->validate(
            $request,
            [
                'referral_partner' => 'required',
                'file' => 'required|mimes:jpg,jpeg,png,pdf,zip|max:35840'
            ]
        );
        $input = \Arr::except($request->all(), array('_token', '_method'));

        try {
            if ($request->hasFile('file')) {
                $exists = RpAgreementDocumentUpload::where('rp_id', $input['referral_partner'])->first();

                if (!empty($exists->sent_files)) {
                    if ($request->hasFile('file')) {
                        $files = $request->file('file');
                        $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                        $imageName = $imageName . '.' . $files->getClientOriginalExtension();
                        $filePath = 'uploads/agreementDocumentsUpload/' . $imageName;
                        Storage::disk('s3')->put($filePath, file_get_contents($files->getRealPath()));
                        $input['files'] = $filePath;
                    }

                    RpAgreementDocumentUpload::where('rp_id', $input['referral_partner'])
                        ->update(['files' => $input['files']]);

                    \Session::put('success', 'Agreement uploaded successfully.');
                } else {
                    \Session::put('error', 'Agreement Not Sent.');
                }
            } else {
                \Session::put('error', 'Please select the document');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            \Session::put('error', 'Something went wrong.');
            return redirect()->back();
        }
    }

    public function bulkUpload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'bulk_users' => 'required|file|mimes:csv,txt'
        ]);
        if ($validator->fails()) {

            $error_per_field = "";
            $all_errors = json_decode(json_encode($validator->errors()), true);
            foreach ($all_errors as $error_field => $field_errors) {
                $error_per_field = implode(' ', $field_errors);
            }
            \Session::put('error', $error_per_field);
        } else {
            $auth_user = auth()->guard('admin')->user();
            try {
                $file_path1 = $request->file('bulk_users')->store('temp');
                $file_path = storage_path('app') . '/' . $file_path1;
                $AdminUsersImport = new AdminUsersImport;
                Excel::import($AdminUsersImport, $file_path);
                if ($AdminUsersImport->getRowCount()) {
                    $msg = 'CSV file uploaded successfully. Total ' . $AdminUsersImport->getRowCount() . " Records Inserted";
                    \Session::put('success', $msg);
                }
            } catch (Exception $th) {
                \Session::put('error', 'Invalid data!');
            }
        }
        return redirect()->route('admin-user.index');
    }
}