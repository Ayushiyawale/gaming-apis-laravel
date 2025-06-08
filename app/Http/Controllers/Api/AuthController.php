<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    
    public function sendOtp(Request $request)
    {
        $request->validate(['mobile' => 'required']);
        $otp = '1234';
        Cache::put('otp_' . $request->mobile, $otp, now()->addMinute());
        return response()->json(['success' => true]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'mobile' => 'required|unique:register_user,mobile',
            'name' => 'required',
            'dob' => 'required|date',
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $otp = Cache::get('otp_' . $request->mobile);
        if (!$otp || $otp != $request->otp) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        $user = User::create($request->only(['mobile', 'name', 'dob', 'email']));
        $token = JWT::encode(['sub' => $user->id, 'iat' => time()], env('JWT_SECRET'), 'HS256');

        return response()->json(['success' => true, 'token' => $token]);
    }
}

