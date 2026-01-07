<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class OeeMonthly extends Model
{
    protected $fillable = [
        'equipment_id',
        'year',
        'month',
        'working_days',
        'working_hours',
        'working_minutes',
        // Plant Operating
        'plant_operating_days',
        'plant_operating_hours',
        'plant_operating_minutes',
        'plant_operating_percentage',
        // Planned Maintenance
        'planned_maintenance_days',
        'planned_maintenance_hours',
        'planned_maintenance_minutes',
        'planned_maintenance_percentage',
        // Plant Production
        'plant_production_days',
        'plant_production_hours',
        'plant_production_minutes',
        'plant_production_percentage',
        // Unplanned Maintenance
        'unplanned_maintenance_days',
        'unplanned_maintenance_hours',
        'unplanned_maintenance_minutes',
        'unplanned_maintenance_percentage',
        // Actual Production
        'actual_production_days',
        'actual_production_hours',
        'actual_production_minutes',
        'actual_production_percentage',
        // Legacy OEE fields
        'availability',
        'performance',
        'quality',
        'oee_percentage',
        'notes',
    ];

    protected $casts = [
        'availability' => 'decimal:2',
        'performance' => 'decimal:2',
        'quality' => 'decimal:2',
        'oee_percentage' => 'decimal:2',
        'plant_operating_percentage' => 'decimal:2',
        'planned_maintenance_percentage' => 'decimal:2',
        'plant_production_percentage' => 'decimal:2',
        'unplanned_maintenance_percentage' => 'decimal:2',
        'actual_production_percentage' => 'decimal:2',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Calculate percentages based on plant breakdown
     */
    public function recalculatePercentages(): void
    {
        $totalMinutes = $this->working_minutes;
        if ($totalMinutes <= 0) return;

        // Plant Operating defaults to same as working
        $this->plant_operating_percentage = 100;
        
        // Planned Maintenance %
        $this->planned_maintenance_percentage = round(($this->planned_maintenance_minutes / $totalMinutes) * 100, 2);
        
        // Plant Production = Operating - Planned
        $this->plant_production_minutes = $this->plant_operating_minutes - $this->planned_maintenance_minutes;
        $this->plant_production_percentage = round(($this->plant_production_minutes / $totalMinutes) * 100, 2);
        
        // Unplanned %
        $this->unplanned_maintenance_percentage = round(($this->unplanned_maintenance_minutes / $this->plant_production_minutes) * 100, 2);
        
        // Actual Production = Plant Production - Unplanned
        $this->actual_production_minutes = $this->plant_production_minutes - $this->unplanned_maintenance_minutes;
        $this->actual_production_percentage = round(($this->actual_production_minutes / $totalMinutes) * 100, 2);
    }

    /**
     * Initialize monthly OEE data for all equipment
     */
    public static function initializeMonthlyData(int $year, int $month): void
    {
        $equipment = Equipment::all();
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $hoursInMonth = $daysInMonth * 24;
        $minutesInMonth = $hoursInMonth * 60;
        
        foreach ($equipment as $equip) {
            self::updateOrCreate(
                ['equipment_id' => $equip->id, 'year' => $year, 'month' => $month],
                [
                    'working_days' => $daysInMonth,
                    'working_hours' => $hoursInMonth,
                    'working_minutes' => $minutesInMonth,
                    // Default: plant operating = working time
                    'plant_operating_days' => $daysInMonth,
                    'plant_operating_hours' => $hoursInMonth,
                    'plant_operating_minutes' => $minutesInMonth,
                    'plant_operating_percentage' => 100,
                    // Default: no planned maintenance
                    'planned_maintenance_days' => 0,
                    'planned_maintenance_hours' => 0,
                    'planned_maintenance_minutes' => 0,
                    'planned_maintenance_percentage' => 0,
                    // Default: all time is production
                    'plant_production_days' => $daysInMonth,
                    'plant_production_hours' => $hoursInMonth,
                    'plant_production_minutes' => $minutesInMonth,
                    'plant_production_percentage' => 100,
                    // Default: no unplanned
                    'unplanned_maintenance_days' => 0,
                    'unplanned_maintenance_hours' => 0,
                    'unplanned_maintenance_minutes' => 0,
                    'unplanned_maintenance_percentage' => 0,
                    // Default: all time is actual production
                    'actual_production_days' => $daysInMonth,
                    'actual_production_hours' => $hoursInMonth,
                    'actual_production_minutes' => $minutesInMonth,
                    'actual_production_percentage' => 100,
                    // Legacy
                    'availability' => 100,
                    'performance' => 100,
                    'quality' => 100,
                    'oee_percentage' => 100,
                ]
            );
        }
    }

    /**
     * Get OEE summary for a specific month
     */
    public static function getMonthlySummary(int $year, int $month, ?int $locationId = null, ?string $sort = 'equipment', ?string $direction = 'asc', ?string $search = null): array
    {
        $query = self::where('year', $year)
            ->where('month', $month)
            ->with('equipment:id,name,serial_number,sublocation_id', 'equipment.sublocation:id,name,location_id', 'equipment.sublocation.location:id,name');

        // Filter by location
        if ($locationId) {
            $query->whereHas('equipment.sublocation', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }

        // Search by equipment name or serial number
        if ($search) {
            $query->whereHas('equipment', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortColumn = match($sort) {
            'availability' => 'availability',
            'performance' => 'performance',
            'quality' => 'quality',
            'oee' => 'oee_percentage',
            'actual_production' => 'actual_production_percentage',
            default => 'equipment_id',
        };

        if ($sort === 'equipment') {
            // Sort by equipment name requires join
            $query->join('equipment', 'oee_monthlies.equipment_id', '=', 'equipment.id')
                  ->select('oee_monthlies.*')
                  ->orderBy('equipment.name', $direction);
        } else {
            $query->orderBy($sortColumn, $direction);
        }

        $data = $query->get();

        return [
            'records' => $data,
            'averageOee' => $data->avg('oee_percentage') ?? 0,
            'averageAvailability' => $data->avg('availability') ?? 0,
            'averagePerformance' => $data->avg('performance') ?? 0,
            'averageQuality' => $data->avg('quality') ?? 0,
            'averageActualProduction' => $data->avg('actual_production_percentage') ?? 0,
            'count' => $data->count(),
        ];
    }
}
