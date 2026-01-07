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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('sublocation_id')->nullable()->constrained('sublocations')->nullOnDelete();
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->nullOnDelete();
            $table->foreignId('period_id')->nullable()->constrained('period_pms')->nullOnDelete();
            $table->foreignId('type_check_id')->nullable()->constrained('type_checks')->nullOnDelete();
            $table->foreignId('maint_category_id')->nullable()->constrained('maint_categories')->nullOnDelete();
            
            // Status: 0=Open, 1=SubmittedToSupervisor, 2=SubmittedToManager, 3=SubmittedToCustomer, 4=Closed
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('priority')->default(1); // 1=Low, 2=Medium, 3=High
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Approvals
            $table->foreignId('approval1_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approval1_at')->nullable();
            $table->foreignId('approval2_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approval2_at')->nullable();
            $table->foreignId('approval3_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approval3_at')->nullable();
            
            // Timing
            $table->date('due_date')->nullable();
            $table->string('duration', 10)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            
            // Details
            $table->text('notes')->nullable();
            $table->string('shift', 10)->nullable();
            $table->string('files', 500)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
