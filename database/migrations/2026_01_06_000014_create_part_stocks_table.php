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
        Schema::create('part_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('part_id', 200)->unique(); // Custom part ID
            $table->string('sap_id', 200)->nullable();
            $table->string('name', 255);
            $table->decimal('quantity', 20, 2)->default(0);
            $table->decimal('min_quantity', 20, 2)->nullable();
            $table->decimal('price', 20, 2)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('part_addresses')->nullOnDelete();
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->nullOnDelete();
            $table->boolean('is_obsolete')->default(false);
            $table->string('image', 200)->nullable();
            $table->integer('reminder_days')->nullable();
            $table->date('last_reminder_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_stocks');
    }
};
