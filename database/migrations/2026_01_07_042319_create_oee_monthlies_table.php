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
        Schema::create('oee_monthlies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->integer('year');
            $table->integer('month');
            $table->integer('working_days')->default(0)->comment('Working days in the month');
            $table->integer('working_hours')->default(0)->comment('Total working hours');
            $table->integer('working_minutes')->default(0)->comment('Total working minutes');
            $table->decimal('availability', 8, 2)->default(100)->comment('Availability % (uptime)');
            $table->decimal('performance', 8, 2)->default(100)->comment('Performance %');
            $table->decimal('quality', 8, 2)->default(100)->comment('Quality %');
            $table->decimal('oee_percentage', 8, 2)->default(100)->comment('OEE = Availability x Performance x Quality');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['equipment_id', 'year', 'month']);
            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oee_monthlies');
    }
};
