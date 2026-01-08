<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Collection $parts;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $parts)
    {
        $this->parts = $parts;
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
            ->subject('Low Stock Alert - Parts Below Minimum Quantity')
            ->greeting("Hello {$notifiable->name},")
            ->line('The following spare parts are below their minimum stock levels:')
            ->line('');

        // Build HTML table
        $tableHtml = '<table style="width:100%; border-collapse: collapse; margin: 20px 0;">';
        $tableHtml .= '<thead><tr style="background-color: #fef2f2;">';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Part Number</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Description</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: right;">Current Stock</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: right;">Min. Quantity</th>';
        $tableHtml .= '<th style="border: 1px solid #e5e7eb; padding: 10px; text-align: left;">Supplier</th>';
        $tableHtml .= '</tr></thead><tbody>';

        // Sort by urgency (biggest gap first)
        $sortedParts = $this->parts->sortBy(function ($part) {
            return $part->quantity - $part->min_quantity;
        });

        foreach ($sortedParts as $part) {
            $shortage = $part->min_quantity - $part->quantity;
            $rowStyle = $part->quantity == 0 ? 'background-color: #fee2e2;' : 'background-color: #fef9c3;';
            
            $tableHtml .= '<tr style="' . $rowStyle . '">';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">' . $part->part_number . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($part->description ?? 'N/A') . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px; text-align: right; font-weight: bold; color: #dc2626;">' . $part->quantity . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px; text-align: right;">' . $part->min_quantity . '</td>';
            $tableHtml .= '<td style="border: 1px solid #e5e7eb; padding: 10px;">' . ($part->supplier?->name ?? 'N/A') . '</td>';
            $tableHtml .= '</tr>';
        }

        $tableHtml .= '</tbody></table>';

        return $mailMessage
            ->line(new \Illuminate\Support\HtmlString($tableHtml))
            ->line('')
            ->line("Total parts below minimum: {$this->parts->count()}")
            ->action('View Part Stocks', url('/panels/part-stocks'))
            ->line('Please arrange procurement or restocking as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Low Stock Alert',
            'message' => "{$this->parts->count()} spare part(s) are below minimum stock levels.",
            'parts_count' => $this->parts->count(),
            'part_ids' => $this->parts->pluck('id')->toArray(),
        ];
    }
}
