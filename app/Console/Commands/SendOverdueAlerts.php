<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Console\Command;

class SendOverdueAlerts extends Command
{
    protected $signature = 'app:send-overdue-alerts';

    protected $description = 'Send email notifications for overdue work orders';

    public function handle(): int
    {
        $this->info('Checking for overdue work orders...');

        $overdueTasks = Task::where('status', '!=', Task::STATUS_CLOSED)
            ->where('due_date', '<', now())
            ->with(['equipment', 'maintCategory', 'assignedUser', 'supervisor'])
            ->get();

        if ($overdueTasks->isEmpty()) {
            $this->info('No overdue work orders found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$overdueTasks->count()} overdue work orders.");

        $notificationsSent = 0;

        foreach ($overdueTasks as $task) {
            // Notify assigned user
            if ($task->assignedUser) {
                $task->assignedUser->notify(new TaskOverdueNotification($task));
                $notificationsSent++;
                $this->line(" - Notified {$task->assignedUser->name} for WO #{$task->id}");
            }

            // Notify supervisor
            if ($task->supervisor && $task->supervisor->id !== $task->assignedUser?->id) {
                $task->supervisor->notify(new TaskOverdueNotification($task));
                $notificationsSent++;
                $this->line(" - Notified supervisor {$task->supervisor->name} for WO #{$task->id}");
            }

            // Notify planners for high priority tasks
            if ($task->priority === Task::PRIORITY_HIGH) {
                $planners = User::whereHas('role', function ($q) {
                    $q->where('name', 'Planner');
                })->get();

                foreach ($planners as $planner) {
                    if ($planner->id !== $task->assigned_user_id && $planner->id !== $task->supervisor_id) {
                        $planner->notify(new TaskOverdueNotification($task));
                        $notificationsSent++;
                    }
                }
            }
        }

        $this->info("Sent {$notificationsSent} overdue notifications.");

        return Command::SUCCESS;
    }
}
