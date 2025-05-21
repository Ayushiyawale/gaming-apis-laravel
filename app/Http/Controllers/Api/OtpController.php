<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Otp;
use Illuminate\Support\Carbon;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^[0-9]{10,15}$/'
        ]);

        $mobile = $request->input('mobile');
        $otp = '1234'; // Hardcoded

        // Store OTP with 1 min expiry
        Otp::updateOrCreate(
            ['mobile' => $mobile],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinute()
            ]
        );

        // Simulate sending OTP here (e.g. log)
        // Log::info("OTP sent to $mobile: $otp");

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully'
        ]);
    }
}

