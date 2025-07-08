<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Test command to manually trigger missing hours check
Artisan::command('test:missing-hours', function () {
    $this->call('hours:check-missing');
})->purpose('Manually trigger missing hours check for testing');

// Schedule the missing hours check to run daily at 6 PM
Schedule::command('hours:check-missing')->dailyAt('18:00');
