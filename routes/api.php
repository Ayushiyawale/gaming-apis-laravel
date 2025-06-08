<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ScoreController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth.jwt'])->group(function () {
    Route::post('/save-score', [ScoreController::class, 'saveScore']);
    Route::get('/overall-score', [ScoreController::class, 'getOverallScore']);
    Route::get('/weekly-score', [ScoreController::class, 'getWeeklyScore']);
});
