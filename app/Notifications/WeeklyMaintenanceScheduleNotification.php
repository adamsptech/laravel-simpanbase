<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class WeeklyMaintenanceScheduleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Collection $tasks;
    protected string $weekStart;
    protected string $weekEnd;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $tasks, string $weekStart, string $weekEnd)
    {
        $this->tasks = $tasks;
        $this->weekStart = $weekStart;
        $this->weekEnd = $weekEnd;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject("Weekly Maintenance Schedule: {$this->weekStart} - {$this->weekEnd}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Here is the scheduled maintenance for the coming week ({$this->weekStart} to {$this->weekEnd}):")
            ->line('');

        // Build HTML table
        $tableHtml = '<table style="width:100%; border-collapse: collapse; margin: 20px 0;">';
        $tableHtml .= '<thead><tr style="background-color: #f3f4f6;">';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Equipment</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Serial Number</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Maint. Type</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Title</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Date</th>';
        $tableHtml .= '</tr></thead><tbody>';

        foreach ($this->tasks as $task) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($task->equipment?->name ?? 'N/A') . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($task->equipment?->serial_number ?? 'N/A') . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($task->maintCategory?->name ?? 'N/A') . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($task->title ?? 'Work Order #' . $task->id) . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($task->due_date?->format('d/m/Y') ?? 'N/A') . '</td>';
            $tableHtml .= '</tr>';
        }

        $tableHtml .= '</tbody></table>';

        return $mailMessage
            ->line(new \Illuminate\Support\HtmlString($tableHtml))
            ->line('')
            ->line("Total scheduled maintenance tasks: {$this->tasks->count()}")
            ->action('View All Work Orders', url('/panels/tasks'))
            ->line('Please ensure all preparations are made for the scheduled maintenance.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Weekly Maintenance Schedule: {$this->weekStart} - {$this->weekEnd}",
            'message' => "There are {$this->tasks->count()} maintenance tasks scheduled for the coming week.",
            'task_count' => $this->tasks->count(),
            'week_start' => $this->weekStart,
            'week_end' => $this->weekEnd,
            'task_ids' => $this->tasks->pluck('id')->toArray(),
        ];
    }
}
