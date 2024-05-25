<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Auth;
use Session;
use Redirect;
use App\User;
use App\Agent;
use App\Bank;
use App\Ticket;
use App\Warning;
use App\SendMail;
use App\SubUsers;
use App\Notification;
use App\Transaction;
use App\Application;
use App\BankApplication;
use App\WLAgent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\adminEmailChange;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{

    protected $user, $ticket, $Warning, $SendMail, $Transaction, $Notification, $application, $agent;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        view()->share('adminTheme', 'layouts.appAdmin');
        view()->share('adminLogin', 'layouts.adminLogin');
        view()->share('projectTitle', 'PaymentDemo');

        $this->user = new User;
        $this->ticket = new Ticket;
        $this->Warning = new Warning;
        $this->SendMail = new SendMail;
        $this->Transaction = new Transaction;
        $this->Notification = new Notification;
        $this->application = new Application;
        $this->agent = new Agent;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $date = \Carbon\Carbon::today()->format("Y-m-d");
        $lastdate = \Carbon\Carbon::now()->subDays(15)->format("Y-m-d");
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        // $inputs['for'] = "Daily";
        // $TransactionSummary = $this->Transaction->getTransactionSummaryRP($inputs);
        $data['transaction'] = $this->Transaction->getMerchantTxnCountPercentage($payment_gateway_id);
        $data['companyList'] = $this->application->getCompanyName();
        $data["agents"] = $this->agent->getAgents();
        $data['Transaction'] = Transaction::select("transactions.*", "users.name as userName", "middetails.bank_name")
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->whereNotIn('transactions.payment_gateway_id', ['1', '2'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();
        $data['TransactionRefund'] = [];

        $data['TransactionChargebacks'] = [];
        $data['TransactionFlagged'] = [];


        $data['Ticket'] = Ticket::select("tickets.*")
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $data['RateDecline'] = Application::select('applications.*', 'users.email as email')
            ->join('users', 'users.id', 'applications.user_id')
            ->where('applications.status', '9')
            ->orderBy('applications.id', 'desc')
            ->take(5)
            ->get();

        $data['RateAccepted'] = Application::select('applications.*', 'users.email as email')
            ->join('users', 'users.id', 'applications.user_id')
            ->where('users.is_rate_sent', '2')
            ->where('applications.status', '10')
            ->orderBy('applications.id', 'desc')
            ->take(5)
            ->get();

        $data['SignedAgreement'] = Application::select('applications.*', 'users.email as email')
            ->join('users', 'users.id', 'applications.user_id')
            ->where('applications.status', '11')
            ->orderBy('applications.id', 'desc')
            ->take(5)
            ->get();

        $data['value'] = '7';
        $data['valuetSummary'] = '0';
        $data['merchants'] = $this->user->merchantCountData();

        $data['dashboardSuccessRecord'] = DB::table("transactions as t")->select("applications.business_name", "middetails.bank_name")
            ->selectRaw("sum(if(t.status = '1', amount_in_usd, 0.00)) as successfullV")
            ->join('middetails', 'middetails.id', 't.payment_gateway_id')
            ->join('users', 'users.id', 't.user_id')
            ->join('applications', 'applications.user_id', 't.user_id')
            ->whereNotIn('t.payment_gateway_id', ['1', '2'])
            ->where(DB::raw('DATE(t.created_at)'), '<=', $date)
            ->where(DB::raw('DATE(t.created_at)'), '>=', $lastdate)
            ->groupBy("t.user_id")
            ->orderBy('successfullV', 'desc')
            ->take(5)
            ->get();
        $data['dashboardMIDRecord'] = DB::table("transactions as t")
            ->select("middetails.bank_name")
            ->selectRaw("sum(if(t.status = '1', amount_in_usd, 0.00)) as successfullV")
            ->join('middetails', 'middetails.id', 't.payment_gateway_id')
            ->whereNotIn('t.payment_gateway_id', ['1', '2'])
            ->where(DB::raw('DATE(t.created_at)'), '<=', $date)
            ->where(DB::raw('DATE(t.created_at)'), '>=', $lastdate)
            ->groupBy("t.payment_gateway_id")->orderBy('successfullV', 'desc')
            ->take(5)
            ->get();
        $data['dashboardChargeback'] = DB::table("transactions as t")
            ->select("applications.business_name")
            ->selectRaw("count(*) as totalCount")
            ->join('users', 'users.id', 't.user_id')
            ->join('applications', 'applications.user_id', 't.user_id')
            ->whereNotIn('t.payment_gateway_id', ['1', '2'])
            ->where(DB::raw('DATE(t.chargebacks_date)'), '<=', $date)
            ->where(DB::raw('DATE(t.chargebacks_date)'), '>=', $lastdate)
            ->where('t.chargebacks', '1')
            ->groupBy("t.user_id")
            ->orderBy("totalCount", "DESC")
            ->take(5)
            ->get();
        $data['dashboardRefund'] = DB::table("transactions as t")
            ->select("applications.business_name")
            ->selectRaw("count(*) as totalCount")
            ->join('users', 'users.id', 't.user_id')
            ->join('applications', 'applications.user_id', 't.user_id')
            ->whereNotIn('t.payment_gateway_id', ['1', '2'])
            ->where(DB::raw('DATE(t.refund_date)'), '<=', $date)
            ->where(DB::raw('DATE(t.refund_date)'), '>=', $lastdate)
            ->where('t.refund', '1')
            ->groupBy("t.user_id")
            ->orderBy("totalCount", "DESC")
            ->take(5)
            ->get();
        $data['dashboardFlagged'] = DB::table("transactions as t")
            ->select("applications.business_name")
            ->selectRaw("count(*) as totalCount")
            ->join('users', 'users.id', 't.user_id')
            ->join('applications', 'applications.user_id', 't.user_id')
            ->whereNotIn('t.payment_gateway_id', ['1', '2'])
            ->where(DB::raw('DATE(t.flagged_date)'), '<=', $date)
            ->where(DB::raw('DATE(t.flagged_date)'), '>=', $lastdate)
            ->where('t.is_flagged', '1')
            ->groupBy("t.user_id")
            ->orderBy("totalCount", "DESC")
            ->take(5)
            ->get();

        return view('admin.dashboard')->with($data);
    }

    // * Perticular merchant trabsactions 
    public function getMerchantTxnPercentage(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];
            $transaction = $this->Transaction->getMerchantTxnCountPercentage($payment_gateway_id, $userId);
            $html = view('partials.adminDashboard.dashboardTxnPercentages', compact('transaction'))->render();
            return response()->json(["status" => 200, 'html' => $html]);
        } catch (\Exception $th) {
            return response()->json(["status" => 500, 'message' => "Something went wrong. please try again later."]);

        }

    }

    public function getRpMerchantOverview(Request $request)
    {
        try {
            $agentId = $request->input('agent_id');
            $merchants = $this->user->merchantCountData($agentId);
            $html = view('partials.adminDashboard.dashboardMerchantStatusOverview', compact('merchants'))->render();
            return response()->json(["status" => 200, "html" => $html]);
        } catch (\Throwable $th) {
            return response()->json(["status" => 500, 'message' => "Something went wrong. please try again later."]);
        }

    }
    public function transactionSummaryFilter(Request $request)
    {
        if ($request->ajax()) {
            $inputs['for'] = "Daily";
            if ($request->value == 7) {
                $inputs['for'] = "Weekly";
            }
            if ($request->value == 30) {
                $inputs['for'] = "Monthly";
            }
            $TransactionSummary = $this->Transaction->getTransactionSummaryRP($inputs);
            $valuetSummary = ($request->value == 7) ? 6 : $request->value;
            $html = view('partials.adminDashboard.dashboardTransactionSummary', compact('TransactionSummary', 'valuetSummary'))->render();
            return response()->json(['status' => 200, 'html' => $html]);
        }
    }
    public function changeStatus(Request $request, $id)
    {
        if (checkAdmin(auth()->guard('admin')->user()->id) == 0) {

            notificationMsg('info', 'Only Super Admin Can Do This!');

            return redirect()->back();
        }
        $status = $request->get('status');

        if ($status == 0) {
            \DB::table('payments')
                ->where('id', $id)
                ->update(['status' => $status]);
        } elseif ($status == 1) {
            \DB::table('payments')
                ->where('id', $id)
                ->update(['status' => $status]);
        } else {
            \DB::table('payments')
                ->where('id', $id)
                ->update(['status' => $status]);
        }

        \Session::put('success', 'Payment Status Updated Successfully!');
        return redirect()->back();
    }
    public function profile()
    {
        $data = \DB::table('admins')->where('id', auth()->guard('admin')->user()->id)->first();
        return view('admin.profile', compact('data'));
    }

    public function updateProfile(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'email' => 'required|email|unique:admins,email,' . $id,
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters.',
            ]
        );
        $input = \Arr::except($request->all(), array('_token', '_method'));
        $admins = \DB::table('admins')->where('id', $id)->first();
        if ($admins->email != $input["email"]) {
            if (empty($admins->token)) {
                $data["id"] = $admins->id;
                $data['token'] = \Str::random(40) . time();
                $input['token'] = $data['token'];
                $input["email_changes"] = $input["email"];
                $data["name"] = $input["name"];
                $data["email"] = $input["email"];
                Mail::to($admins->email)->send(new adminEmailChange($data));
                unset($input["email"]);
                \DB::table('admins')->where('id', $id)->update($input);
                notificationMsg('success', 'You will shortly receive an email to activate your new email.');
            } else {
                notificationMsg('error', 'We already received an email change request.');
            }
        } else {
            \DB::table('admins')->where('id', $id)->update($input);
            Session::put('user_name', $input["name"]);
            notificationMsg('success', 'Profile Update Successfully!');
        }
        return redirect()->back();
    }

    public function resendMail()
    {
        $adminId = auth()->guard('admin')->user()->id;
        $admins = \DB::table('admins')->where('id', $adminId)->first();
        if (!is_null($admins)) {
            $data["id"] = $admins->id;
            $data['token'] = \Str::random(40) . time();
            $data["name"] = $admins->name;
            $data["email"] = $admins->email_changes;
            \DB::table('admins')->where('id', $adminId)->update(['token' => $data['token']]);
            Mail::to($admins->email)->send(new adminEmailChange($data));
            notificationMsg('success', 'You will shortly receive an email to activate your new email.');
        } else {
            notificationMsg('error', 'Something went Wrong.!');
        }
        return redirect()->back();
    }

    public function changePass(Request $request)
    {

        $this->validate(
            $request,
            [
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));
        \DB::table('admins')
            ->where('id', auth()->guard('admin')->user()->id)
            ->update(['password' => bcrypt($input['password'])]);
        $notification = [
            'user_id' => auth()->guard('admin')->user()->id,
            'sendor_id' => auth()->guard('admin')->user()->id,
            'type' => 'admin',
            'title' => 'Password Reset',
            'body' => 'Password Updated successfully',
            'url' => '/dashboard',
            'is_read' => '0'
        ];

        $realNotification = addNotification($notification);
        \Session::put('success', 'Your Password successfully Updated');
        return Redirect::back();
    }

    public function userLoginByAdmin(Request $request)
    {
        $email = decrypt($request->query('email'));
        if (auth()->guard('admin')->user()) {
            $user = User::where('email', $email)->first();
            if (isset($user)) {
                Auth::login($user);
                notificationMsg('success', 'User Login Successful!');
                return redirect()->route('dashboardPage');
            } else {
                notificationMsg('warning', 'This User is not available.');
                return redirect()->back();
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function subUserLoginByAdmin(Request $request)
    {
        if (auth()->guard('admin')->user()) {
            $user = SubUsers::where('email', $request->input('email'))->first();
            if (isset($user)) {
                Auth::guard('subUsers')->login($user);
                notificationMsg('success', 'User Login Successful!');
                return redirect()->route('dashboard');
            } else {
                notificationMsg('warning', 'This User is not available.');
                return redirect()->back();
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function agentLoginByAdmin(Request $request)
    {
        $email = decrypt($request->query('email'));
        if (auth()->guard('admin')->user()) {
            $agent = Agent::where('email', $email)->first();
            if (isset($agent)) {
                Auth::guard('agentUser')->login($agent);
                notificationMsg('success', 'Agent Login Successfully!');
                return redirect()->route('rp.dashboard');
            } else {
                notificationMsg('warning', 'This Agent is not available.');
                return redirect()->back();
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function bankLoginByAdmin(Request $request)
    {
        // echo "adasd";exit;
        if (auth()->guard('admin')->user()) {
            $bank = Bank::where('email', $request->input('email'))->first();
            if (isset($bank)) {
                Auth::guard('bankUser')->login($bank);
                notificationMsg('success', 'Bank Login Successfully!');

                $applicationStart = BankApplication::where('bank_id', $bank->id)->first();

                if (empty($applicationStart)) {
                    return redirect()->route('bank.my-application.create');
                } else {
                    if ($applicationStart->status == 1) {
                        return redirect()->route('bank.dashboard');
                    } else {
                        return redirect()->route('bank.my-application.detail');
                    }
                }

                return redirect()->route('bank.dashboard');
            } else {
                notificationMsg('warning', 'This Bank is not available.');
                return redirect()->back();
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function wlAgentLoginByAdmin(Request $request)
    {
        $email = decrypt($request->query('email'));
        if (auth()->guard('admin')->user()) {
            $WLAgent = WLAgent::where('email', $email)->first();
            if (isset($WLAgent)) {
                Auth::guard('agentUserWL')->login($WLAgent);
                notificationMsg('success', 'Login Successfully!');
                return redirect()->route('wl-merchant-management');
            } else {
                notificationMsg('warning', 'This RP is not available.');
                return redirect()->back();
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function verifyAdminChangeEmail(Request $request)
    {
        $check = DB::table('admins')->where('id', $request->id)->first();
        if (!is_null($check)) {
            if (!empty($check->email_changes)) {
                \DB::table('admins')->where('id', $request->id)->update(['email' => $check->email_changes, 'token' => '', "email_changes" => '']);
                \Session::put('success', 'Your new email has been changed successfully.');
                return redirect()->to('admin/profile');
            } else {
                \Session::put('error', 'Email already changed');
                return redirect()->to('admin/profile');
            }
        } else {
            return redirect()->to('login')->with('error', "Don't find your record.");
        }
    }

    public function saveLocalTimezone(Request $request)
    {
        if (!empty($request->timezone)) {
            \Session::put('localtimezone', $request->timezone);
        }

        return response([
            'status' => true,
            'message' => 'timezone save successfully.'
        ]);
    }
}