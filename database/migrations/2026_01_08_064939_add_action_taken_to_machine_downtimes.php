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
            $table->text('action_taken')->nullable()->after('root_cause');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_downtimes', function (Blueprint $table) {
            $table->dropColumn('action_taken');
        });
    }
};
