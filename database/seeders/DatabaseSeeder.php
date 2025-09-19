<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\DebugUserSeeder; // Import the DebugUserSeeder
use Database\Seeders\ShiftSeeder; // Import the ShiftSeeder
use Database\Seeders\TeamsSeeder; // Import the TeamsSeeder

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.§
     */
    public function run(): void
    {
        // Call the seeders in order
        $this->call([
            TeamsSeeder::class, // Add teams first since users might reference them
            DebugUserSeeder::class,
            ShiftSeeder::class,
        ]);
    }
}