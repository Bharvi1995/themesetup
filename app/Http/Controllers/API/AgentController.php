<?php

namespace App\Http\Controllers\Api;

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
use App\Mail\agentRegisterMail;
use Illuminate\Support\Facades\Mail;

class AgentController extends Controller
{
    public function __construct()
    {
        // parent::__construct();
        $this->agent = new Agent;
        $this->template = new MailTemplates;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view($this->moduleTitleP . '.register');
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
                'name' => 'required',
                'email' => 'required|string|email|max:255|unique:agents,email,NULL,id,deleted_at,NULL',
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));
        $input['password'] = bcrypt($input['password']);

        $input['token'] = Str::random(40) . time();
        $input['is_active'] = '0';
        $agent = $this->agent->storeData($input);
        $ArrRequest = ['name' => $request->name, 'email' => $request->email, 'password' => $request->password];
        addAdminLog(AdminAction::CREATE_REFERRAL_PARTNER, $agent->id, $ArrRequest, "Agent Created Successfully!");

        $referral_code = $agent->id . strtoupper(Str::random(10));

        \DB::table('agents')
            ->where('id', $agent->id)
            ->update(['referral_code' => $referral_code]);

        $data['token'] = $input['token'];
        $data['name'] = $input['name'];
        Mail::to($input['email'])->send(new agentRegisterMail($data));

        /* $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        Notification::route('mail', $data['email'])->notify(new NewAgentEmail($data)); */
        
        return response()->json([
            'status' => 'success',
            'message' => 'Your account has been registered successfully. You will receive an email shortly to activate your account.',
        ]);
    }
}
