<?php

namespace App\Notifications;

use App\Models\Equipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class WarrantyExpirationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Collection $equipment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $equipment)
    {
        $this->equipment = $equipment;
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
            ->subject('Equipment Warranty Expiration Alert - 60 Days Notice')
            ->greeting("Hello {$notifiable->name},")
            ->line('The following equipment has warranties expiring within the next 60 days:')
            ->line('');

        // Build HTML table
        $tableHtml = '<table style="width:100%; border-collapse: collapse; margin: 20px 0;">';
        $tableHtml .= '<thead><tr style="background-color: #fef2f2;">';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Equipment</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Serial Number</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Location</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Warranty Expiry</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Days Left</th>';
        $tableHtml .= '</tr></thead><tbody>';

        foreach ($this->equipment as $item) {
            $daysLeft = now()->diffInDays($item->warranty_expiry_date, false);
            $rowStyle = $daysLeft <= 30 ? 'background-color: #fef2f2;' : '';
            
            $tableHtml .= '<tr style="' . $rowStyle . '">';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . $item->name . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($item->serial_number ?? 'N/A') . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($item->sublocation?->name ?? 'N/A') . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . $item->warranty_expiry_date->format('d/m/Y') . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">' . $daysLeft . ' days</td>';
            $tableHtml .= '</tr>';
        }

        $tableHtml .= '</tbody></table>';

        return $mailMessage
            ->line(new \Illuminate\Support\HtmlString($tableHtml))
            ->line('')
            ->line("Total equipment with expiring warranties: {$this->equipment->count()}")
            ->action('View Equipment', url('/panels/equipment'))
            ->line('Please take action to renew or update warranties before expiration.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Equipment Warranty Expiration Alert',
            'message' => "{$this->equipment->count()} equipment item(s) have warranties expiring within 60 days.",
            'equipment_count' => $this->equipment->count(),
            'equipment_ids' => $this->equipment->pluck('id')->toArray(),
        ];
    }
}
