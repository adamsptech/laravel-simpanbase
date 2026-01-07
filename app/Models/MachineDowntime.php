<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class MachineDowntime extends Model
{
    protected $fillable = [
        'equipment_id',
        'problem',
        'root_cause',
        'start_datetime',
        'end_datetime',
        'downtime_minutes',
        'year',
        'month',
        'reported_by',
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
    public static function getAvailabilitySummary($year, $month): array
    {
        // Calculate working minutes for the month (24 hours per day, all days)
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $workingMinutes = $daysInMonth * 24 * 60;

        return DB::select("
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
            LEFT JOIN machine_downtimes d ON e.id = d.equipment_id 
                AND d.year = ? 
                AND d.month = ?
            GROUP BY e.id, e.name, e.serial_number
            ORDER BY e.name
        ", [$workingMinutes, $workingMinutes, $workingMinutes, $workingMinutes, $year, $month]);
    }

    /**
     * Get statistics for summary cards
     */
    public static function getStatistics($year, $month): array
    {
        $totalFrequency = self::where('year', $year)->where('month', $month)->count();
        
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $workingMinutes = $daysInMonth * 24 * 60;
        
        $equipmentCount = Equipment::count();
        $totalDowntime = self::where('year', $year)->where('month', $month)->sum('downtime_minutes');
        
        $totalPossibleMinutes = $workingMinutes * $equipmentCount;
        $averageAvailability = $totalPossibleMinutes > 0 
            ? round(100 - (($totalDowntime / $totalPossibleMinutes) * 100), 2)
            : 100;

        $lastDowntime = self::where('year', $year)->where('month', $month)
            ->orderBy('start_datetime', 'desc')
            ->first();

        return [
            'totalFrequency' => $totalFrequency,
            'averageAvailability' => $averageAvailability,
            'totalDowntimeMinutes' => $totalDowntime,
            'lastDowntime' => $lastDowntime?->start_datetime,
        ];
    }
}
