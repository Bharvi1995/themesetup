<?php

namespace App\Http\Controllers;

use DB;
use URL;
use Log;
use Session;
use App\User;
use App\Agent;
use App\Merchantapplication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\userRegisterMail;
use Illuminate\Validation\Rule;

class ApplyNowController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User;
        $this->Merchantapplication = new Merchantapplication;
    }

    /**
     * show registration form
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('applynow');
    }

    // ================================================
    /* method : store
    * @param  :
    * @Description : store merchant details
    */ // ==============================================
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|max:50',
                'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
                'mobile_no' => 'required|max:14',
                'password' => 'required||min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        $input = \Arr::except($request->all(), array('_token', '_method'));
        if(isset($input['RP']) && $input['RP'] != ''){
            $agentData = Agent::where('referral_code', $input['RP'])->first();
            $input['agent_id'] = $agentData->id;
        }else{
            $input['agent_id'] = NULL;
        }

        $uuid = Str::uuid()->toString();

        unset($input['password_confirmation']);
        $input['uuid'] = $uuid;
        $input['token'] = Str::random(40) . time();
        $input['is_active'] = '0';
        try {
            $userData = $this->user->storeData($input);
            $data = [];
            $data['token'] = $input['token'];
            $data['name'] = $input['name'];
            Mail::to($input['email'])->send(new userRegisterMail($data));
        } catch (\Exception $e) {
            return redirect()->route('login')->with(['error' => 'There seems to be an issue. Please try again later.']);
        }
        return redirect()->route('login')
            ->with(['success' => 'Your account registration was successful. An activation email will be sent to you shortly.']);
    }

    public function verifyUserEmail($token)
    {
        $check = DB::table('users')->where('token', $token)->first();
        if (!is_null($check)) {
            if ($check->is_active == 1) {
                return redirect()->to('login')
                    ->with('success', "The user has already been activated.");
            }

            $user = $this->user::where('token', $token)->first();
            $token_api = \Str::random(30).time();
            $this->user::where('token', $token)->update(['is_active' => '1', 'token' => '', 'email_verified_at' => date('Y-m-d H:i:s'), 'api_key' => $token_api]);
            return redirect()->to('user/confirmation')
                ->with('success', "Your account has been successfully activated. <br> Please log in using your email address and password.");
        }
        return redirect()->to('login')->with('error', "The provided token is invalid..");
    }

    public function verifyUserChangeEmail(Request $request)
    {
        $check = DB::table('users')->where('id', $request->id)->first();
        if (!is_null($check)) {
            if (!empty($check->email_changes)) {
                $this->user::where('id', $request->id)->update(['email' => $check->email_changes, 'token' => '', "email_changes" => ""]);
                \Session::put('success', 'Your new email has been changed successfully.');
                return redirect()->to('setting');
            } else {
                \Session::put('error', 'Email already changed');
                return redirect()->to('setting');
            }
        } else {
            return redirect()->to('login')->with('error', "Don't find your record.");
        }
    }

    public function confirmMailActive()
    {
        return view('auth.confirmMailActive');
    }
}
