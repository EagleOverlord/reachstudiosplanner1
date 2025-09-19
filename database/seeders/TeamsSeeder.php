<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            [
                'key' => 'slt',
                'name' => 'Senior Leadership Team',
                'description' => 'Executive and senior management team responsible for strategic decisions',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mobile',
                'name' => 'Mobile Development',
                'description' => 'iOS and Android mobile application development team',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'front_end',
                'name' => 'Frontend Development',
                'description' => 'Web frontend development team specializing in user interfaces',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'back_end',
                'name' => 'Backend Development',
                'description' => 'Server-side development and API team',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'design',
                'name' => 'Design Team',
                'description' => 'UI/UX design and creative team',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'e_commerce',
                'name' => 'E-Commerce',
                'description' => 'Online retail and e-commerce solutions team',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bdm',
                'name' => 'Business Development & Marketing',
                'description' => 'Business development, marketing, and client relations team',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert teams into the database
        DB::table('teams')->insert($teams);
    }
}