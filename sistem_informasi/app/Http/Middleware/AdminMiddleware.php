<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle($request, Closure $next)
{
    // kalau belum login → jangan ganggu
    if (!$request->user()) {
        return $next($request);
    }

    // kalau sudah login tapi bukan admin
    if ($request->user()->role !== 'admin') {
        return response()->json([
            'message' => 'Akses hanya untuk admin'
        ], 403);
    }

    return $next($request);
}
}