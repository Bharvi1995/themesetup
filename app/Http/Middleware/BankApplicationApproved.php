<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\BankApplication;

class BankApplicationApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $bank_application = new BankApplication();
        $application = $bank_application->FindDataFromUser(Auth::guard('bankUser')->user()->id);
        
        if(!$application){
            return redirect()->route('bank.my-application.create');
        }elseif($application->status != '1'){
            return redirect()->route('bank.my-application.detail');
        }
        
        $response = $next($request);

        return $response;
    }
}
