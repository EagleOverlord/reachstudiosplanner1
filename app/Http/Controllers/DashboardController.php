<?php

namespace App\Http\Controllers;

use App\Models\Shift;

class DashboardController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('user')->get()->map(function ($shift) {
            return [
                'title' => $shift->user->name . ' - ' . ucfirst($shift->location),
                'start' => $shift->start_time,
                'end' => $shift->end_time,
                'backgroundColor' => match ($shift->location) {
                    'office' => '#4CAF50',
                    'home' => '#2196F3',
                    'holiday' => '#FF9800',
                    default => '#9E9E9E',
                },
                'has_key' => $shift->user->keys_status === 'yes',
            ];
        });

        return view('dashboard', compact('shifts'));
    }
}