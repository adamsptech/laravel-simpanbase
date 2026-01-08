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
        Schema::table('machine_downtimes', function (Blueprint $table) {
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open')->after('downtime_minutes');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->foreignId('picked_up_by')->nullable()->constrained('users')->nullOnDelete()->after('submitted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_downtimes', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['picked_up_by']);
            $table->dropColumn(['status', 'submitted_by', 'picked_up_by']);
        });
    }
};
