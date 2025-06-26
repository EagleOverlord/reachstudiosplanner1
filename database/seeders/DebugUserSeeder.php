<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DebugUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the debug admin user
        User::create([
            'name' => 'Debug Admin',
            'email' => 'debug@example.com',
            'password' => Hash::make('debug123'),
            'team' => 'slt',
            'admin_status' => 'yes',
            'keys_status' => 'yes',
            'email_verified_at' => now(),
        ]);

        // Create additional fake users
        User::create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'password' => Hash::make('password123'),
            'team' => 'mobile',
            'admin_status' => 'no',
            'keys_status' => 'yes',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'password' => Hash::make('password123'),
            'team' => 'front_end',
            'admin_status' => 'no',
            'keys_status' => 'no',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Carol Williams',
            'email' => 'carol@example.com',
            'password' => Hash::make('password123'),
            'team' => 'back_end',
            'admin_status' => 'no',
            'keys_status' => 'yes',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'David Brown',
            'email' => 'david@example.com',
            'password' => Hash::make('password123'),
            'team' => 'design',
            'admin_status' => 'no',
            'keys_status' => 'yes',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Eva Davis',
            'email' => 'eva@example.com',
            'password' => Hash::make('password123'),
            'team' => 'e_commerce',
            'admin_status' => 'no',
            'keys_status' => 'no',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Frank Wilson',
            'email' => 'frank@example.com',
            'password' => Hash::make('password123'),
            'team' => 'bdm',
            'admin_status' => 'no',
            'keys_status' => 'yes',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Grace Miller',
            'email' => 'grace@example.com',
            'password' => Hash::make('password123'),
            'team' => 'mobile',
            'admin_status' => 'no',
            'keys_status' => 'no',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Henry Moore',
            'email' => 'henry@example.com',
            'password' => Hash::make('password123'),
            'team' => 'front_end',
            'admin_status' => 'no',
            'keys_status' => 'yes',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Isabella Taylor',
            'email' => 'isabella@example.com',
            'password' => Hash::make('password123'),
            'team' => 'back_end',
            'admin_status' => 'no',
            'keys_status' => 'no',
            'email_verified_at' => now(),
        ]);
    }
}