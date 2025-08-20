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
        $defaultDate = $this->getNextAvailableWeekday($user);
        return view('schedule.create', compact('user', 'defaultDate'));
    }
    
    /**
     * Get the next available weekday (Monday-Friday) for the user
     */
    private function getNextAvailableWeekday($user)
    {
        $currentDate = Carbon::today();
        
        // Start checking from today
        $checkDate = $currentDate->copy();
        
        // Loop through dates until we find an available weekday
        for ($i = 0; $i < 30; $i++) { // Check up to 30 days ahead
            // Skip weekends
            if ($checkDate->isWeekday()) {
                // Check if user has any shifts on this date
                $hasShift = Shift::where('user_id', $user->id)
                    ->whereDate('start_time', $checkDate)
                    ->exists();
                
                if (!$hasShift) {
                    return $checkDate->format('Y-m-d');
                }
            }
            
            $checkDate->addDay();
        }
        
        // If no available date found in 30 days, return tomorrow or next Monday
        $fallbackDate = Carbon::tomorrow();
        while (!$fallbackDate->isWeekday()) {
            $fallbackDate->addDay();
        }
        
        return $fallbackDate->format('Y-m-d');
    }

    public function checkOfficeAccess(Request $request)
    {
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d'
        ]);
        
        $date = $request->input('date');
        $user = Auth::user();

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
            'consecutive_days' => 'nullable|integer|min:1|max:10',
        ]);

        $user = Auth::user();
        $consecutiveDays = $validated['consecutive_days'] ?? 1;
        
        // Combine date and time for start and end
        $startDateTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        $endDateTime = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);
        
        // Validate that end is after start
        if ($endDateTime <= $startDateTime) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        $durationHours = $endDateTime->diffInHours($startDateTime, true);
        $warnings = [];
        $createdShifts = [];
        
        // Create shifts for consecutive days
        for ($day = 0; $day < $consecutiveDays; $day++) {
            $currentStartDate = $startDateTime->copy()->addDays($day);
            $currentEndDate = $endDateTime->copy()->addDays($day);
            
            // Skip weekends for work and holiday shifts
            if (($validated['type'] === 'work' || $validated['type'] === 'holiday') && !$currentStartDate->isWeekday()) {
                // Add extra days to compensate for skipped weekends
                $consecutiveDays++;
                continue;
            }
            
            // Check if user already has a shift on this date
            $existingShift = Shift::where('user_id', Auth::id())
                ->whereDate('start_time', $currentStartDate->toDateString())
                ->first();
                
            if ($existingShift) {
                $warnings[] = "Skipped {$currentStartDate->format('M j, Y')} - you already have a shift scheduled.";
                continue;
            }

            // Check for duration warnings (only for work shifts)
            if ($validated['type'] === 'work' && $durationHours < 8) {
                $warnings[] = "Warning: Your scheduled shifts are only {$durationHours} hours each, which is less than the standard 8-hour workday.";
            }

            // Check for office access if location is office and type is work
            if ($validated['location'] === 'office' && $validated['type'] === 'work' && !$user->hasKeys()) {
                $date = $currentStartDate->toDateString();
                $usersWithKeysInOffice = Shift::whereDate('start_time', $date)
                    ->where('location', 'office')
                    ->where('type', 'work')
                    ->whereHas('user', function($query) {
                        $query->where('keys_status', 'yes');
                    })
                    ->with('user:id,name,keys_status')
                    ->get();

                if ($usersWithKeysInOffice->count() === 0) {
                    $warnings[] = "Warning: No one with keys is scheduled for office work on {$currentStartDate->format('M j, Y')}. You may not be able to access the building.";
                }
            }

            // Create the shift
            $shift = Shift::create([
                'user_id' => Auth::id(),
                'start_time' => $currentStartDate,
                'end_time' => $currentEndDate,
                'location' => $validated['location'],
                'type' => $validated['type'],
            ]);
            
            $createdShifts[] = $currentStartDate->format('M j, Y');
        }

        $message = count($createdShifts) > 1 
            ? 'Schedules created successfully for: ' . implode(', ', $createdShifts) . '!'
            : 'Schedule created successfully!';
            
        if (!empty($warnings)) {
            $message .= ' ' . implode(' ', array_unique($warnings));
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
        
        $defaultDate = $this->getNextAvailableWeekday($user);
        return view('schedule.create', compact('user', 'shift', 'defaultDate'));
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
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date',
            'end_time' => 'required|date_format:H:i',
            'location' => 'required|in:home,office,meeting',
            'type' => 'required|in:work,holiday,meeting',
        ]);

        // Combine date and time for start and end
        $startTime = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        $endTime = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);
        
        // Validate that end is after start
        if ($endTime <= $startTime) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }
        
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
            'start_time' => $startTime,
            'end_time' => $endTime,
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