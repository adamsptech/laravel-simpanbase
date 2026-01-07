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
        Schema::table('tasks', function (Blueprint $table) {
            // Performance indexes for common queries
            $table->index('due_date', 'idx_tasks_due_date');
            $table->index('status', 'idx_tasks_status');
            $table->index('priority', 'idx_tasks_priority');
            $table->index('series_id', 'idx_tasks_series_id');
            
            // Composite indexes for common filters
            $table->index(['status', 'due_date'], 'idx_tasks_status_due_date');
            $table->index(['maint_category_id', 'due_date'], 'idx_tasks_category_due_date');
            $table->index(['assigned_to', 'status'], 'idx_tasks_assigned_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_due_date');
            $table->dropIndex('idx_tasks_status');
            $table->dropIndex('idx_tasks_priority');
            $table->dropIndex('idx_tasks_series_id');
            $table->dropIndex('idx_tasks_status_due_date');
            $table->dropIndex('idx_tasks_category_due_date');
            $table->dropIndex('idx_tasks_assigned_status');
        });
    }
};
