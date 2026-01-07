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
        Schema::create('part_adds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_stock_id')->constrained('part_stocks')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 18, 2)->nullable();
            $table->string('opb_number', 50)->nullable(); // Purchase order number
            $table->string('serial_number', 50)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->integer('stock_after')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_adds');
    }
};
