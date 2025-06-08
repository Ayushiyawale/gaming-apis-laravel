<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Models\User;

class JwtMiddleware
{
     public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) return response()->json(['error' => 'Token not provided'], 401);

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $request->user = User::find($decoded->sub);
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
