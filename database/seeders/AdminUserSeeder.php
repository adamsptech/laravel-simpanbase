<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@simpanbase.com',
            'password' => Hash::make('password123'),
            'role_id' => 1, // Admin role
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Planner user
        DB::table('users')->insert([
            'name' => 'Planner User',
            'email' => 'planner@simpanbase.com',
            'password' => Hash::make('password123'),
            'role_id' => 2, // Planner role
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Engineer user
        DB::table('users')->insert([
            'name' => 'Engineer User',
            'email' => 'engineer@simpanbase.com',
            'password' => Hash::make('password123'),
            'role_id' => 3, // Engineer role
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Supervisor user
        DB::table('users')->insert([
            'name' => 'Supervisor User',
            'email' => 'supervisor@simpanbase.com',
            'password' => Hash::make('password123'),
            'role_id' => 4, // Supervisor role
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Manager user
        DB::table('users')->insert([
            'name' => 'Manager User',
            'email' => 'manager@simpanbase.com',
            'password' => Hash::make('password123'),
            'role_id' => 5, // Manager role
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Customer user
        DB::table('users')->insert([
            'name' => 'Customer User',
            'email' => 'customer@simpanbase.com',
            'password' => Hash::make('password123'),
            'role_id' => 6, // Customer role
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
