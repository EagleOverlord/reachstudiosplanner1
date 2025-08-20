<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        // Clear any existing shifts first
        Shift::truncate();

        // Get or create users to assign shifts
        $users = User::all();
        if ($users->isEmpty()) {
            User::factory()->count(5)->create();
            $users = User::all();
        }

        // Pick a special day to concentrate the shifts — e.g. next Monday
        $day = Carbon::now()->startOfWeek()->addDays(1)->setTime(0, 0); // Monday

        // Generate 10–15 shifts on that day
        foreach (range(1, rand(10, 15)) as $i) {
            $startHour = rand(6, 17); // Between 6AM and 5PM
            $startTime = $day->copy()->addHours($startHour)->addMinutes([0, 15, 30, 45][rand(0, 3)]);
            $endTime = $startTime->copy()->addHours(rand(1, 3));

            // Randomly choose type and appropriate location
            $type = collect(['work', 'holiday', 'meeting'])->random();
            $location = match($type) {
                'holiday' => 'home', // Holidays default to home
                'meeting' => collect(['office', 'meeting'])->random(), // Meetings can be in office or meeting room
                'work' => collect(['office', 'home'])->random(), // Work can be office or home
            };

            Shift::create([
                'user_id' => $users->random()->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => $location,
                'type' => $type,
            ]);
        }

        // Optionally: add a few random shifts throughout the rest of the week
        foreach (range(1, 5) as $i) {
            $startTime = Carbon::now()
                ->addDays(rand(2, 7))
                ->setTime(rand(7, 16), 0);
            $endTime = $startTime->copy()->addHours(rand(1, 5));
            
            // Randomly choose type and appropriate location
            $type = collect(['work', 'holiday', 'meeting'])->random();
            $location = match($type) {
                'holiday' => 'home', // Holidays default to home
                'meeting' => collect(['office', 'meeting'])->random(), // Meetings can be in office or meeting room
                'work' => collect(['office', 'home'])->random(), // Work can be office or home
            };
            
            Shift::create([
                'user_id' => $users->random()->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => $location,
                'type' => $type,
            ]);
        }
    }
}