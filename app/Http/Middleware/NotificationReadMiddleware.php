<?php

namespace App\Http\Middleware;

use Closure;
use App\Notification;
use Route;

class NotificationReadMiddleware
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
        $Notification = Notification::where('type','admin')->where('is_read','0')->get();
        foreach ($Notification as $key => $value) {
            if($value->url == '/'.$request->path()){
                if(isset($request->query()['for']) && $request->query()['for'] == 'read'){
                    Notification::where('id',$value->id)->update(['is_read'=>'1']);
                }
            }
        }
        return $next($request);
    }
}
