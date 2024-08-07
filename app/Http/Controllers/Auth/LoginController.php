<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use App\User;
use App\Application;
use Session;

class LoginController extends Controller
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('throttle:500,1')->only('login');
    }

    public function login(Request $request)
    {
        if(config('app.env') == 'production')
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
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $response_body = curl_exec($ch);

            curl_close($ch);

            $response_data = json_decode($response_body, true);

            if ($response_data['success'] == false) {
                \Session::put('error', 'Recaptcha verification failed.');

                return redirect()->back();
            }
        }
        $userData = User::where(['email' => $request->input('email')])->first();

        if (!$userData) {
            \Session::put('error', "No account was found associated with this email.");
            return redirect()->back();
        }

        if ($userData['is_active'] == '1') {
            if ($userData && \Hash::check($request->input('password'), $userData->password)) {
                
                if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                    User::where(['email' => $request->input('email')])->update(['otp' => '']);

                    if ($userData->is_active == '0') {
                        \Session::put('maintenance', true);
                    }
                    $user = auth()->user();
                    return redirect()->route('dashboardPage');
                    // $applicationStart = Application::where('user_id', auth()->user()->id)->first();                  
                    // if (empty($applicationStart) && auth()->user()->main_user_id == 0) {
                    //     return redirect()->route('my-application');
                    // } else {
                    //     if ($applicationStart->status == 4 || $applicationStart->status == 5 || $applicationStart->status == 6) {
                    //         return redirect()->route('dashboardPage');
                    //     } else {
                    //         return redirect()->route('my-application');
                    //     }
                    // }
                } else {
                    \Session::put('error', 'Incorrect credentials, kindly reset your password to log in.');
                    return redirect()->back();
                }
            } else {
                \Session::put('error', 'Incorrect credentials, kindly reset your password to log in.');
                return redirect()->back();
            }
        } else {
            return back()->with('error', 'Your account has not been activated yet.');
        }
    }

    public function addMobileNo(Request $request)
    {
        $this->validate($request, [
            'mobile_no' => 'required|unique:users',
            'country_code' => 'required',
        ]);

        $updateUser = User::where('id', $request->id)->update(['country_code' => $request->country_code, 'mobile_no' => $request->mobile_no]);
        if ($updateUser) {
            Session::put('success', 'Your mobile number has beed added successfully , you will receive the OTP at the time of your login on your mobile number as well as email id.');
            return view('auth.login');
        } else {
            return back()->with('error', 'Something went wront with adding your mobile no., please try again.');
        }
    }

    public function resendotp(Request $request)
    {
        $user = User::where(['email' => \Session::get('email')])->first();
        $OTP = rand(111111, 999999);
        $generateOTP = User::where(['email' => \Session::get('email')])->update(['otp' => $OTP]);

        if (empty($user)) {
            \Session::put('error', 'Your Email Id not Found!.');
            return redirect()->back();
        }

        if (isset($request->type) && $request->type == 'em') {
            $content = [
                'otp' => $OTP,
            ];
            try {
                \Mail::to(\Session::get('email'))->queue(new OtpMail($content));
                \Session::put('success', 'OTP has been successfully sent. Please check your registered mail.');
            } catch (\Exception $e) {
                \Session::put('error', 'Mail not sent, Please contact ' . config("app.name") . ' for support.');
                return redirect()->back();
            }
            return redirect()->route('testpay-otp');
        }

        $response = $this->sendOtpSMS($user);

        if ($response == true) {
            \Session::put('success', 'OTP has been resent on your registered email id.');
            return redirect()->route('testpay-otp');
        } else {
            \Session::put('error', 'OTP send fail, Please try again.');
            return redirect()->route('testpay-otp');
        }
    }

    public function otpform()
    {
        return view('auth.otpform');
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
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response_body = curl_exec($ch);

        curl_close($ch);

        $response_data = json_decode($response_body, true);

        if ($response_data['success'] == false) {
            \Session::put('error', 'Recaptcha verification failed.');

            return redirect()->back();
        }

        $userData = User::where(['email' => \Session::get('email')])->first();

        if (empty($userData)) {
            \Session::put('error', 'Wrong OTP , Please try again');
            return redirect()->back();
        }
        if (isset($userData->otp) && $userData->otp != $request->otp) {
            \Session::put('error', 'Wrong OTP , Please try again');
            return redirect()->back();
        }

        if (auth()->attempt(['email' => \Session::get('email'), 'password' => \Session::get('password')])) {
            addToLog('OTP Log.', $request->all(), 'general', $userData->id);

            User::where(['email' => \Session::get('email')])->update(['otp' => '']);

            if ($userData->is_active == '0') {
                \Session::put('maintenance', true);
            }
            $user = auth()->user();
            \Session::forget('email');
            \Session::forget('password');

            $applicationStart = Application::where('user_id', auth()->user()->id)->first();
            $notification = [
                'user_id' => auth()->user()->id,
                'sendor_id' => auth()->user()->id,
                'type' => 'user',
                'title' => 'Account Login',
                'body' => 'Logged in successfully',
                'url' => '/dashboard',
                'is_read' => '0'
            ];

            $realNotification = addNotification($notification);

            if ($userData->is_white_label != 0) {
                return redirect()->route('gettransactions');
            }

            if (empty($applicationStart) && auth()->user()->main_user_id == 0) {
                return redirect()->route('my-application');
            } else {
                if ($applicationStart->status == 4 || $applicationStart->status == 5 || $applicationStart->status == 6) {
                    return redirect()->route('dashboardPage');
                } else {
                    return redirect()->route('my-application');
                }
            }
        } else {
            \Session::put('error', 'Wrong OTP , Please try again');
            return redirect()->back();
        }
    }

    public function sendOtpSMS($user)
    {
        if (!$user) {
            Session::flush();
            return redirect()->route('login');
        }
        $OTP = rand(111111, 999999);
        $generateOTP = User::where(['email' => $user->email])->update(['otp' => $OTP]);
        $user = User::where(['email' => $user->email])->first();
        try {
            // \Log::info([
            //     'User Email' => $user->email
            // ]);
            \Mail::to($user->email)
                ->send(new OtpMail($user));
            \Session::put('success', 'OTP has been successfully sent. Please check your registered mail.');
        } catch (\Exception $e) {
            \Log::info([
                'OTP Exception' => $e->getMessage()
            ]);
        }
        return true;
    }

    public function logout(Request $request)
    {
        \Cookie::queue(\Cookie::forget('message-modal'));
        $user = auth()->user();
        if ($user) {
            addToLog('user logout successfully.', $user->toArray(), 'general');
        }
        auth()->logout();
        return redirect()->route('login');
    }
}