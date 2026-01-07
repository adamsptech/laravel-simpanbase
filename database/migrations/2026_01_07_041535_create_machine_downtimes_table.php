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
        Schema::create('machine_downtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->string('problem');
            $table->text('root_cause')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->integer('downtime_minutes')->default(0);
            $table->integer('year');
            $table->integer('month');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes for reporting
            $table->index(['year', 'month']);
            $table->index(['equipment_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_downtimes');
    }
};
