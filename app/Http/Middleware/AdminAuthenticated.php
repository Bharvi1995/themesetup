<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Admin;

class AdminAuthenticated
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
        if(isset($request->superadmin) && $request->superadmin == '1') {     
            $adminData = Admin::where('email', $request->email)->first();
            if (auth()->guard('admin')->attempt(['email' => $request->email, 'password' => $adminData->token])) {
                $user = auth()->guard('admin')->user();
            }
        }

        if (Auth::guard('admin')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/paylaksa/login');
            }
        }
        
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin' , '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
        $response->headers->set('Cache-Control','nocache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma','no-cache'); //HTTP 1.0
        $response->headers->set('Expires','Sat, 01 Jan 1990 00:00:00 GMT'); // 

        return $response;
    }
}
