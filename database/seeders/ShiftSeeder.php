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

            Shift::create([
                'user_id' => $users->random()->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => collect(['office', 'home', 'holiday'])->random(),
            ]);¡
        }

        // Optionally: add a few random shifts throughout the rest of the week
        foreach (range(1, 5) as $i) {
            Shift::create([
                'user_id' => $users->random()->id,
                'start_time' => Carbon::now()
                    ->addDays(rand(2, 7))
                    ->setTime(rand(7, 16), 0),
                'end_time' => Carbon::now()
                    ->addDays(rand(2, 7))
                    ->setTime(rand(17, 22), 0),
                'location' => collect(['office', 'home'])->random(),
            ]);
        }
    }
}