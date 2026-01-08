<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class MachineDowntime extends Model
{
    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'equipment_id',
        'problem',
        'root_cause',
        'action_taken',
        'start_datetime',
        'end_datetime',
        'downtime_minutes',
        'year',
        'month',
        'reported_by',
        'status',
        'submitted_by',
        'picked_up_by',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'picked_up_by');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_CLOSED => 'Closed',
            default => 'Unknown',
        };
    }

    /**
     * Check if customer can edit this record
     */
    public function canCustomerEdit(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if engineer can edit this record
     */
    public function canEngineerEdit(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Calculate downtime minutes from start and end datetime
     */
    public static function calculateDowntimeMinutes($startDateTime, $endDateTime): int
    {
        $start = strtotime($startDateTime);
        $end = strtotime($endDateTime);
        
        if ($start === false || $end === false || $end < $start) {
            return 0;
        }
        
        return (int) round(($end - $start) / 60);
    }

    /**
     * Format minutes to HH:MM format
     */
    public static function formatDowntime($minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }

    /**
     * Get availability summary for a specific month
     * Returns: Equipment, Frequency, Total Downtime, Availability %
     */
    public static function getAvailabilitySummary($year, $month, ?int $locationId = null, ?string $search = null, ?string $sort = 'equipment', ?string $direction = 'asc'): array
    {
        // Calculate working minutes for the month (24 hours per day, all days)
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $workingMinutes = $daysInMonth * 24 * 60;

        $query = "
            SELECT 
                e.id as equipment_id,
                e.name as equipment_name,
                e.serial_number,
                COUNT(d.id) as frequency,
                COALESCE(SUM(d.downtime_minutes), 0) as total_downtime_minutes,
                CASE 
                    WHEN ? > 0 
                    THEN ROUND((COALESCE(SUM(d.downtime_minutes), 0) / ?) * 100, 2)
                    ELSE 0
                END as downtime_percentage,
                CASE 
                    WHEN ? > 0 
                    THEN ROUND(100 - ((COALESCE(SUM(d.downtime_minutes), 0) / ?) * 100), 2)
                    ELSE 100
                END as availability_percentage
            FROM equipment e
            LEFT JOIN sublocations s ON e.sublocation_id = s.id
            LEFT JOIN machine_downtimes d ON e.id = d.equipment_id 
                AND d.year = ? 
                AND d.month = ?
            WHERE 1=1
        ";
        
        $params = [$workingMinutes, $workingMinutes, $workingMinutes, $workingMinutes, $year, $month];
        
        if ($locationId) {
            $query .= " AND s.location_id = ?";
            $params[] = $locationId;
        }
        
        if ($search) {
            $query .= " AND (e.name LIKE ? OR e.serial_number LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        $query .= " GROUP BY e.id, e.name, e.serial_number";
        
        // Apply sorting
        $sortColumn = match($sort) {
            'frequency' => 'frequency',
            'downtime' => 'total_downtime_minutes',
            'availability' => 'availability_percentage',
            default => 'e.name',
        };
        
        $query .= " ORDER BY {$sortColumn} " . ($direction === 'desc' ? 'DESC' : 'ASC');

        return DB::select($query, $params);
    }

    /**
     * Get statistics for summary cards
     */
    public static function getStatistics($year, $month, ?int $locationId = null, ?string $search = null): array
    {
        $query = self::where('year', $year)->where('month', $month);
        
        if ($locationId) {
            $query->whereHas('equipment.sublocation', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }
        
        if ($search) {
            $query->whereHas('equipment', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }
        
        $totalFrequency = $query->count();
        
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $workingMinutes = $daysInMonth * 24 * 60;
        
        // Get filtered equipment count
        $equipmentQuery = Equipment::query();
        if ($locationId) {
            $equipmentQuery->whereHas('sublocation', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }
        if ($search) {
            $equipmentQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }
        $equipmentCount = $equipmentQuery->count();
        
        $totalDowntime = (clone $query)->sum('downtime_minutes');
        
        $totalPossibleMinutes = $workingMinutes * $equipmentCount;
        $averageAvailability = $totalPossibleMinutes > 0 
            ? round(100 - (($totalDowntime / $totalPossibleMinutes) * 100), 2)
            : 100;

        $lastDowntime = (clone $query)->orderBy('start_datetime', 'desc')->first();

        return [
            'totalFrequency' => $totalFrequency,
            'averageAvailability' => $averageAvailability,
            'totalDowntimeMinutes' => $totalDowntime,
            'lastDowntime' => $lastDowntime?->start_datetime,
        ];
    }
}
