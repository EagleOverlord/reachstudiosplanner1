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
        // Call the DebugUserSeeder and ShiftSeeder
        $this->call([
            DebugUserSeeder::class,
            ShiftSeeder::class, // Add the ShiftSeeder here
        ]);
    }
}