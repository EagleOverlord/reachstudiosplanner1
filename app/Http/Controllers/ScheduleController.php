<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function create()
    {
        return view('schedule.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'location' => 'required|in:home,office',
        ]);

        Shift::create([
            'user_id' => Auth::id(),
            'start_time' => str_replace('T', ' ', $validated['start']),
            'end_time' => str_replace('T', ' ', $validated['end']),
            'location' => $validated['location'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Schedule created successfully!');
    }
}