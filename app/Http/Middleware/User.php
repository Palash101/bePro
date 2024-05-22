<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use Spatie\Permission\Models\Role;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $status)
    {
        if (auth()->user()->status) {
            return $next($request);
        }
        return response(['message' => 'Please contact front desk'],401);
    }
}

