<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('maint_categories')->insert([
            ['name' => 'Preventive Maintenance', 'description' => 'Scheduled maintenance to prevent equipment failure', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Corrective Maintenance', 'description' => 'Maintenance to fix equipment issues', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
