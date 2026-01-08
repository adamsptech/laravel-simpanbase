<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = match($this->task->status) {
            Task::STATUS_OPEN => 'Open',
            Task::STATUS_SUBMITTED_SUPERVISOR => 'Pending Supervisor',
            Task::STATUS_SUBMITTED_MANAGER => 'Pending Manager',
            Task::STATUS_SUBMITTED_CUSTOMER => 'Pending Customer',
            Task::STATUS_CLOSED => 'Closed',
            default => 'Unknown',
        };

        $priorityLabel = match($this->task->priority) {
            Task::PRIORITY_LOW => 'Low',
            Task::PRIORITY_MEDIUM => 'Medium',
            Task::PRIORITY_HIGH => 'High',
            default => 'Normal',
        };

        return (new MailMessage)
            ->subject("Work Order #{$this->task->id} Assigned to You")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new work order has been assigned to you.")
            ->line("**Work Order Details:**")
            ->line("- **WO#:** {$this->task->id}")
            ->line("- **Equipment:** " . ($this->task->equipment?->name ?? 'N/A'))
            ->line("- **Type:** " . ($this->task->maintCategory?->name ?? 'N/A'))
            ->line("- **Priority:** {$priorityLabel}")
            ->line("- **Due Date:** " . ($this->task->due_date?->format('M j, Y') ?? 'Not set'))
            ->line("- **Status:** {$statusLabel}")
            ->action('View Work Order', url("/panels/tasks/{$this->task->id}"))
            ->line('Please complete this work order before the due date.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => "Work Order #{$this->task->id} assigned to you",
            'equipment' => $this->task->equipment?->name,
            'due_date' => $this->task->due_date?->format('Y-m-d'),
        ];
    }
}
