<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification implements ShouldQueue
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
        $daysOverdue = now()->diffInDays($this->task->due_date);

        $priorityLabel = match($this->task->priority) {
            Task::PRIORITY_LOW => 'Low',
            Task::PRIORITY_MEDIUM => 'Medium',
            Task::PRIORITY_HIGH => 'High',
            default => 'Normal',
        };

        return (new MailMessage)
            ->subject("⚠️ OVERDUE: Work Order #{$this->task->id}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("**This work order is overdue by {$daysOverdue} day(s)!**")
            ->line("**Work Order Details:**")
            ->line("- **WO#:** {$this->task->id}")
            ->line("- **Equipment:** " . ($this->task->equipment?->name ?? 'N/A'))
            ->line("- **Type:** " . ($this->task->maintCategory?->name ?? 'N/A'))
            ->line("- **Priority:** {$priorityLabel}")
            ->line("- **Due Date:** " . ($this->task->due_date?->format('M j, Y') ?? 'Not set'))
            ->line("- **Assigned To:** " . ($this->task->assignedUser?->name ?? 'Unassigned'))
            ->action('View Work Order', url("/panels/tasks/{$this->task->id}"))
            ->line('Please complete this work order as soon as possible.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => "⚠️ WO #{$this->task->id} is overdue!",
            'equipment' => $this->task->equipment?->name,
            'due_date' => $this->task->due_date?->format('Y-m-d'),
            'days_overdue' => now()->diffInDays($this->task->due_date),
        ];
    }
}
