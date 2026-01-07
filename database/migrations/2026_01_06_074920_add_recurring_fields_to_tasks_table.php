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
            // Series identifier - groups all tasks belonging to the same recurring series
            $table->string('series_id', 50)->nullable()->after('notes');
            
            // Recurrence type: single, daily, weekly, monthly
            $table->enum('recurrence_type', ['single', 'daily', 'weekly', 'monthly'])->default('single')->after('series_id');
            
            // End date for the recurring series
            $table->date('recurrence_end_date')->nullable()->after('recurrence_type');
            
            // Flag indicating if the task was individually modified (exception from series)
            $table->boolean('is_series_exception')->default(false)->after('recurrence_end_date');
            
            // Indexes for performance
            $table->index('series_id');
            $table->index('recurrence_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['series_id']);
            $table->dropIndex(['recurrence_type']);
            $table->dropColumn(['series_id', 'recurrence_type', 'recurrence_end_date', 'is_series_exception']);
        });
    }
};
