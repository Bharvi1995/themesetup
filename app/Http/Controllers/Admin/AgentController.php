<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Collection;
use Str;
use Validator;
use App\Agent;
use App\AdminAction;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAgentEmail;
use App\AgentBankDetails;
use App\Exports\AgentExport;
use Maatwebsite\Excel\Facades\Excel;
use App\MailTemplates;
use App\Jobs\SendBulkEmailToUsers;
use DB;
use Log;

class AgentController extends AdminController
{
    protected $agent, $template, $moduleTitleS, $moduleTitleP;
    public function __construct()
    {
        parent::__construct();
        $this->agent = new Agent;
        $this->template = new MailTemplates;

        $this->moduleTitleS = 'Agent';
        $this->moduleTitleP = 'admin.agents';

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
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 15;
        }
        $data = $this->agent->getData($input, $noList);

        $template = $this->template->getListForMail();

        $rp = $this->agent::select('id', 'name')
            ->where('main_agent_id', '0')
            ->orderBy('id', 'desc')
            ->get();

        return view($this->moduleTitleP . '.index', compact('data', 'template', 'rp'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->moduleTitleP . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

        $agent = $this->agent->storeData($input);
        $ArrRequest = ['name' => $request->name, 'email' => $request->email, 'password' => $request->password];
        addAdminLog(AdminAction::CREATE_REFERRAL_PARTNER, $agent->id, $ArrRequest, "Agent Created Successfully!");

        $referral_code = $agent->id . strtoupper(Str::random(10));

        \DB::table('agents')
            ->where('id', $agent->id)
            ->update(['referral_code' => $referral_code]);

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        Notification::route('mail', $data['email'])->notify(new NewAgentEmail($data));

        notificationMsg('success', 'Agent Created Successfully!');
        return redirect()->route('agents.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function show(Agent $agent)
    {
        return view($this->moduleTitleP . '.show', compact('agent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function edit(Agent $agent)
    {
        $data = $agent;
        return view($this->moduleTitleP . '.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agent $agent)
    {
        $this->validate($request, [
            'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
            'email' => 'required|email|unique:agents,email,' . $agent->id,
            'password' => 'nullable|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'confirm_password' => 'same:password',
        ], [
            'name.regex' => 'Please Enter Only Alphanumeric Characters.',
            'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)'
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));

        if ($input['password'] != '') {
            $input['password'] = \Hash::make($input['password']);
            $password = $input['password'];
        } else {
            $password = "";
            unset($input['password']);
        }

        unset($input['confirm_password']);

        $this->agent->updateData($agent->id, $input);

        $ArrRequest = ['name' => $request->name, 'email' => $request->email];
        addAdminLog(AdminAction::UPDATE_REFERRAL_PARTNER, $agent->id, $ArrRequest, "Agent Update Successfully!");
        notificationMsg('success', 'Agent Update Successfully!');
        return redirect()->route('agents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->agent->destroyData($id);
        notificationMsg('success', 'Agent Delete Successfully!');
        $ArrRequest = [];
        addAdminLog(AdminAction::DELETE_REFERRAL_PARTNER, $id, $ArrRequest, "Agent Delete Successfully!");
        return redirect()->route('agents.index');
    }

    public function changeStatus(Request $request, $id)
    {
        $status = $request->get('status');
        $this->agent->updateData($id, ['is_active' => $status]);
        $ArrRequest = [];
        addAdminLog(AdminAction::CHANGE_REFERRAL_PARTNER_STATUS, $id, $ArrRequest, "Status Change Successfully!");
        notificationMsg('success', 'Status Change Successfully!');
        return redirect()->route('agents.index');
    }

    public function getAgentBankDetails($id)
    {
        $bankDetails = AgentBankDetails::where('agent_id', $id)->first();
        return view('admin.agents.bankDetails')->with('bankDetails', $bankDetails);
    }

    public function export(Request $request)
    {
        return Excel::download(new AgentExport, 'RPList_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function sendMultiMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_template' => 'required',
            'subject' => 'required',
            'bodycontent' => 'required',
        ]);
        try {
            $input = \Arr::except($request->all(), array('_token', '_method'));
            if ($validator->passes()) {
                $ids = explode(',', $input['id']);
                unset($input['id']);
                $agents = DB::table('agents')->select("email")->whereIn("id", $ids)->orderBy("id", "desc");
                $agents->chunk(100, function ($data) use ($input) {
                    $userCollection = new Collection($data);
                    $userChunks = $userCollection->chunk(50);
                    foreach ($userChunks as $userChunk) {
                        SendBulkEmailToUsers::dispatch($userChunk, $input);
                    }
                });
                return response()->json([
                    'success' => true,
                ]);
            }
            return response()->json(['errors' => $validator->errors()]);
        } catch (\Exception $err) {
            Log::error(["users-bulk-email-error" => $err->getMessage()]);
        }


        return response()->json(['errors' => $validator->errors()]);
    }

    public function deleteMultiAgent(Request $request)
    {
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $this->agent->destroyData($value);
            }
            return response()->json([
                'success' => true,
            ]);
        }
    }
}