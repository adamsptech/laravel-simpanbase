<?php

namespace Database\Seeders;

use App\Models\Checklist;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\MaintCategory;
use App\Models\PartStock;
use App\Models\PeriodPm;
use App\Models\Sublocation;
use App\Models\Supplier;
use App\Models\TypeCheck;
use Illuminate\Database\Seeder;

class ProductionSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. LOCATIONS (Plant Areas) - only 'name' field
        // ==========================================
        $locations = [
            ['name' => 'Plant A - Main Production'],
            ['name' => 'Plant B - Assembly'],
            ['name' => 'Plant C - Warehouse'],
            ['name' => 'Plant D - Utilities'],
            ['name' => 'Plant E - Quality Lab'],
            ['name' => 'Maintenance Workshop'],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(['name' => $loc['name']], $loc);
        }

        $locationIds = Location::pluck('id', 'name');
        $this->command->info('✓ Created ' . count($locations) . ' locations');

        // ==========================================
        // 2. SUBLOCATIONS (Specific Areas)
        // ==========================================
        $sublocations = [
            // Plant A
            ['name' => 'Press Line 1', 'location_id' => $locationIds['Plant A - Main Production']],
            ['name' => 'Press Line 2', 'location_id' => $locationIds['Plant A - Main Production']],
            ['name' => 'CNC Machining Area', 'location_id' => $locationIds['Plant A - Main Production']],
            ['name' => 'Welding Bay', 'location_id' => $locationIds['Plant A - Main Production']],
            ['name' => 'Painting Booth', 'location_id' => $locationIds['Plant A - Main Production']],
            // Plant B
            ['name' => 'Assembly Line A', 'location_id' => $locationIds['Plant B - Assembly']],
            ['name' => 'Assembly Line B', 'location_id' => $locationIds['Plant B - Assembly']],
            ['name' => 'Packaging Area', 'location_id' => $locationIds['Plant B - Assembly']],
            // Plant C
            ['name' => 'Raw Material Storage', 'location_id' => $locationIds['Plant C - Warehouse']],
            ['name' => 'Finished Goods Storage', 'location_id' => $locationIds['Plant C - Warehouse']],
            ['name' => 'Spare Parts Room', 'location_id' => $locationIds['Plant C - Warehouse']],
            // Plant D
            ['name' => 'Compressor Room', 'location_id' => $locationIds['Plant D - Utilities']],
            ['name' => 'Electrical Substation', 'location_id' => $locationIds['Plant D - Utilities']],
            ['name' => 'Chiller Plant', 'location_id' => $locationIds['Plant D - Utilities']],
            ['name' => 'Boiler Room', 'location_id' => $locationIds['Plant D - Utilities']],
        ];

        foreach ($sublocations as $sub) {
            Sublocation::firstOrCreate(
                ['name' => $sub['name'], 'location_id' => $sub['location_id']],
                $sub
            );
        }

        $sublocationIds = Sublocation::pluck('id', 'name');
        $this->command->info('✓ Created ' . count($sublocations) . ' sublocations');

        // ==========================================
        // 3. SUPPLIERS - fields: name, address, phone, pic, email
        // ==========================================
        $suppliers = [
            ['name' => 'PT. Sumber Bearing Indonesia', 'pic' => 'Budi Santoso', 'phone' => '021-5551234', 'email' => 'sales@sbi.co.id', 'address' => 'Jl. Industri Raya No. 45, Bekasi'],
            ['name' => 'CV. Teknik Jaya Abadi', 'pic' => 'Ahmad Wijaya', 'phone' => '021-5552345', 'email' => 'order@teknikjaya.com', 'address' => 'Jl. Rungkut Industri III/12, Surabaya'],
            ['name' => 'PT. Hydraulic Power Systems', 'pic' => 'David Chen', 'phone' => '021-5553456', 'email' => 'support@hps.co.id', 'address' => 'Kawasan EJIP Plot 8B, Cikarang'],
            ['name' => 'PT. Pneumatic Solutions', 'pic' => 'Hendro Kusuma', 'phone' => '021-5554567', 'email' => 'sales@pneumaticsol.com', 'address' => 'Jl. MM2100 Industrial Town, Cibitung'],
            ['name' => 'PT. Elektrik Prima', 'pic' => 'Siti Rahayu', 'phone' => '021-5555678', 'email' => 'info@elektrikprima.co.id', 'address' => 'Jl. Gatot Subroto Km 7, Tangerang'],
            ['name' => 'PT. Lubricant Indonesia', 'pic' => 'Agus Hermawan', 'phone' => '021-5556789', 'email' => 'order@lubricantindo.com', 'address' => 'Jl. Raya Narogong Km 15, Bogor'],
            ['name' => 'PT. Filter & Gasket Specialist', 'pic' => 'Rina Susanti', 'phone' => '021-5557890', 'email' => 'sales@fgs.co.id', 'address' => 'Jl. Jababeka VI Blok J No. 12, Cikarang'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        $supplierIds = Supplier::pluck('id', 'name');
        $this->command->info('✓ Created ' . count($suppliers) . ' suppliers');

        // ==========================================
        // 4. MAINTENANCE CATEGORIES - fields: name, description
        // ==========================================
        $maintCategories = [
            ['name' => 'Preventive Maintenance', 'description' => 'Scheduled maintenance to prevent failures'],
            ['name' => 'Predictive Maintenance', 'description' => 'Condition-based maintenance using monitoring'],
            ['name' => 'Corrective Maintenance', 'description' => 'Repair after failure or breakdown'],
            ['name' => 'Periodic Maintenance', 'description' => 'Time-based scheduled maintenance'],
            ['name' => 'Overhaul', 'description' => 'Major equipment rebuild or restoration'],
            ['name' => 'Calibration', 'description' => 'Instrument and sensor calibration'],
        ];

        foreach ($maintCategories as $cat) {
            MaintCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
        $this->command->info('✓ Created ' . count($maintCategories) . ' maintenance categories');

        // ==========================================
        // 5. PERIOD PMs - fields: name, days
        // ==========================================
        $periods = [
            ['name' => 'Daily', 'days' => 1],
            ['name' => 'Weekly', 'days' => 7],
            ['name' => 'Bi-Weekly', 'days' => 14],
            ['name' => 'Monthly', 'days' => 30],
            ['name' => 'Quarterly', 'days' => 90],
            ['name' => 'Semi-Annual', 'days' => 180],
            ['name' => 'Annual', 'days' => 365],
        ];

        foreach ($periods as $period) {
            PeriodPm::firstOrCreate(['name' => $period['name']], $period);
        }
        $this->command->info('✓ Created ' . count($periods) . ' maintenance periods');

        // ==========================================
        // 6. EQUIPMENT (40+ machines) - fields: sublocation_id, supplier_id, name, serial_number, category
        // ==========================================
        $equipmentData = [
            // Hydraulic Presses (8 units)
            ['name' => 'Hydraulic Press 500T #1', 'serial_number' => 'HYD-500-001', 'category' => 'Hydraulic Press', 'sublocation' => 'Press Line 1'],
            ['name' => 'Hydraulic Press 500T #2', 'serial_number' => 'HYD-500-002', 'category' => 'Hydraulic Press', 'sublocation' => 'Press Line 1'],
            ['name' => 'Hydraulic Press 800T #1', 'serial_number' => 'HYD-800-001', 'category' => 'Hydraulic Press', 'sublocation' => 'Press Line 1'],
            ['name' => 'Hydraulic Press 800T #2', 'serial_number' => 'HYD-800-002', 'category' => 'Hydraulic Press', 'sublocation' => 'Press Line 1'],
            ['name' => 'Hydraulic Press 300T #1', 'serial_number' => 'HYD-300-001', 'category' => 'Hydraulic Press', 'sublocation' => 'Press Line 2'],
            ['name' => 'Hydraulic Press 300T #2', 'serial_number' => 'HYD-300-002', 'category' => 'Hydraulic Press', 'sublocation' => 'Press Line 2'],
            ['name' => 'Mechanical Press 200T #1', 'serial_number' => 'MEC-200-001', 'category' => 'Mechanical Press', 'sublocation' => 'Press Line 2'],
            ['name' => 'Mechanical Press 200T #2', 'serial_number' => 'MEC-200-002', 'category' => 'Mechanical Press', 'sublocation' => 'Press Line 2'],

            // CNC Machines (8 units)
            ['name' => 'CNC Lathe Mazak #1', 'serial_number' => 'CNC-LTH-001', 'category' => 'CNC Machine', 'sublocation' => 'CNC Machining Area'],
            ['name' => 'CNC Lathe Mazak #2', 'serial_number' => 'CNC-LTH-002', 'category' => 'CNC Machine', 'sublocation' => 'CNC Machining Area'],
            ['name' => 'CNC Milling DMG #1', 'serial_number' => 'CNC-MIL-001', 'category' => 'CNC Machine', 'sublocation' => 'CNC Machining Area'],
            ['name' => 'CNC Milling DMG #2', 'serial_number' => 'CNC-MIL-002', 'category' => 'CNC Machine', 'sublocation' => 'CNC Machining Area'],
            ['name' => 'CNC Milling Haas', 'serial_number' => 'CNC-MIL-003', 'category' => 'CNC Machine', 'sublocation' => 'CNC Machining Area'],
            ['name' => 'CNC Grinding Studer', 'serial_number' => 'CNC-GRD-001', 'category' => 'CNC Machine', 'sublocation' => 'CNC Machining Area'],
            ['name' => 'CNC EDM Wire Cut', 'serial_number' => 'CNC-EDM-001', 'category' => 'CNC Machine', 'sublocation' => 'CNC Machining Area'],
            ['name' => 'CNC Drill Press', 'serial_number' => 'CNC-DRL-001', 'category' => 'CNC Machine', 'sublocation' => 'CNC Machining Area'],

            // Welding Equipment (4 units)
            ['name' => 'Robot Welder Fanuc #1', 'serial_number' => 'WLD-RBT-001', 'category' => 'Welding', 'sublocation' => 'Welding Bay'],
            ['name' => 'Robot Welder Fanuc #2', 'serial_number' => 'WLD-RBT-002', 'category' => 'Welding', 'sublocation' => 'Welding Bay'],
            ['name' => 'MIG Welder Station #1', 'serial_number' => 'WLD-MIG-001', 'category' => 'Welding', 'sublocation' => 'Welding Bay'],
            ['name' => 'TIG Welder Station #1', 'serial_number' => 'WLD-TIG-001', 'category' => 'Welding', 'sublocation' => 'Welding Bay'],

            // Painting Equipment (3 units)
            ['name' => 'Paint Spray Booth #1', 'serial_number' => 'PNT-BTH-001', 'category' => 'Painting', 'sublocation' => 'Painting Booth'],
            ['name' => 'Powder Coating System', 'serial_number' => 'PNT-PWD-001', 'category' => 'Painting', 'sublocation' => 'Painting Booth'],
            ['name' => 'Curing Oven', 'serial_number' => 'PNT-OVN-001', 'category' => 'Painting', 'sublocation' => 'Painting Booth'],

            // Assembly Line Equipment (6 units)
            ['name' => 'Conveyor System Line A', 'serial_number' => 'CNV-ASM-001', 'category' => 'Conveyor', 'sublocation' => 'Assembly Line A'],
            ['name' => 'Assembly Robot ABB #1', 'serial_number' => 'ASM-RBT-001', 'category' => 'Robot', 'sublocation' => 'Assembly Line A'],
            ['name' => 'Assembly Robot ABB #2', 'serial_number' => 'ASM-RBT-002', 'category' => 'Robot', 'sublocation' => 'Assembly Line A'],
            ['name' => 'Conveyor System Line B', 'serial_number' => 'CNV-ASM-002', 'category' => 'Conveyor', 'sublocation' => 'Assembly Line B'],
            ['name' => 'Torque Station Atlas', 'serial_number' => 'TRQ-STN-001', 'category' => 'Assembly Tool', 'sublocation' => 'Assembly Line B'],
            ['name' => 'Vision Inspection Cognex', 'serial_number' => 'VIS-INS-001', 'category' => 'Inspection', 'sublocation' => 'Assembly Line B'],

            // Utilities Equipment (8 units)
            ['name' => 'Air Compressor Atlas #1', 'serial_number' => 'CMP-AIR-001', 'category' => 'Compressor', 'sublocation' => 'Compressor Room'],
            ['name' => 'Air Compressor Atlas #2', 'serial_number' => 'CMP-AIR-002', 'category' => 'Compressor', 'sublocation' => 'Compressor Room'],
            ['name' => 'Air Dryer Ingersoll', 'serial_number' => 'DRY-AIR-001', 'category' => 'Air Treatment', 'sublocation' => 'Compressor Room'],
            ['name' => 'Transformer 1000 KVA', 'serial_number' => 'TRF-ELC-001', 'category' => 'Electrical', 'sublocation' => 'Electrical Substation'],
            ['name' => 'Main Distribution Panel', 'serial_number' => 'PNL-ELC-001', 'category' => 'Electrical', 'sublocation' => 'Electrical Substation'],
            ['name' => 'Chiller Carrier 200RT', 'serial_number' => 'CHL-200-001', 'category' => 'HVAC', 'sublocation' => 'Chiller Plant'],
            ['name' => 'Cooling Tower Marley', 'serial_number' => 'TWR-COL-001', 'category' => 'HVAC', 'sublocation' => 'Chiller Plant'],
            ['name' => 'Steam Boiler Miura 5T', 'serial_number' => 'BLR-STM-001', 'category' => 'Boiler', 'sublocation' => 'Boiler Room'],

            // Warehouse Equipment (5 units)
            ['name' => 'Forklift Toyota #1', 'serial_number' => 'FLT-ELC-001', 'category' => 'Material Handling', 'sublocation' => 'Raw Material Storage'],
            ['name' => 'Forklift Toyota #2', 'serial_number' => 'FLT-ELC-002', 'category' => 'Material Handling', 'sublocation' => 'Finished Goods Storage'],
            ['name' => 'Pallet Wrapping Machine', 'serial_number' => 'WRP-PLT-001', 'category' => 'Packaging', 'sublocation' => 'Packaging Area'],
            ['name' => 'Forklift Komatsu #1', 'serial_number' => 'FLT-GAS-001', 'category' => 'Material Handling', 'sublocation' => 'Raw Material Storage'],
            ['name' => 'Hand Pallet Truck', 'serial_number' => 'HPT-MAN-001', 'category' => 'Material Handling', 'sublocation' => 'Spare Parts Room'],
        ];

        foreach ($equipmentData as $equip) {
            $sublocationId = $sublocationIds[$equip['sublocation']] ?? null;
            Equipment::firstOrCreate(
                ['serial_number' => $equip['serial_number']],
                [
                    'name' => $equip['name'],
                    'serial_number' => $equip['serial_number'],
                    'category' => $equip['category'],
                    'sublocation_id' => $sublocationId,
                ]
            );
        }
        $this->command->info('✓ Created ' . count($equipmentData) . ' equipment records');

        // ==========================================
        // 7. PART STOCKS - fields: part_id, sap_id, name, quantity, min_quantity, price, supplier_id
        // ==========================================
        $parts = [
            // Bearings
            ['part_id' => 'BRG-6205-2RS', 'name' => 'Ball Bearing 6205 2RS', 'quantity' => 50, 'min_quantity' => 20, 'price' => 85000, 'supplier' => 'PT. Sumber Bearing Indonesia'],
            ['part_id' => 'BRG-6206-2RS', 'name' => 'Ball Bearing 6206 2RS', 'quantity' => 45, 'min_quantity' => 20, 'price' => 95000, 'supplier' => 'PT. Sumber Bearing Indonesia'],
            ['part_id' => 'BRG-6306-2RS', 'name' => 'Ball Bearing 6306 2RS', 'quantity' => 30, 'min_quantity' => 15, 'price' => 125000, 'supplier' => 'PT. Sumber Bearing Indonesia'],
            ['part_id' => 'BRG-22210-E', 'name' => 'Spherical Roller Bearing 22210', 'quantity' => 12, 'min_quantity' => 5, 'price' => 450000, 'supplier' => 'PT. Sumber Bearing Indonesia'],
            ['part_id' => 'BRG-32210', 'name' => 'Tapered Roller Bearing 32210', 'quantity' => 20, 'min_quantity' => 10, 'price' => 275000, 'supplier' => 'PT. Sumber Bearing Indonesia'],
            
            // Hydraulic Parts
            ['part_id' => 'HYD-SEAL-50', 'name' => 'Hydraulic Cylinder Seal Kit 50mm', 'quantity' => 25, 'min_quantity' => 10, 'price' => 350000, 'supplier' => 'PT. Hydraulic Power Systems'],
            ['part_id' => 'HYD-SEAL-80', 'name' => 'Hydraulic Cylinder Seal Kit 80mm', 'quantity' => 20, 'min_quantity' => 8, 'price' => 450000, 'supplier' => 'PT. Hydraulic Power Systems'],
            ['part_id' => 'HYD-HOSE-1/2', 'name' => 'Hydraulic Hose 1/2" x 1m', 'quantity' => 50, 'min_quantity' => 20, 'price' => 185000, 'supplier' => 'PT. Hydraulic Power Systems'],
            ['part_id' => 'HYD-PUMP-A10', 'name' => 'Hydraulic Pump A10VSO 28', 'quantity' => 3, 'min_quantity' => 2, 'price' => 15500000, 'supplier' => 'PT. Hydraulic Power Systems'],
            ['part_id' => 'HYD-VALVE-DIR', 'name' => 'Directional Control Valve 4WE6', 'quantity' => 8, 'min_quantity' => 3, 'price' => 2750000, 'supplier' => 'PT. Hydraulic Power Systems'],
            
            // Pneumatic Parts
            ['part_id' => 'PNU-CYL-32x100', 'name' => 'Pneumatic Cylinder 32x100', 'quantity' => 15, 'min_quantity' => 5, 'price' => 385000, 'supplier' => 'PT. Pneumatic Solutions'],
            ['part_id' => 'PNU-SOL-5/2', 'name' => 'Solenoid Valve 5/2 Way 1/4"', 'quantity' => 20, 'min_quantity' => 8, 'price' => 275000, 'supplier' => 'PT. Pneumatic Solutions'],
            ['part_id' => 'PNU-FRL-1/2', 'name' => 'Air Filter Regulator Lubricator', 'quantity' => 10, 'min_quantity' => 4, 'price' => 550000, 'supplier' => 'PT. Pneumatic Solutions'],
            
            // Electrical Parts
            ['part_id' => 'ELC-CNTR-S7', 'name' => 'Siemens Contactor 3RT1035', 'quantity' => 12, 'min_quantity' => 5, 'price' => 875000, 'supplier' => 'PT. Elektrik Prima'],
            ['part_id' => 'ELC-MCB-32A', 'name' => 'MCB 3P 32A Schneider', 'quantity' => 20, 'min_quantity' => 8, 'price' => 285000, 'supplier' => 'PT. Elektrik Prima'],
            ['part_id' => 'ELC-VFD-7.5', 'name' => 'VFD Inverter 7.5KW ABB', 'quantity' => 4, 'min_quantity' => 2, 'price' => 8500000, 'supplier' => 'PT. Elektrik Prima'],
            ['part_id' => 'ELC-PROX-M18', 'name' => 'Proximity Sensor M18 NPN', 'quantity' => 30, 'min_quantity' => 15, 'price' => 165000, 'supplier' => 'PT. Elektrik Prima'],
            ['part_id' => 'ELC-MOTOR-5.5', 'name' => 'Electric Motor 5.5KW 4P', 'quantity' => 3, 'min_quantity' => 2, 'price' => 7500000, 'supplier' => 'PT. Elektrik Prima'],
            
            // Lubricants
            ['part_id' => 'LUB-HYD-46', 'name' => 'Hydraulic Oil ISO VG 46 (drum)', 'quantity' => 20, 'min_quantity' => 10, 'price' => 2850000, 'supplier' => 'PT. Lubricant Indonesia'],
            ['part_id' => 'LUB-GRS-EP2', 'name' => 'Grease EP2 Lithium Base (kg)', 'quantity' => 50, 'min_quantity' => 20, 'price' => 85000, 'supplier' => 'PT. Lubricant Indonesia'],
            ['part_id' => 'LUB-COOL-10', 'name' => 'Cutting Coolant Concentrate (ltr)', 'quantity' => 40, 'min_quantity' => 15, 'price' => 125000, 'supplier' => 'PT. Lubricant Indonesia'],
            
            // Filters & Gaskets
            ['part_id' => 'FLT-AIR-CMP', 'name' => 'Air Filter Compressor Atlas', 'quantity' => 10, 'min_quantity' => 4, 'price' => 450000, 'supplier' => 'PT. Filter & Gasket Specialist'],
            ['part_id' => 'FLT-OIL-HYD', 'name' => 'Hydraulic Oil Filter 10 Micron', 'quantity' => 20, 'min_quantity' => 8, 'price' => 385000, 'supplier' => 'PT. Filter & Gasket Specialist'],
            ['part_id' => 'GSK-FLNG-4"', 'name' => 'Flange Gasket 4" ANSI 150', 'quantity' => 30, 'min_quantity' => 10, 'price' => 45000, 'supplier' => 'PT. Filter & Gasket Specialist'],
            
            // Belts & Chains
            ['part_id' => 'BLT-V-B68', 'name' => 'V-Belt B68', 'quantity' => 15, 'min_quantity' => 5, 'price' => 95000, 'supplier' => 'CV. Teknik Jaya Abadi'],
            ['part_id' => 'BLT-TIM-HTD', 'name' => 'Timing Belt HTD 8M 1200', 'quantity' => 8, 'min_quantity' => 3, 'price' => 285000, 'supplier' => 'CV. Teknik Jaya Abadi'],
            ['part_id' => 'CHN-ROLL-40', 'name' => 'Roller Chain #40 x 3m', 'quantity' => 10, 'min_quantity' => 4, 'price' => 175000, 'supplier' => 'CV. Teknik Jaya Abadi'],
        ];

        foreach ($parts as $part) {
            $supplierId = $supplierIds[$part['supplier']] ?? null;
            PartStock::firstOrCreate(
                ['part_id' => $part['part_id']],
                [
                    'part_id' => $part['part_id'],
                    'name' => $part['name'],
                    'quantity' => $part['quantity'],
                    'min_quantity' => $part['min_quantity'],
                    'price' => $part['price'],
                    'supplier_id' => $supplierId,
                ]
            );
        }
        $this->command->info('✓ Created ' . count($parts) . ' spare parts records');

        // ==========================================
        // 8. TYPE CHECKS - First create for equipment categories
        // Fields: equipment_id, period_id, name
        // ==========================================
        $periodIds = PeriodPm::pluck('id', 'name');
        $equipmentIds = Equipment::pluck('id', 'serial_number');

        // Create TypeChecks for specific equipment
        $typeChecksData = [
            ['equipment' => 'HYD-500-001', 'period' => 'Weekly', 'name' => 'Weekly Hydraulic Press PM'],
            ['equipment' => 'HYD-500-001', 'period' => 'Monthly', 'name' => 'Monthly Hydraulic Press PM'],
            ['equipment' => 'CNC-LTH-001', 'period' => 'Daily', 'name' => 'Daily CNC Lathe Check'],
            ['equipment' => 'CNC-LTH-001', 'period' => 'Weekly', 'name' => 'Weekly CNC Lathe PM'],
            ['equipment' => 'CMP-AIR-001', 'period' => 'Daily', 'name' => 'Daily Compressor Check'],
            ['equipment' => 'CMP-AIR-001', 'period' => 'Monthly', 'name' => 'Monthly Compressor PM'],
            ['equipment' => 'CHL-200-001', 'period' => 'Weekly', 'name' => 'Weekly Chiller Check'],
            ['equipment' => 'BLR-STM-001', 'period' => 'Daily', 'name' => 'Daily Boiler Check'],
        ];

        foreach ($typeChecksData as $tc) {
            $equipId = $equipmentIds[$tc['equipment']] ?? null;
            $periodId = $periodIds[$tc['period']] ?? null;
            if ($equipId && $periodId) {
                TypeCheck::firstOrCreate(
                    ['equipment_id' => $equipId, 'period_id' => $periodId, 'name' => $tc['name']],
                    ['equipment_id' => $equipId, 'period_id' => $periodId, 'name' => $tc['name']]
                );
            }
        }
        $this->command->info('✓ Created ' . count($typeChecksData) . ' type checks');

        // ==========================================
        // 9. CHECKLISTS - fields: type_check_id, name, recommended
        // ==========================================
        $typeCheckIds = TypeCheck::pluck('id', 'name');

        $checklists = [
            // Weekly Hydraulic Press PM
            ['type_check' => 'Weekly Hydraulic Press PM', 'name' => 'Check hydraulic oil level', 'recommended' => 'Oil level should be between MIN-MAX marks'],
            ['type_check' => 'Weekly Hydraulic Press PM', 'name' => 'Check for oil leaks on cylinders', 'recommended' => 'No visible leaks allowed'],
            ['type_check' => 'Weekly Hydraulic Press PM', 'name' => 'Check hydraulic filter pressure differential', 'recommended' => 'Delta P < 2 bar'],
            ['type_check' => 'Weekly Hydraulic Press PM', 'name' => 'Lubricate slide guides', 'recommended' => 'Apply grease EP2'],
            ['type_check' => 'Weekly Hydraulic Press PM', 'name' => 'Check safety guards and interlocks', 'recommended' => 'All guards must function properly'],
            
            // Daily CNC Lathe Check
            ['type_check' => 'Daily CNC Lathe Check', 'name' => 'Check coolant level', 'recommended' => 'Min 80% full'],
            ['type_check' => 'Daily CNC Lathe Check', 'name' => 'Check spindle oil level', 'recommended' => 'Oil in sight glass'],
            ['type_check' => 'Daily CNC Lathe Check', 'name' => 'Clean chip tray', 'recommended' => 'Remove all chips'],
            ['type_check' => 'Daily CNC Lathe Check', 'name' => 'Check tool holder condition', 'recommended' => 'No damage or wear'],
            
            // Daily Compressor Check  
            ['type_check' => 'Daily Compressor Check', 'name' => 'Check oil level in compressor', 'recommended' => 'Oil in sight glass'],
            ['type_check' => 'Daily Compressor Check', 'name' => 'Drain condensate from air receiver', 'recommended' => 'Drain until dry air'],
            ['type_check' => 'Daily Compressor Check', 'name' => 'Check discharge pressure', 'recommended' => '7-8 bar normal operation'],
            ['type_check' => 'Daily Compressor Check', 'name' => 'Check for unusual noise or vibration', 'recommended' => 'No abnormal sounds'],
            
            // Daily Boiler Check
            ['type_check' => 'Daily Boiler Check', 'name' => 'Check water level and alarms', 'recommended' => 'Water level in sight glass'],
            ['type_check' => 'Daily Boiler Check', 'name' => 'Blow down boiler', 'recommended' => 'Blow down for 5 seconds'],
            ['type_check' => 'Daily Boiler Check', 'name' => 'Check gas pressure', 'recommended' => '0.5-1.0 bar'],
            ['type_check' => 'Daily Boiler Check', 'name' => 'Check steam pressure', 'recommended' => 'Normal operating pressure'],
        ];

        foreach ($checklists as $check) {
            $typeCheckId = $typeCheckIds[$check['type_check']] ?? null;
            if ($typeCheckId) {
                Checklist::firstOrCreate(
                    ['type_check_id' => $typeCheckId, 'name' => $check['name']],
                    ['type_check_id' => $typeCheckId, 'name' => $check['name'], 'recommended' => $check['recommended']]
                );
            }
        }
        $this->command->info('✓ Created ' . count($checklists) . ' checklist items');

        $this->command->info('');
        $this->command->info('=== Sample Data Creation Complete ===');
        $this->command->info('Total: 6 Locations, 15 Sublocations, 7 Suppliers, 6 Maint Categories');
        $this->command->info('       7 Periods, 44 Equipment, 27 Parts, 8 Type Checks, 17 Checklists');
    }
}
