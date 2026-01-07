<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Location;
use App\Models\MachineDowntime;
use App\Models\MaintCategory;
use App\Models\OeeMonthly;
use App\Models\Role;
use App\Models\Sublocation;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class December2025DataSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. CREATE 5 ENGINEER ACCOUNTS
        // ==========================================
        $engineerRole = Role::where('name', 'Engineer')->first();
        
        $engineers = [
            ['name' => 'Andi Pratama', 'email' => 'engineer1@simpanbase.com'],
            ['name' => 'Budi Setiawan', 'email' => 'engineer2@simpanbase.com'],
            ['name' => 'Candra Wijaya', 'email' => 'engineer3@simpanbase.com'],
            ['name' => 'Dedi Kurniawan', 'email' => 'engineer4@simpanbase.com'],
            ['name' => 'Eko Prasetyo', 'email' => 'engineer5@simpanbase.com'],
        ];

        $engineerIds = [];
        foreach ($engineers as $eng) {
            $user = User::firstOrCreate(
                ['email' => $eng['email']],
                [
                    'name' => $eng['name'],
                    'email' => $eng['email'],
                    'password' => Hash::make('password123'),
                    'role_id' => $engineerRole?->id,
                    'is_active' => true,
                ]
            );
            $engineerIds[] = $user->id;
        }
        $this->command->info('✓ Created 5 engineer accounts (engineer1-5@simpanbase.com)');

        // Get supervisor for approval
        $supervisor = User::whereHas('role', fn($q) => $q->where('name', 'Supervisor'))->first();
        $manager = User::whereHas('role', fn($q) => $q->where('name', 'Manager'))->first();
        
        // ==========================================
        // 2. GET MASTER DATA
        // ==========================================
        $equipment = Equipment::with('sublocation.location')->get();
        $maintCategories = MaintCategory::pluck('id', 'name');
        $locations = Location::pluck('id', 'name');
        $sublocations = Sublocation::pluck('id', 'name');

        $preventiveId = $maintCategories['Preventive Maintenance'] ?? 1;
        $correctiveId = $maintCategories['Corrective Maintenance'] ?? 3;
        $periodicId = $maintCategories['Periodic Maintenance'] ?? 4;

        // Status: 0=Open, 1=SubmittedToSupervisor, 2=SubmittedToManager, 3=SubmittedToCustomer, 4=Closed
        // Priority: 1=Low, 2=Medium, 3=High
        $shifts = ['Morning', 'Afternoon', 'Night'];

        // ==========================================
        // 3. CREATE WORK ORDERS FOR DECEMBER 2026
        // ==========================================
        $december2025 = Carbon::create(2025, 12, 1);
        $workOrdersCreated = 0;

        // Create 2-4 work orders per equipment for December
        foreach ($equipment as $equip) {
            $numWorkOrders = rand(2, 4);
            
            for ($i = 0; $i < $numWorkOrders; $i++) {
                $dayOfMonth = rand(1, 28);
                $dueDate = Carbon::create(2025, 12, $dayOfMonth);
                
                // Assign to random engineer
                $assignedTo = $engineerIds[array_rand($engineerIds)];
                
                // Random maintenance type - mostly Preventive (60%), some Corrective (25%), Periodic (15%)
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

                // Random status - weighted towards closed for past dates
                // 0=Open, 1=SubmittedToSupervisor, 2=SubmittedToManager, 3=SubmittedToCustomer, 4=Closed
                if ($dayOfMonth <= 20) {
                    // Past dates - mostly closed
                    $statusRand = rand(1, 100);
                    if ($statusRand <= 70) {
                        $status = 4; // Closed
                    } elseif ($statusRand <= 85) {
                        $status = 2; // SubmittedToManager (in_progress)
                    } elseif ($statusRand <= 95) {
                        $status = 1; // SubmittedToSupervisor (pending)
                    } else {
                        $status = 0; // Open
                    }
                } else {
                    // Future dates - more open/pending
                    $statusRand = rand(1, 100);
                    if ($statusRand <= 30) {
                        $status = 4; // Closed
                    } elseif ($statusRand <= 50) {
                        $status = 2; // SubmittedToManager
                    } elseif ($statusRand <= 75) {
                        $status = 1; // SubmittedToSupervisor
                    } else {
                        $status = 0; // Open
                    }
                }

                $priority = rand(1, 3); // 1=Low, 2=Medium, 3=High
                $shift = $shifts[array_rand($shifts)];
                
                // Calculate started_at and ended_at for closed/in_progress tasks
                $startedAt = null;
                $endedAt = null;
                $duration = null;

                if (in_array($status, [2, 3, 4])) { // SubmittedToManager, SubmittedToCustomer, Closed
                    $startHour = rand(7, 16);
                    $startedAt = Carbon::create(2025, 12, $dayOfMonth, $startHour, rand(0, 59), 0);
                    
                    if ($status === 4) { // Closed
                        $durationMinutes = rand(30, 240); // 30 min to 4 hours
                        $endedAt = $startedAt->copy()->addMinutes($durationMinutes);
                        $hours = floor($durationMinutes / 60);
                        $mins = $durationMinutes % 60;
                        $duration = sprintf('%02d:%02d', $hours, $mins); // Format as string
                    }
                }

                // Approval fields for closed tasks
                $approval1By = null;
                $approval1At = null;
                $approval2By = null;
                $approval2At = null;

                if ($status === 4 && $supervisor) { // Closed tasks get approvals
                    $approval1By = $supervisor->id;
                    $approval1At = $endedAt?->copy()->addHours(rand(1, 4));
                    
                    if ($manager && rand(1, 100) <= 70) {
                        $approval2By = $manager->id;
                        $approval2At = $approval1At?->copy()->addHours(rand(1, 8));
                    }
                }

                // Generate work order title
                $titles = [
                    'Preventive' => [
                        "Weekly PM - {$equip->name}",
                        "Monthly Inspection - {$equip->name}",
                        "Lubrication Check - {$equip->name}",
                        "Safety Inspection - {$equip->name}",
                        "Filter Replacement - {$equip->name}",
                    ],
                    'Corrective' => [
                        "Repair - {$equip->name} Malfunction",
                        "Fix {$equip->name} Error",
                        "Replace Worn Parts - {$equip->name}",
                        "Emergency Repair - {$equip->name}",
                        "Troubleshoot - {$equip->name}",
                    ],
                    'Periodic' => [
                        "Annual Overhaul - {$equip->name}",
                        "Semi-Annual Service - {$equip->name}",
                        "Quarterly Calibration - {$equip->name}",
                        "Bi-Annual Inspection - {$equip->name}",
                    ],
                ];

                $titleList = $titles[$maintType] ?? $titles['Preventive'];
                $title = $titleList[array_rand($titleList)];

                // Notes based on status
                $notesList = [
                    0 => ['Scheduled for maintenance', 'Awaiting parts', 'Pending engineer assignment'],
                    1 => ['Waiting for supervisor approval', 'Parts ordered', 'Engineer on the way'],
                    2 => ['Waiting for manager approval', 'Work in progress', 'Inspection ongoing'],
                    3 => ['Waiting for customer approval', 'Almost complete'],
                    4 => ['Completed successfully', 'All checks passed', 'Parts replaced as scheduled', 'No issues found'],
                ];

                $notes = $notesList[$status][array_rand($notesList[$status])];

                Task::firstOrCreate(
                    [
                        'equipment_id' => $equip->id,
                        'due_date' => $dueDate,
                        'maint_category_id' => $maintCatId,
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
        $this->command->info("✓ Created {$workOrdersCreated} work orders for December 2025");

        // ==========================================
        // 4. CREATE MACHINE DOWNTIME RECORDS
        // ==========================================
        $downtimeProblems = [
            'Hydraulic leak detected',
            'Motor overheating',
            'Bearing failure',
            'Electrical fault',
            'Sensor malfunction',
            'Pneumatic pressure drop',
            'PLC communication error',
            'Belt slippage',
            'Coolant system failure',
            'Gearbox noise',
        ];

        $rootCauses = [
            'Seal wear due to age',
            'Insufficient lubrication',
            'Overloading during operation',
            'Electrical surge',
            'Contaminated oil',
            'Normal wear and tear',
            'Improper maintenance',
            'Operator error',
            'Material fatigue',
            'Environmental factors',
        ];

        $downtimeCreated = 0;
        
        // Create 1-2 downtime events for ~30% of equipment
        foreach ($equipment as $equip) {
            if (rand(1, 100) <= 30) {
                $numDowntimes = rand(1, 2);
                
                for ($i = 0; $i < $numDowntimes; $i++) {
                    $dayOfMonth = rand(1, 28);
                    $startHour = rand(6, 20);
                    $downtimeMinutes = rand(30, 480); // 30 min to 8 hours
                    
                    $startDatetime = Carbon::create(2025, 12, $dayOfMonth, $startHour, rand(0, 59), 0);
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
                            'year' => 2025,
                            'month' => 12,
                        ]
                    );
                    $downtimeCreated++;
                }
            }
        }
        $this->command->info("✓ Created {$downtimeCreated} machine downtime records for December 2025");

        // ==========================================
        // 5. CREATE OEE MONTHLY DATA FOR DECEMBER 2026
        // ==========================================
        $oeeCreated = 0;
        $daysInDec = 31;
        $hoursInDec = $daysInDec * 24;
        $minutesInDec = $hoursInDec * 60;

        foreach ($equipment as $equip) {
            // Random but realistic OEE values
            $plannedMaintDays = rand(0, 3);
            $plannedMaintHours = $plannedMaintDays * 24;
            $plannedMaintMinutes = $plannedMaintHours * 60;
            
            $unplannedMaintHours = rand(0, 48);
            $unplannedMaintMinutes = $unplannedMaintHours * 60;
            
            $plantProdMinutes = $minutesInDec - $plannedMaintMinutes;
            $actualProdMinutes = $plantProdMinutes - $unplannedMaintMinutes;
            
            $plantProdPct = round(($plantProdMinutes / $minutesInDec) * 100, 2);
            $plannedMaintPct = round(($plannedMaintMinutes / $minutesInDec) * 100, 2);
            $unplannedMaintPct = $plantProdMinutes > 0 ? round(($unplannedMaintMinutes / $plantProdMinutes) * 100, 2) : 0;
            $actualProdPct = round(($actualProdMinutes / $minutesInDec) * 100, 2);
            
            OeeMonthly::updateOrCreate(
                ['equipment_id' => $equip->id, 'year' => 2025, 'month' => 12],
                [
                    'working_days' => $daysInDec,
                    'working_hours' => $hoursInDec,
                    'working_minutes' => $minutesInDec,
                    'plant_operating_days' => $daysInDec,
                    'plant_operating_hours' => $hoursInDec,
                    'plant_operating_minutes' => $minutesInDec,
                    'plant_operating_percentage' => 100,
                    'planned_maintenance_days' => $plannedMaintDays,
                    'planned_maintenance_hours' => $plannedMaintHours,
                    'planned_maintenance_minutes' => $plannedMaintMinutes,
                    'planned_maintenance_percentage' => $plannedMaintPct,
                    'plant_production_days' => $daysInDec - $plannedMaintDays,
                    'plant_production_hours' => $hoursInDec - $plannedMaintHours,
                    'plant_production_minutes' => $plantProdMinutes,
                    'plant_production_percentage' => $plantProdPct,
                    'unplanned_maintenance_days' => ceil($unplannedMaintHours / 24),
                    'unplanned_maintenance_hours' => $unplannedMaintHours,
                    'unplanned_maintenance_minutes' => $unplannedMaintMinutes,
                    'unplanned_maintenance_percentage' => $unplannedMaintPct,
                    'actual_production_days' => $daysInDec - $plannedMaintDays - ceil($unplannedMaintHours / 24),
                    'actual_production_hours' => floor($actualProdMinutes / 60),
                    'actual_production_minutes' => $actualProdMinutes,
                    'actual_production_percentage' => $actualProdPct,
                    'availability' => rand(85, 99),
                    'performance' => rand(80, 98),
                    'quality' => rand(90, 100),
                    'oee_percentage' => rand(70, 95),
                ]
            );
            $oeeCreated++;
        }
        $this->command->info("✓ Created {$oeeCreated} OEE records for December 2025");

        $this->command->info('');
        $this->command->info('=== December 2025 Data Creation Complete ===');
        $this->command->info("Engineers: 5 accounts (engineer1-5@simpanbase.com, password: password123)");
        $this->command->info("Work Orders: {$workOrdersCreated} tasks linked to equipment");
        $this->command->info("Downtime: {$downtimeCreated} machine downtime events");
        $this->command->info("OEE Data: {$oeeCreated} monthly OEE records");
    }
}
