<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\MachineDowntime;
use App\Models\MaintCategory;
use App\Models\OeeMonthly;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class January2026DataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing engineers created in December seeder
        $engineerIds = User::whereHas('role', fn($q) => $q->where('name', 'Engineer'))->pluck('id')->toArray();
        
        if (empty($engineerIds)) {
            $this->command->warn('No engineers found. Please run December2025DataSeeder first.');
            return;
        }
        
        $supervisor = User::whereHas('role', fn($q) => $q->where('name', 'Supervisor'))->first();
        $manager = User::whereHas('role', fn($q) => $q->where('name', 'Manager'))->first();
        
        $equipment = Equipment::with('sublocation.location')->get();
        $maintCategories = MaintCategory::pluck('id', 'name');
        
        $preventiveId = $maintCategories['Preventive Maintenance'] ?? 1;
        $correctiveId = $maintCategories['Corrective Maintenance'] ?? 3;
        $periodicId = $maintCategories['Periodic Maintenance'] ?? 4;

        $shifts = ['Morning', 'Afternoon', 'Night'];
        
        // ==========================================
        // 1. CREATE WORK ORDERS FOR JANUARY 2027
        // ==========================================
        $workOrdersCreated = 0;
        $today = 7; // Current date is January 7, 2026

        foreach ($equipment as $equip) {
            $numWorkOrders = rand(2, 4);
            
            for ($i = 0; $i < $numWorkOrders; $i++) {
                $dayOfMonth = rand(1, 31);
                $dueDate = Carbon::create(2026, 1, $dayOfMonth);
                
                $assignedTo = $engineerIds[array_rand($engineerIds)];
                
                // Maintenance type distribution
                $rand = rand(1, 100);
                if ($rand <= 60) {
                    $maintCatId = $preventiveId;
                    $maintType = 'Preventive';
                } elseif ($rand <= 85) {
                    $maintCatId = $correctiveId;
                    $maintType = 'Corrective';
                } else {
                    $maintCatId = $periodicId;
                    $maintType = 'Periodic';
                }

                // Status based on date relative to "today" (Jan 7)
                // 0=Open, 1=SubmittedToSupervisor, 2=SubmittedToManager, 3=SubmittedToCustomer, 4=Closed
                if ($dayOfMonth < $today) {
                    // Past dates - mostly closed
                    $statusRand = rand(1, 100);
                    if ($statusRand <= 70) {
                        $status = 4; // Closed
                    } elseif ($statusRand <= 85) {
                        $status = 2; // SubmittedToManager
                    } elseif ($statusRand <= 95) {
                        $status = 1; // SubmittedToSupervisor
                    } else {
                        $status = 0; // Open (overdue)
                    }
                } elseif ($dayOfMonth == $today) {
                    // Today - in progress
                    $status = rand(0, 2); // Open, SubmittedToSupervisor, or SubmittedToManager
                } else {
                    // Future dates - more open/scheduled
                    $statusRand = rand(1, 100);
                    if ($statusRand <= 60) {
                        $status = 0; // Open (scheduled)
                    } elseif ($statusRand <= 80) {
                        $status = 1; // SubmittedToSupervisor
                    } else {
                        $status = 2; // SubmittedToManager
                    }
                }

                $priority = rand(1, 3);
                $shift = $shifts[array_rand($shifts)];
                
                $startedAt = null;
                $endedAt = null;
                $duration = null;

                if (in_array($status, [2, 3, 4])) {
                    $startHour = rand(7, 16);
                    $startedAt = Carbon::create(2026, 1, $dayOfMonth, $startHour, rand(0, 59), 0);
                    
                    if ($status === 4) {
                        $durationMinutes = rand(30, 240);
                        $endedAt = $startedAt->copy()->addMinutes($durationMinutes);
                        $hours = floor($durationMinutes / 60);
                        $mins = $durationMinutes % 60;
                        $duration = sprintf('%02d:%02d', $hours, $mins);
                    }
                }

                $approval1By = null;
                $approval1At = null;
                $approval2By = null;
                $approval2At = null;

                if ($status === 4 && $supervisor) {
                    $approval1By = $supervisor->id;
                    $approval1At = $endedAt?->copy()->addHours(rand(1, 4));
                    
                    if ($manager && rand(1, 100) <= 70) {
                        $approval2By = $manager->id;
                        $approval2At = $approval1At?->copy()->addHours(rand(1, 8));
                    }
                }

                $titles = [
                    'Preventive' => [
                        "Weekly PM - {$equip->name}",
                        "Monthly Inspection - {$equip->name}",
                        "Lubrication Check - {$equip->name}",
                        "Safety Inspection - {$equip->name}",
                    ],
                    'Corrective' => [
                        "Repair - {$equip->name} Malfunction",
                        "Fix {$equip->name} Error",
                        "Replace Worn Parts - {$equip->name}",
                    ],
                    'Periodic' => [
                        "Annual Overhaul - {$equip->name}",
                        "Semi-Annual Service - {$equip->name}",
                    ],
                ];

                $titleList = $titles[$maintType] ?? $titles['Preventive'];
                $title = $titleList[array_rand($titleList)];

                $notesList = [
                    0 => ['Scheduled for maintenance', 'Awaiting parts', 'Pending engineer assignment'],
                    1 => ['Waiting for supervisor approval', 'Parts ordered'],
                    2 => ['Waiting for manager approval', 'Work in progress'],
                    3 => ['Waiting for customer approval'],
                    4 => ['Completed successfully', 'All checks passed', 'No issues found'],
                ];

                $notes = $notesList[$status][array_rand($notesList[$status])];

                Task::firstOrCreate(
                    [
                        'equipment_id' => $equip->id,
                        'due_date' => $dueDate,
                        'maint_category_id' => $maintCatId,
                        'notes' => $notes, // Additional uniqueness
                    ],
                    [
                        'location_id' => $equip->sublocation?->location_id,
                        'sublocation_id' => $equip->sublocation_id,
                        'equipment_id' => $equip->id,
                        'maint_category_id' => $maintCatId,
                        'status' => $status,
                        'priority' => $priority,
                        'assigned_to' => $assignedTo,
                        'supervisor_id' => $supervisor?->id,
                        'approval1_by' => $approval1By,
                        'approval1_at' => $approval1At,
                        'approval2_by' => $approval2By,
                        'approval2_at' => $approval2At,
                        'due_date' => $dueDate,
                        'duration' => $duration,
                        'started_at' => $startedAt,
                        'ended_at' => $endedAt,
                        'shift' => $shift,
                        'notes' => $notes,
                    ]
                );
                $workOrdersCreated++;
            }
        }
        $this->command->info("✓ Created {$workOrdersCreated} work orders for January 2026");

        // ==========================================
        // 2. CREATE MACHINE DOWNTIME RECORDS
        // ==========================================
        $downtimeProblems = [
            'Motor overheating',
            'Bearing failure',
            'Electrical fault',
            'PLC communication error',
            'Coolant system failure',
        ];

        $rootCauses = [
            'Normal wear and tear',
            'Improper maintenance',
            'Material fatigue',
            'Environmental factors',
        ];

        $downtimeCreated = 0;
        
        foreach ($equipment as $equip) {
            if (rand(1, 100) <= 25) { // 25% of equipment has downtime
                $numDowntimes = rand(1, 2);
                
                for ($i = 0; $i < $numDowntimes; $i++) {
                    $dayOfMonth = rand(1, 7); // Only for days that have passed (1-7 Jan)
                    $startHour = rand(6, 20);
                    $downtimeMinutes = rand(30, 360);
                    
                    $startDatetime = Carbon::create(2026, 1, $dayOfMonth, $startHour, rand(0, 59), 0);
                    $endDatetime = $startDatetime->copy()->addMinutes($downtimeMinutes);
                    
                    MachineDowntime::firstOrCreate(
                        [
                            'equipment_id' => $equip->id,
                            'start_datetime' => $startDatetime,
                        ],
                        [
                            'equipment_id' => $equip->id,
                            'problem' => $downtimeProblems[array_rand($downtimeProblems)],
                            'root_cause' => $rootCauses[array_rand($rootCauses)],
                            'start_datetime' => $startDatetime,
                            'end_datetime' => $endDatetime,
                            'downtime_minutes' => $downtimeMinutes,
                            'year' => 2026,
                            'month' => 1,
                        ]
                    );
                    $downtimeCreated++;
                }
            }
        }
        $this->command->info("✓ Created {$downtimeCreated} machine downtime records for January 2026");

        // ==========================================
        // 3. CREATE OEE MONTHLY DATA FOR JANUARY 2027
        // ==========================================
        $oeeCreated = 0;
        $daysInJan = 31;
        $hoursInJan = $daysInJan * 24;
        $minutesInJan = $hoursInJan * 60;

        foreach ($equipment as $equip) {
            $plannedMaintDays = rand(0, 2);
            $plannedMaintHours = $plannedMaintDays * 24;
            $plannedMaintMinutes = $plannedMaintHours * 60;
            
            $unplannedMaintHours = rand(0, 36);
            $unplannedMaintMinutes = $unplannedMaintHours * 60;
            
            $plantProdMinutes = $minutesInJan - $plannedMaintMinutes;
            $actualProdMinutes = $plantProdMinutes - $unplannedMaintMinutes;
            
            $plantProdPct = round(($plantProdMinutes / $minutesInJan) * 100, 2);
            $plannedMaintPct = round(($plannedMaintMinutes / $minutesInJan) * 100, 2);
            $unplannedMaintPct = $plantProdMinutes > 0 ? round(($unplannedMaintMinutes / $plantProdMinutes) * 100, 2) : 0;
            $actualProdPct = round(($actualProdMinutes / $minutesInJan) * 100, 2);
            
            OeeMonthly::updateOrCreate(
                ['equipment_id' => $equip->id, 'year' => 2026, 'month' => 1],
                [
                    'working_days' => $daysInJan,
                    'working_hours' => $hoursInJan,
                    'working_minutes' => $minutesInJan,
                    'plant_operating_days' => $daysInJan,
                    'plant_operating_hours' => $hoursInJan,
                    'plant_operating_minutes' => $minutesInJan,
                    'plant_operating_percentage' => 100,
                    'planned_maintenance_days' => $plannedMaintDays,
                    'planned_maintenance_hours' => $plannedMaintHours,
                    'planned_maintenance_minutes' => $plannedMaintMinutes,
                    'planned_maintenance_percentage' => $plannedMaintPct,
                    'plant_production_days' => $daysInJan - $plannedMaintDays,
                    'plant_production_hours' => $hoursInJan - $plannedMaintHours,
                    'plant_production_minutes' => $plantProdMinutes,
                    'plant_production_percentage' => $plantProdPct,
                    'unplanned_maintenance_days' => ceil($unplannedMaintHours / 24),
                    'unplanned_maintenance_hours' => $unplannedMaintHours,
                    'unplanned_maintenance_minutes' => $unplannedMaintMinutes,
                    'unplanned_maintenance_percentage' => $unplannedMaintPct,
                    'actual_production_days' => $daysInJan - $plannedMaintDays - ceil($unplannedMaintHours / 24),
                    'actual_production_hours' => floor($actualProdMinutes / 60),
                    'actual_production_minutes' => $actualProdMinutes,
                    'actual_production_percentage' => $actualProdPct,
                    'availability' => rand(88, 99),
                    'performance' => rand(82, 98),
                    'quality' => rand(92, 100),
                    'oee_percentage' => rand(72, 96),
                ]
            );
            $oeeCreated++;
        }
        $this->command->info("✓ Created {$oeeCreated} OEE records for January 2026");

        $this->command->info('');
        $this->command->info('=== January 2026 Data Creation Complete ===');
    }
}
