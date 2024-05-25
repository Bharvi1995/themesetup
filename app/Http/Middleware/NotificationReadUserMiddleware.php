<?php

namespace App\Http\Middleware;

use Closure;
use App\Notification;
use Route;
use Auth;

class NotificationReadUserMiddleware
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
        if (Auth::check()) {
            $Notification = Notification::where('type','user')->where('is_read','0')->where('user_id',Auth::user()->id)->get();
            foreach ($Notification as $key => $value) {
                if($value->url == '/'.$request->path()){
                    if($value->url != '/my-application'){
                        Notification::where('id',$value->id)->update(['is_read'=>'1']);
                    }
                }
            }
        }
        return $next($request);
    }
}
