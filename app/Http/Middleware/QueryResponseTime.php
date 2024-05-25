<?php

namespace App\Http\Middleware;

use Closure;

class QueryResponseTime
{
    protected $startTime;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->startTime = microtime(true);
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $responseTime = microtime(true) - $this->startTime;
        \Log::info('Response Time in seconds', ['time' => $responseTime]);
    }
}
