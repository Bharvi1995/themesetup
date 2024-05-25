<?php

namespace App\Http\Controllers\Admin;

use Str;
use Hash;
use Validator;
use App\WLAgent;
use App\AdminAction;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewWLAgentEmail;
use App\Exports\WLAgentExport;
use Maatwebsite\Excel\Facades\Excel;

class WLAgentController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->wlagent = new WLAgent;

        $this->moduleTitleS = 'White Label Agent';
        $this->moduleTitleP = 'admin.wlAgents';

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
        $data = $this->wlagent->getData($input, $noList);

        return view($this->moduleTitleP . '.index', compact('data'))
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
                'email' => 'required|string|email|max:255|unique:wl_agents,email,NULL,id,deleted_at,NULL',
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'discount_rate' => 'required',
                'discount_rate_master_card' => 'required',
                'setup_fee' => 'required',
                'setup_fee_master_card' => 'required',
                'rolling_reserve_paercentage' => 'required',
                'transaction_fee' => 'required',
                'refund_fee' => 'required',
                'chargeback_fee' => 'required',
                'flagged_fee' => 'required',
                'retrieval_fee' => 'required'
            ],
            [
                'name.regex' => 'Please Enter Only Alphanumeric Characters.',
                'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)',
                'flagged_fee.required' => 'The suspicious transaction fee field is required.'
            ]
        );

        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));
        $input['password'] = bcrypt($input['password']);

        $wlagent = $this->wlagent->storeData($input);
        $ArrRequest = ['name' => $request->name, 'email' => $request->email, 'password' => $request->password];
        addAdminLog(AdminAction::CREATE_REFERRAL_PARTNER, $wlagent->id, $ArrRequest, "White Label RP Created Successfully!");

        $referral_code = $wlagent->id . strtoupper(Str::random(10));

        \DB::table('wl_agents')
            ->where('id', $wlagent->id)
            ->update(['referral_code' => $referral_code]);

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        Notification::route('mail', $data['email'])->notify(new NewWLAgentEmail($data));

        notificationMsg('success', 'White Label RP Created Successfully!');
        return redirect()->route('wl-agents.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function show(WLAgent $wlagent)
    {
        return view($this->moduleTitleP . '.show', compact('wlagent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = WLAgent::find($id);
        return view($this->moduleTitleP . '.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
            'email' => 'required|email|unique:wl_agents,email,' . $id,
            'discount_rate' => 'required',
            'discount_rate_master_card' => 'required',
            'setup_fee' => 'required',
            'setup_fee_master_card' => 'required',
            'rolling_reserve_paercentage' => 'required',
            'transaction_fee' => 'required',
            'refund_fee' => 'required',
            'chargeback_fee' => 'required',
            'flagged_fee' => 'required',
            'retrieval_fee' => 'required'
        ],
        [
            'name.regex' => 'Please Enter Only Alphanumeric Characters.'
        ]);

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if ($input['password'] != '') {
            $this->validate($request, [
                'password_confirmation' => "same:password",
                'password' => 'required|min:9|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ], ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']);

            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        unset($input['password_confirmation']);
        $this->wlagent->updateData($id, $input);
        $ArrRequest = ['name' => $request->name, 'email' => $request->email];
        addAdminLog(AdminAction::UPDATE_REFERRAL_PARTNER, $id, $ArrRequest, "White Label RP Update Successfully!");
        notificationMsg('success', 'White Label RP Update Successfully!');
        return redirect()->route('wl-agents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->wlagent->destroyData($id);
        notificationMsg('success', 'White Label RP Delete Successfully!');
        $ArrRequest = [];
        addAdminLog(AdminAction::DELETE_REFERRAL_PARTNER, $id, $ArrRequest, "White Label RP Delete Successfully!");
        return redirect()->route('wl-agents.index');
    }

    public function changeStatus(Request $request, $id)
    {
        $status = $request->get('status');
        $this->wlagent->updateData($id, ['is_active' => $status]);
        $ArrRequest = [];
        addAdminLog(AdminAction::CHANGE_REFERRAL_PARTNER_STATUS, $id, $ArrRequest, "Status Change Successfully!");
        notificationMsg('success', 'Status Change Successfully!');
        return redirect()->route('wl-agents.index');
    }

    public function export(Request $request)
    {
        return Excel::download(new WLAgentExport, 'White_Label_RP_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function deleteMultiWlAgent(Request $request)
    {
        if ($request->get('type') == 'forall') {
            $allID = $request->get('id');
            foreach ($allID as $key => $value) {
                $this->wlagent->destroyData($value);
                $ArrRequest = [];
                addAdminLog(AdminAction::CHANGE_REFERRAL_PARTNER_STATUS, $value, $ArrRequest, "White Label RP Delete Successfully!");
            }
            return response()->json([
                'success' => true,
            ]);
        } 
    }
}
