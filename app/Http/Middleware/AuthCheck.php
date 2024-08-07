<?php

namespace App\Http\Middleware;

use App\Application;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() == 'null' || empty(Auth::user()) || Auth::user()  == '' || Auth::user()  == null) {
            return route('login');
        } else {
            // if (auth()->user()->main_user_id == 0) {
            //     $application = Application::where('user_id', auth()->user()->id)->first();
            //     if (!is_null($application)) {
            //         if ($application->status == 4 || $application->status == 5 || $application->status == 6) {
            //         }
            //     } else {
            //         return redirect()->route('dashboardPage');
            //     }
            // }
        }
        return $next($request);
    }
}
