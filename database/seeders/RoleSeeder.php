<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'default_page' => '/admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Planner', 'default_page' => '/admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Engineer', 'default_page' => '/admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supervisor', 'default_page' => '/admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'default_page' => '/admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Customer', 'default_page' => '/customer', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
