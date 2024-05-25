<?php

namespace App\Http\Controllers\Bank;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Admin;
use App\Bank;
use App\ApplicationAssignToBank;
use View;
use Redirect;
use Hash;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class BankUserBaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $bankUser;
    public function __construct()
    {
        view()->share('bankUserTheme', 'layouts.bank.default');

        $this->middleware(function ($request, $next) {
            $userData = Bank::where('banks.id', auth()->guard('bankUser')->user()->id)
                ->first();

            view()->share('userData', $userData);
            return $next($request);
        });
        $this->user = new User;
        $this->bankUser = new Bank;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $application = ApplicationAssignToBank::where('bank_user_id', auth()->guard('bankUser')->user()->id)->get();

        return view('bank.dashboard', compact('application'));
    }

    public function profile()
    {
        $data = Bank::where('banks.id', auth()->guard('bankUser')->user()->id)
            ->first();

        return view('bank.profile.index', compact('data'));
    }

    public  function updateProfile(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'bank_name' => 'required|regex:/^[a-z\d\-_\s\.]+$/i',
            'email' => 'required|email|unique:banks,email,' . auth()->guard('bankUser')->user()->id,
            'password' => 'confirmed',
        ],
        [
            'bank_name.regex' => 'Please Enter Only Alphanumeric Characters.',
        ]);

        $input = \Arr::except($input, array('_token', 'password_confirmation'));
        if ($input['password'] != null) {
            $input['token'] = $input['password'];
            $input['password'] = bcrypt($input['password']);
        } else {
            $input = \Arr::except($input, array('password'));
        }

        $this->bankUser->updateData(auth()->guard('bankUser')->user()->id, $input);

        notificationMsg('success', 'Profile Updated Successfully!');

        return redirect()->route('profile-bank');
    }
}
