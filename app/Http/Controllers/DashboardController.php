<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $shifts = Shift::with('user')->get()->map(function ($shift) use ($user) {
            // Generate title based on type
            $title = match ($shift->type ?? 'work') {
                'holiday' => $shift->user->name . ' - Holiday',
                'meeting' => $shift->user->name . ' - Meeting',
                'work' => $shift->user->name . ' - ' . ucfirst($shift->location),
                default => $shift->user->name . ' - ' . ucfirst($shift->location),
            };

            return [
                'id' => $shift->id,
                'title' => $title,
                'start' => $shift->start_time->format('Y-m-d\TH:i:s'),
                'end' => $shift->end_time->format('Y-m-d\TH:i:s'),
                'extendedProps' => [
                    'location' => $shift->location,
                    'type' => $shift->type ?? 'work',
                    'has_key' => $shift->user->keys_status === 'yes',
                    'user_id' => $shift->user_id,
                    'is_own_shift' => $shift->user_id === $user->id,
                    'is_upcoming' => $shift->isUpcoming(),
                    'is_editable' => $shift->user_id === $user->id && $shift->isUpcoming(),
                ],
                'backgroundColor' => match ($shift->type ?? 'work') {
                    'holiday' => '#FF5722',  // Red for holidays
                    'meeting' => '#9C27B0',  // Purple for meetings
                    'work' => match ($shift->location) {
                        'office' => '#4CAF50',  // Green for office work
                        'home' => '#2196F3',    // Blue for home work
                        'meeting' => '#9C27B0', // Purple for meeting location
                        default => '#9E9E9E',
                    },
                    default => '#9E9E9E',
                },
            ];
        });

        return view('dashboard', compact('shifts'));
    }
}