<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Notifications\WeeklyMaintenanceScheduleNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendWeeklyMaintenanceSchedule extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:send-weekly-maintenance-schedule';

    /**
     * The console command description.
     */
    protected $description = 'Send weekly maintenance schedule email for the coming week (Mon-Fri)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Fetching maintenance schedule for the coming week...');

        // Calculate coming week dates (Monday to Friday)
        $nextMonday = now()->next('Monday');
        $nextFriday = $nextMonday->copy()->addDays(4);

        $weekStart = $nextMonday->format('d/m/Y');
        $weekEnd = $nextFriday->format('d/m/Y');

        $this->info("Date range: {$weekStart} - {$weekEnd}");

        // Get tasks scheduled for the coming week (Monday to Friday)
        $tasks = Task::with(['equipment', 'maintCategory', 'assignedUser'])
            ->whereBetween('due_date', [$nextMonday->startOfDay(), $nextFriday->endOfDay()])
            ->where('status', '!=', Task::STATUS_CLOSED)
            ->orderBy('due_date')
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No maintenance tasks scheduled for the coming week.');
            return self::SUCCESS;
        }

        $this->info("Found {$tasks->count()} tasks scheduled for the coming week.");

        // Get recipients
        $customers = User::whereHas('role', function ($query) {
            $query->where('name', 'Customer');
        })->get();

        $managers = User::whereHas('role', function ($query) {
            $query->where('name', 'Manager');
        })->get();

        $supervisors = User::whereHas('role', function ($query) {
            $query->where('name', 'Supervisor');
        })->get();

        // Send to all customers
        if ($customers->isNotEmpty()) {
            $this->info("Sending to {$customers->count()} Customer(s)...");
            Notification::send(
                $customers,
                new WeeklyMaintenanceScheduleNotification($tasks, $weekStart, $weekEnd)
            );
        }

        // CC to managers
        if ($managers->isNotEmpty()) {
            $this->info("CC to {$managers->count()} Manager(s)...");
            Notification::send(
                $managers,
                new WeeklyMaintenanceScheduleNotification($tasks, $weekStart, $weekEnd)
            );
        }

        // CC to supervisors
        if ($supervisors->isNotEmpty()) {
            $this->info("CC to {$supervisors->count()} Supervisor(s)...");
            Notification::send(
                $supervisors,
                new WeeklyMaintenanceScheduleNotification($tasks, $weekStart, $weekEnd)
            );
        }

        $this->info('Weekly maintenance schedule sent successfully!');

        return self::SUCCESS;
    }
}
