<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Shift;
use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckMissingHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hours:check-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for users who haven\'t logged their hours for the current day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $usersWithoutHours = collect();

        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Check if user has logged any shifts for today
            $hasShiftToday = Shift::where('user_id', $user->id)
                ->whereDate('start_time', $today)
                ->exists();

            if (!$hasShiftToday) {
                $usersWithoutHours->push($user);
            }
        }

        // Create notifications for users without hours
        foreach ($usersWithoutHours as $user) {
            // Check if we already sent a notification today for this user
            $existingNotification = Notification::where('type', 'missing_hours')
                ->where('data->user_id', $user->id)
                ->whereDate('created_at', $today)
                ->exists();

            if (!$existingNotification) {
                Notification::createUserNotification(
                    'missing_hours',
                    'Missing Hours Alert',
                    "User '{$user->name}' has not logged their hours for " . $today->format('Y-m-d') . ".",
                    [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'date' => $today->format('Y-m-d'),
                        'team' => $user->team,
                    ]
                );
            }
        }

        $count = $usersWithoutHours->count();
        $this->info("Checked {$users->count()} users. Found {$count} users without hours for today.");

        return Command::SUCCESS;
    }
}
