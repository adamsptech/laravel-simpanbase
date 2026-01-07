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
        Schema::create('part_additions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_stock_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->decimal('price', 12, 2)->nullable();
            $table->string('opb_number')->nullable()->comment('Purchase order/OPB number');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->date('add_date');
            $table->integer('current_stock_after')->default(0)->comment('Stock after this addition');
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['part_stock_id', 'add_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_additions');
    }
};
