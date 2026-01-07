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
        Schema::create('type_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('period_id')->nullable()->constrained('period_pms')->nullOnDelete();
            $table->string('name', 50); // Checklist type name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_checks');
    }
};
