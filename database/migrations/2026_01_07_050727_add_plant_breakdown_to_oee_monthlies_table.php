<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oee_monthlies', function (Blueprint $table) {
            // Plant Operating (same as calendar initially)
            $table->integer('plant_operating_days')->default(0)->after('working_minutes');
            $table->integer('plant_operating_hours')->default(0)->after('plant_operating_days');
            $table->integer('plant_operating_minutes')->default(0)->after('plant_operating_hours');
            $table->decimal('plant_operating_percentage', 8, 2)->default(100)->after('plant_operating_minutes');
            
            // Planned Maintenance
            $table->integer('planned_maintenance_days')->default(0)->after('plant_operating_percentage');
            $table->integer('planned_maintenance_hours')->default(0)->after('planned_maintenance_days');
            $table->integer('planned_maintenance_minutes')->default(0)->after('planned_maintenance_hours');
            $table->decimal('planned_maintenance_percentage', 8, 2)->default(0)->after('planned_maintenance_minutes');
            
            // Plant Production (after planned maintenance)
            $table->integer('plant_production_days')->default(0)->after('planned_maintenance_percentage');
            $table->integer('plant_production_hours')->default(0)->after('plant_production_days');
            $table->integer('plant_production_minutes')->default(0)->after('plant_production_hours');
            $table->decimal('plant_production_percentage', 8, 2)->default(100)->after('plant_production_minutes');
            
            // Unplanned Maintenance
            $table->integer('unplanned_maintenance_days')->default(0)->after('plant_production_percentage');
            $table->integer('unplanned_maintenance_hours')->default(0)->after('unplanned_maintenance_days');
            $table->integer('unplanned_maintenance_minutes')->default(0)->after('unplanned_maintenance_hours');
            $table->decimal('unplanned_maintenance_percentage', 8, 2)->default(0)->after('unplanned_maintenance_minutes');
            
            // Actual Plant Production
            $table->integer('actual_production_days')->default(0)->after('unplanned_maintenance_percentage');
            $table->integer('actual_production_hours')->default(0)->after('actual_production_days');
            $table->integer('actual_production_minutes')->default(0)->after('actual_production_hours');
            $table->decimal('actual_production_percentage', 8, 2)->default(100)->after('actual_production_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oee_monthlies', function (Blueprint $table) {
            $table->dropColumn([
                'plant_operating_days', 'plant_operating_hours', 'plant_operating_minutes', 'plant_operating_percentage',
                'planned_maintenance_days', 'planned_maintenance_hours', 'planned_maintenance_minutes', 'planned_maintenance_percentage',
                'plant_production_days', 'plant_production_hours', 'plant_production_minutes', 'plant_production_percentage',
                'unplanned_maintenance_days', 'unplanned_maintenance_hours', 'unplanned_maintenance_minutes', 'unplanned_maintenance_percentage',
                'actual_production_days', 'actual_production_hours', 'actual_production_minutes', 'actual_production_percentage',
            ]);
        });
    }
};
