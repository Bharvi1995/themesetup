<?php

namespace App\Http\Controllers\Auth;

use DB;
use Str;
use App\User;
use App\Agent;
use App\RpApplication;
use Validator;
use Carbon\Carbon;
use App\Mail\SendForgotEmailAgent;
use App\Mail\AgentOtpMail;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Agent\AgentUserBaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use \Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\AdminAction;
use App\Mail\AgentRegisterMail;
use Illuminate\Support\Facades\Session;

class AgentUserAuthController extends AgentUserBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/rp/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->agent = new Agent;
        $this->middleware('guest', ['except' => 'logout']);
        $this->middleware('throttle:200,1')->only('postLogin');
    }

    public function getLogin()
    {
        return view('auth.agentUser.login');
    }

    /**
     * Show the application loginprocess.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
            // 'g-recaptcha-response' => 'required'
        ]);

        // $request_url = 'https://www.google.com/recaptcha/api/siteverify';

        // $request_data = [
        //     'secret' => config('app.captch_secret'),
        //     'response' => $request['g-recaptcha-response']
        // ];

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $request_url);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $response_body = curl_exec($ch);

        // curl_close($ch);

        // $response_data = json_decode($response_body, true);

        // if ($response_data['success'] == false) {
        //     \Session::put('error', 'Recaptcha verification failed.');

        //     return redirect()->back();
        // }

        $user = Agent::where(["email" => $request->input('email')])->first();

        if ($user && \Hash::check($request->input('password'), $user->password)) {

            if ($user->is_active == '0') {
                return back()->with('error', 'Your account is inactive. Please contact the administration for assistance.');
            } else {

                
                    if (auth()->guard('agentUser')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {

                        $user = auth()->guard('agentUser')->user();

                        $applicationStart = RpApplication::where('agent_id', auth()->guard('agentUser')->user()->id)->first();

                        // if (empty($applicationStart) && auth()->guard('agentUser')->user()->main_agent_id == 0) {
                        //     \Session::put('success', 'You have successfully logged in!');
                        //     return redirect()->route('rp.my-application.create');
                        // } else {
                        //     if ($applicationStart->status == 0) {
                        //         return redirect()->route('rp.my-application.detail');
                        //     } else {
                        //         return redirect()->route('rp.dashboard');
                        //     }
                        // }
                        return redirect()->route('rp.dashboard');
                    } else {
                        return back()->with('error', 'Your username or password is incorrect. Please try again.');
                    }
            }
        } else {
            return back()->with('error', 'Your username or password is incorrect. Please try again.');
        }
    }

    /**
     * Show the application logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->guard('agentUser')->logout();
        return redirect()->to('/affiliate/login');
    }

    public function agentForgetPassword(Request $request)
    {
        return view('auth.agentUser.agent_password_email');
    }

    public function agentForgetEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            // 'g-recaptcha-response' => 'required'
        ]);

        // $request_url = 'https://www.google.com/recaptcha/api/siteverify';

        // $request_data = [
        //     'secret' => config('app.captch_secret'),
        //     'response' => $request['g-recaptcha-response']
        // ];

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $request_url);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $response_body = curl_exec($ch);

        // curl_close($ch);

        // $response_data = json_decode($response_body, true);

        // if ($response_data['success'] == false) {
        //     \Session::put('error', 'Recaptcha verification failed.');

        //     return redirect()->back();
        // }

        $user = Agent::where(['email' => $request->email])->first();
        //Check if the user exists
        if ($user == NULL) {
            return redirect()->back()->with(['error' => 'User does not exist']);
        }
        DB::table('agents_password_resets')->where('email', $request->email)->delete();
        //Create Password Reset Token
        DB::table('agents_password_resets')->insert([
            'email' => $request->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);
        //Get the token just created above
        $tokenData = DB::table('agents_password_resets')->where('email', $request->email)->first();
        //try {
            \Mail::to($request->email)->send(new SendForgotEmailAgent($tokenData));
            return redirect()->back()->with(['status' => 'A password reset link has been sent to your email address.']);
        //} catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Something went wrong. Please try again.']);
        //}
    }

    public function agentForgetPasswordForm(Request $request, $token)
    {
        return view('auth.agentUser.agent_password_reset', compact('token'));
    }

    public function agentForgetPasswordFormPost(Request $request)
    {
        //Validate input
        $this->validate(
            $request,
            [
                'email' => 'required|string|email|max:255|exists:agents,email',
                'password' => 'required|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => "same:password",
                //'g-recaptcha-response' => 'required'
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        // $request_url = 'https://www.google.com/recaptcha/api/siteverify';

        // $request_data = [
        //     'secret' => config('app.captch_secret'),
        //     'response' => $request['g-recaptcha-response']
        // ];

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $request_url);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $response_body = curl_exec($ch);

        // curl_close($ch);

        // $response_data = json_decode($response_body, true);

        // if ($response_data['success'] == false) {
        //     \Session::put('error', 'Recaptcha verification failed.');

        //     return redirect()->back();
        // }

        try {
            $tokenData = DB::table('agents_password_resets')->where('token', $request->token)->first();
            // Redirect the user back if the email is invalid
            if (!$tokenData) {
                return redirect()->back()->with(['error' => 'Token not found']);
            }
            $user = Agent::where(['email' => $tokenData->email])->first();
            // Redirect the user back if the email is invalid
            if (!$user) {
                return redirect()->back()->with(['error' => 'Email not found']);
            }
            $user->password = \Hash::make($request->password);
            $user->update();
            //Delete the token
            DB::table('agents_password_resets')->where('email', $user->email)->delete();
            Session::put('success', 'Your password has been reset successfully.');
            return redirect()->to('/affiliate/login');
        } catch (Exception $e) {
            Session::put('error', 'Something went Wrong!');
            return redirect()->to('/affiliate/login');
        }
    }

    public function otpform()
    {
        return view('auth.agentUser.otpform');
    }

    public function sendOtpSMS($user)
    {
        $OTP = rand(111111, 999999);
        $generateOTP = Agent::where(['email' => $user->email])->update(['login_otp' => $OTP]);
        $message = "Use " . $OTP . " to sign in to your " . config('app.name') . " CRM account. Never forward this code.";

        $content = [
            'otp' => $OTP,
            'name' => $user->name
        ];
        try {
            \Mail::to($user->email)->send(new AgentOtpMail($content));
            \Session::put('success', 'OTP has been successfully sent. Please check your registered mail.');
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }

        return true;
    }

    public function resendotp()
    {
        $user = Agent::where(['email' => \Session::get('email')])->first();

        if (empty($user)) {
            \Session::put('error', 'OTP send fail, Please try again.');
            return redirect()->route('rp.testpay-otp');
        }

        $OTP = rand(111111, 999999);
        $generateOTP = Agent::where(['email' => \Session::get('email')])->update(['login_otp' => $OTP]);

        $response = $this->sendOtpSMS($user);

        // if($response->type == 'success') {
        if ($response == true) {
            \Session::put('success', 'OTP has been successfully sent. Please check your registered mail.');
            return redirect()->route('rp.testpay-otp');
        } else {
            \Session::put('error', 'OTP send fail, Please try again.');
            return redirect()->route('rp.testpay-otp');
        }
    }

    public function checkotp(Request $request)
    {
        $this->validate($request, [
            'otp' => 'required',
            'g-recaptcha-response' => 'required'
        ]);

        $request_url = 'https://www.google.com/recaptcha/api/siteverify';

        $request_data = [
            'secret' => config('app.captch_secret'),
            'response' => $request['g-recaptcha-response']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response_body = curl_exec($ch);

        curl_close($ch);

        $response_data = json_decode($response_body, true);

        if ($response_data['success'] == false) {
            \Session::put('error', 'Recaptcha verification failed.');

            return redirect()->back();
        }

        $userData = Agent::where(['email' => \Session::get('email')])->first();

        if (isset($userData->login_otp) && $userData->login_otp != $request->otp) {

            \Session::put('error', 'Wrong OTP , Please try again');
            return redirect()->back();
        }

        if (auth()->guard('agentUser')->attempt(['email' => \Session::get('email'), 'password' => \Session::get('password')])) {
            Agent::where(['email' => \Session::get('email')])->update(['login_otp' => '']);
            $user = auth()->guard('agentUser')->user();
            Session::put('user_name', $user->name);
            \Session::forget('email');
            \Session::forget('password');
            return redirect()->route('rp.dashboard');
        } else {
            \Session::put('error', 'Wrong OTP , Please try again');
            return redirect()->back();
        }
    }

    public function register()
    {
        return view('auth.agentUser.register');
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required',
                'email' => 'required|string|email|max:255|unique:agents,email,NULL,id,deleted_at,NULL',
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                // 'g-recaptcha-response' => 'required'
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

        // $request_url = 'https://www.google.com/recaptcha/api/siteverify';

        // $request_data = [
        //     'secret' => config('app.captch_secret'),
        //     'response' => $request['g-recaptcha-response']
        // ];

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $request_url);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $response_body = curl_exec($ch);

        // curl_close($ch);

        // $response_data = json_decode($response_body, true);

        // if ($response_data['success'] == false) {
        //     \Session::put('error', 'Recaptcha verification failed.');

        //     return redirect()->back();
        // }

        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));
        $input['password'] = bcrypt($input['password']);

        $input['token'] = Str::random(40) . time();
        $input['is_active'] = '0';

        $agent = $this->agent->storeData($input);
        $ArrRequest = ['name' => $request->name, 'email' => $request->email, 'password' => $request->password];
        // addAdminLog(AdminAction::CREATE_REFERRAL_PARTNER, $agent->id, $ArrRequest, "Agent Created Successfully!");

        $referral_code = $agent->id . strtoupper(Str::random(10));

        \DB::table('agents')
            ->where('id', $agent->id)
            ->update(['referral_code' => $referral_code]);

        $data['token'] = $input['token'];
        $data['name'] = $input['name'];

        Mail::to($input['email'])->send(new AgentRegisterMail($data));

        notificationMsg('success', 'Your registration has been completed successfully. An email will be sent to you shortly to activate your account.');
        return redirect()->route('rp/login');
    }

    public function verifyUserEmail($token)
    {
        $check = DB::table('agents')->where('token', $token)->first();
        if (!is_null($check)) {
            if ($check->is_active == 1) {
                return redirect()->to('affiliate/login')
                    ->with('success', "The user has already been activated.");
            }

            $agent = $this->agent::where('token', $token)->first();
            $this->agent::where('token', $token)->update(['is_active' => '1', 'token' => '', 'email_verified_at' => date('Y-m-d H:i:s')]);
            return redirect()->to('affiliate/login')
                ->with('success', "Your account has been successfully activated. Please log in using your email address and password.");
        }
        return redirect()->to('affiliate/login')
            ->with('error', "Your token is not valid..");
    }
}
