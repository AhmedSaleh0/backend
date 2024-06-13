<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class OptionalAuth
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
        // Attempt to authenticate the user using the 'api' guard
        if ($request->bearerToken()) {
            Auth::shouldUse('api');
            Auth::onceBasic('email') ?: Auth::guard('api')->user();
        }

        return $next($request);
    }
}
