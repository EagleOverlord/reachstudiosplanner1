<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $user = Auth::user();
        $isAdmin = method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        $next7 = $now->copy()->addDays(7);
        $next30 = $now->copy()->addDays(30);

        // Cross‑DB hours expression (sqlite/pgsql/mysql)
        $driver = DB::connection()->getDriverName();
        $hoursExpr = match ($driver) {
            'sqlite' => "SUM((julianday(end_time) - julianday(start_time)) * 24.0)",
            'pgsql'  => "SUM(EXTRACT(EPOCH FROM (end_time - start_time)) / 3600.0)",
            default  => "SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) / 60.0",
        };

        if ($isAdmin) {
            // Global stats
            $totalShifts = Shift::count();
            $totalHours = (float) (Shift::select(DB::raw($hoursExpr . ' as h'))->value('h') ?? 0);

            $byType = Shift::select('type', DB::raw('COUNT(*) as c'))
                ->groupBy('type')->pluck('c', 'type')->toArray();

            $byLocation = Shift::select('location', DB::raw('COUNT(*) as c'))
                ->groupBy('location')->pluck('c', 'location')->toArray();

            $upcomingWeekShifts = Shift::whereBetween('start_time', [$now, $next7])->count();

            $next30ByLocation = Shift::whereBetween('start_time', [$now, $next30])
                ->select('location', DB::raw('COUNT(*) as c'))
                ->groupBy('location')->pluck('c', 'location')->toArray();

            // Office access coverage in next 30 days (anyone)
            $officeDays = Shift::whereBetween('start_time', [$now->copy()->startOfDay(), $next30->copy()->endOfDay()])
                ->where('location', 'office')->where('type', 'work')
                ->join('users', 'users.id', '=', 'shifts.user_id')
                ->select(
                    DB::raw('DATE(start_time) as d'),
                    DB::raw("SUM(CASE WHEN users.keys_status = 'yes' THEN 1 ELSE 0 END) as keyholders"),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy(DB::raw('DATE(start_time)'))
                ->get();
            $officeDaysWithKey = $officeDays->where('keyholders', '>', 0)->count();
            $officeDaysWithoutKey = $officeDays->where('keyholders', 0)->count();

            // Per-team shift counts (next 30 days)
            $perTeamNext30 = Shift::whereBetween('start_time', [$now, $next30])
                ->join('users', 'users.id', '=', 'shifts.user_id')
                ->select('users.team', DB::raw('COUNT(*) as c'))
                ->groupBy('users.team')->pluck('c', 'users.team')->toArray();

            // Top users by scheduled hours in next 30 days
            $topUsersNext30 = Shift::whereBetween('start_time', [$now, $next30])
                ->join('users', 'users.id', '=', 'shifts.user_id')
                ->select('users.id', 'users.name', DB::raw($hoursExpr . ' as hours'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('hours')
                ->limit(5)
                ->get();

            // Average daily headcount (next 30 days)
            $dailyCounts = Shift::whereBetween('start_time', [$now->copy()->startOfDay(), $next30->copy()->endOfDay()])
                ->select(DB::raw('DATE(start_time) as d'), DB::raw('COUNT(*) as c'))
                ->groupBy(DB::raw('DATE(start_time)'))->get();
            $avgDailyShifts = $dailyCounts->avg('c') ?? 0;
        } else {
            // User‑scoped stats (no data about others)
            $totalShifts = Shift::where('user_id', $user->id)->count();
            $totalHours = (float) (Shift::where('user_id', $user->id)->select(DB::raw($hoursExpr . ' as h'))->value('h') ?? 0);

            $byType = Shift::where('user_id', $user->id)
                ->select('type', DB::raw('COUNT(*) as c'))
                ->groupBy('type')->pluck('c', 'type')->toArray();

            $byLocation = Shift::where('user_id', $user->id)
                ->select('location', DB::raw('COUNT(*) as c'))
                ->groupBy('location')->pluck('c', 'location')->toArray();

            $upcomingWeekShifts = Shift::where('user_id', $user->id)
                ->whereBetween('start_time', [$now, $next7])->count();

            $next30ByLocation = Shift::where('user_id', $user->id)
                ->whereBetween('start_time', [$now, $next30])
                ->select('location', DB::raw('COUNT(*) as c'))
                ->groupBy('location')->pluck('c', 'location')->toArray();

            // Admin‑only metrics not computed for regular users
            $officeDaysWithKey = null;
            $officeDaysWithoutKey = null;
            $perTeamNext30 = [];
            $topUsersNext30 = collect();
            $avgDailyShifts = null;
        }

        $avgHoursPerShift = $totalShifts > 0 ? round($totalHours / $totalShifts, 2) : 0.0;

        return view('stats.index', [
            'isAdmin' => $isAdmin,
            'totalShifts' => $totalShifts,
            'totalHours' => $totalHours,
            'avgHoursPerShift' => $avgHoursPerShift,
            'byType' => $byType,
            'byLocation' => $byLocation,
            'upcomingWeekShifts' => $upcomingWeekShifts,
            'next30ByLocation' => $next30ByLocation,
            'officeDaysWithKey' => $officeDaysWithKey,
            'officeDaysWithoutKey' => $officeDaysWithoutKey,
            'perTeamNext30' => $perTeamNext30,
            'topUsersNext30' => $topUsersNext30,
            'avgDailyShifts' => $avgDailyShifts,
        ]);
    }
}
