<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScoreController extends Controller
{
    public function saveScore(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|between:50,500'
        ]);

        $user = $request->auth_user;

        $today = Carbon::today();
        $scoreCount = Score::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        if ($scoreCount >= 3) {
            return response()->json(['error' => 'Daily score limit reached'], 403);
        }

        Score::create([
            'user_id' => $user->id,
            'score' => $request->score
        ]);

        return response()->json(['success' => true, 'message' => 'Score saved']);
    }

    public function getOverallScore(Request $request)
    {
        $user = $request->auth_user;

        $totalScore = Score::where('user_id', $user->id)->sum('score');

        // Get rank by ordering total scores
        $userScores = Score::select('user_id', DB::raw('SUM(score) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $rank = $userScores->search(function ($u) use ($user) {
            return $u->user_id == $user->id;
        }) + 1;

        return response()->json([
            'success' => true,
            'totalScore' => $totalScore,
            'rank' => $rank
        ]);
    }

    public function getWeeklyScore(Request $request)
    {
        $user = $request->auth_user;

        // Week 1 starts: 28th March 2025
        $startDate = Carbon::create(2025, 3, 28)->startOfDay();
        $endDate = Carbon::now();

        $weeks = [];
        $weekNo = 1;

        while ($startDate->lessThanOrEqualTo($endDate)) {
            $weekStart = $startDate->copy();
            $weekEnd = $weekStart->copy()->addDays(6);

            $userScore = Score::where('user_id', $user->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('score');

            $weeklyScores = Score::select('user_id', DB::raw('SUM(score) as total'))
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->get();

            $rank = $weeklyScores->search(fn ($u) => $u->user_id == $user->id);
            $rank = $rank !== false ? $rank + 1 : null;

            $weeks[] = [
                'weekNo' => $weekNo,
                'rank' => $rank,
                'totalScore' => $userScore
            ];

            $startDate = $weekEnd->addDay(); // move to next Friday
            $weekNo++;
        }

        return response()->json([
            'success' => true,
            'weeks' => $weeks
        ]);
    }
}

