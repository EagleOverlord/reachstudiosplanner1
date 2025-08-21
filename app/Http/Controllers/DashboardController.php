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

        // Pre-load all teams into a key-value array for quick lookup
        $allTeams = Team::pluck('name', 'key');

        $shifts = Shift::with('user')->get()->map(function ($shift) use ($user, $allTeams) {
            $userTeam = $shift->user->team;
            // Look up the team name from the pre-loaded array instead of querying the database
            $teamName = $allTeams[$userTeam] ?? 'No Team';
            
            $userName = $shift->user->name;
            $location = $shift->location;

            return [
                'id' => $shift->id,
                'title' => htmlspecialchars($userName) . ' - ' . ucfirst($location) . ' (' . htmlspecialchars($teamName) . ')',
                'start' => $shift->start_time->format('Y-m-d\TH:i:s'),
                'end' => $shift->end_time->format('Y-m-d\TH:i:s'),
                'extendedProps' => [
                    'name' => $userName,
                    'location_display' => ucfirst($location),
                    'team_name_display' => $teamName,
                    'location' => $location,
                    'type' => $shift->type ?? 'work', // Add the missing type field
                    'has_key' => $shift->user->keys_status === 'yes',
                    'user_id' => $shift->user_id,
                    'user_team' => $userTeam,
                    'team_name' => $teamName,
                    'is_own_shift' => $shift->user_id === $user->id,
                    'is_upcoming' => $shift->isUpcoming(),
                    'is_editable' => $shift->user_id === $user->id && $shift->isUpcoming(),
                ],
                'backgroundColor' => $shift->getEventColor(),
            ];
        });

        return view('dashboard', compact('shifts', 'teams'));
    }
}