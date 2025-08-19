<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\DebugUserSeeder; // Import the DebugUserSeeder
use Database\Seeders\ShiftSeeder; // Import the ShiftSeeder

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.§
     */
    public function run(): void
    {
        if (app()->environment('local')) {
            $this->call(DebugUserSeeder::class);
        }

        $this->call([
            ShiftSeeder::class, // Add the ShiftSeeder here
        ]);
    }
}