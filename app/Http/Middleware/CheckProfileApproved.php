<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class CheckProfileApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = null;
        if (Auth::user()->main_user_id) {
            $user = User::where('id', Auth::user()->main_user_id)->first();
        } else {
            $user = Auth::user();
        }
        if (empty($user->application)) {
            return redirect()->route('my-application');
        } else if ($user->application->status == 0) {
            return redirect()->route('my-application');
        } else if (
            $user->application->status == 4 ||
            $user->application->status == 5 ||
            $user->application->status == 6 ||
            $user->application->status == 10 ||
            $user->application->status == 11
        ) {
            return $next($request);
        } else {
            return redirect()->route('my-application');
        }
    }
}
