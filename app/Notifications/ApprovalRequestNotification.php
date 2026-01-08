<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $approverRole = 'Supervisor'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $priorityLabel = match($this->task->priority) {
            Task::PRIORITY_LOW => 'Low',
            Task::PRIORITY_MEDIUM => 'Medium',
            Task::PRIORITY_HIGH => 'High',
            default => 'Normal',
        };

        return (new MailMessage)
            ->subject("Approval Required: Work Order #{$this->task->id}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A work order requires your approval as **{$this->approverRole}**.")
            ->line("**Work Order Details:**")
            ->line("- **WO#:** {$this->task->id}")
            ->line("- **Equipment:** " . ($this->task->equipment?->name ?? 'N/A'))
            ->line("- **Type:** " . ($this->task->maintCategory?->name ?? 'N/A'))
            ->line("- **Priority:** {$priorityLabel}")
            ->line("- **Submitted By:** " . ($this->task->assignedUser?->name ?? 'Unknown'))
            ->action('Review & Approve', url("/panels/tasks/{$this->task->id}"))
            ->line('Please review and approve or reject this work order.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => "Approval needed for WO #{$this->task->id}",
            'equipment' => $this->task->equipment?->name,
            'approver_role' => $this->approverRole,
        ];
    }
}
