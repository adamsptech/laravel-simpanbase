<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Task extends Model
{
    use Auditable;
    protected $fillable = [
        'location_id',
        'sublocation_id',
        'equipment_id',
        'period_id',
        'type_check_id',
        'maint_category_id',
        'status',
        'priority',
        'assigned_to',
        'supervisor_id',
        'approval1_by',
        'approval1_at',
        'approval2_by',
        'approval2_at',
        'approval3_by',
        'approval3_at',
        'due_date',
        'duration',
        'started_at',
        'ended_at',
        'notes',
        'shift',
        'files',
        // Recurring fields
        'series_id',
        'recurrence_type',
        'recurrence_end_date',
        'is_series_exception',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'approval1_at' => 'datetime',
        'approval2_at' => 'datetime',
        'approval3_at' => 'datetime',
        'recurrence_end_date' => 'date',
        'is_series_exception' => 'boolean',
    ];

    // Status constants
    const STATUS_OPEN = 0;
    const STATUS_SUBMITTED_SUPERVISOR = 1;
    const STATUS_SUBMITTED_MANAGER = 2;
    const STATUS_SUBMITTED_CUSTOMER = 3;
    const STATUS_CLOSED = 4;

    // Priority constants
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    // Recurrence types
    const RECURRENCE_SINGLE = 'single';
    const RECURRENCE_DAILY = 'daily';
    const RECURRENCE_WEEKLY = 'weekly';
    const RECURRENCE_MONTHLY = 'monthly';

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function sublocation(): BelongsTo
    {
        return $this->belongsTo(Sublocation::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PeriodPm::class, 'period_id');
    }

    public function typeCheck(): BelongsTo
    {
        return $this->belongsTo(TypeCheck::class);
    }

    public function maintCategory(): BelongsTo
    {
        return $this->belongsTo(MaintCategory::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function approval1User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval1_by');
    }

    public function approval2User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval2_by');
    }

    public function approval3User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval3_by');
    }

    public function taskDetails(): HasMany
    {
        return $this->hasMany(TaskDetail::class);
    }

    public function cmDetail(): HasOne
    {
        return $this->hasOne(CmDetail::class);
    }

    public function cmDetails(): HasMany
    {
        return $this->hasMany(CmDetail::class);
    }

    public function partUsages(): HasMany
    {
        return $this->hasMany(PartUsage::class);
    }

    // Series relationship - get all tasks in the same series
    public function seriesTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'series_id', 'series_id');
    }

    // Check if task is part of a recurring series
    public function isRecurring(): bool
    {
        return $this->recurrence_type !== self::RECURRENCE_SINGLE && !empty($this->series_id);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'Open',
            self::STATUS_SUBMITTED_SUPERVISOR => 'Pending Supervisor',
            self::STATUS_SUBMITTED_MANAGER => 'Pending Manager',
            self::STATUS_SUBMITTED_CUSTOMER => 'Pending Customer',
            self::STATUS_CLOSED => 'Closed',
            default => 'Unknown',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            default => 'Unknown',
        };
    }

    public function getRecurrenceLabelAttribute(): string
    {
        return match ($this->recurrence_type) {
            self::RECURRENCE_DAILY => 'Daily',
            self::RECURRENCE_WEEKLY => 'Weekly',
            self::RECURRENCE_MONTHLY => 'Monthly',
            default => 'Single',
        };
    }

    // Static methods for series creation
    public static function generateSeriesId(): string
    {
        return 'SERIES-' . Str::random(12) . '-' . time();
    }

    /**
     * Skip weekend dates - move to Monday
     */
    public static function skipWeekend(Carbon $date): Carbon
    {
        if ($date->isSaturday()) {
            return $date->addDays(2);
        }
        if ($date->isSunday()) {
            return $date->addDay();
        }
        return $date;
    }

    /**
     * Create series of recurring tasks
     */
    public static function createSeriesTasks(array $baseData, string $recurrenceType, Carbon $startDate, Carbon $endDate): array
    {
        $seriesId = self::generateSeriesId();
        $createdTasks = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $taskDate = self::skipWeekend($currentDate->copy());
            
            // Only create if within end date after weekend skip
            if ($taskDate->lte($endDate)) {
                $taskData = array_merge($baseData, [
                    'series_id' => $seriesId,
                    'recurrence_type' => $recurrenceType,
                    'recurrence_end_date' => $endDate,
                    'due_date' => $taskDate,
                    'is_series_exception' => false,
                ]);

                $createdTasks[] = self::create($taskData);
            }

            // Move to next occurrence
            $currentDate = match ($recurrenceType) {
                self::RECURRENCE_DAILY => $currentDate->addWeekday(), // Skip weekends
                self::RECURRENCE_WEEKLY => $currentDate->addWeek(),
                self::RECURRENCE_MONTHLY => self::getNextMonthlyDate($currentDate, $startDate),
                default => $currentDate->addYear(), // Should never happen
            };
        }

        return $createdTasks;
    }

    /**
     * Get next monthly date (same day-of-week occurrence)
     * e.g., 3rd Tuesday of January -> 3rd Tuesday of February
     */
    public static function getNextMonthlyDate(Carbon $currentDate, Carbon $originalDate): Carbon
    {
        $dayOfWeek = $originalDate->dayOfWeek;
        $weekOccurrence = (int) ceil($originalDate->day / 7);
        
        $nextMonth = $currentDate->copy()->addMonth()->startOfMonth();
        
        // Find the nth occurrence of the day in the next month
        $targetDate = $nextMonth->copy();
        $occurrence = 0;
        
        while ($targetDate->month === $nextMonth->month) {
            if ($targetDate->dayOfWeek === $dayOfWeek) {
                $occurrence++;
                if ($occurrence === $weekOccurrence) {
                    return $targetDate;
                }
            }
            $targetDate->addDay();
        }
        
        // If 5th occurrence doesn't exist, use last occurrence
        $lastMonth = $nextMonth->copy()->endOfMonth();
        while ($lastMonth->dayOfWeek !== $dayOfWeek) {
            $lastMonth->subDay();
        }
        
        return $lastMonth;
    }

    /**
     * Delete all tasks in a series
     */
    public static function deleteSeriesTasks(string $seriesId): int
    {
        return self::where('series_id', $seriesId)->delete();
    }

    /**
     * Delete this and all future tasks in a series
     */
    public static function deleteThisAndFutureTasks(string $seriesId, $fromDate): int
    {
        // Ensure date is formatted for SQL comparison
        $dateString = $fromDate instanceof Carbon ? $fromDate->format('Y-m-d') : $fromDate;
        
        return self::where('series_id', $seriesId)
            ->whereDate('due_date', '>=', $dateString)
            ->delete();
    }

    /**
     * Update all tasks in a series
     */
    public static function updateSeriesTasks(string $seriesId, array $data): int
    {
        return self::where('series_id', $seriesId)->update($data);
    }

    /**
     * Update this and all future tasks in a series
     */
    public static function updateThisAndFutureTasks(string $seriesId, $fromDate, array $data): int
    {
        // Ensure date is formatted for SQL comparison
        $dateString = $fromDate instanceof Carbon ? $fromDate->format('Y-m-d') : $fromDate;
        
        return self::where('series_id', $seriesId)
            ->whereDate('due_date', '>=', $dateString)
            ->update($data);
    }

    /**
     * Shift all dates in a series by a given number of days
     */
    public static function shiftSeriesDates(string $seriesId, int $dayOffset): int
    {
        if ($dayOffset === 0) {
            return 0;
        }

        $operator = $dayOffset > 0 ? '+' : '-';
        $days = abs($dayOffset);
        
        return self::where('series_id', $seriesId)
            ->update([
                'due_date' => \DB::raw("DATE_ADD(due_date, INTERVAL {$days} DAY)")
            ]);
    }

    /**
     * Shift this and all future dates in a series by a given number of days
     */
    public static function shiftThisAndFutureDates(string $seriesId, $fromDate, int $dayOffset): int
    {
        if ($dayOffset === 0) {
            return 0;
        }

        $dateString = $fromDate instanceof Carbon ? $fromDate->format('Y-m-d') : $fromDate;
        $days = abs($dayOffset);
        $interval = $dayOffset > 0 ? "INTERVAL {$days} DAY" : "INTERVAL -{$days} DAY";
        
        return self::where('series_id', $seriesId)
            ->whereDate('due_date', '>=', $dateString)
            ->update([
                'due_date' => \DB::raw("DATE_ADD(due_date, {$interval})")
            ]);
    }

    /**
     * Regenerate future tasks with proper pattern from new start date
     * This deletes all future tasks and recreates them following the correct pattern
     */
    public static function regenerateFutureTasks(Task $currentTask, Carbon $newStartDate, array $updateFields = []): array
    {
        $seriesId = $currentTask->series_id;
        $recurrenceType = $currentTask->recurrence_type;
        $endDate = $currentTask->recurrence_end_date;
        
        if (!$seriesId || !$recurrenceType || $recurrenceType === self::RECURRENCE_SINGLE) {
            return [];
        }

        // Get template data from current task (merge with any updates)
        $templateData = array_merge([
            'location_id' => $currentTask->location_id,
            'sublocation_id' => $currentTask->sublocation_id,
            'equipment_id' => $currentTask->equipment_id,
            'period_id' => $currentTask->period_id,
            'type_check_id' => $currentTask->type_check_id,
            'maint_category_id' => $currentTask->maint_category_id,
            'status' => self::STATUS_OPEN,
            'priority' => $currentTask->priority,
            'assigned_to' => $currentTask->assigned_to,
            'supervisor_id' => $currentTask->supervisor_id,
            'notes' => $currentTask->notes,
            'shift' => $currentTask->shift,
        ], $updateFields);

        // Delete existing future tasks (after the original date, NOT the current task)
        $originalDueDate = $currentTask->getOriginal('due_date') ?? $currentTask->due_date;
        $deletedCount = self::where('series_id', $seriesId)
            ->where('id', '!=', $currentTask->id)
            ->whereDate('due_date', '>', $originalDueDate instanceof Carbon ? $originalDueDate->format('Y-m-d') : $originalDueDate)
            ->delete();

        // Calculate next date after the new start date
        $currentDate = match ($recurrenceType) {
            self::RECURRENCE_DAILY => $newStartDate->copy()->addWeekday(),
            self::RECURRENCE_WEEKLY => $newStartDate->copy()->addWeek(),
            self::RECURRENCE_MONTHLY => self::getNextMonthlyDate($newStartDate, $newStartDate),
            default => $newStartDate->copy()->addYear(),
        };

        $createdTasks = [];

        // Create new future tasks
        while ($currentDate->lte($endDate)) {
            $taskDate = self::skipWeekend($currentDate->copy());
            
            if ($taskDate->lte($endDate)) {
                $taskData = array_merge($templateData, [
                    'series_id' => $seriesId,
                    'recurrence_type' => $recurrenceType,
                    'recurrence_end_date' => $endDate,
                    'due_date' => $taskDate,
                    'is_series_exception' => false,
                ]);

                $createdTasks[] = self::create($taskData);
            }

            $currentDate = match ($recurrenceType) {
                self::RECURRENCE_DAILY => $currentDate->addWeekday(),
                self::RECURRENCE_WEEKLY => $currentDate->addWeek(),
                self::RECURRENCE_MONTHLY => self::getNextMonthlyDate($currentDate, $newStartDate),
                default => $currentDate->addYear(),
            };
        }

        return $createdTasks;
    }
}

