<?php

namespace App\Http\Controllers\Agent;

use Auth;
use Session;
use Validator;
use App\Agent;
use App\RpAgreementDocumentUpload;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Str;
use Storage;
use DB;

class SubRpController extends AgentUserBaseController
{
	public function __construct()
    {
        parent::__construct();
        $this->agent = new Agent;
        $this->middleware(function ($request, $next) {
            if(RpApplicationStatus(auth()->guard('agentUser')->user()->id) != 1){
                return redirect()->route('rp.my-application.create');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }

        $data = $this->agent->getAllSubAgent($input, $noList);

        return view('agent.subAgent.index', compact('data'));
    }

    public function create()
    {
        return view('agent.subAgent.create');
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
                'email' => 'required|string|email|max:255|unique:agents,email,NULL,id,deleted_at,NULL',
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)'
            ]
        );

        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));
        $input['password'] = bcrypt($input['password']);
        $input['main_agent_id'] = auth()->guard('agentUser')->user()->id;

        $agent = $this->agent->storeData($input);

        $referral_code = $agent->id . strtoupper(Str::random(10));

        \DB::table('agents')
            ->where('id', $agent->id)
            ->update(['referral_code' => $referral_code]);

        // $data = [
        //     'email' => $request->email,
        //     'password' => $request->password
        // ];
        // Notification::route('mail', $data['email'])->notify(new NewAgentEmail($data));

        notificationMsg('success', 'Sub User Created Successfully!');

        return redirect()->route('sub-rp.index');
    }

    public function edit($id)
    {
        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }
    	$data = Agent::where('id', $id)->where('main_agent_id', $agentId)->first();
        if(!empty($data)){
            return view('agent.subAgent.edit', compact('data'));
        } else {
            notificationMsg('error', 'You have not Access!');
            return redirect()->route('sub-rp.index');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
            'email' => 'required|email|unique:agents,email,' . $id,
        ],
        [
            'name.regex' => 'Please Enter Only Alphanumeric Characters.',
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        $this->agent->updateData($id, $input);

        notificationMsg('success', 'Sub User Update Successfully!');

        return redirect()->route('sub-rp.index');
    }

    public function destroy(Request $request, $id)
    {
        if(auth()->guard('agentUser')->user()->main_agent_id == 0){
            $agentId = auth()->guard('agentUser')->user()->id;
        }else{
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }
        $data = Agent::where('id', $id)->where('main_agent_id', $agentId)->first();
        if(!empty($data)){
            DB::beginTransaction();
            try {
                Agent::where('id', $id)->delete();
                \DB::commit();
                notificationMsg('success','Sub User deleted successfully!');
                return redirect()->back();
            } catch (\Exception $e) {
                \DB::rollback();
                notificationMsg('error','Something went wrong, please try again.');
                return redirect()->back();
            }
        } else {
            notificationMsg('error', 'You have not Access!');
            return redirect()->route('sub-rp.index');
        }
    }
}
