<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodPmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('period_pms')->insert([
            ['name' => 'Daily', 'days' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Weekly', 'days' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bi-weekly', 'days' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly', 'days' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Quarterly', 'days' => 90, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Semi-annual', 'days' => 180, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Annual', 'days' => 365, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
