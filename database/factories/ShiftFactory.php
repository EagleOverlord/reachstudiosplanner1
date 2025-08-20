<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 week');
        $endTime = $this->faker->dateTimeBetween($startTime, $startTime->format('Y-m-d H:i:s') . ' +3 hours');
        
        // Randomly choose type and appropriate location
        $type = $this->faker->randomElement(['work', 'holiday', 'meeting']);
        $location = match($type) {
            'holiday' => 'home', // Holidays default to home
            'meeting' => $this->faker->randomElement(['office', 'meeting']), // Meetings can be in office or meeting room
            'work' => $this->faker->randomElement(['office', 'home']), // Work can be office or home
        };
        
        return [
            'user_id' => \App\Models\User::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $location,
            'type' => $type,
        ];
    }
}
