<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $request->merge(['auth_user' => $user]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized: Invalid or missing token'], 401);
        }

        return $next($request);
    }
}
