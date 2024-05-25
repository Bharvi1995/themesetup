<?php

namespace App\Http\Controllers\Auth;

use App\ReportUser;
use Validator;
use Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportUserController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class ReportUserAuthController extends ReportUserController
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
    protected $redirectTo = '/report/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function getLogin()
    {
        return view('auth.reportUserLogin');
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
        ]);

        // Get user record
        $user = ReportUser::where('email', $request->get('email'))->first();
        
        if(!$user) {
            \Session::put('error', 'Your user name not match in our system!');
            return back();
        }

        if($request->get('email') != $user->email) {
            \Session::put('error', 'Your user name not match in our system!');
            return back();
        }        
        // Check Password
        if (!\Hash::check($request->get('password'), ($user)?$user->password:''))
        {   
            \Session::put('error', 'Your username and password wrong!');
            return back();
        } 

        // Set Auth Details
        \Auth::login($user);

        \Session::put('currentUser',$user);

        \Session::put('ReportUser', true);

        \notificationMsg('success','You are Login successfully!');

        return redirect()->route('report-user-dashboard');

    }

    /**
     * Show the application logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->guard('reportuser')->logout();
        Session::flush();
        // Session::put('success','you are logout Successfully');
        return redirect()->to('/report/login');
    }
}
