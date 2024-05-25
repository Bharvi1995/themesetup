<?php

namespace App\Http\Controllers\WLAgent;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\WLRPUserExport;
use App\Mail\APIKeyIPMail;
use App\WLAgent;
use App\Application;
use App\Categories;
use App\Transaction;
use App\WebsiteUrl;
use App\Admin;
use App\User;
use View;
use Redirect;
use Hash;
use Auth;

class WLAgentUserBaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $agentUserWL;
    public function __construct()
    {
        view()->share('WLAgentUserTheme', 'layouts.WLAgent.default');

        $this->middleware(function ($request, $next) {
            $userData = WLAgent::where('wl_agents.id', auth()->guard('agentUserWL')->user()->id)
                ->first();

            view()->share('userData', $userData);
            return $next($request);
        });

        $this->wlAgentUser = new WLAgent;
        $this->User = new User;
        $this->Transaction = new Transaction;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [];

        $agentId = auth()->guard('agentUserWL')->user()->id;

        $userIds = \DB::table('users')->where('white_label_agent_id', $agentId)->pluck('id');

        $transaction = DB::table("transactions as t")
            ->selectRaw(
                "
                        sum(if(t.status = '1', amount, 0.00)) as successfullV,
                        sum(if(t.status = '1', 1, 0)) as successfullC,
                        round((100*sum(if(t.status = '1', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) , 2) as successfullP,

                        sum(if(t.status = '0' , amount,0.00 )) as declinedV,
                        sum(if(t.status = '0', 1, 0)) as declinedC,
                        round((100*sum(if(t.status = '0', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) ,2) as declinedP,

                        sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as chargebackV,
                        sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) as chargebackC,
                        round((100*sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as chargebackP,

                        sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0)) as suspiciousV,
                        sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)) as suspiciousC,
                        round((100*sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as suspiciousP,

                        sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as refundV,
                        sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) as refundC,
                        round((100*sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2) as refundP",
            )
            ->whereIn('t.user_id', $userIds)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway_id)
            ->where('t.deleted_at', NULL)
            ->first();

        $latestMerchants = $this->User->getWLAgentUsers();

        $latest10Transactions = $this->Transaction->latest10TransactionsForWLAgent();

        $agentId = auth()->guard('agentUserWL')->user()->id;

        $userIds = \DB::table('users')->where('white_label_agent_id', $agentId)->pluck('id');
        $date = \Carbon\Carbon::today()->subDays(6)->format("Y-m-d");
        $TransactionSummary = DB::table("transactions as t")
            ->select("currency", DB::raw("sum(if(t.status = '1', amount, 0.00)) as successAmount"), DB::raw("sum(if(t.status = '1', 1, 0)) as successCount"), DB::raw("sum(if(t.status = '0' , amount,0.00 )) as declinedAmount"), DB::raw("sum(if(t.status = '0', 1, 0)) as declinedCount"), DB::raw("sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as chargebackAmount"), DB::raw("sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) as chargebackCount"), DB::raw("sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as refundAmount"), DB::raw("sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) as refundCount"), DB::raw("sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0)) as flagAmount"), DB::raw("sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)) as flagCount"))
            ->where('t.transaction_date', '>=', $date)
            ->whereIn('t.user_id', $userIds)
            ->whereNotIn('t.payment_gateway_id', $payment_gateway_id)
            ->where('t.deleted_at', NULL)
            ->groupBy("currency")
            ->get();

        return view('WLAgent.dashboard',compact('transaction','latestMerchants','latest10Transactions','TransactionSummary'));
    }

    public function merchantManagement(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $dataT = $this->User->getMainWLUserData($input, $noList);

        $companyName = Application::select('applications.user_id', 'applications.business_name')
                            ->join('users','users.id','applications.user_id')
                            ->orderBy('users.id', 'desc')
                            ->where('users.is_white_label','1')
                            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
                            ->get();

        $payment_gateway_id = \DB::table('middetails')->get();

        $categories = Categories::orderBy('name')->get();

        return view('WLAgent.merchantManagement.index',compact('dataT','noList','companyName','payment_gateway_id','categories'));
    }

    public function export(Request $request)
    {
        return Excel::download(new WLRPUserExport, 'User_List_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function toDelimitedString($array)
    {
        $data = '';
        foreach ($array as $value) {
            $data .= '"' . implode(",", $value) . ' \n" + ';
        }
        $data = rtrim($data, ' ');
        return rtrim($data, '+');
    }

    public function profile()
    {
        $data = WLAgent::where('wl_agents.id', auth()->guard('agentUserWL')->user()->id)
            ->first();

        return view('WLAgent.profile.index', compact('data'));
    }

    public  function updateProfile(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:wl_agents,email,' . auth()->guard('agentUserWL')->user()->id,
            'password' => 'confirmed',
        ]);

        $input = \Arr::except($input, array('_token', 'password_confirmation'));
        if ($input['password'] != null) {
            $input['token'] = $input['password'];
            $input['password'] = bcrypt($input['password']);
        } else {
            $input = \Arr::except($input, array('password'));
        }

        $this->wlAgentUser->updateData(auth()->guard('agentUserWL')->user()->id, $input);

        notificationMsg('success', 'Profile Updated Successfully!');

        return redirect()->route('wl-profile-rp');
    }

    public function whiteListIp() {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1') {
            $userID = \Auth::user()->main_user_id;
        } else {
            $userID = \Auth::user()->id;
        }
        $data = $this->User->where('users.main_user_id', '0')->where('users.is_white_label', '1')
                        ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
                        ->pluck('id', 'id')->all();
        $apiWebsiteUrlIP = WebsiteUrl::select("website_url.*", 'users.email', 'applications.business_name')
                                    ->join('users', 'users.id', 'website_url.user_id')
                                    ->join('applications', 'applications.user_id', 'website_url.user_id')
                                    ->whereIn('website_url.user_id',$data)->get();
        return view('WLAgent.whitelistIp.list', compact('data','apiWebsiteUrlIP'));
    }

    public function addWhiteListIp() {
        $users = User::where('users.main_user_id', '0')
                        ->where('users.is_white_label', '1')
                        ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
                        ->get();
        return view('WLAgent.whitelistIp.add', compact('users'));
    }

    public function addWhiteListSubmit(Request $request) {

        $input = \Arr::except($request->all(), array('_token', '_method'));
        $validation = [];
        foreach ($request->generate_apy_key as $key => $value) {
            $validation['generate_apy_key.' . $key . '.user_id'] = 'required';
            $validation['generate_apy_key.' . $key . '.website_name'] = 'required';
            $validation['generate_apy_key.' . $key . '.ip_address'] = 'required|ip';
        }
        $this->validate($request, $validation);
        if (Auth::user()->api_key == null) {
            $api_key = Auth::user()->id . \Str::random(64);
            $input2 = ['api_key' => $api_key];
            $this->User->updateData(Auth::user()->id, $input2);
        } else {
            $api_key = User::where('id', Auth::user()->id)->value('api_key');
        }
        try {
            foreach ($request->generate_apy_key as $key => $value) {
                $data = [
                    'user_id' => $value['user_id'],
                    'website_name' => $value['website_name'],
                    'ip_address' => $value['ip_address']
                ];
                WebsiteUrl::create($data);
                $company = Application::where('user_id', $value['user_id'])->first();
                $Dataemail[] = ['ip_address' => $value['ip_address']];
                $content = [
                    'company' => $company->business_name,
                    'websites' => $Dataemail,
                    'api_key' => $api_key,
                ];
                $adminEmail = Admin::find('1')->first();
                \Mail::to($adminEmail->email)->send(new APIKeyIPMail($content));
                addToLog('IP whitelist created successfully', $request->generate_apy_key, 'general');
            }
            \Session::put('success', 'IP address added successfully!');
            \Session::forget('api_key');
            return redirect()->route('wl-rp-whitelist-ip');
        } catch (\Exception $e) {
            \Session::put('error', 'API Key not Saved!');
            \Session::forget('api_key');
            return redirect()->route('wl-rp-whitelist-ip');
        }
    }

    public function deleteWebsiteUrl(Request $request, $id) {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1') {
            $userID = \Auth::user()->main_user_id;
        } else {
            $userID = \Auth::user()->id;
        }
        if($userID) {
            $data = $this->User->where('users.main_user_id', '0')->where('users.is_white_label', '1')
                            ->where('users.white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
                            ->pluck('id', 'id')->all();
            $websites = WebsiteUrl::findOrFail($id);
            if(in_array($websites->user_id, $data)){
                WebsiteUrl::where('id', $id)->delete();
                addToLog('IP whitelist delete successfully', [$id], 'general');
                \Session::put('success', 'Your IP whitelist Delete Successfully !!');
            }else{
                \Session::put('error', 'Something went wrong !!');
            }
        } else {
            \Session::put('error', 'Something went wrong !!');
        }
        return Redirect::back();
    }

    public function rateFee(Request $request) {
        return view('WLAgent.merchantManagement.rateFee');
    }

    // ================================================
    /*  method : showUserDetails
    * @ param  :
    * @ Description : get wl user details and show on model
    */ // ==============================================
    public function showUserDetails(Request $request)
    {
        $data = $this->User->where('id', $request->input('id'))->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)->first();
        if ($data != null) {
            $html = view('WLAgent.merchantManagement.userdetails', compact('data'))->render();
            return response()->json([
                'html' => $html,
            ]);
        } else {
            return response()->json([
                'html' => 'No user details found, please try again.',
            ]);
        }
    }
}
