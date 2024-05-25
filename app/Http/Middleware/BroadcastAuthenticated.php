<?php

namespace App\Http\Middleware;

use Closure;

class BroadcastAuthenticated
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
        if (empty($request->header('authorization'))) {
            return abort(401);
        }

        $userType = $request->header('authorization');
        if ($userType === 'admin') {
            $request->setUserResolver(function () {
                return auth()->guard('admin')->user();
            });
        } else {
            $request->setUserResolver(function () {
                return auth()->user();
            });
        }

        return $next($request);
    }
}
