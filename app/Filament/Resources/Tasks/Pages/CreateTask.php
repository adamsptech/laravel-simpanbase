<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Filament\Resources\Pages\CreateRecord;
use App\Models\PeriodPm;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Get the make_recurring flag (not stored in DB, just used for logic)
        $makeRecurring = $this->data['make_recurring'] ?? false;
        $recurrenceDuration = $this->data['recurrence_duration'] ?? 1;

        // If not recurring, create single task
        if (!$makeRecurring || empty($data['period_id']) || empty($data['due_date'])) {
            $data['recurrence_type'] = Task::RECURRENCE_SINGLE;
            return static::getModel()::create($data);
        }

        // Get the period to determine recurrence type
        $period = PeriodPm::find($data['period_id']);
        if (!$period) {
            $data['recurrence_type'] = Task::RECURRENCE_SINGLE;
            return static::getModel()::create($data);
        }

        // Determine recurrence type from period days
        $recurrenceType = match (true) {
            $period->days <= 1 => Task::RECURRENCE_DAILY,
            $period->days <= 14 => Task::RECURRENCE_WEEKLY,
            default => Task::RECURRENCE_MONTHLY,
        };

        // Calculate end date based on duration (years)
        $startDate = Carbon::parse($data['due_date']);
        $endDate = $startDate->copy()->addYears((int) $recurrenceDuration);

        // Create series of tasks
        $baseData = collect($data)
            ->except(['make_recurring', 'recurrence_duration'])
            ->toArray();

        $createdTasks = Task::createSeriesTasks($baseData, $recurrenceType, $startDate, $endDate);

        $count = count($createdTasks);
        
        Notification::make()
            ->success()
            ->title('Recurring Series Created')
            ->body("Created {$count} work orders for {$recurrenceDuration} year(s)")
            ->send();

        // Return the first task as the created record
        return $createdTasks[0] ?? static::getModel()::create($data);
    }
}
