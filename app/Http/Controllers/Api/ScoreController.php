<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// use DB;

class ScoreController extends Controller
{
    
    public function saveScore(Request $request)
    {
        $request->validate(['score' => 'required|integer|between:50,500']);
        $user = $request->user;
        $today = Carbon::today();

        $count = Score::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        if ($count >= 3) {
            return response()->json(['error' => 'Limit exceeded'], 400);
        }

        Score::create(['user_id' => $user->id, 'score' => $request->score]);
        return response()->json(['success' => true]);
    }

    public function getOverallScore(Request $request)
    {
        $user = $request->user;
        $total = Score::where('user_id', $user->id)->sum('score');

        $scores = Score::select('user_id', DB::raw('SUM(score) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $rank = $scores->search(fn ($item) => $item->user_id == $user->id) + 1;

        return response()->json(['success' => true, 'rank' => $rank, 'totalScore' => $total]);
    }

    public function getWeeklyScore(Request $request)
    {
        $user = $request->user;
        $start = Carbon::create(2025, 3, 28)->startOfDay();
        $now = Carbon::now();
        $weeks = [];
        $i = 1;

        while ($start->lt($now)) {
            $end = $start->copy()->addDays(6)->endOfDay();
            $total = Score::where('user_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('score');

            $ranks = Score::select('user_id', DB::raw('SUM(score) as total'))
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->get();

            $rank = $total > 0 ? $ranks->search(fn($r) => $r->user_id == $user->id) + 1 : null;

            $weeks[] = [
                'weekNo' => $i,
                'rank' => $rank,
                'totalScore' => $total
            ];
            $i++;
            $start->addDays(7);
        }

        return response()->json(['success' => true, 'weeks' => $weeks]);
    }
}

