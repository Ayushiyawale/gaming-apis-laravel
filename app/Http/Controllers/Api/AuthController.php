<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
     public function register(Request $request)
    {
        // return "test--------->";
        $request->validate([
            'mobile' => 'required|unique:register_user',
            'name' => 'required',
            'email' => 'nullable|email',
            'dob' => 'nullable|date',
            'password' => 'required|min:4',
        ]);

        $user = User::create([
            'mobile' => $request->mobile,
            'name' => $request->name,
            'email' => $request->email,
            'dob' => $request->dob,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('mobile', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
}

