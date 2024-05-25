<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\User;
use DB;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(HasherContract $hasher)
    {
        $request = \Request::all();
        if ($request && isset($request['token']) && isset($request['password']) && isset($request['password_confirmation'])) {

            $userData = User::where('email', $request['email'])->first();
            $reset = DB::table("password_resets")->where('email', $request['email'])->first();

            $this->hasher = $hasher;

            if (isset($request['token']) && isset($request['password']) && isset($request['password_confirmation']) && $this->hasher->check($request['token'], $reset->token)) {
                addToLog('Reset Password', $request, 'general', $userData->id);
            }
        }
        $this->middleware('guest');
        $this->middleware('throttle:20,1');
    }
}
