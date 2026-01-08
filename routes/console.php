<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
|
| Here you can define your scheduled artisan commands. They will run
| automatically when the Laravel scheduler is invoked.
|
| To activate the scheduler, add this cron entry to your server:
| * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Send overdue work order alerts daily at 8:00 AM
Schedule::command('app:send-overdue-alerts')->dailyAt('08:00');

// Send weekly maintenance schedule every Thursday at 8:00 AM
Schedule::command('app:send-weekly-maintenance-schedule')->weeklyOn(4, '08:00');

// Check for equipment with warranties expiring within 60 days - daily at 9:00 AM
Schedule::command('app:check-warranty-expiration')->dailyAt('09:00');

// Send low stock alerts every Monday at 8:00 AM
Schedule::command('app:send-low-stock-alert')->weeklyOn(1, '08:00');

// Optional: Generate monthly OEE data on the 1st of each month at 1:00 AM
// Schedule::command('app:generate-monthly-oee')->monthlyOn(1, '01:00');


