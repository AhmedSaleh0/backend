<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Token;

class OptionalAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($token = $request->bearerToken()) {
            $tokenId = optional((new TokenRepository)->find($token))->id;
            if ($tokenId) {
                $user = optional(Token::find($tokenId))->user;
                if ($user) {
                    Auth::setUser($user);
                }
            }
        }

        return $next($request);
    }
}
