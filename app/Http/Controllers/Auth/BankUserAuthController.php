<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bank;
use App\BankApplication;
use View;
use Redirect;
use Str;
use Auth;
use Storage;
use Session;
use Validator;
use App\Categories;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use \Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\BankOtpMail;
use App\Mail\SendForgotEmailBank;
use Carbon\Carbon;
use App\Mail\BankRegisterMail;

class BankUserAuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/bank/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bank = new Bank;
        $this->middleware('guest', ['except' => 'logout']);
        $this->middleware('throttle:50,1');
    }

    public function getLogin()
    {
        return view('auth.bankUser.login');
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

        $user = Bank::where(["email" => $request->input('email')])->first();

        if ($user && \Hash::check($request->input('password'), $user->password)) {
            if ($user->is_active == '0') {
                return back()->with('error', 'Your account is not active. Please contact administration');
            } elseif ($user->is_otp_required == '0') {
                auth()->guard('bankUser')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')]);
                $user = auth()->guard('bankUser')->user();
                $applicationStart = BankApplication::where('bank_id',auth()->guard('bankUser')->user()->id)->first();

                if(empty($applicationStart)){
                    return redirect()->route('bank.my-application.create');
                }else{
                    if($applicationStart->status == 1 ){
                        return redirect()->route('bank.dashboard');
                    }else{
                        return redirect()->route('bank.my-application.detail');
                    }
                }
                \Session::put('success', 'You have logged in successfully!');
                return redirect()->route('bank.dashboard');
            } else {
                $response = $this->sendOtpSMS($user);
                if ($response == true) {
                    \Session::put('email', $request->input('email'));
                    \Session::put('password', $request->input('password'));
                    Session::put('success', 'Enter the OTP received on your registered email id.');
                    return redirect()->route('bank.testpay-otp');
                }
            }
        } else {
            return back()->with('error', 'your username and password are wrong.');
        }
    }

    public function sendOtpSMS($user)
    {
        $OTP = rand(111111, 999999);
        $generateOTP = Bank::where(['email' => $user->email])->update(['otp' => $OTP]);
        $message = "Use " . $OTP . " to sign in to your ".config('app.name')." CRM account. Never forward this code.";

        $content = [
            'otp' => $OTP,
            'name' => $user->name
        ];
        try {
            \Mail::to($user->email)->send(new BankOtpMail($content));
            \Session::put('success', 'OTP has been successfully sent. Please check your registered mail.');
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }

        return true;
    }

    public function otpform()
    {
        return view('auth.bankUser.otpform');
    }

    public function resendotp()
    {
        $user = Bank::where(['email' => \Session::get('email')])->first();

        if (empty($user)) {
            \Session::put('error', 'OTP send fail, Please try again.');
            return redirect()->route('bank.testpay-otp');
        }

        $OTP = rand(111111, 999999);
        $generateOTP = Bank::where(['email' => \Session::get('email')])->update(['otp' => $OTP]);

        $response = $this->sendOtpSMS($user);

        // if($response->type == 'success') {
        if ($response == true) {
            \Session::put('success', 'OTP has been successfully sent. Please check your registered mail.');
            return redirect()->route('bank.testpay-otp');
        } else {
            \Session::put('error', 'OTP send fail, Please try again.');
            return redirect()->route('bank.testpay-otp');
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

        $userData = Bank::where(['email' => \Session::get('email')])->first();

        if (isset($userData->otp) && $userData->otp != $request->otp) {

            \Session::put('error', 'Wrong OTP , Please try again');
            return redirect()->back();
        }

        if (auth()->guard('bankUser')->attempt(['email' => \Session::get('email'), 'password' => \Session::get('password')])) {
            Bank::where(['email' => \Session::get('email')])->update(['otp' => '']);
            $user = auth()->guard('bankUser')->user();

            $applicationStart = BankApplication::where('bank_id',$user->id)->first();
            
            if(empty($applicationStart)){
                return redirect()->route('bank.my-application.create');
            }else{
                if($applicationStart->status == 1){
                    Session::put('user_name', $user->name);
                    \Session::forget('email');
                    \Session::forget('password');
                    return redirect()->route('bank.dashboard');
                } else {
                    Session::put('user_name', $user->name);
                    \Session::forget('email');
                    \Session::forget('password');
                    return redirect()->route('bank.my-application.create');
                }
            }
        } else {
            \Session::put('error', 'Wrong OTP , Please try again');
            return redirect()->back();
        }
    }

    public function logout()
    {
        auth()->guard('bankUser')->logout();
        return redirect()->to('/bank/login');
    }

    public function agentForgetPassword(Request $request)
    {
        return view('auth.bankUser.password_email');
    }

    public function bankForgetEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
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

        $user = Bank::where(['email' => $request->email])->first();
        //Check if the user exists
        if ($user == NULL) {
            return redirect()->back()->with(['error' => 'User does not exist']);
        }
        DB::table('banks_password_resets')->where('email', $request->email)->delete();
        //Create Password Reset Token
        DB::table('banks_password_resets')->insert([
            'email' => $request->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);
        //Get the token just created above
        $tokenData = DB::table('banks_password_resets')->where('email', $request->email)->first();
        try {
            \Mail::to($request->email)->send(new SendForgotEmailBank($tokenData));
            return redirect()->back()->with(['status' => 'A reset link has been sent to your email address.']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'A Network Error occurred. Please try again.']);
        }
    }

    public function bankForgetPasswordForm(Request $request, $token)
    {
        return view('auth.bankUser.password_reset', compact('token'));
    }

    public function bankForgetPasswordFormPost(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|string|email|max:255|exists:banks,email',
                'password' => 'required|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => "same:password",
                'g-recaptcha-response' => 'required'
            ],
            ['password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)']
        );

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

        try {
            $tokenData = DB::table('banks_password_resets')->where('token', $request->token)->first();
            // Redirect the user back if the email is invalid
            if (!$tokenData) {
                return redirect()->back()->with(['error' => 'Token not found']);
            }
            $user = Bank::where(['email' => $tokenData->email])->first();
            // Redirect the user back if the email is invalid
            if (!$user) {
                return redirect()->back()->with(['error' => 'Email not found']);
            }
            $user->password = \Hash::make($request->password);
            $user->update();
            //Delete the token
            DB::table('banks_password_resets')->where('email', $user->email)->delete();
            Session::put('success', 'Your Password Reset Successfully');
            return redirect()->to('/bank/login');
        } catch (Exception $e) {
            Session::put('error', 'Something went Wrong!');
            return redirect()->to('/bank/login');
        }
    }

    public function register()
    {
        $category = Categories::orderBy("categories.id", "ASC")->pluck('name', 'id')->toArray();
        return view('auth.bankUser.register', compact('category'));
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'bank_name' => 'required',
                'email' => 'required|string|email|max:255|unique:banks,email,NULL,id,deleted_at,NULL',
                'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'country' => 'required',
                'processing_country' => 'required',
                'category_id' => 'required',
                'g-recaptcha-response' => 'required'
            ],
            [
                'password.regex' => 'Enter valid format.(One Upper,Lower,Numeric,and Special character.)',
                'category_id.required' => 'The category field is required.'
            ]
        );

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

        $input = \Arr::except($request->all(), array('_token', 'password_confirmation'));
        $input['password'] = bcrypt($input['password']);
        
        $input['category_id'] = implode(",",$input['category_id']);
        $input['processing_country'] = json_encode($input['processing_country']);
        
        $input['token'] = Str::random(40) . time();
        $input['is_active'] = '0';
        $bank = $this->bank->storeData($input);
        
        $referral_code = $bank->id . strtoupper(Str::random(10));

        \DB::table('banks')
            ->where('id', $bank->id)
            ->update(['referral_code' => $referral_code]);

        $data['token'] = $input['token'];
        $data['name'] = $input['bank_name'];
        Mail::to($input['email'])->send(new BankRegisterMail($data));

        notificationMsg('success', 'Your account has been registered successfully. You will receive an email shortly to activate your account.');

        return redirect()->route('bank/login');
    }

    public function verifyUserEmail($token)
    {
        $check = DB::table('banks')->where('token', $token)->first();
        if (!is_null($check)) {
            if ($check->is_active == 1) {
                return redirect()->to('bank/login')
                    ->with('success', "user are already actived.");
            }

            $bank = $this->bank::where('token', $token)->first();
            $this->bank::where('token', $token)->update(['is_active' => '1', 'token' => '', 'email_verified_at' => date('Y-m-d H:i:s')]);
            return redirect()->to('bank/login')
                ->with('success', "Your account has been activated successfully. Please login using your email id and password.");
        }
        return redirect()->to('bank/login')
            ->with('error', "Your token is invalid.");
    }
}