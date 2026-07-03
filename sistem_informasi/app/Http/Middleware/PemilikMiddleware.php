<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PemilikMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return redirect('/admin/login');
        }

        if ($request->user()->role !== 'pemilik') {
            abort(403, 'Akses hanya untuk pemilik.');
        }

        return $next($request);
    }
}