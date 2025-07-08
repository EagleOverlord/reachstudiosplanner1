<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        return view('schedule.create', compact('user'));
    }

    public function checkOfficeAccess(Request $request)
    {
        $date = $request->input('date');
        $user = Auth::user();
        
        if (!$date) {
            return response()->json(['hasAccess' => false, 'message' => 'Date is required']);
        }

        // Check if user has keys
        if ($user->hasKeys()) {
            return response()->json(['hasAccess' => true, 'message' => 'You have office keys']);
        }

        // Check if someone with keys is already scheduled for office work on this date
        $usersWithKeysInOffice = Shift::whereDate('start_time', $date)
            ->where('location', 'office')
            ->where('type', 'work')
            ->whereHas('user', function($query) {
                $query->where('keys_status', 'yes');
            })
            ->with('user:id,name,keys_status')
            ->get();

        if ($usersWithKeysInOffice->count() > 0) {
            $names = $usersWithKeysInOffice->pluck('user.name')->toArray();
            return response()->json([
                'hasAccess' => true, 
                'message' => 'Office access available - ' . implode(', ', $names) . ' will be there with keys',
                'keyHolders' => $names
            ]);
        }

        return response()->json([
            'hasAccess' => false, 
            'message' => 'No one with keys is scheduled for office work on this date'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date',
            'end_time' => 'required|date_format:H:i',
            'location' => 'required|in:home,office,meeting',
            'type' => 'required|in:work,holiday,meeting',
        ]);

        $user = Auth::user();
        
        // Combine date and time for start and end
        $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        $endDateTime = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);
        
        // Validate that end is after start
        if ($endDateTime <= $startDateTime) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        $durationHours = $endDateTime->diffInHours($startDateTime, true);

        // Check for warnings
        $warnings = [];
        
        // Only check duration for work shifts
        if ($validated['type'] === 'work' && $durationHours < 8) {
            $warnings[] = "Warning: Your scheduled shift is only {$durationHours} hours, which is less than the standard 8-hour workday.";
        }

        // Check for office access if location is office and type is work
        if ($validated['location'] === 'office' && $validated['type'] === 'work' && !$user->hasKeys()) {
            $date = $startDateTime->toDateString();
            $usersWithKeysInOffice = Shift::whereDate('start_time', $date)
                ->where('location', 'office')
                ->where('type', 'work')
                ->whereHas('user', function($query) {
                    $query->where('keys_status', 'yes');
                })
                ->with('user:id,name,keys_status')
                ->get();

            if ($usersWithKeysInOffice->count() === 0) {
                $warnings[] = "Warning: No one with keys is scheduled for office work on this date. You may not be able to access the building.";
            }
        }

        Shift::create([
            'user_id' => Auth::id(),
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'location' => $validated['location'],
            'type' => $validated['type'],
        ]);

        $message = 'Schedule created successfully!';
        if (!empty($warnings)) {
            $message .= ' ' . implode(' ', $warnings);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
    
    public function edit(Shift $shift)
    {
        $user = Auth::user();
        
        // Check if user owns this shift
        if (!$shift->belongsToUser($user)) {
            abort(403, 'You can only edit your own shifts.');
        }
        
        // Check if shift is in the future
        if (!$shift->isUpcoming()) {
            return redirect()->route('dashboard')->with('error', 'You cannot edit shifts that are in the past.');
        }
        
        return view('schedule.create', compact('user', 'shift'));
    }
    
    public function update(Request $request, Shift $shift)
    {
        $user = Auth::user();
        
        // Check if user owns this shift
        if (!$shift->belongsToUser($user)) {
            abort(403, 'You can only edit your own shifts.');
        }
        
        // Check if shift is in the future
        if (!$shift->isUpcoming()) {
            return redirect()->route('dashboard')->with('error', 'You cannot edit shifts that are in the past.');
        }
        
        $validated = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'location' => 'required|in:home,office,meeting',
            'type' => 'required|in:work,holiday,meeting',
        ]);

        $startTime = \Carbon\Carbon::parse($validated['start']);
        $endTime = \Carbon\Carbon::parse($validated['end']);
        $durationHours = $endTime->diffInHours($startTime, true);

        // Check for warnings
        $warnings = [];
        
        // Only check duration for work shifts
        if ($validated['type'] === 'work' && $durationHours < 8) {
            $warnings[] = "Warning: Your scheduled shift is only {$durationHours} hours, which is less than the standard 8-hour workday.";
        }

        // Check for office access if location is office and type is work
        if ($validated['location'] === 'office' && $validated['type'] === 'work' && !$user->hasKeys()) {
            $date = $startTime->toDateString();
            $usersWithKeysInOffice = Shift::whereDate('start_time', $date)
                ->where('location', 'office')
                ->where('type', 'work')
                ->where('id', '!=', $shift->id) // Exclude current shift
                ->whereHas('user', function($query) {
                    $query->where('keys_status', 'yes');
                })
                ->with('user:id,name,keys_status')
                ->get();

            if ($usersWithKeysInOffice->count() === 0) {
                $warnings[] = "Warning: No one with keys is scheduled for office work on this date. You may not be able to access the building.";
            }
        }

        $shift->update([
            'start_time' => str_replace('T', ' ', $validated['start']),
            'end_time' => str_replace('T', ' ', $validated['end']),
            'location' => $validated['location'],
            'type' => $validated['type'],
        ]);

        $message = 'Schedule updated successfully!';
        if (!empty($warnings)) {
            $message .= ' ' . implode(' ', $warnings);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
    
    public function destroy(Shift $shift)
    {
        $user = Auth::user();
        
        // Check if user owns this shift
        if (!$shift->belongsToUser($user)) {
            abort(403, 'You can only delete your own shifts.');
        }
        
        // Check if shift is in the future
        if (!$shift->isUpcoming()) {
            return redirect()->route('dashboard')->with('error', 'You cannot delete shifts that are in the past.');
        }
        
        $shift->delete();
        
        return redirect()->route('dashboard')->with('success', 'Schedule deleted successfully!');
    }
}