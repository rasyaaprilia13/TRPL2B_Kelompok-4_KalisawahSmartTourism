<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle($request, Closure $next)
{
    Log::info('Request masuk', [
        'method' => $request->method(),
        'url' => $request->fullUrl(),
        'ip' => $request->ip()
    ]);

    return $next($request);
}
}
