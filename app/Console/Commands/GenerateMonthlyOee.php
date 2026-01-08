<?php

namespace App\Console\Commands;

use App\Models\Equipment;
use App\Models\MachineDowntime;
use App\Models\OeeMonthly;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyOee extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:generate-monthly-oee {--month= : Month (1-12), defaults to previous month} {--year= : Year, defaults to current year}';

    /**
     * The console command description.
     */
    protected $description = 'Generate OEE monthly records for all equipment (runs on 1st of each month for previous month)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Determine target month/year (default: previous month)
        $year = $this->option('year') ?? now()->year;
        $month = $this->option('month') ?? now()->subMonth()->month;

        // If previous month was December, adjust year
        if ($month == 12 && !$this->option('month')) {
            $year = now()->subMonth()->year;
        }

        $this->info("Generating OEE data for {$month}/{$year}...");

        // Get all equipment
        $equipment = Equipment::all();

        if ($equipment->isEmpty()) {
            $this->warn('No equipment found.');
            return self::SUCCESS;
        }

        $this->info("Processing {$equipment->count()} equipment items...");

        $created = 0;
        $skipped = 0;

        foreach ($equipment as $eq) {
            // Check if record already exists
            $existing = OeeMonthly::where('equipment_id', $eq->id)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            // Calculate calendar data
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $workingDays = $this->calculateWorkingDays($year, $month);
            $calendarHours = $workingDays * 24;
            $calendarMinutes = $calendarHours * 60;

            // Get planned maintenance time from closed tasks
            $plannedMaintenanceMinutes = DB::table('tasks')
                ->join('maint_categories', 'tasks.maint_category_id', '=', 'maint_categories.id')
                ->where('tasks.equipment_id', $eq->id)
                ->whereYear('tasks.due_date', $year)
                ->whereMonth('tasks.due_date', $month)
                ->where('tasks.status', 4) // Closed
                ->whereNotNull('tasks.started_at')
                ->whereNotNull('tasks.ended_at')
                ->where(function ($q) {
                    $q->where('maint_categories.name', 'like', '%Preventive%')
                      ->orWhere('maint_categories.name', 'like', '%Periodic%')
                      ->orWhere('maint_categories.name', 'like', '%Predictive%');
                })
                ->selectRaw('COALESCE(SUM(TIMESTAMPDIFF(MINUTE, tasks.started_at, tasks.ended_at)), 0) as total_minutes')
                ->value('total_minutes') ?? 0;

            // Get unplanned maintenance (downtime) from machine_downtimes
            $unplannedMaintenanceMinutes = MachineDowntime::where('equipment_id', $eq->id)
                ->where('year', $year)
                ->where('month', $month)
                ->sum('downtime_minutes') ?? 0;

            // Calculate values
            $plantOperatingMinutes = $calendarMinutes - ($plannedMaintenanceMinutes + $unplannedMaintenanceMinutes);
            $plantOperatingMinutes = max(0, $plantOperatingMinutes); // Ensure non-negative

            // For now, set actual production equal to plant operating (can be edited later)
            $actualProductionMinutes = $plantOperatingMinutes;

            // Create OEE record
            OeeMonthly::create([
                'equipment_id' => $eq->id,
                'year' => $year,
                'month' => $month,
                'calendar_days' => $daysInMonth,
                'calendar_hours' => $calendarHours,
                'calendar_minutes' => $calendarMinutes,
                'working_days' => $workingDays,
                'plant_operating_hours' => round($plantOperatingMinutes / 60, 2),
                'plant_operating_minutes' => $plantOperatingMinutes,
                'planned_maintenance_hours' => round($plannedMaintenanceMinutes / 60, 2),
                'planned_maintenance_minutes' => $plannedMaintenanceMinutes,
                'plant_production_hours' => round($plantOperatingMinutes / 60, 2),
                'plant_production_minutes' => $plantOperatingMinutes,
                'unplanned_maintenance_hours' => round($unplannedMaintenanceMinutes / 60, 2),
                'unplanned_maintenance_minutes' => $unplannedMaintenanceMinutes,
                'actual_production_hours' => round($actualProductionMinutes / 60, 2),
                'actual_production_minutes' => $actualProductionMinutes,
            ]);

            $created++;
        }

        $this->info("Created {$created} OEE records, skipped {$skipped} existing records.");

        return self::SUCCESS;
    }

    /**
     * Calculate working days in a month (excluding weekends)
     */
    protected function calculateWorkingDays(int $year, int $month): int
    {
        $workingDays = 0;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $timestamp = mktime(0, 0, 0, $month, $day, $year);
            $dayOfWeek = date('N', $timestamp);
            
            // Monday = 1, Sunday = 7
            if ($dayOfWeek <= 5) {
                $workingDays++;
            }
        }

        return $workingDays;
    }
}
