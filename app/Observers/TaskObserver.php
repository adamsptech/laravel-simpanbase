<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\User;
use App\Notifications\ApprovalRequestNotification;
use App\Notifications\TaskAssignedNotification;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // Notify assigned user when task is created with assignment
        if ($task->assigned_to) {
            $task->load(['equipment', 'maintCategory', 'assignedUser']);
            $task->assignedUser?->notify(new TaskAssignedNotification($task));
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $task->load(['equipment', 'maintCategory', 'assignedUser', 'supervisor']);

        // Check if assigned_to changed (task reassigned)
        if ($task->isDirty('assigned_to') && $task->assigned_to) {
            $task->assignedUser?->notify(new TaskAssignedNotification($task));
        }

        // Check if status changed to submitted for approval
        if ($task->isDirty('status')) {
            $this->handleStatusChange($task);
        }
    }

    /**
     * Handle status transitions and send approval notifications
     */
    protected function handleStatusChange(Task $task): void
    {
        switch ($task->status) {
            case Task::STATUS_SUBMITTED_SUPERVISOR:
                // Notify supervisor
                if ($task->supervisor) {
                    $task->supervisor->notify(new ApprovalRequestNotification($task, 'Supervisor'));
                }
                break;

            case Task::STATUS_SUBMITTED_MANAGER:
                // Notify managers (users with role.name = 'Manager')
                $managers = User::whereHas('role', function ($q) {
                    $q->where('name', 'Manager');
                })->get();

                foreach ($managers as $manager) {
                    $manager->notify(new ApprovalRequestNotification($task, 'Manager'));
                }
                break;

            case Task::STATUS_SUBMITTED_CUSTOMER:
                // Notify customers (users with role.name = 'Customer')
                $customers = User::whereHas('role', function ($q) {
                    $q->where('name', 'Customer');
                })->get();

                foreach ($customers as $customer) {
                    $customer->notify(new ApprovalRequestNotification($task, 'Customer'));
                }
                break;
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
