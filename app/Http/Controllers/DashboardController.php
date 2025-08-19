<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $teams = Team::getTeamsArray(); // Get all teams for the legend
        
        $shifts = Shift::with('user')->get()->map(function ($shift) use ($user) {
            $userTeam = $shift->user->team;
            $teamName = $userTeam ? Team::getTeamName($userTeam) : 'No Team';
            
            return [
                'id' => $shift->id,
                'title' => $shift->user->name . ' - ' . ucfirst($shift->location) . ' (' . $teamName . ')',
                'start' => $shift->start_time->format('Y-m-d\TH:i:s'),
                'end' => $shift->end_time->format('Y-m-d\TH:i:s'),
                'extendedProps' => [
                    'location' => $shift->location,
                    'has_key' => $shift->user->keys_status === 'yes',
                    'user_id' => $shift->user_id,
                    'user_team' => $userTeam,
                    'team_name' => $teamName,
                    'is_own_shift' => $shift->user_id === $user->id,
                    'is_upcoming' => $shift->isUpcoming(),
                    'is_editable' => $shift->user_id === $user->id && $shift->isUpcoming(),
                ],
                'backgroundColor' => match ($shift->location) {
                    'office' => '#4CAF50',
                    'home' => '#2196F3',
                    'holiday' => '#FF9800',
                    default => '#9E9E9E',
                },
            ];
        });

        return view('dashboard', compact('shifts', 'teams'));
    }
}